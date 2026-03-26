<?php

namespace App\Console\Commands;

use App\Models\PaymentWebhookEvent;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ReplayPaymentWebhooks extends Command
{
    protected $signature = 'payment:webhooks:replay
        {--id= : Replay event tertentu berdasarkan ID tabel payment_webhook_events}
        {--provider= : Filter provider (midtrans|xendit)}
        {--limit=50 : Batas jumlah event yang direplay}
        {--dry-run : Simulasi tanpa memproses event}';

    protected $description = 'Replay webhook payment yang gagal dari tabel payment_webhook_events.';

    public function handle(): int
    {
        $eventId = $this->option('id');
        $provider = $this->option('provider');
        $dryRun = (bool) $this->option('dry-run');
        $limit = max(1, (int) $this->option('limit'));

        $query = PaymentWebhookEvent::query()
            ->orderBy('created_at');

        if ($eventId) {
            $query->where('id', (int) $eventId);
        } else {
            $query->where('status', 'failed');
        }

        if ($provider) {
            $query->where('provider', $provider);
        }

        $events = $query->limit($limit)->get();

        if ($events->isEmpty()) {
            $this->info('Tidak ada event webhook yang perlu direplay.');
            return self::SUCCESS;
        }

        $processed = 0;
        $failed = 0;

        /** @var PaymentWebhookEvent $event */
        foreach ($events as $event) {
            $this->line("Memproses event #{$event->id} ({$event->provider})...");

            if ($dryRun) {
                $this->comment('Dry-run aktif: event tidak dieksekusi.');
                continue;
            }

            $result = PaymentService::handleNotificationForProvider(
                $event->provider,
                is_array($event->payload) ? $event->payload : [],
                is_array($event->headers) ? $event->headers : []
            );

            $event->attempts = (int) $event->attempts + 1;
            $event->processed_at = Carbon::now();

            if (($result['success'] ?? false) === true) {
                $event->status = 'processed';
                $event->response_code = 200;
                $event->error_message = null;
                $processed++;
            } else {
                $event->status = 'failed';
                $event->response_code = 400;
                $event->error_message = (string) ($result['message'] ?? 'Replay gagal');
                $failed++;
            }

            $event->save();
        }

        if ($dryRun) {
            $this->info('Dry-run selesai. Tidak ada perubahan data.');
            return self::SUCCESS;
        }

        $this->info("Replay selesai. Berhasil: {$processed}, Gagal: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
