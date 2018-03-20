<?php
// +----------------------------------------------------------------------
// | ZipkinAsyncReporter.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Support\Reporter;

use App\Jobs\Reporter\ZipkinLoggerReporter;
use App\Utils\Queue;
use Zipkin\Reporter;

class ZipkinAsyncReporter implements Reporter
{
    public function report(array $spans)
    {
        Queue::push(new ZipkinLoggerReporter($spans));
    }
}