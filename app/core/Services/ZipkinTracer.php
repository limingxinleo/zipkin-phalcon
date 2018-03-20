<?php
// +----------------------------------------------------------------------
// | Console.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Services;

use App\Core\Support\Reporter\ZipkinAsyncReporter;
use Phalcon\Config;
use Phalcon\DI\FactoryDefault;
use Zipkin\Endpoint;
use GuzzleHttp\Client;
use Zipkin\Annotation;
use Zipkin\Samplers\BinarySampler;
use Zipkin\TracingBuilder;
use Zipkin\Reporters\HttpLogging;

class ZipkinTracer implements ServiceProviderInterface
{
    public function register(FactoryDefault $di, Config $config)
    {
        $di->setShared('zipkinTracer', function () use ($di, $config) {
            $endpoint = Endpoint::create($config->name);

            $reporter = new ZipkinAsyncReporter();
            $sampler = BinarySampler::createAsAlwaysSample();
            $tracing = TracingBuilder::create()
                ->havingLocalEndpoint($endpoint)
                ->havingSampler($sampler)
                ->havingReporter($reporter)
                ->build();

            return $tracing;
        });
    }
}
