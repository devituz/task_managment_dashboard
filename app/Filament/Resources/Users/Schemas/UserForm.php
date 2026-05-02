<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->schema([
                        Grid::make(12)->schema([
                            FileUpload::make('avatar_path')
                                ->label('Avatar')
                                ->avatar()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('avatars')
                                ->columnSpan(3),

                            Grid::make(9)->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpanFull(),
                                TextInput::make('telegram_id')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->helperText('Leave blank to keep the current password.')
                                    ->dehydrated(fn ($state): bool => filled($state))
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                                    ->columnSpanFull(),
                                Placeholder::make('role')
                                    ->content(User::ROLE_EMPLOYER)
                                    ->columnSpanFull(),
                            ]),
                        ]),
                    ]),
            ]);
    }
}
