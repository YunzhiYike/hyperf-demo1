<?php

namespace App\Controller;

use Hyperf\Contract\OnReceiveInterface;
use Swoole\Coroutine\Server\Connection;
use Swoole\Server as SwooleServer;

class TcpServer implements OnReceiveInterface
{

    public function onReceive($server, int $fd, int $reactorId, string $data): void
    {
        $server->send($fd, 'recv:' . $data);
    }
}
