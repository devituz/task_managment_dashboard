<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->weight('bold')->size('lg'),
                TextEntry::make('description')->columnSpanFull()->prose(),
                ImageEntry::make('employee.avatar_path')
                    ->label('Employee')
                    ->disk('public')
                    ->circular()
                    ->size(48)
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Employee&background=111827&color=fff'),
                TextEntry::make('employee.name')->label('Assigned employee'),
                TextEntry::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Task::priorityLabel($state))
                    ->color(fn (string $state) => Task::priorityColor($state)),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Task::statusLabel($state))
                    ->color(fn (string $state) => Task::statusColor($state)),
                TextEntry::make('deadline')->date('d-m-Y'),
                TextEntry::make('attachments')
                    ->label('Files')
                    ->bulleted()
                    ->formatStateUsing(fn ($state) => collect($state ?? [])->map(fn ($path) => basename($path))->all()),
            ]);
    }
}
