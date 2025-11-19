<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth; 
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $yandex_url = Setting::where([['key', 'yandex_url'], ['user_id', $user->id]])->first();
        return Inertia::render('Settings', [
            'yandex_url' => $yandex_url?->value
        ]);
    }

    public function updateYandexUrl(Request $request)
    {
        $request->validate([
            'yandex_url' => 'required|url|max:255', 
        ]);
        
        $modifiedRequest = $request->merge(['key' => 'yandex_url', 'value' => $request->yandex_url]);

        return $this->update($modifiedRequest);
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);
        
        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => $validated['key']],
            ['value' => $validated['value']]
        );

        return back()->with('success', "Настройка '{$validated['key']}' успешно обновлена.");
    }
}
