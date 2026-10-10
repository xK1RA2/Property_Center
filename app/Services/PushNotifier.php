<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends push notifications through Expo's push service.
 * https://docs.expo.dev/push-notifications/sending-notifications/
 *
 * Failures are logged, never thrown: a push problem must not break a booking.
 */
class PushNotifier
{
    private const ENDPOINT = 'https://exp.host/--/api/v2/push/send';
    private const BATCH = 100; // Expo accepts at most 100 messages per request

    /**
     * @param  array<string,mixed>  $data  delivered to the app with the notification (e.g. ['screen' => 'bookings'])
     */
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

    /** @param string[] $tokens */
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

    /** Tickets come back in the same order as the messages we sent. */
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
        // optional: only needed if "enhanced push security" is enabled for the Expo project
        $token = config('services.expo.access_token');

        return $token ? ['Authorization' => "Bearer {$token}"] : [];
    }
}
