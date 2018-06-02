<?php
// +----------------------------------------------------------------------
// | RpcServer.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Support\Server;

use App\Core\Enums\CoreEnum;
use App\Core\Support\Recording\Span;
use Xin\Swoole\Rpc\Enum;
use Xin\Swoole\Rpc\Server;
use Zipkin\DefaultTracing;
use Zipkin\Propagation\TraceContext;
use swoole_server;
use ReflectionClass;

class RpcServer extends Server
{
    public function receive(swoole_server $server, $fd, $reactor_id, $data)
    {
        try {
            $data = json_decode($data, true);
            $service = $data[Enum::SERVICE];
            $method = $data[Enum::METHOD];
            $arguments = $data[Enum::ARGUMENTS];

            if ($parentSpan = $data[CoreEnum::JIPKIN_SPAN]) {
                $spanName = $service . '@' . $method;
                $context = TraceContext::create(
                    $parentSpan['traceId'],
                    $parentSpan['spanId'],
                    $parentSpan['parentSpanId'],
                    $parentSpan['sampled']
                );
                /** @var DefaultTracing $tracing */
                $tracing = di('zipkinTracer');
                $tracer = $tracing->getTracer();
                $trace = $tracer->newChild($context);
                $trace->setName($spanName);
                $trace->start();

                $context = $trace->getContext();
                $span = Span::getInstance();
                $span->traceId = $context->getTraceId();
                $span->parentSpanId = $context->getParentId();
                $span->spanId = $context->getSpanId();
                $span->sampled = $context->isSampled();
            }

            if (!isset($this->services[$service])) {
                throw new RpcException('The service handler is not exist!');
            }

            $ref = new ReflectionClass($this->services[$service]);
            $handler = $ref->newInstance($server, $fd, $reactor_id);
            $result = $handler->$method(...$arguments);

            $response = $this->success($result);
            $server->send($fd, json_encode($response));

            if ($this->debug && $this->logger && $this->logger instanceof LoggerInterface) {
                $this->logger->info($data, $response);
            }
        } catch (\Exception $ex) {
            $response = $this->fail($ex->getCode(), $ex->getMessage());
            $server->send($fd, json_encode($response));

            if ($this->logger && $this->logger instanceof LoggerInterface) {
                $this->logger->error($data, $response, $ex);
            }
        } finally {
            if (isset($tracer) && isset($trace)) {
                $trace->finish();
                $tracer->flush();
            }
        }
    }
}