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

use GuzzleHttp\Client;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Hyperf\Utils\Parallel;

/**
 * @Crontab(name="Foo", rule="* * * * * *", callback="execute", memo="这是一个示例的定时任务")
 */
class TestLogCrontab
{
    /**
     * @param Redis $redis
     */
    protected $redis;

    /**
     * @Inject
     * @var \Hyperf\Contract\StdoutLoggerInterface
     */
    private $logger;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function execute()
    {
        if (! $this->redis->set('alock', 1, ['nx', 'ex' => 10000])) {
            return;
        }
        $gz = make(Client::class);
        $data = '{"kpn":"KUAISHOU_VISION","kpf":"PC_WEB","subBiz":"SINGLE_ROW_WEB","sdkVersion":"1.1.2.2.0","shareChannel":"WEIBO","shareMethod":"CARD","shareObjectId":"3xm54e8eg2fi29i","extTokenStoreParams":{"sharePath":"/short-video/3xm54e8eg2fi29i?fid=2149050647&cc=share_copylink&followRefer=151&shareMethod=TOKEN&docId=9&kpn=KUAISHOU&subBiz=BROWSE_SLIDE_PHOTO&photoId=3xm54e8eg2fi29i&shareId=17231163518125&shareToken=X6R3ypIObhly1CV&shareResourceType=PHOTO_OTHER&userId=3x4cyr74exfkg3w&shareType=1&et=1_a%2F2001764882143588738_sr0&shareMode=APP&originShareId=17231163518125&appType=1&shareObjectId=5207287158176655291&shareUrlOpened=0&timestamp=1669616136337&utm_source=app_share&utm_medium=app_share&utm_campaign=app_share&location=app_share&utm_source=pc_share&utm_medium=pc_share&utm_campaign=pc_share","title":"#好物推荐 #世界杯官方授权 #世界杯幸运金球","coverUrl":"https://p2.a.yximgs.com/upic/2022/11/28/14/BMjAyMjExMjgxNDExMjFfMjc0NTI1NTIxMl84OTgxNzYwMDgyNl8yXzM=_B2460a1b79ed317dd207f22819ec8f68c.jpg?tag=1-1669617329-xpcwebdetail-0-cqbpcvugyu-d454f2b34435f27f&clientCacheKey=3xm54e8eg2fi29i.jpg&di=777a5b0c&bp=10004","nickname":"聪聪"}}';
        $data = json_decode($data, true);
        $option = [
            'json' => $data,
            'headers' => [
                'content-type' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
            ],
        ];
        $masterParallel = new Parallel(500);
        $i = 0;
        while ($i < 1000000) {
            $this->redis->expire('alock', 10000);
            $masterParallel->add(function () use ($i, $gz, $option) {
                $res = $gz->post('https://www.kuaishou.com/rest/zt/share/w/any', $option)->getBody()->getContents();
                $ress = json_decode($res, true);
                if ($ress['result'] == 1) {
                    $this->logger->info(sprintf('[Task: %d] [Success]', $i));
                    return;
                }
                $this->logger->error(sprintf('[Task: %d] [Error] [%s]', $i, $ress['error_msg'] ?? $res));
            });
            ++$i;
        }

        try {
            $masterParallel->wait();
        } catch (\Throwable $exception) {
            throw $exception;
        } finally {
            $this->redis->expire('alock', 0);
        }
    }
}
