<?php
// +----------------------------------------------------------------------
// | 控制器基类 [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Controllers;

use App\Core\Support\Recording\Span;
use Zipkin\DefaultTracing;
use Zipkin\Tracer;

abstract class Controller extends \Phalcon\Mvc\Controller
{
    /** @var Tracer */
    public $tracer;

    /** @var \Zipkin\Span */
    public $trace;

    public function initialize()
    {
    }

    public function beforeExecuteRoute()
    {
        /** @var DefaultTracing $tracing */
        $tracing = di('zipkinTracer');
        $this->tracer = $tracing->getTracer();
        $spanName = $this->router->getRewriteUri();

        $this->trace = $this->tracer->newTrace();
        $this->trace->setName($spanName);
        $this->trace->start();

        $context = $this->trace->getContext();

        $span = Span::getInstance();
        $span->traceId = $context->getTraceId();
        $span->parentSpanId = $context->getParentId();
        $span->spanId = $context->getSpanId();
        $span->sampled = $context->isSampled();
    }

    public function afterExecuteRoute()
    {
        $this->trace->finish();
        $this->tracer->flush();
    }
}
