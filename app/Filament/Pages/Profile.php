<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class Profile extends EditProfile
{
    protected static ?string $navigationLabel = 'Profile';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-circle';

    protected function getNameFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('name')
            ->label('Full name')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->live(debounce: 500);
    }

    protected function getPasswordFormComponent(): \Filament\Schemas\Components\Component
    {
        return parent::getPasswordFormComponent();
    }

    protected function getPasswordConfirmationFormComponent(): \Filament\Schemas\Components\Component
    {
        return parent::getPasswordConfirmationFormComponent();
    }

    protected function getCurrentPasswordFormComponent(): \Filament\Schemas\Components\Component
    {
        return parent::getCurrentPasswordFormComponent();
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_path')
                    ->label('Avatar')
                    ->avatar()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('avatars'),
                TextInput::make('telegram_id')
                    ->label('Telegram ID')
                    ->maxLength(255),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }
}
