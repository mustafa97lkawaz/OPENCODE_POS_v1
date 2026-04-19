<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Setting;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class PrintController extends Controller
{
    /**
     * Paper sizes constants
     */
    const PAPER_80MM = '80mm';
    const PAPER_58MM = '58mm';

    /**
     * Character widths for different paper sizes
     */
    const WIDTH_80MM = 48;
    const WIDTH_58MM = 32;

    /**
     * Get printer settings and configuration
     */
    private function getPrinterSettings()
    {
        $settings = Setting::first();
        
        return [
            'printer_type' => $settings->printer_type ?? self::PAPER_80MM,
            'receipt_header' => $settings->receipt_header ?? '',
            'receipt_footer' => $settings->receipt_footer ?? '',
            'store_name' => $settings->store_name ?? 'متجر我们的商店',
            'vat_number' => $settings->vat_number ?? '',
        ];
    }

    /**
     * Get character width based on printer type
     */
    private function getCharWidth($printerType)
    {
        return $printerType === self::PAPER_58MM ? self::WIDTH_58MM : self::WIDTH_80MM;
    }

    /**
     * Format text to fit within character width
     */
    private function formatText($text, $width, $align = 'left')
    {
        $text = mb_substr($text, 0, $width);
        
        switch ($align) {
            case 'center':
                return mb_str_pad($text, $width, ' ', STR_PAD_BOTH);
            case 'right':
                return mb_str_pad($text, $width, ' ', STR_PAD_LEFT);
            default:
                return mb_str_pad($text, $width, ' ', STR_PAD_RIGHT);
        }
    }

    /**
     * Format number with currency symbol
     */
    private function formatAmount($amount, $width)
    {
        $formatted = number_format($amount, 2);
        return mb_str_pad($formatted, $width, ' ', STR_PAD_LEFT);
    }

    /**
     * Print receipt to thermal printer
     */
    public function printReceipt($saleId)
    {
        // Get sale with items
        $sale = Sale::with(['saleItems.product', 'customer'])->find($saleId);
        
        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'الفاتورة غير موجودة'
            ], 404);
        }

        // Get printer settings
        $settings = $this->getPrinterSettings();
        $printerType = $settings['printer_type'];
        $charWidth = $this->getCharWidth($printerType);

        try {
            // Get printer name from settings or use default
            $printerName = $settings['printer_name'] ?? 'POS58';
            
            // Create connector - try Windows printer first, then network
            $connector = $this->createConnector($printerName);
            
            // Create printer instance
            $printer = new Printer($connector);
            
            // Set encoding for Arabic support
            $printer->setEncoding('UTF-8');
            
            // Print receipt
            $this->printReceiptContent($printer, $sale, $settings, $charWidth);
            
            // Cut paper
            $printer->cut();
            
            // Close printer
            $printer->close();
            
            return response()->json([
                'success' => true,
                'message' => 'تم طباعة الفاتورة بنجاح'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في الطباعة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create print connector based on printer configuration
     */
    private function createConnector($printerName)
    {
        // Try Windows printer first
        try {
            return new WindowsPrintConnector($printerName);
        } catch (\Exception $e) {
            // If Windows connector fails, try network connector
            // Default network printer IP - configure based on your setup
            $printerIp = '192.168.1.100';
            $printerPort = 9100;
            
            return new NetworkPrintConnector($printerIp, $printerPort);
        }
    }

    /**
     * Print receipt content
     */
    private function printReceiptContent($printer, $sale, $settings, $charWidth)
    {
        $printerType = $settings['printer_type'];
        
        // ==================== HEADER ====================
        
        // Store name - centered and bold
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($this->formatText($settings['store_name'], $charWidth, 'center') . "\n");
        
        // Receipt header from settings (if exists)
        if (!empty($settings['receipt_header'])) {
            $printer->setTextSize(0, 0);
            $printer->text($this->formatText($settings['receipt_header'], $charWidth, 'center') . "\n");
        }
        
        // VAT Number
        if (!empty($settings['vat_number'])) {
            $printer->text($this->formatText('الرقم الضريبي: ' . $settings['vat_number'], $charWidth, 'center') . "\n");
        }
        
        // separator
        $printer->text($this->createSeparator($charWidth) . "\n");
        
        // ==================== INVOICE INFO ====================
        
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setTextSize(0, 0);
        
        // Invoice number
        $printer->text($this->formatText('رقم الفاتورة: ' . $sale->invoice_number, $charWidth) . "\n");
        
        // Date/time
        $date = $sale->created_at->format('Y-m-d H:i:s');
        $printer->text($this->formatText('التاريخ: ' . $date, $charWidth) . "\n");
        
        // Cashier name
        $printer->text($this->formatText('الكاشير: ' . ($sale->Created_by ?? 'غير محدد'), $charWidth) . "\n");
        
        // Customer name (if exists)
        if ($sale->customer) {
            $printer->text($this->formatText('العميل: ' . $sale->customer->Customer_name, $charWidth) . "\n");
        }
        
        // Separator
        $printer->text($this->createSeparator($charWidth) . "\n");
        
        // ==================== ITEMS HEADER ====================
        
        $printer->setTextSize(0, 0);
        
        // Item header based on paper size
        if ($printerType === self::PAPER_58MM) {
            // 58mm format: Product | Qty | Total
            $printer->text($this->formatText('المنتج', 15) . $this->formatText('الكمية', 5) . $this->formatText('الاجمالي', 10) . "\n");
        } else {
            // 80mm format: Product | Qty | Price | Total
            $printer->text($this->formatText('المنتج', 20) . $this->formatText('الكمية', 6) . $this->formatText('السعر', 10) . $this->formatText('الاجمالي', 10) . "\n");
        }
        
        // Separator
        $printer->text($this->createSeparator($charWidth) . "\n");
        
        // ==================== ITEMS ====================
        
        foreach ($sale->saleItems as $item) {
            $productName = $item->product->Product_name ?? 'منتج محذوف';
            $qty = $item->qty;
            $unitPrice = $item->unit_price;
            $total = $item->total;
            
            if ($printerType === self::PAPER_58MM) {
                // 58mm format
                $productName = mb_substr($productName, 0, 15);
                $line = $this->formatText($productName, 15) 
                      . $this->formatText((string)$qty, 5) 
                      . $this->formatText(number_format($total, 2), 10) 
                      . "\n";
            } else {
                // 80mm format
                $productName = mb_substr($productName, 0, 20);
                $line = $this->formatText($productName, 20) 
                      . $this->formatText((string)$qty, 6) 
                      . $this->formatText(number_format($unitPrice, 2), 10) 
                      . $this->formatText(number_format($total, 2), 10) 
                      . "\n";
            }
            
            $printer->text($line);
        }
        
        // Separator
        $printer->text($this->createSeparator($charWidth) . "\n");
        
        // ==================== TOTALS ====================
        
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        
        // Subtotal
        $printer->text($this->formatText('المجموع: ', 20) . $this->formatText(number_format($sale->subtotal, 2), 20) . "\n");
        
        // Tax
        $printer->text($this->formatText('الضريبة (15%): ', 20) . $this->formatText(number_format($sale->tax_amount, 2), 20) . "\n");
        
        // Discount
        if ($sale->discount > 0) {
            $printer->text($this->formatText('الخصم: ', 20) . $this->formatText('-' . number_format($sale->discount, 2), 20) . "\n");
        }
        
        // Total - bold
        $printer->setTextSize(1, 1);
        $printer->text($this->formatText('الاجمالي: ', 20) . $this->formatText(number_format($sale->total, 2), 20) . "\n");
        
        $printer->setTextSize(0, 0);
        
        // Separator
        $printer->text($this->createSeparator($charWidth) . "\n");
        
        // ==================== PAYMENT INFO ====================
        
        // Payment method
        $paymentMethod = '';
        switch ($sale->payment_method) {
            case 'cash':
                $paymentMethod = 'نقدي';
                break;
            case 'card':
                $paymentMethod = 'بطاقة';
                break;
            default:
                $paymentMethod = 'Split';
        }
        
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text($this->formatText('طريقة الدفع: ' . $paymentMethod, $charWidth) . "\n");
        
        // Amount paid
        $printer->text($this->formatText('المبلغ المدفوع: ' . number_format($sale->paid_amount, 2), $charWidth) . "\n");
        
        // Change due
        if ($sale->change_due > 0) {
            $printer->text($this->formatText('الباقي: ' . number_format($sale->change_due, 2), $charWidth) . "\n");
        }
        
        // Separator
        $printer->text($this->createSeparator($charWidth) . "\n");
        
        // ==================== FOOTER ====================
        
        if (!empty($settings['receipt_footer'])) {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(0, 0);
            $printer->text($this->formatText($settings['receipt_footer'], $charWidth, 'center') . "\n");
            $printer->text("\n");
        }
        
        // Thank you message
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($this->formatText('شكراً لتعاملكم', $charWidth, 'center') . "\n");
        $printer->text($this->formatText('المرتجيات خلال 14 يوم', $charWidth, 'center') . "\n");
        
        // Feed and cut
        $printer->feed(3);
    }

    /**
     * Create separator line
     */
    private function createSeparator($width)
    {
        return str_repeat('-', $width);
    }

    /**
     * Print test page
     */
    public function testPrint()
    {
        $settings = $this->getPrinterSettings();
        $printerType = $settings['printer_type'];
        $charWidth = $this->getCharWidth($printerType);

        try {
            $connector = $this->createConnector('POS58');
            $printer = new Printer($connector);
            
            $printer->setEncoding('UTF-8');
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(1, 1);
            
            $printer->text($this->formatText('اختبار الطابعة', $charWidth, 'center') . "\n");
            $printer->text($this->formatText('Test Print', $charWidth, 'center') . "\n");
            
            $printer->feed(3);
            $printer->cut();
            $printer->close();
            
            return response()->json([
                'success' => true,
                'message' => 'تم اختبار الطابعة بنجاح'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available printers (Windows only)
     */
    public function getPrinters()
    {
        try {
            $printers = [];
            
            // Windows printer registry
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $cmd = 'wmic printer get name';
                exec($cmd, $output, $result);
                
                if ($result === 0 && !empty($output)) {
                    foreach ($output as $printer) {
                        $printer = trim($printer);
                        if (!empty($printer) && $printer !== 'Name') {
                            $printers[] = $printer;
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'printers' => $printers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}