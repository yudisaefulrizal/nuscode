<?php

namespace App\Filament\Widgets;

use App\Models\Aplikasi;
use App\Models\Article;
use App\Models\Order;
use App\Models\Prompt;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $productCount = Prompt::count();
        $paidToday = Order::where('status', 'paid')->whereDate('paid_at', today())->count();
        $pending = Order::where('status', 'pending')->count();
        $revenueToday = Order::where('status', 'paid')->whereDate('paid_at', today())->sum('amount');

        return [
            Stat::make('Artikel', Article::count()),
            Stat::make('Produk', $productCount),
            Stat::make('Portofolio Aplikasi', Aplikasi::count()),
            Stat::make('Order Pending', $pending),
            Stat::make('Order Lunas Hari Ini', $paidToday),
            Stat::make('Pendapatan Hari Ini', 'Rp'.number_format($revenueToday, 0, ',', '.')),
        ];
    }
}
