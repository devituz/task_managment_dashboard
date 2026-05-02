<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'settings' => [
                'telegram_bot_token' => Setting::getValue('telegram_bot_token'),
                'telegram_channel_id' => Setting::getValue('telegram_channel_id'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'telegram_bot_token' => ['nullable', 'string', 'max:255'],
            'telegram_channel_id' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::setValue('telegram_bot_token', $data['telegram_bot_token'] ?? null);
        Setting::setValue('telegram_channel_id', $data['telegram_channel_id'] ?? null);

        return redirect()->route('settings.edit')->with('success', __('app.settings_saved'));
    }
}
