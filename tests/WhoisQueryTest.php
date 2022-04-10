<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Whois\Tests;

use Larva\Whois\WhoisQuery;

class WhoisQueryTest extends TestCase
{
    public function testParseDomain()
    {
        $domain = 'www.china.com.cn';
        $whois = new WhoisQuery();
        $result = $whois->parseDomain($domain);
        $this->assertTrue($result->registrableDomain()->toString() == 'china.com.cn');
    }

    public function testLookupRaw()
    {
        $domain = 'www.google.com';
        $whois = new WhoisQuery();
        $result = $whois->lookupRaw($domain);
        $this->assertIsString($result);
    }

    public function testLookupInfo()
    {
        $domain = 'www.qq.com';
        $whois = new WhoisQuery();
        $result = $whois->lookupInfo($domain);
        $this->assertIsString($result->getResponse()->text);
    }
}