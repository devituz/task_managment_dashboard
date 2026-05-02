<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            ViewAction::make(),
        ];

        if (static::$resource::canDelete($this->getRecord())) {
            $actions[] = DeleteAction::make();
        }

        return $actions;
    }
}
