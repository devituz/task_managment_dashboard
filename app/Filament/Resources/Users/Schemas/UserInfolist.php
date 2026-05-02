<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('avatar_path')
                    ->disk('public')
                    ->circular()
                    ->size(72)
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Employee&background=111827&color=fff'),
                TextEntry::make('name')->weight('bold'),
                TextEntry::make('email'),
                TextEntry::make('telegram_id')->label('Telegram ID'),
                TextEntry::make('created_at')->date('d-m-Y H:i'),
            ]);
    }
}
