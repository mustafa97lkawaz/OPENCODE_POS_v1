<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Setting;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class PrintController extends Controller
{
    const PAPER_80MM     = '80mm';
    const PAPER_58MM     = '58mm';
    const WIDTH_80MM     = 48;
    const WIDTH_58MM     = 32;
    const IMG_WIDTH_80MM = 576;
    const IMG_WIDTH_58MM = 384;

    private function getPrinterSettings()
    {
        $settings = Setting::first();
        return [
            'printer_type'   => $settings->printer_type   ?? self::PAPER_80MM,
            'printer_name'   => $settings->printer_name   ?? 'XP-80',
            'receipt_header' => $settings->receipt_header ?? '',
            'receipt_footer' => $settings->receipt_footer ?? '',
            'store_name'     => $settings->store_name     ?? 'متجري',
            'vat_number'     => $settings->vat_number     ?? '',
        ];
    }

    private function getCharWidth($printerType)
    {
        return $printerType === self::PAPER_58MM ? self::WIDTH_58MM : self::WIDTH_80MM;
    }

    private function getPrintWidthPx(string $printerType): int
    {
        return $printerType === self::PAPER_58MM ? self::IMG_WIDTH_58MM : self::IMG_WIDTH_80MM;
    }

    private function createSeparator($width)
    {
        return str_repeat('-', $width);
    }

    private function getArabicFont(): string
    {
        foreach (['C:/Windows/Fonts/tahoma.ttf', 'C:/Windows/Fonts/arial.ttf', 'C:/Windows/Fonts/calibri.ttf'] as $f) {
            if (file_exists($f)) return $f;
        }
        throw new \RuntimeException('Arabic font not found in C:/Windows/Fonts.');
    }

    private function reshapeArabic(string $text): string
    {
        static $arabic = null;
        if ($arabic === null) {
            require_once base_path('vendor/khaled.alshamaa/ar-php/src/Arabic.php');
            $arabic = new \ArPHP\I18N\Arabic();
        }
        return $arabic->utf8Glyphs($text);
    }

    private function renderTextImage(string $text, int $imgWidth, string $align = 'right', int $fontSize = 16): string
    {
        $text = $this->reshapeArabic($text);
        $font = $this->getArabicFont();
        $bbox = imagettfbbox($fontSize, 0, $font, $text);
        $tw   = abs($bbox[4] - $bbox[0]);
        $th   = abs($bbox[5] - $bbox[1]);

        $img   = imagecreatetruecolor($imgWidth, $th + 8);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        switch ($align) {
            case 'center': $x = max(0, intdiv($imgWidth - $tw, 2)); break;
            case 'left':   $x = 2; break;
            default:       $x = max(0, $imgWidth - $tw - 2);
        }

        imagettftext($img, $fontSize, 0, $x, $th + 4, $black, $font, $text);

        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'pos_' . uniqid() . '.png';
        imagepng($img, $path);
        imagedestroy($img);
        return $path;
    }

    private function printLine(Printer $printer, string $text, int $imgWidth, string $align = 'right', int $fontSize = 16): void
    {
        if (trim($text) === '') return;
        $path = $this->renderTextImage($text, $imgWidth, $align, $fontSize);
        try {
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->bitImage(EscposImage::load($path, false));
        } finally {
            @unlink($path);
        }
    }

    private function printItemRow(Printer $printer, string $name, string $qty, string $unitPrice, string $total, int $imgWidth, bool $is58mm): void
    {
        $font = $this->getArabicFont();
        $fs   = 13;
        $img  = imagecreatetruecolor($imgWidth, $fs + 12);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);
        $y = $fs + 4;

        if ($is58mm) {
            imagettftext($img, $fs, 0, 2, $y, $black, $font, $total);
            imagettftext($img, $fs, 0, (int)($imgWidth * 0.26), $y, $black, $font, $qty);
        } else {
            imagettftext($img, $fs, 0, 2, $y, $black, $font, $total);
            imagettftext($img, $fs, 0, (int)($imgWidth * 0.21), $y, $black, $font, $unitPrice);
            imagettftext($img, $fs, 0, (int)($imgWidth * 0.42), $y, $black, $font, $qty);
        }

        $name = $this->reshapeArabic($name);
        $bbox = imagettfbbox($fs, 0, $font, $name);
        $nw   = abs($bbox[4] - $bbox[0]);
        imagettftext($img, $fs, 0, max(0, $imgWidth - $nw - 2), $y, $black, $font, $name);

        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'pos_item_' . uniqid() . '.png';
        imagepng($img, $path);
        imagedestroy($img);
        try {
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->bitImage(EscposImage::load($path, false));
        } finally {
            @unlink($path);
        }
    }

    private function printReceiptContent($printer, $sale, $settings, $charWidth)
    {
        $printerType = $settings['printer_type'];
        $imgWidth    = $this->getPrintWidthPx($printerType);
        $is58mm      = ($printerType === self::PAPER_58MM);
        $sep         = fn() => $printer->text($this->createSeparator($charWidth) . "\n");

        $this->printLine($printer, $settings['store_name'], $imgWidth, 'center', 20);
        if (!empty($settings['receipt_header']))
            $this->printLine($printer, $settings['receipt_header'], $imgWidth, 'center');
        if (!empty($settings['vat_number']))
            $this->printLine($printer, 'الرقم الضريبي: ' . $settings['vat_number'], $imgWidth, 'center');
        $sep();

        $this->printLine($printer, 'رقم الفاتورة: ' . $sale->invoice_number, $imgWidth);
        $this->printLine($printer, 'التاريخ: ' . $sale->created_at->format('Y-m-d H:i:s'), $imgWidth);
        $this->printLine($printer, 'الكاشير: ' . ($sale->Created_by ?? 'غير محدد'), $imgWidth);
        if ($sale->customer)
            $this->printLine($printer, 'العميل: ' . $sale->customer->Customer_name, $imgWidth);
        $sep();

        $this->printItemRow($printer, 'المنتج', 'الكمية', 'السعر', 'الاجمالي', $imgWidth, $is58mm);
        $sep();

        foreach ($sale->saleItems as $item) {
            $this->printItemRow(
                $printer,
                $item->product->Product_name ?? 'منتج محذوف',
                (string) $item->qty,
                number_format($item->unit_price, 2),
                number_format($item->total, 2),
                $imgWidth,
                $is58mm
            );
        }
        $sep();

        $this->printLine($printer, 'المجموع: ' . number_format($sale->subtotal, 2), $imgWidth);
        $this->printLine($printer, 'الضريبة (15%): ' . number_format($sale->tax_amount, 2), $imgWidth);
        if ($sale->discount > 0)
            $this->printLine($printer, 'الخصم: -' . number_format($sale->discount, 2), $imgWidth);
        $this->printLine($printer, 'الاجمالي: ' . number_format($sale->total, 2), $imgWidth, 'right', 18);
        $sep();

        $method = match ($sale->payment_method) {
            'cash'  => 'نقدي',
            'card'  => 'بطاقة',
            default => 'Split',
        };
        $this->printLine($printer, 'طريقة الدفع: ' . $method, $imgWidth);
        $this->printLine($printer, 'المبلغ المدفوع: ' . number_format($sale->paid_amount, 2), $imgWidth);
        if ($sale->change_due > 0)
            $this->printLine($printer, 'الباقي: ' . number_format($sale->change_due, 2), $imgWidth);
        $sep();

        if (!empty($settings['receipt_footer']))
            $this->printLine($printer, $settings['receipt_footer'], $imgWidth, 'center');
        $this->printLine($printer, 'شكراً لتعاملكم', $imgWidth, 'center');
        $this->printLine($printer, 'المرتجعات خلال 14 يوم', $imgWidth, 'center');
        $printer->feed(3);
    }

    public function printReceipt($saleId)
    {
        $sale = Sale::with(['saleItems.product', 'customer'])->find($saleId);
        if (!$sale)
            return response()->json(['success' => false, 'message' => 'الفاتورة غير موجودة'], 404);

        $settings  = $this->getPrinterSettings();
        $charWidth = $this->getCharWidth($settings['printer_type']);

        try {
            $tempFile = $this->buildReceiptFile(function ($printer) use ($sale, $settings, $charWidth) {
                $this->printReceiptContent($printer, $sale, $settings, $charWidth);
                $printer->cut();
            });
            $this->sendFileToPrinter($tempFile, $settings['printer_name']);
            return response()->json(['success' => true, 'message' => 'تم طباعة الفاتورة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'خطأ في الطباعة: ' . $e->getMessage()], 500);
        }
    }

    public function testPrint()
    {
        $settings  = $this->getPrinterSettings();
        $charWidth = $this->getCharWidth($settings['printer_type']);
        $imgWidth  = $this->getPrintWidthPx($settings['printer_type']);

        try {
            $tempFile = $this->buildReceiptFile(function ($printer) use ($charWidth, $imgWidth) {
                $this->printLine($printer, 'اختبار الطابعة', $imgWidth, 'center', 18);
                $this->printLine($printer, 'Test Print OK', $imgWidth, 'center', 16);
                $printer->text($this->createSeparator($charWidth) . "\n");
                $printer->feed(3);
                $printer->cut();
            });
            $this->sendFileToPrinter($tempFile, $settings['printer_name']);
            return response()->json(['success' => true, 'message' => 'تم اختبار الطابعة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()], 500);
        }
    }

    public function getPrinters()
    {
        try {
            $printers = [];
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec('wmic printer get name', $output, $result);
                if ($result === 0) {
                    foreach ($output as $line) {
                        $line = trim($line);
                        if (!empty($line) && $line !== 'Name') $printers[] = $line;
                    }
                }
            }
            return response()->json(['success' => true, 'printers' => $printers]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function debugPrinter($printerName = 'XP-80')
    {
        $port = $this->getWindowsPrinterPort($printerName);
        exec('wmic printer where "Name=\'' . $printerName . '\'" get PortName /format:value 2>&1', $wmicOut);
        return response()->json([
            'printer_name'  => $printerName,
            'resolved_port' => $port,
            'hostname'      => php_uname('n'),
            'wmic_output'   => $wmicOut,
        ]);
    }

    private function buildReceiptFile(callable $printCallback): string
    {
        $tempFile  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'pos_receipt_' . uniqid() . '.bin';
        $connector = new FilePrintConnector($tempFile);
        $printer   = new Printer($connector);
        $printCallback($printer);
        $printer->close();
        return $tempFile;
    }

    private function sendFileToPrinter(string $tempFile, string $printerName): void
    {
        $ps  = realpath(base_path('electron/rawprint.ps1'));
        $cmd = 'powershell -NoProfile -NonInteractive -ExecutionPolicy Bypass -File "'
             . $ps . '" -PrinterName "' . addslashes($printerName)
             . '" -FilePath "' . addslashes($tempFile) . '" 2>&1';
        exec($cmd, $output, $code);
        @unlink($tempFile);
        if ($code !== 0)
            throw new \Exception('فشل الطباعة: ' . implode(' ', $output));
    }

    private function getWindowsPrinterPort(string $printerName): ?string
    {
        $escaped = str_replace("'", "\\'", $printerName);
        exec('wmic printer where "Name=\'' . $escaped . '\'" get PortName /format:value 2>&1', $lines);
        foreach ($lines as $line) {
            $line = trim($line);
            if (stripos($line, 'PortName=') === 0) {
                $port = trim(substr($line, 9));
                if ($port !== '') return $port;
            }
        }
        return null;
    }
}
