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
namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;

/**
 * @Crontab(name="Foo", rule="* * * * * *", callback="execute", memo="这是一个示例的定时任务")
 */
class TestLogCrontab
{
    /**
     * @Inject
     * @var \Hyperf\Contract\StdoutLoggerInterface
     */
    private $logger;

    public function execute()
    {
        $this->logger->info('日志定时任务：'.date('Y-m-d H:i:s', time()));
    }
}
