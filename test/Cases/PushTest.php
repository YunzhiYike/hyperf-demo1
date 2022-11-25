<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Cases;

use GuzzleHttp\Client;
use Hyperf\Utils\Parallel;
use HyperfTest\HttpTestCase;

/**
 * @internal
 * @coversNothing
 */
class PushTest extends HttpTestCase
{
    public function testPush()
    {
        $master = 45;
        $masterParallel = new Parallel(40);
        // 模拟一个master用这么多任务
        $sub = 6600000 / $master;
        $subParallel = new Parallel(50);
        $client = make(Client::class);
        while ($master > 0) {
            $masterParallel->add(function () use ($sub, $subParallel, $client) {
                $task = $sub;
                while ($task > 0) {
                    $subParallel->add(function () use ($client, $task) {
                        $res = $client->get('https://www.baidu.com')->getBody()->getContents();
                        echo $task . PHP_EOL;
                        echo $res . PHP_EOL;
                    });
                    --$task;
                }

                $subParallel->wait();
            });

            $masterParallel->wait();
            --$master;
        }
    }
}
