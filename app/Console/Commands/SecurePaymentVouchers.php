<?php

namespace App\Console\Commands;

use App\Models\PaymentTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SecurePaymentVouchers extends Command
{
    protected $signature = 'payments:secure-vouchers {--dry-run : Show public receipts without moving them}';

    protected $description = 'Move legacy payment receipts from public storage to private local storage';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $referencedPaths = PaymentTransaction::query()
            ->whereNotNull('voucher_path')
            ->pluck('voucher_path')
            ->filter(fn ($path): bool => is_string($path) && $public->exists($path))
            ->all();
        $paths = collect(array_merge($public->allFiles('payment-vouchers'), $referencedPaths))
            ->unique()->values();

        if ($this->option('dry-run')) {
            $paths->each(fn (string $path) => $this->line($path));
            $this->info($paths->count().' public payment receipt(s) found. No files were moved.');

            return self::SUCCESS;
        }

        $moved = 0;
        foreach ($paths as $path) {
            if ($private->exists($path)) {
                $public->delete($path);
                $moved++;
                continue;
            }

            $stream = $public->readStream($path);
            if (! is_resource($stream)) {
                $this->error('Could not read: '.$path);
                continue;
            }

            try {
                if ($private->writeStream($path, $stream)) {
                    $public->delete($path);
                    $moved++;
                    $this->line('Secured: '.$path);
                }
            } finally {
                fclose($stream);
            }
        }

        $this->info("Secured {$moved} payment receipt(s).");

        return self::SUCCESS;
    }
}
