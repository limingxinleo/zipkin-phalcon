<?php
// +----------------------------------------------------------------------
// | ZipkinLoggerReporter.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Jobs\Reporter;

use App\Jobs\Contract\JobInterface;
use Zipkin\Reporters\Http;

class ZipkinLoggerReporter implements JobInterface
{
    public $span;

    public function __construct(array $span)
    {
        $this->span = $span;
    }

    public function handle()
    {
        $http = new Http();
        $http->report($this->span);
    }
}