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
        $gz = make(Client::class);
        $data = '{"kpn":"KUAISHOU_VISION","kpf":"PC_WEB","subBiz":"SINGLE_ROW_WEB","sdkVersion":"1.1.2.2.0","shareChannel":"WEIBO","shareMethod":"CARD","shareObjectId":"3xm54e8eg2fi29i","extTokenStoreParams":{"sharePath":"/short-video/3xm54e8eg2fi29i?fid=2149050647&cc=share_copylink&followRefer=151&shareMethod=TOKEN&docId=9&kpn=KUAISHOU&subBiz=BROWSE_SLIDE_PHOTO&photoId=3xm54e8eg2fi29i&shareId=17231163518125&shareToken=X6R3ypIObhly1CV&shareResourceType=PHOTO_OTHER&userId=3x4cyr74exfkg3w&shareType=1&et=1_a%2F2001764882143588738_sr0&shareMode=APP&originShareId=17231163518125&appType=1&shareObjectId=5207287158176655291&shareUrlOpened=0&timestamp=1669616136337&utm_source=app_share&utm_medium=app_share&utm_campaign=app_share&location=app_share&utm_source=pc_share&utm_medium=pc_share&utm_campaign=pc_share","title":"#好物推荐 #世界杯官方授权 #世界杯幸运金球","coverUrl":"https://p2.a.yximgs.com/upic/2022/11/28/14/BMjAyMjExMjgxNDExMjFfMjc0NTI1NTIxMl84OTgxNzYwMDgyNl8yXzM=_B2460a1b79ed317dd207f22819ec8f68c.jpg?tag=1-1669617329-xpcwebdetail-0-cqbpcvugyu-d454f2b34435f27f&clientCacheKey=3xm54e8eg2fi29i.jpg&di=777a5b0c&bp=10004","nickname":"聪聪"}}';
        $data = json_decode($data, true);
        $option = [
            'json' => $data,
            'headers' => [
                'content-type' => 'application/json',
                'cookie' => 'kpf=PC_WEB; kpn=KUAISHOU_VISION; clientid=3; did=web_f6df1768ab8970267fb89144447d0985; client_key=65890b29; ksliveShowClipTip=true; didv=1669616151000; userId=2149050647; kuaishou.server.web_st=ChZrdWFpc2hvdS5zZXJ2ZXIud2ViLnN0EqAB83o-hhBdC_7itLK3e_2yt_ZLU2SomcWkOSfINiAd0Rn6qtKlZHbmhLlqEC6DAji1b4J6zZv-6ltpBNu40UsIx6Xt9RYL8Lm4VKMbUidZZX-OXxp43UsnoEZdjfCUXiXhG7aMTKU1UXlSABtB1-FB8EzH9Af3JXebmhT8dT_yDXB0ZxpQXrDfDPn4np4W_VNaZLFFHivAV-9bRfly1EBncxoSsguEA2pmac6i3oLJsA9rNwKEIiDdAKTOnQhu64lQh68UUemT7FB1qLfGMjwwKOts19ACmygFMAE; kuaishou.server.web_ph=2580ea3e406f11a0e21e9f73d0992c78a857',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
            ],
        ];
//        ini_set('memory_limit', '4G');
        $masterParallel = new Parallel(500);
        $i = 0;
        while ($i < 1000000) {
            $masterParallel->add(function () use ($i, $gz, $option) {
                $res = $gz->post('https://www.kuaishou.com/rest/zt/share/w/any', $option)->getBody()->getContents();
                $ress = json_decode($res, true);
                if ($ress['result'] == 1) {
                    echo sprintf('[Task: %d] [Success]', $i) . PHP_EOL;
                    return;
                }
                echo sprintf('[Task: %d] [Error] [%s]', $i, $ress['error_msg'] ?? $res) . PHP_EOL;
            });
            ++$i;
        }

        try {
            $masterParallel->wait();
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

//    public function testPush1()
//    {
//        $master = 45;
//        $masterParallel = new Parallel(40);
//        // 模拟一个master用这么多任务
//        $sub = 6600000 / $master;
//        $subParallel = new Parallel(50);
//        $client = make(Client::class);
//        while ($master > 0) {
//            $masterParallel->add(function () use ($sub, $subParallel, $client) {
//                $task = $sub;
//                while ($task > 0) {
//                    $subParallel->add(function () use ($client, $task) {
//                        $res = $client->get('https://www.baidu.com')->getBody()->getContents();
//                        echo $task . PHP_EOL;
//                        echo $res . PHP_EOL;
//                    });
//                    --$task;
//                }
//
//                $subParallel->wait();
//            });
//
//            $masterParallel->wait();
//            --$master;
//        }
//    }
}
