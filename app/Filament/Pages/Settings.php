<?php

namespace App\Filament\Pages;

use App\Models\Setting as SettingModel;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Settings extends Page
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Settings';

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'telegram_bot_token' => SettingModel::getValue('telegram_bot_token'),
            'telegram_channel_id' => SettingModel::getValue('telegram_channel_id'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Telegram settings')
                    ->description('Task xabarlarini bevosita Telegram kanalga yuborish uchun bot token va kanal chat ID shu yerda sozlanadi.')
                    ->schema([
                        TextInput::make('telegram_bot_token')
                            ->label('Bot token')
                            ->password()
                            ->revealable()
                            ->autocomplete('off'),
                        TextInput::make('telegram_channel_id')
                            ->label('Channel chat ID')
                            ->placeholder('@company_tasks or -1001234567890'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SettingModel::setValue($key, $value);
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return (bool) Filament::auth()->user()?->isSuperadmin();
    }
}
