<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use App\Support\BarColor;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ManageGeneralSettings extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configurações';

    protected static ?int $navigationSort = 99;

    protected static ?string $title = 'Configurações gerais';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    TextInput::make('company_name')
                        ->label('Nome da empresa')
                        ->maxLength(255)
                        ->helperText('Deixe em branco para não exibir nenhum nome na barra.'),

                    Toggle::make('show_topbar')
                        ->label('Mostrar barra superior')
                        ->helperText('Exibe a logo e o nome da empresa no topo das propostas públicas.'),

                    Select::make('bar_color')
                        ->label('Cor da barra superior')
                        ->options(BarColor::options())
                        ->required()
                        ->native(false),

                    Select::make('slug_mode')
                        ->label('Modo de geração de slug')
                        ->options([
                            'auto_title' => 'Automático (baseado no título)',
                            'random' => 'Aleatório',
                            'prefix' => 'Prefixo + aleatório',
                            'manual' => 'Manual',
                        ])
                        ->required()
                        ->live(),

                    TextInput::make('slug_prefix')
                        ->label('Prefixo do slug')
                        ->maxLength(255)
                        ->visible(fn (Get $get) => $get('slug_mode') === 'prefix'),
                ])
                    ->columnSpan(2),

                Group::make([
                    FileUpload::make('logo_path')
                        ->label('Logo')
                        ->image()
                        ->disk('public')
                        ->directory('branding')
                        ->live()
                        ->imagePreviewHeight('120'),

                    Placeholder::make('logo_preview')
                        ->label('Pré-visualização')
                        ->content(function (Get $get) {
                            $path = $get('logo_path');

                            if (blank($path)) {
                                return new HtmlString('<span class="text-sm text-gray-500">Nenhuma logo enviada.</span>');
                            }

                            $url = is_string($path)
                                ? Storage::disk('public')->url($path)
                                : (method_exists($path, 'temporaryUrl') ? $path->temporaryUrl() : null);

                            if (blank($url)) {
                                return new HtmlString('<span class="text-sm text-gray-500">Nenhuma logo enviada.</span>');
                            }

                            return new HtmlString(
                                '<div class="rounded-lg border border-gray-700 bg-gray-950 p-4 flex items-center justify-center">'
                                . '<img src="' . e($url) . '" alt="Logo" style="max-height: 6rem; max-width: 100%;">'
                                . '</div>'
                            );
                        }),
                ])
                    ->columnSpan(1),
            ]);
    }
}
