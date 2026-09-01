<?php

namespace App\Filament\Resources\Proposals\Tables;

use App\Models\Proposal;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Js;

class ProposalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('directory.name')
                    ->label('Diretório')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('URL pública')
                    ->getStateUsing(fn (Proposal $record) => $record->public_url)
                    ->searchable(['slug'])
                    ->sortable(['slug']),
                TextColumn::make('expires_at')
                    ->label('Expira em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Nunca')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (Proposal $record) => $record->isExpired() ? 'Expirado' : 'Ativo')
                    ->badge()
                    ->color(fn (Proposal $record) => $record->isExpired() ? 'danger' : 'success'),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('active')
                    ->label('Ativas')
                    ->query(fn (Builder $query) => $query->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))),
                Filter::make('expired')
                    ->label('Expiradas')
                    ->query(fn (Builder $query) => $query->whereNotNull('expires_at')->where('expires_at', '<=', now())),
            ])
            ->recordActions([
                Action::make('copyUrl')
                    ->label('Copiar link')
                    ->icon(Heroicon::OutlinedLink)
                    ->color('gray')
                    ->alpineClickHandler(function (Proposal $record) {
                        $urlJs = Js::from($record->public_url);

                        return <<<JS
                            window.navigator.clipboard.writeText({$urlJs})
                            \$tooltip('URL copiada!', { theme: \$store.theme, timeout: 2000 })
                            JS;
                    }),
                EditAction::make(),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
