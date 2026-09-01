<?php

namespace App\Filament\Widgets;

use App\Models\Proposal;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ProposalStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total = Proposal::query()->count();
        $expired = Proposal::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->count();

        return [
            Stat::make('Propostas ativas', $total - $expired)
                ->color('success'),

            Stat::make('Propostas totais', $total)
                ->color('gray'),

            Stat::make('Propostas expiradas', $expired)
                ->color('danger'),
        ];
    }
}
