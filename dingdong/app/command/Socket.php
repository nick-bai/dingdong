<?php

namespace app\command;

use app\command\service\GlobalData;
use app\command\service\Server;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Socket extends Command
{
    protected function configure()
    {
        $this->setName('socketio server')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('host', 'H', Option::VALUE_OPTIONAL, 'the host of workerman service.', null)
            ->addOption('port', 'p', Option::VALUE_OPTIONAL, 'the port of workerman service.', null)
            ->addOption('daemon', 'd', Option::VALUE_NONE, 'Run the workerman service in daemon mode.')
            ->setDescription('socketio服务器');
    }

    protected function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');

        if (DIRECTORY_SEPARATOR !== '\\') {
            if (!in_array($action, ['start', 'stop', 'reload', 'restart', 'status', 'connections'])) {
                $output->writeln("<error>Invalid argument action:{$action}, Expected start|stop|restart|reload|status|connections .</error>");
                return false;
            }

            global $argv;
            array_shift($argv);
            array_shift($argv);
            array_shift($argv);
            array_unshift($argv, 'think', $action);
        }

        $logo =<<<EOL
      _ _                 _                   
     | (_)               | |                  
   __| |_ _ __   __ _  __| | ___  _ __   __ _ 
  / _` | | '_ \ / _` |/ _` |/ _ \| '_ \ / _` |
 | (_| | | | | | (_| | (_| | (_) | | | | (_| |
  \__,_|_|_| |_|\__, |\__,_|\___/|_| |_|\__, |
                 __/ |                   __/ |
                |___/                   |___/ 
EOL;

        $output->writeln($logo . PHP_EOL);

        GlobalData::start();
        Server::start();
    }
}