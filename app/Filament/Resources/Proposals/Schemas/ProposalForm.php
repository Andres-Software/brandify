<?php

namespace App\Filament\Resources\Proposals\Schemas;

use App\Exceptions\InvalidEmbedException;
use App\Models\Directory;
use App\Services\GammaEmbedParser;
use App\Services\SlugGenerator;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProposalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('embed_raw')
                    ->label('Snippet do iframe (Gamma)')
                    ->required()
                    ->rows(4)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get, GammaEmbedParser $parser, SlugGenerator $slugGenerator) {
                        if (blank($state)) {
                            return;
                        }

                        try {
                            $parsed = $parser->parse($state);

                            $set('embed_src', $parsed->src);

                            if (filled($parsed->title) && blank($get('description'))) {
                                $set('description', $parsed->title);
                            }

                            if (blank($get('slug'))) {
                                $suggested = $slugGenerator->suggest($parsed->title);

                                if (filled($suggested)) {
                                    $set('slug', $slugGenerator->ensureUnique($suggested));
                                }
                            }
                        } catch (InvalidEmbedException $exception) {
                            Notification::make()
                                ->danger()
                                ->title('Snippet inválido')
                                ->body($exception->getMessage())
                                ->send();
                        }
                    })
                    ->columnSpanFull(),

                Hidden::make('embed_src')
                    ->required(),

                TextInput::make('description')
                    ->label('Descrição')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('directory_id')
                    ->label('Diretório')
                    ->relationship('directory', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if (blank($get('slug'))) {
                                    $set('slug', str($state)->slug()->toString());
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Directory::class, 'slug')
                            ->rule('not_in:admin,login,livewire'),
                    ]),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Usado na URL pública: dominio.com/{diretorio}/{slug}'),

                DateTimePicker::make('expires_at')
                    ->label('Expira em')
                    ->helperText('Deixe em branco para nunca expirar'),
            ]);
    }
}
