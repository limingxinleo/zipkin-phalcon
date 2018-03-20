<?php
// +----------------------------------------------------------------------
// | RpcClient.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Support\Client;

use App\Core\Enums\CoreEnum;
use App\Core\Support\Recording\Span;
use Xin\Swoole\Rpc\Client\Client;
use Xin\Swoole\Rpc\Enum;

class RpcClient extends Client
{
    protected function getData($name, $arguments)
    {
        return [
            Enum::SERVICE => $this->service,
            Enum::METHOD => $name,
            Enum::ARGUMENTS => $arguments,
            CoreEnum::JIPKIN_SPAN => Span::getInstance(),
        ];
    }
}