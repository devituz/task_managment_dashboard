<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Models\Task;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                ImageColumn::make('employee.avatar_path')
                    ->label('Employee')
                    ->disk('public')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=Employee&background=111827&color=fff'),
                TextColumn::make('employee.name')
                    ->label('Assigned to')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Task::priorityLabel($state))
                    ->color(fn (string $state) => Task::priorityColor($state))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Task::statusLabel($state))
                    ->color(fn (string $state) => Task::statusColor($state))
                    ->sortable(),
                TextColumn::make('deadline')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('attachments')
                    ->label('Files')
                    ->state(fn (Task $record): string => (string) count($record->attachments ?? [])),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'todo' => Task::statusLabel('todo'),
                        'in_progress' => Task::statusLabel('in_progress'),
                        'testing' => Task::statusLabel('testing'),
                        'done' => Task::statusLabel('done'),
                        'complete' => Task::statusLabel('complete'),
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'low' => Task::priorityLabel('low'),
                        'medium' => Task::priorityLabel('medium'),
                        'high' => Task::priorityLabel('high'),
                    ]),
                SelectFilter::make('employee')
                    ->relationship('employee', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
