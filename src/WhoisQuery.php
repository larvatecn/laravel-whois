<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Whois;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Carbon;
use Iodev\Whois\Factory;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Larva\GeoIP\Models\GeoIPModel;

/**
 * Class WhoisQuery
 * @author Tongle Xu <xutongle@gmail.com>
 */
class WhoisQuery
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The configuration repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create a new manager instance.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
//    public function __construct(Container $container)
//    {
//        $this->container = $container;
//        $this->config = $container->make('config');
//    }

    /**
     * 查询原始 Whois
     * @param string $domain
     * @return false|string
     */
    public function lookupRaw(string $domain)
    {
        // Creating default configured client
        $whois = Factory::get()->createWhois();
        try {
            $response = $whois->lookupDomain($domain);
            return $response->text;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 查询 Whois Info
     * @param string $domain
     * @return false|\Iodev\Whois\Modules\Tld\TldInfo
     */
    public function lookupInfo(string $domain)
    {
        try {
            // Creating default configured client
            $whois = Factory::get()->createWhois();
            // Getting parsed domain info
            return $whois->loadDomainInfo($domain);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 查询 Whois
     * @param string $domain
     * @param false $refresh
     * @return array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function lookup(string $domain, $refresh = false)
    {
        if ($refresh == false && ($info = Domain::getDomainInfo($domain)) != false) {
            return $info;
        } else {
            $response = $this->lookupInfo($domain);
            return Domain::updateOrCreate([
                'name' => $response->domainName,
                'registrar' => $response->registrar,
                'owner' => $response->owner,
                'whois_server' => $response->whoisServer,
                'states' => $response->states,
                'name_servers' => $response->nameServers,
                'creation_date' => Carbon::createFromTimestamp($response->creationDate),
                'expiration_date' => Carbon::createFromTimestamp($response->expirationDate),
                'raw_data' => $response->getResponse()->text,
            ]);
        }
    }
}