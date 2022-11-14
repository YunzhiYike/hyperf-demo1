<?php

namespace App\Controller;

use Hyperf\Contract\OnReceiveInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Utils\ApplicationContext;
use Swoole\Coroutine\Server\Connection;
use Swoole\Server as SwooleServer;

class TcpServer implements OnReceiveInterface
{

    public function onReceive($server, int $fd, int $reactorId, string $data): void
    {
        ApplicationContext::getContainer()->get(StdoutLoggerInterface::class)->info("tcp请求一次");
        $server->send($fd, '我收到了你用TCP发的:' . $data);
    }
}
