<?php

namespace App\Filament\Widgets;

use App\Models\Proposal;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestProposals extends TableWidget
{
    protected static ?string $heading = 'Últimas propostas criadas';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Proposal::query()->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(40)
                    ->placeholder('Sem descrição'),
                TextColumn::make('slug')
                    ->label('URL pública')
                    ->getStateUsing(fn (Proposal $record) => $record->public_url)
                    ->copyable()
                    ->copyMessage('URL copiada!'),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (Proposal $record) => $record->isExpired() ? 'Expirado' : 'Ativo')
                    ->badge()
                    ->color(fn (Proposal $record) => $record->isExpired() ? 'danger' : 'success'),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
