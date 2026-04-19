param(
    [string]$PrinterName,
    [string]$FilePath
)

Add-Type @"
using System;
using System.Runtime.InteropServices;

public class RawPrint {
    [DllImport("winspool.Drv", CharSet=CharSet.Ansi)]
    public static extern bool OpenPrinter(string szPrinter, out IntPtr hPrinter, IntPtr pd);

    [DllImport("winspool.Drv")]
    public static extern bool ClosePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", CharSet=CharSet.Ansi)]
    public static extern int StartDocPrinter(IntPtr hPrinter, int level, ref DOCINFO di);

    [DllImport("winspool.Drv")]
    public static extern bool EndDocPrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv")]
    public static extern bool StartPagePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv")]
    public static extern bool EndPagePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv")]
    public static extern bool WritePrinter(IntPtr hPrinter, byte[] buf, int count, out int written);
}

[StructLayout(LayoutKind.Sequential, CharSet=CharSet.Ansi)]
public struct DOCINFO {
    [MarshalAs(UnmanagedType.LPStr)] public string docName;
    [MarshalAs(UnmanagedType.LPStr)] public string outputFile;
    [MarshalAs(UnmanagedType.LPStr)] public string dataType;
}
"@

try {
    $bytes   = [System.IO.File]::ReadAllBytes($FilePath)
    $hPrinter = [IntPtr]::Zero

    if (-not [RawPrint]::OpenPrinter($PrinterName, [ref]$hPrinter, [IntPtr]::Zero)) {
        Write-Error "Cannot open printer: $PrinterName"
        exit 1
    }

    $doc = New-Object DOCINFO
    $doc.docName  = "POS Receipt"
    $doc.dataType = "RAW"

    [RawPrint]::StartDocPrinter($hPrinter, 1, [ref]$doc) | Out-Null
    [RawPrint]::StartPagePrinter($hPrinter) | Out-Null

    $written = 0
    [RawPrint]::WritePrinter($hPrinter, $bytes, $bytes.Length, [ref]$written) | Out-Null

    [RawPrint]::EndPagePrinter($hPrinter)  | Out-Null
    [RawPrint]::EndDocPrinter($hPrinter)   | Out-Null
    [RawPrint]::ClosePrinter($hPrinter)    | Out-Null

    Write-Host "OK:$written"
    exit 0
} catch {
    Write-Error $_.Exception.Message
    exit 1
}
