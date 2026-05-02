<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSuperadmin = (bool) Filament::auth()->user()?->isSuperadmin();

        return $schema
            ->components([
                Section::make('Task details')
                    ->schema([
                        Grid::make(12)->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->disabled(! $isSuperadmin),

                            Textarea::make('description')
                                ->rows(6)
                                ->columnSpanFull()
                                ->disabled(! $isSuperadmin),

                            Select::make('user_id')
                                ->label('Assigned employee')
                                ->relationship('employee', 'name', fn ($query) => $query->where('role', User::ROLE_EMPLOYER))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(4)
                                ->disabled(! $isSuperadmin),

                            Select::make('priority')
                                ->options([
                                    'low' => Task::priorityLabel('low'),
                                    'medium' => Task::priorityLabel('medium'),
                                    'high' => Task::priorityLabel('high'),
                                ])
                                ->required()
                                ->columnSpan(4)
                                ->disabled(! $isSuperadmin),

                            Select::make('status')
                                ->options([
                                    'todo' => Task::statusLabel('todo'),
                                    'in_progress' => Task::statusLabel('in_progress'),
                                    'testing' => Task::statusLabel('testing'),
                                    'done' => Task::statusLabel('done'),
                                    'complete' => Task::statusLabel('complete'),
                                ])
                                ->required()
                                ->columnSpan(4),

                            DatePicker::make('deadline')
                                ->native(false)
                                ->displayFormat('d-m-Y')
                                ->columnSpan(4)
                                ->disabled(! $isSuperadmin),

                            FileUpload::make('attachments')
                                ->multiple()
                                ->reorderable()
                                ->downloadable()
                                ->openable()
                                ->appendFiles()
                                ->disk('public')
                                ->directory('tasks')
                                ->columnSpanFull()
                                ->disabled(! $isSuperadmin),

                            Placeholder::make('meta')
                                ->content(fn ($record) => $record?->exists ? 'Created: ' . $record->created_at?->format('d-m-Y H:i') . ' | Updated: ' . $record->updated_at?->format('d-m-Y H:i') : 'Task details will be saved after creation.')
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
