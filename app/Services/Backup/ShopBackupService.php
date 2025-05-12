<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ShopBackupService
{
    protected string $baseUrl;
    protected int $perPage = 3;
    protected BackupProcessor $processor;

    public function __construct(BackupProcessor $processor)
    {
        $this->baseUrl = config('services.shop.api_url');
        $this->processor = $processor;
    }

    /**
     * Main method to run the backup process.
     */
    public function run(): void
    {
        Log::info("Starting Shop backup process...");

        try {
            $products = $this->fetchProducts();

            if (empty($products)) {
                Log::warning("No products fetched from Shop API.");
                return;
            }

            $this->processor->process($products);

            Log::info("Shop backup completed successfully. Total products: " . count($products));

        } catch (Exception $e) {
            Log::error("Backup process failed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            report($e);
            throw new Exception("Backup failed. Check logs for details.");
        }
    }

    public function backup(): void
    {
        $this->run();
    }

    /**
     * Fetch all products from the Shop API with pagination.
     */
    protected function fetchProducts(): array
    {
        $allProducts = [];
        $page = 1;

        do {
            try {
                Log::info("Fetching page {$page} from Shop API...");

                $response = Http::timeout(20)->get("{$this->baseUrl}/products", [
                    'page' => $page,
                    'per_page' => $this->perPage,
                ]);

                if ($response->failed()) {
                    Log::error("Shop API responded with failure on page {$page}: " . $response->body());
                    break;
                }

                $data = $response->json();
                $items = $data['data'] ?? [];

                $allProducts = array_merge($allProducts, $items);
                $itemCount = count($items);

                logger()->info("Fetched {$itemCount} products from page {$page}");

                $page++;

            } catch (Exception $e) {
                Log::error("Fetch failed on page {$page}: " . $e->getMessage());
                break;
            }

        } while ($itemCount === $this->perPage);

        Log::info("Fetched total products: " . count($allProducts));
        return $allProducts;
    }
}
