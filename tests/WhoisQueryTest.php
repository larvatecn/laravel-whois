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

    public function testLookupRaw()
    {
        $doamin = 'google.com';
        $whois = new WhoisQuery();
        $result = $whois->lookupRaw($doamin);
        $this->assertIsString($result);
    }

    public function testLookup()
    {
        $doamin = 'tintsoft.com';
        $whois = new WhoisQuery();
        $result = $whois->lookup($doamin);
        print_r($result);
    }
}