<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Whois;

use Illuminate\Support\Facades\Facade;

/**
 * Class Whois
 * @method static Domain lookup(string $domain, $refresh = false)
 * @method static array lookupRaw(string $domain)
 * @method static \Iodev\Whois\Modules\Tld\TldInfo lookupInfo(string $domain)
 * @see \Larva\Whois\WhoisQuery
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Whois extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return WhoisQuery::class;
    }
}