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
        $doamin = 'www.china.com.cn';
        $whois = new WhoisQuery();
        $result = $whois->parseDomain($doamin);
        $this->assertTrue($result->registrableDomain()->toString() == 'china.com.cn');
    }

    public function testLookupRaw()
    {
        $doamin = 'www.google.com';
        $whois = new WhoisQuery();
        $result = $whois->lookupRaw($doamin);
        $this->assertIsString($result);
    }

    public function testLookupInfo()
    {
        $doamin = 'www.qq.com';
        $whois = new WhoisQuery();
        $result = $whois->lookupInfo($doamin);
        $this->assertIsString($result->getResponse()->text);
    }
}