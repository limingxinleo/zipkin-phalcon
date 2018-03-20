<?php
// +----------------------------------------------------------------------
// | Span.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Support\Recording;

use Xin\Traits\Common\InstanceTrait;

class Span
{
    use InstanceTrait;

    public $traceId;

    public $spanId;

    public $parentSpanId;

    public $sampled;
}