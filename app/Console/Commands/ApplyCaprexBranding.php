<?php

namespace App\Console\Commands;

use App\Models\BasicControl;
use App\Models\ContentDetails;
use App\Models\Page;
use App\Models\PageDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApplyCaprexBranding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:apply-branding {--brand=CaprexInvest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply bulk production branding cleanup and remove placeholder lorem ipsum text';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $brand = trim((string) $this->option('brand'));
        if ($brand === '') {
            $this->error('Brand name cannot be empty.');
            return self::FAILURE;
        }

        DB::transaction(function () use ($brand) {
            $basicControl = BasicControl::firstOrCreate();
            $basicControl->site_title = $brand;
            $basicControl->save();

            Page::query()->chunkById(100, function ($pages) use ($brand) {
                foreach ($pages as $page) {
                    $page->name = $this->sanitizeText((string) $page->name, $brand);
                    $page->slug = $this->sanitizeSlug((string) $page->slug, $brand);
                    $page->page_title = $this->sanitizeText((string) $page->page_title, $brand);
                    $page->meta_title = $this->sanitizeText((string) $page->meta_title, $brand);
                    $page->meta_description = $this->sanitizeText((string) $page->meta_description, $brand);
                    $page->og_description = $this->sanitizeText((string) $page->og_description, $brand);
                    $page->save();
                }
            });

            PageDetail::query()->chunkById(100, function ($details) use ($brand) {
                foreach ($details as $detail) {
                    $detail->name = $this->sanitizeText((string) $detail->name, $brand);
                    $detail->content = $this->sanitizeHtml((string) $detail->content, $brand);
                    $detail->save();
                }
            });

            ContentDetails::query()->chunkById(100, function ($contentDetails) use ($brand) {
                foreach ($contentDetails as $contentDetail) {
                    $description = $contentDetail->description;
                    $arrayDescription = json_decode(json_encode($description), true);
                    if (!is_array($arrayDescription)) {
                        $arrayDescription = [];
                    }
                    $contentDetail->description = $this->sanitizeArray($arrayDescription, $brand);
                    $contentDetail->save();
                }
            });
        });

        $this->info("Branding cleanup completed for '{$brand}'.");
        return self::SUCCESS;
    }

    private function sanitizeArray(array $data, string $brand): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value, $brand);
            } elseif (is_string($value)) {
                $data[$key] = $this->sanitizeText($value, $brand);
            }
        }

        return $data;
    }

    private function sanitizeText(string $text, string $brand): string
    {
        if ($text === '') {
            return $text;
        }

        $text = str_ireplace(['bullion', 'bullionx', 'bullion investment'], [$brand, $brand, "{$brand} investment"], $text);
        $text = preg_replace('/lorem ipsum[^.!?]*[.!?]?/i', "{$brand} provides secure, transparent and performance-focused investment services.", $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    private function sanitizeHtml(string $html, string $brand): string
    {
        return $this->sanitizeText($html, $brand);
    }

    private function sanitizeSlug(string $slug, string $brand): string
    {
        if ($slug === '' || $slug === '/') {
            return $slug;
        }

        if (str_contains(strtolower($slug), 'bullion')) {
            $cleanBrand = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $brand) ?? 'caprexinvest');
            $cleanBrand = trim($cleanBrand, '-');
            return str_ireplace('bullion', $cleanBrand, $slug);
        }

        return $slug;
    }
}

