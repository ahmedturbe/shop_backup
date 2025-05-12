<?php

namespace App\Services\Backup;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BackupProcessor
{
    public function process(array $products): void
    {
        DB::beginTransaction();

        try {
            Log::info("Backup process started. Total products to process: " . count($products));

            foreach ($products as $productData) {
                try {
                    // Basic validation
                    if (!isset($productData['uuid'], $productData['title'])) {
                        Log::warning("Skipping product due to missing uuid or title.", $productData);
                        continue;
                    }

                    Log::info("Processing product: {$productData['title']} ({$productData['uuid']})");

                    // Create or update product
                    $product = Product::updateOrCreate(
                        ['product_uuid' => $productData['uuid']],
                        [
                            'name' => $productData['title'],
                            'product_handle' => $productData['handle'],
                            'product_price' => $productData['price'],
                            'created_at' => $productData['created_at'] ?? now(),
                            'updated_at' => $productData['updated_at'] ?? now(),
                        ]
                    );

                    Log::info("Product saved: {$product->name} ({$product->product_uuid})");

                    // Process pictures product
                    if (!empty($productData['images']) && is_array($productData['images'])) {
                        foreach ($productData['images'] as $imageData) {
                            if (is_array($imageData) && isset($imageData['uuid'], $imageData['url'])) {
                                $product->images()->updateOrCreate(
                                    ['product_uuid' => $imageData['uuid']],
                                    [
                                        'url' => $imageData['url'],
                                        'created_at' => $imageData['created_at'] ?? now(),
                                        'updated_at' => $imageData['updated_at'] ?? now(),
                                    ]
                                );
                            }
                        }
                    }

                    // Process varijants
                    if (!empty($productData['variants']) && is_array($productData['variants'])) {
                        foreach ($productData['variants'] as $variantData) {
                            if (!isset($variantData['uuid'], $variantData['handle'])) {
                                Log::warning("Skipping variant with missing uuid or handle.", $variantData);
                                continue;
                            }

                            $variant = $product->variants()->updateOrCreate(
                                ['variant_uuid' => $variantData['uuid']],
                                [
                                    'product_uuid' => $product->product_uuid,
                                    'variant_handle' => $variantData['handle'],
                                    'variant_price' => $variantData['price'],
                                    'created_at' => $variantData['created_at'] ?? now(),
                                    'updated_at' => $variantData['updated_at'] ?? now(),
                                ]
                            );

                            // Obradi slike varijante
                            if (!empty($variantData['images']) && is_array($variantData['images'])) {
                                foreach ($variantData['images'] as $imageData) {
                                    if (is_array($imageData) && isset($imageData['uuid'], $imageData['url'])) {
                                        $variant->images()->updateOrCreate(
                                            ['variant_uuid' => $imageData['uuid']],
                                            [
                                                'url' => $imageData['url'],
                                                'created_at' => $imageData['created_at'] ?? now(),
                                                'updated_at' => $imageData['updated_at'] ?? now(),
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }

                } catch (Exception $e) {
                    Log::error("Failed to process product {$productData['uuid']}: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                    ]);
                    continue;
                }
            }

            DB::commit();
            Log::info("Backup process completed successfully.");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Critical failure during backup process: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            report($e);
            throw new Exception("Error processing backup data. Check logs for details.");
        }
    }
}
