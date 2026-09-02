<?php

namespace App\Http\Controllers;

use App\Jobs\SendTelegramNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'service' => 'nullable|string|max:255',
        ]);

        SendTelegramNotification::dispatch($this->formatMessage($validated));

        return response()->json(['success' => true]);
    }

    private function formatMessage(array $data): string
    {
        $lines = [
            '📞 <b>Заявка на консультацію</b>',
            "Ім'я: {$data['name']}",
            "Телефон: {$data['phone']}",
        ];

        if (! empty($data['service'])) {
            $lines[] = "Послуга: {$data['service']}";
        }

        return implode("\n", $lines);
    }
}
