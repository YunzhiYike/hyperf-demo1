<?php

namespace App;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

class StdoutLoggerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get();
    }
}
