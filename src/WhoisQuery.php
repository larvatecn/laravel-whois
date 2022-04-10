<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

namespace Larva\Whois;

use Illuminate\Support\Carbon;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;
use Iodev\Whois\Modules\Tld\TldInfo;
use Pdp\ResolvedDomainName;
use Pdp\Rules;
use Pdp\Domain as WhoisDomain;

/**
 * Class WhoisQuery
 * @author Tongle Xu <xutongle@gmail.com>
 */
class WhoisQuery
{
    /**
     * 解析域名
     *
     * @param string $host
     * @return ResolvedDomainName
     * @throws IllegalDomainException
     */
    public function parseDomain(string $host): ResolvedDomainName
    {
        if (str_contains($host, '://')) {
            $url = parse_url($host);
            if (isset ($url ['host'])) {
                $host = $url ['host'];
            }
        }
        if ($host && str_contains($host, '.')) {
            $publicSuffixList = Rules::fromPath(__DIR__ . '/../resources/public_suffix_list.dat');
            $domain = WhoisDomain::fromIDNA2008($host);
            return $publicSuffixList->resolve($domain);
        } else {
            throw new IllegalDomainException("Illegal domain name");
        }
    }

    /**
     * 查询原始 Whois
     * @param string $domain
     * @return string
     * @throws ConnectionException
     * @throws ServerMismatchException
     * @throws WhoisException
     * @throws IllegalDomainException
     */
    public function lookupRaw(string $domain): string
    {
        $domain = $this->parseDomain($domain);
        $whois = Factory::get()->createWhois();
        return $whois->lookupDomain($domain->registrableDomain()->toString())->text;
    }

    /**
     * 查询 Whois Info
     * @param string|ResolvedDomainName $domain
     * @return TldInfo
     * @throws ConnectionException
     * @throws ServerMismatchException
     * @throws WhoisException
     * @throws IllegalDomainException
     */
    public function lookupInfo($domain)
    {
        $whois = Factory::get()->createWhois();
        if (!$domain instanceof ResolvedDomainName) {
            $domain = $this->parseDomain($domain);
        }
        return $whois->loadDomainInfo($domain->registrableDomain()->toString());
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
    public function lookup(string $domain, bool $refresh = false): Domain
    {
        $domain = $this->parseDomain($domain);
        if ($refresh == false && ($info = Domain::getDomainInfo($domain->registrableDomain()->toString())) != false) {
            return $info;
        } else {
            $response = $this->lookupInfo($domain);
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