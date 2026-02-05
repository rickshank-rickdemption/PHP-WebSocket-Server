<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use React\Socket\SocketServer;

$port = (int) (getenv('PORT') ?: 8080);
$host = getenv('WS_HOST') ?: '0.0.0.0';
$authToken = getenv('WS_TOKEN') ?: 'changeme';

$chat = new Chat($authToken);

$loop = Loop::get();
$socket = new SocketServer("{$host}:{$port}", [], $loop);
$server = new IoServer(
    new HttpServer(new WsServer($chat)),
    $socket,
    $loop
);

fwrite(STDOUT, "WebSocket server running on port {$port}" . PHP_EOL);
$server->run();
