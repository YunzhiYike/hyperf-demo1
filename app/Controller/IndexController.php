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
namespace App\Controller;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Codec\Json;

class IndexController extends AbstractController
{
    public function index()
    {
        $headers = $this->request->getHeaders();
        ApplicationContext::getContainer()->get(StdoutLoggerInterface::class)->info(Json::encode(compact('headers')));
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello [ {$user} ]",
        ];
    }
}
