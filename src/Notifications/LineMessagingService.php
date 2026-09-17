<?php

namespace App\Notifications;

use App\Core\Env;

/**
 * LINE Messaging API Push Service (Strategy Implementation)
 */
class LineMessagingService implements NotificationChannelInterface
{
    private string $channelToken;
    private string $adminGroupId;

    public function __construct()
    {
        $this->channelToken = (string) Env::get('LINE_CHANNEL_ACCESS_TOKEN', '');
        $this->adminGroupId = (string) Env::get('LINE_ADMIN_GROUP_ID', '');
    }

    /**
     * Send Push Message via LINE Messaging API
     */
    public function send(string $target, string $message, array $extra = []): bool
    {
        // Default to configured admin group/user if target is empty
        $to = $target ?: $this->adminGroupId;

        // If no token is provided (dev/offline mode), record to mock log for demonstration
        if (empty($this->channelToken) || empty($to)) {
            $this->logMockNotification($to, $message);
            return true;
        }

        $body = [
            'to' => $to,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ],
            ],
        ];

        $response = $this->callApi('https://api.line.me/v2/bot/message/push', $body);
        return isset($response['http_code']) && $response['http_code'] === 200;
    }

    /**
     * Make authenticated cURL request to LINE API
     */
    private function callApi(string $endpoint, array $body): array
    {
        $ch = curl_init($endpoint);
        $payload = json_encode($body, JSON_UNESCAPED_UNICODE);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json; charset=UTF-8',
                'Authorization: Bearer ' . $this->channelToken,
            ],
            CURLOPT_TIMEOUT        => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'http_code' => $httpCode,
            'response'  => json_decode($response ?: '', true),
            'error'     => $error,
        ];
    }

    private function logMockNotification(string $target, string $message): void
    {
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/line_notify.log';
        $entry = sprintf("[%s] [MOCK LINE PUSH to: %s]\n%s\n%s\n",
            date('Y-m-d H:i:s'),
            $target ?: 'ALL_STAFF_BROADCAST',
            $message,
            str_repeat('-', 60)
        );
        @file_put_contents($logFile, $entry, FILE_APPEND);
    }
}
