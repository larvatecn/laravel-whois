<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Whois;

use Illuminate\Support\ServiceProvider;

/**
 * Whois 服务提供者
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class WhoisServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(WhoisQuery::class, function () {
            return new WhoisQuery();
        });
    }
}