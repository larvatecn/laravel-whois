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
use Iodev\Whois\Helpers\DomainHelper;

/**
 * Class Domain
 * @property int $id
 * @property string $name 域名
 * @property string $registrar 所有者
 * @property array $states 域名状态
 * @property string $whois_server whois 服务器
 * @property array $name_servers NS服务器
 * @property Carbon $creation_date
 * @property Carbon $expiration_date
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 *
 * @property-read string $nameUnicode
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Domain extends Model
{
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
        'created_at',
        'updated_at',
    ];

    /**
     * 允许批量赋值的属性
     * @var array
     */
    public $fillable = [
        'name', 'owner', 'registrar', 'whois_server', 'states', 'name_servers', 'creation_date', 'expiration_date',
        'raw'
    ];

    /**
     * 获取格式化后的文件大小
     * @return string
     */
    public function getNameUnicodeAttribute()
    {
        return DomainHelper::toUnicode($this->name);
    }

    public function refresh()
    {

    }
}