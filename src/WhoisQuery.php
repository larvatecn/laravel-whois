<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Whois;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;
use Iodev\Whois\Modules\Tld\TldInfo;
use Pdp\Rules;

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
     * 解析域名
     *
     * @param string $host
     * @return bool|\Pdp\Domain
     */
    public function parseDomain(string $host)
    {
        if (strpos($host, '://') !== false) {
            $url = parse_url($host);
            if (isset ($url ['host'])) {
                $host = $url ['host'];
            }
        }
        if ($host && strpos($host, '.') !== false) {
            return Rules::createFromString(__DIR__ . '/../resources/public_suffix_list.dat')->resolve($host);
        }
        return false;
    }

    /**
     * 查询原始 Whois
     * @param string $domain
     * @return string
     * @throws ConnectionException
     * @throws IllegalDomainException
     * @throws ServerMismatchException
     * @throws WhoisException
     */
    public function lookupRaw(string $domain): string
    {
        if (($domain = $this->parseDomain($domain)) == false) {
            throw new IllegalDomainException("Illegal domain name");
        } else {
            // Creating default configured client
            $whois = Factory::get()->createWhois();
            return $whois->lookupDomain($domain->getRegistrableDomain())->text;
        }
    }

    /**
     * 查询 Whois Info
     * @param string $domain
     * @param bool $resolve 是否解析域名
     * @return TldInfo
     * @throws ConnectionException
     * @throws IllegalDomainException
     * @throws ServerMismatchException
     * @throws WhoisException
     */
    public function lookupInfo(string $domain, $resolve = true)
    {
        if ($resolve == true && ($domain = $this->parseDomain($domain)) == false) {
            throw new IllegalDomainException("Illegal domain name");
        } else {
            // Creating default configured client
            $whois = Factory::get()->createWhois();
            return $whois->loadDomainInfo($domain instanceof Domain ? $domain->getRegistrableDomain() : $domain);
        }
    }

    /**
     * 查询 Whois
     * @param string $domain
     * @param false $refresh
     * @return Domain
     * @throws ConnectionException
     * @throws IllegalDomainException
     * @throws ServerMismatchException
     * @throws WhoisException
     */
    public function lookup(string $domain, $refresh = false): Domain
    {
        if (($domain = $this->parseDomain($domain)) == false) {
            throw new IllegalDomainException("Illegal domain name");
        }
        if ($refresh == false && ($info = Domain::getDomainInfo($domain->getRegistrableDomain())) != false) {
            return $info;
        } else {
            $response = $this->lookupInfo($domain->getRegistrableDomain(), false);
            if (($info = Domain::getDomainInfo($response->domainName)) == false) {
                $info = new Domain(['name' => $response->domainName]);
            }
            $info->registrar = $response->registrar;
            $info->owner = $response->owner;
            $info->whois_server = $response->whoisServer;
            $info->states = $response->states;
            $info->name_servers = $response->nameServers;
            $info->creation_date = Carbon::createFromTimestamp($response->creationDate);
            $info->expiration_date = Carbon::createFromTimestamp($response->expirationDate);
            $info->raw_data = $response->getResponse()->text;
            $info->save();
            return $info;
        }
    }
}