<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class MigrateArticleContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-article-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi 4 artikel Blade lama (osilayer, Cyber-Security-Digital-Twin, monitoring, rancang-bangun-agent-router-sub-agent) ke tabel articles. Sekali jalan, aman diulang (skip kalau slug sudah ada).';

    /**
     * Tiap entri: file sumber (di resources/views/), rentang baris yang diambil
     * (dari <style> sampai penutup <script>, supaya visual & perilaku identik
     * dengan tampilan lama tanpa perlu menyatukan CSS antar-artikel), plus metadata.
     *
     * @var array<int, array{file: string, from: int, to: int, slug: string, title: string, author: ?string}>
     */
    private array $articles = [
        [
            'file' => 'osilayer.blade.php',
            'from' => 12,
            'to' => 1253,
            'slug' => 'osi-layers',
            'title' => 'Memahami OSI Layer: Fondasi Jaringan Komputer',
            'author' => 'Yudi Saeful Rizal',
        ],
        [
            'file' => 'Cyber-Security-Digital-Twin.blade.php',
            'from' => 12,
            'to' => 1151,
            'slug' => 'cyber-security-digital-twin',
            'title' => 'Analyzing the Impact of Cyber-Attacks on Digital Twin-Based Industrial Organizations',
            'author' => 'Ridwan Mustofa, Md. Rafiquzzaman, Niamat Ullah Ibne Hossain',
        ],
        [
            'file' => 'monitoring.blade.php',
            'from' => 12,
            'to' => 1106,
            'slug' => 'digital-twin-monitoring-device',
            'title' => 'Implementasi Digital Twin Berbasis ESP32 untuk Monitoring Infrastruktur Jaringan',
            'author' => 'Yudi Saeful Rizal',
        ],
        [
            'file' => 'rancang-bangun-agent-router-sub-agent.blade.php',
            'from' => 12,
            'to' => 766,
            'slug' => 'rancang-bangun-agent-router-sub-agent',
            'title' => 'Rancang Bangun Agent Router + Sub-Agent',
            'author' => null,
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach ($this->articles as $def) {
            if (Article::where('slug', $def['slug'])->exists()) {
                $this->warn("Lewati (sudah ada): {$def['slug']}");

                continue;
            }

            $path = resource_path('views/'.$def['file']);

            if (! is_file($path)) {
                $this->error("File sumber tidak ditemukan: {$def['file']}");

                continue;
            }

            $lines = file($path);
            $slice = array_slice($lines, $def['from'] - 1, $def['to'] - $def['from'] + 1);
            $content = implode('', $slice);

            Article::create([
                'title' => $def['title'],
                'slug' => $def['slug'],
                'content' => $content,
                'author' => $def['author'],
                'published_at' => now(),
            ]);

            $this->info("Berhasil migrasi: {$def['slug']} ({$def['file']}, ".count($slice).' baris)');
        }

        return self::SUCCESS;
    }
}
