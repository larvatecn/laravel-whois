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
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->config = $container->make('config');
    }

    /**
     * 查询原始Whois
     * @param string $domain
     * @return false|string
     */
    public function lookupRaw(string $domain)
    {
        // Creating default configured client
        $whois = Factory::get()->createWhois();
        try {
            $response =  $whois->lookupDomain($domain);
            return $response->text;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function lookup($domain, $refresh = false)
    {
        if ($refresh == false) {
            $info = Domain::query()->where('domain', $domain)->first();
            if ($info) {
                return $info;
            }
        }
        try {
            // Creating default configured client
            $whois = Factory::get()->createWhois();
            // Getting parsed domain info
            $info = $whois->loadDomainInfo($domain);
            return [
                'domainName' => $info->domainName,
                'DomainNameUnicode' => $info->getDomainNameUnicode(),
                'registrar' => $info->registrar,
                'whoisServer' => $info->whoisServer,
                'nameServers' => $info->nameServers,
                'states' => $info->states,
                'owner' => $info->owner,
                'creationDate' => Carbon::createFromTimestamp($info->creationDate),
                'expirationDate' => Carbon::createFromTimestamp($info->expirationDate),
                'raw' => $info->getResponse()->text,
            ];
        } catch (ConnectionException $e) {
            $msg = 'Disconnect or connection timeout';
        } catch (ServerMismatchException $e) {
            $msg = 'TLD server (.com for google.com) not found in current server hosts';
        } catch (WhoisException $e) {
            $msg = "Whois server responded with error '{$e->getMessage()}'";
        }
    }
}