<?php

declare(strict_types=1);

namespace App;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\WebSocket\WsConnection;

class Chat implements MessageComponentInterface
{
    private \SplObjectStorage $clients;

    private string $authToken;
    private int $rateLimitMax;
    private int $rateLimitWindow;
    private int $maxMessageLength;
    private array $rateBuckets = [];

    public function __construct(string $authToken, int $rateLimitMax = 5, int $rateLimitWindow = 2, int $maxMessageLength = 500)
    {
        $this->clients = new \SplObjectStorage();
        $this->authToken = $authToken;
        $this->rateLimitMax = $rateLimitMax;
        $this->rateLimitWindow = $rateLimitWindow;
        $this->maxMessageLength = $maxMessageLength;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        if (!$this->isAuthorized($conn)) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'Unauthorized',
            ], JSON_THROW_ON_ERROR));
            $conn->close();
            return;
        }

        $this->clients->attach($conn);
        fwrite(STDOUT, "Connected: {$conn->resourceId}" . PHP_EOL);

        $conn->send(json_encode([
            'type' => 'system',
            'message' => 'Connected. Your ID: ' . $conn->resourceId,
        ], JSON_THROW_ON_ERROR));

        $this->sendUserCount($conn);
        $this->broadcastUserCount();
    }


    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $payload = trim((string) $msg);
        if ($payload === '') {
            return;
        }

        if (mb_strlen($payload) > $this->maxMessageLength) {
            $from->send(json_encode([
                'type' => 'system',
                'message' => 'Message too long. Max ' . $this->maxMessageLength . ' characters.',
            ], JSON_THROW_ON_ERROR));
            return;
        }

        if (!$this->allowMessage($from)) {
            $from->send(json_encode([
                'type' => 'system',
                'message' => 'Rate limit exceeded. Slow down.',
            ], JSON_THROW_ON_ERROR));
            return;
        }

        $decoded = json_decode($payload, true);
        if (is_array($decoded) && ($decoded['type'] ?? '') === 'new_order') {
            $order = [
                'type' => 'order',
                'from' => $from->resourceId,
                'timestamp' => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format(DATE_ATOM),
                'items' => $decoded['items'] ?? [],
                'table' => $decoded['table'] ?? 'Unknown',
                'total' => $decoded['total'] ?? 0,
            ];

            foreach ($this->clients as $client) {
                $client->send(json_encode($order, JSON_THROW_ON_ERROR));
            }

            $from->send(json_encode([
                'type' => 'confirm',
                'message' => 'Order received by kitchen.',
            ], JSON_THROW_ON_ERROR));
            return;
        }

        $payload = $this->sanitizeMessage($payload);
        if ($payload === '') {
            return;
        }

        fwrite(STDOUT, "Message from {$from->resourceId}: {$payload}" . PHP_EOL);

        $data = [
            'type' => 'message',
            'from' => $from->resourceId,
            'message' => $payload,
            'timestamp' => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format(DATE_ATOM),
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($data, JSON_THROW_ON_ERROR));
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
        unset($this->rateBuckets[$conn->resourceId]);
        fwrite(STDOUT, "Disconnected: {$conn->resourceId}" . PHP_EOL);
        $this->broadcastUserCount();
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        fwrite(STDERR, "WebSocket error: {$e->getMessage()}" . PHP_EOL);
        $conn->close();
    }

    private function isAuthorized(ConnectionInterface $conn): bool
    {
        if (!$conn instanceof WsConnection) {
            return false;
        }

        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $params);

        return isset($params['token']) && hash_equals($this->authToken, (string) $params['token']);
    }

    private function broadcastUserCount(): void
    {
        $data = [
            'type' => 'users',
            'count' => $this->clients->count(),
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($data, JSON_THROW_ON_ERROR));
        }
    }

    private function sendUserCount(ConnectionInterface $conn): void
    {
        $conn->send(json_encode([
            'type' => 'users',
            'count' => $this->clients->count(),
        ], JSON_THROW_ON_ERROR));
    }

    private function allowMessage(ConnectionInterface $conn): bool
    {
        $id = $conn->resourceId;
        $now = microtime(true);

        if (!isset($this->rateBuckets[$id])) {
            $this->rateBuckets[$id] = ['start' => $now, 'count' => 0];
        }

        $bucket = $this->rateBuckets[$id];
        if (($now - $bucket['start']) > $this->rateLimitWindow) {
            $bucket = ['start' => $now, 'count' => 0];
        }

        $bucket['count']++;
        $this->rateBuckets[$id] = $bucket;

        return $bucket['count'] <= $this->rateLimitMax;
    }

    private function sanitizeMessage(string $message): string
    {
        $message = strip_tags($message);
        $message = preg_replace('/[\\x00-\\x1F\\x7F]/u', '', $message) ?? '';
        return trim($message);
    }
}
