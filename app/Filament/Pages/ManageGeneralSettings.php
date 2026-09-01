<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageGeneralSettings extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configurações';

    protected static ?string $title = 'Configurações gerais';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('logo_path')
                    ->label('Logo')
                    ->image()
                    ->disk('public')
                    ->directory('branding')
                    ->columnSpanFull(),

                TextInput::make('company_name')
                    ->label('Nome da empresa')
                    ->required()
                    ->maxLength(255),

                Toggle::make('show_topbar')
                    ->label('Mostrar barra superior')
                    ->helperText('Exibe a logo e o nome da empresa no topo das propostas públicas.'),

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
            ]);
    }
}
