<?php

namespace App\Jobs;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateArticleBarcodeOnSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $articleCode;
    public $barcode;

    public function __construct($site, string $articleCode, string $barcode)
    {
        $this->site        = $site;
        $this->articleCode = $articleCode;
        $this->barcode     = $barcode;
    }

    public function handle()
    {
        if (!$this->site || !$this->site->url) {
            Log::warning('UpdateArticleBarcodeOnSite: site URL manquante');
            return;
        }

        try {
            $response = Http::timeout(10)
    ->withHeaders(['X-API-KEY' => $this->site->api_key])
    ->post($this->site->url . '/api/articles/update-barcode', [
        'article_code' => $this->articleCode,
        'barcode'      => $this->barcode,
    ]);

            if ($response->successful()) {
                Log::info('Barcode mis à jour sur ' . $this->site->name, [
                    'article' => $this->articleCode,
                    'barcode' => $this->barcode,
                ]);
            } else {
                Log::warning('Echec mise à jour barcode sur ' . $this->site->name, [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('UpdateArticleBarcodeOnSite exception: ' . $e->getMessage());
        }
    }
}