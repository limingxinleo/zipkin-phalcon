<?php
// +----------------------------------------------------------------------
// | 基础测试类 [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace Tests\Units;

use GuzzleHttp\Client;
use Tests\HttpTestCase;

/**
 * Class UnitTest
 */
class BaseTest extends HttpTestCase
{
    public function testBaseCase()
    {
        $this->assertTrue(
            extension_loaded('phalcon')
        );
    }

    public function testZipkinCase()
    {
        $res = $this->post('/index/zipkin');
        $json = json_decode($res->getBody()->getContents(), true);
        $this->assertTrue($json['success']);
        $this->assertEquals(di('config')->version, $json['data']['version']);

        $traceId = $json['data']['zipkin']['traceId'];
        sleep(1);
        $url = 'trace/' . $traceId;
        $res = $this->getZipkinClient()->get($url);
        $json = json_decode($res->getBody()->getContents(), true);
        $this->assertEquals(2, count($json));
    }

    public function getZipkinClient()
    {
        return new Client([
            'base_uri' => 'http://127.0.0.1:9411/zipkin/api/v1/'
        ]);
    }
}
