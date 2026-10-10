<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotifier
{
    private const ENDPOINT = 'https://exp.host/--/api/v2/push/send';
    private const BATCH = 100;

    public function toUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::where('user_id', $user->id)->pluck('token')->all();

        if (!$tokens) {
            return;
        }

        foreach (array_chunk($tokens, self::BATCH) as $chunk) {
            $this->send($chunk, $title, $body, $data);
        }
    }

    private function send(array $tokens, string $title, string $body, array $data): void
    {
        $messages = array_map(fn (string $token) => [
            'to' => $token,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'sound' => 'default',
            'channelId' => 'default',
        ], $tokens);

        try {
            $response = Http::acceptJson()
                ->timeout(5)
                ->withHeaders($this->authHeader())
                ->post(self::ENDPOINT, $messages);

            if ($response->failed()) {
                Log::warning('Expo push request failed', ['status' => $response->status()]);

                return;
            }

            $this->pruneDeadTokens($tokens, $response->json('data') ?? []);
        } catch (\Throwable $e) {
            Log::warning('Expo push request threw', ['error' => $e->getMessage()]);
        }
    }

    private function pruneDeadTokens(array $tokens, array $tickets): void
    {
        $dead = [];

        foreach ($tickets as $i => $ticket) {
            if (($ticket['status'] ?? null) === 'error'
                && ($ticket['details']['error'] ?? null) === 'DeviceNotRegistered'
                && isset($tokens[$i])) {
                $dead[] = $tokens[$i];
            }
        }

        if ($dead) {
            DeviceToken::whereIn('token', $dead)->delete();
        }
    }

    private function authHeader(): array
    {
        $token = config('services.expo.access_token');

        return $token ? ['Authorization' => "Bearer {$token}"] : [];
    }
}
