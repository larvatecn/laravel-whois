<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Whois;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Iodev\Whois\Helpers\DomainHelper;
use Larva\Support\IPHelper;

/**
 * Class Domain
 * @property int $id
 * @property string $name 域名
 * @property string $owner 所有者
 * @property string $registrar 注册商
 * @property array $states 域名状态
 * @property string $whois_server whois 服务器
 * @property array $name_servers NS服务器
 * @property Carbon $creation_date
 * @property Carbon $expiration_date
 * @property string $raw_data 原始数据
 * @property Carbon $updated_at 更新时间
 *
 * @property-read string $nameUnicode 域名Unicode
 * @property-read string $rawHtml
 * @property-read array $dnsServers
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Domain extends Model
{

    const CREATED_AT = null;

    const CACHE_TAG = 'domains:';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'domains';

    /**
     * 属性类型转换
     *
     * @var array
     */
    protected $casts = [
        'name_servers' => 'array',
        'states' => 'array',
    ];

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = [
        'creation_date',
        'expiration_date',
        'updated_at',
    ];

    /**
     * 允许批量赋值的属性
     * @var array
     */
    public $fillable = [
        'name', 'owner', 'registrar', 'whois_server', 'states', 'name_servers', 'creation_date', 'expiration_date',
        'raw_data'
    ];

    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }

    /**
     * 获取域名Unicode
     * @return string
     */
    public function getNameUnicodeAttribute(): string
    {
        return DomainHelper::toUnicode($this->name);
    }

    /**
     * 已经注册的天数
     * @return int
     */
    public function lifespanInDays(): int
    {
        return $this->creation_date->diffInDays(Carbon::now());
    }

    /**
     * 获取剩余有效的天数
     * @return int
     */
    public function daysUntilExpirationDate(): int
    {
        $interval = Carbon::now()->diff($this->expiration_date);
        return (int)$interval->format('%r%a');
    }

    /**
     * 获取 Whois HTML
     * @return string
     */
    public function getRawHtmlAttribute(): string
    {
        return implode("<br>\n", explode("\n", $this->raw_data));
    }

    /**
     * 获取 DNS 服务器
     * @return array
     */
    public function getDnsServersAttribute(): array
    {
        $servers = [];
        foreach ($this->name_servers as $name_server) {
            $servers[$name_server] = IPHelper::getHostIpV4($name_server);
        }
        return $servers;
    }

    /**
     * 查询缓存的Whois信息
     * @param string $domain
     * @return false|Domain
     */
    public static function getDomainInfo(string $domain)
    {
        if (($info = static::where('name', $domain)->first()) != null) {
            return $info;
        }
        return false;
    }

    /**
     * 获取缓存的总数
     * @param int $cacheMinutes
     * @return mixed
     */
    public static function getTotal($cacheMinutes = 60)
    {
        return Cache::remember(static::CACHE_TAG . 'total', Carbon::now()->addMinutes($cacheMinutes), function () {
            return static::count();
        });
    }
}