<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Section;

class CrawlSections extends Command
{
    protected $signature = 'sections:crawl';
    protected $description = 'Crawl dữ liệu ống thép từ ongthephoaphat.net';

    public function handle()
    {
        $client = new Client();
        $url = 'https://ongthephoaphat.net/bang-gia.html';
        $res = $client->get($url);
        $html = (string) $res->getBody();

        $crawler = new Crawler($html);

        $crawler->filter('table tbody tr')->each(function (Crawler $node) {
            // Lấy inner HTML, loại bỏ tag <sup>, <sub>, <span> nếu có
            $cols = $node->filter('td')->each(fn($td) => trim(strip_tags($td->html())));

            if (count($cols) >= 7) {
                $name = $cols[0];
                $type = $cols[1];
                $diameter = $this->parseNumber($cols[2] ?? '0');
                $thickness = $this->parseNumber($cols[3] ?? '0');
                $weight_per_m = $this->parseNumber($cols[4] ?? '0');
                $price = $this->parseNumber($cols[5] ?? '0');
                $standard_ref = $cols[6] ?? null;

                Section::updateOrCreate(
                    ['name' => $name, 'type' => $type],
                    [
                        'properties' => json_encode([
                            'diameter' => $diameter,
                            'thickness' => $thickness,
                            'weight_per_m' => $weight_per_m,
                            'price' => $price,
                            'standard_ref' => $standard_ref,
                        ]),
                        'diameter' => $diameter,
                        'thickness' => $thickness,
                        'weight_per_m' => $weight_per_m,
                        'price' => $price,
                        'standard_ref' => $standard_ref,
                    ]
                );
            }
        });

        $this->info('Crawl xong với dữ liệu đầy đủ!');
    }

    /**
     * Helper parse number từ chuỗi có dấu ',' hoặc '.' hoặc ký tự thừa
     */
    private function parseNumber(string $text): float
    {
        $clean = preg_replace('/[^\d\.,]/', '', $text);

        // Nếu có cả dấu ',' và '.', giả sử ',' là thập phân nếu '.' không có
        if (strpos($clean, ',') !== false && strpos($clean, '.') === false) {
            $clean = str_replace(',', '.', $clean);
        } else {
            $clean = str_replace(',', '', $clean); // xóa dấu ngăn nghìn
        }

        return floatval($clean);
    }
}