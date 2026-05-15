<?php

namespace App\Jobs;

use App\Http\Controllers\PrintController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PrintReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Retry a flaky/offline printer a few times before giving up. */
    public int $tries = 3;

    /** Seconds between retries (printer may be momentarily busy/offline). */
    public int $backoff = 5;

    /** Don't let one stuck print job wedge the worker. */
    public int $timeout = 60;

    public function __construct(public int $saleId)
    {
    }

    public function handle(PrintController $controller): void
    {
        // Throws on failure → queue retries, then lands in failed_jobs.
        $controller->renderSaleReceipt($this->saleId);
    }

    public function failed(\Throwable $e): void
    {
        Log::error("PrintReceiptJob failed for sale {$this->saleId}: {$e->getMessage()}");
    }
}
