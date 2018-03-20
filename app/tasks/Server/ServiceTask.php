<?php

namespace App\Tasks\Server;

use App\Biz\Service\BasicService;
use App\Common\Logger\Rpc\LoggerHandler;
use App\Core\Support\Server\RpcServer;
use App\Tasks\Task;
use Xin\Phalcon\Cli\Traits\Input;

class ServiceTask extends Task
{
    use Input;

    public function mainAction()
    {
        $server = new RpcServer();
        $pid = di('config')->application->pidsDir . 'service.pid';
        $rpc = di('config')->rpc;
        $daemonize = $this->option('daemonize', $rpc->daemonize);
        $host = $this->option('host', $rpc->host);
        $port = $this->option('port', $rpc->port);

        $server->setHandler('test', BasicService::getInstance());
        $server->setLoggerHandler(LoggerHandler::getInstance());
        $server->serve($host, $port, [
            'pid_file' => $pid,
            'daemonize' => $daemonize,
            'max_request' => 500,
        ]);
    }
}
