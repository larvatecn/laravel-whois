<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Whois\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Larva\Whois\Whois;

class RefreshJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * 如果任务的模型不再存在，则删除该任务
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * @var string
     */
    protected $domain;

    /**
     * Create a new job instance.
     *
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * 计算在重试任务之前需等待的秒数
     *
     * @return array
     */
    public function backoff()
    {
        return [3, 5, 15];
    }

    /**
     * Execute the job.
     * @reurn void
     */
    public function handle()
    {
        Whois::lookupInfo($this->domain, true);
    }
}