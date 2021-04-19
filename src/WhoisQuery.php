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
use Illuminate\Support\Facades\Log;
use Iodev\Whois\Factory;

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
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * 查询 Whois
     * @param string $domain
     * @param false $refresh
     * @return Domain|false
     */
    public function lookup(string $domain, $refresh = false)
    {
        if ($refresh == false && ($info = Domain::getDomainInfo($domain)) != false) {
            return $info;
        } else {
            $response = $this->lookupInfo($domain);
            if ($response != false) {
                if (($info = Domain::getDomainInfo($domain)) == false) {
                    $info = new Domain(['name' => $domain]);
                }
                $info->registrar = $response->registrar;
                $info->owner = $response->owner;
                $info->whois_server = $response->whoisServer;
                $info->states = $response->states;
                $info->name_servers = $response->nameServers;
                $info->creation_date = Carbon::createFromTimestamp($response->creationDate);
                $info->expiration_date = Carbon::createFromTimestamp($response->expirationDate);
                $info->raw_data = $response->getResponse()->text;
                $info->saveQuietly();

                return $info;
            } else {
                return false;
            }
        }
    }
}