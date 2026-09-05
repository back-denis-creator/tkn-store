<?php

namespace App\Http\Controllers;

use App\Jobs\SendTelegramNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'category' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        SendTelegramNotification::dispatch($this->formatMessage($validated));

        return response()->json(['success' => true]);
    }

    private function formatMessage(array $data): string
    {
        $lines = [
            '✉️ <b>Повідомлення з форми контактів</b>',
            "Ім'я: {$data['name']}",
            "Email: {$data['email']}",
        ];

        if (! empty($data['category'])) {
            $lines[] = "Тема: {$data['category']}";
        }

        $lines[] = "Повідомлення: {$data['message']}";

        return implode("\n", $lines);
    }
}
