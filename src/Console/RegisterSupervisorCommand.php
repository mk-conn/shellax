<?php
/**
 * Created by PhpStorm.
 * User: mkruege
 * Date: 23.06.17
 * Time: 14:55
 */

namespace Mkconn\Shellax\Console;


use Illuminate\Console\Command;
use MkConn\Shellax\Supervisor;

/**
 * Class RegisterSupervisorCommand
 *
 * @package Mdc\Supervisor\Console
 */
class RegisterSupervisorCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'shellax:supervisor-register
                            {--name= : The programm name.} 
                            {--user= : The user the process should run with.} 
                            {--command= : The command to be executed.}
                            {--numprocs=4 : The number of processes to be run.}
                            {--logfile= : Logfile to write.}
                            {--environment= : Environment (e.g. PATH=%s).}';

    /**
     * @var string
     */
    protected $description = 'Register a programm in supervisor (you probably have to run this command as root)';

    /**
     * @var Supervisor
     */
    protected $supervisor;

    /**
     * RegisterSupervisorCommand constructor.
     *
     * @param Supervisor $supervisor
     */
    public function __construct(Supervisor $supervisor)
    {
        parent::__construct();
        $this->supervisor = $supervisor;
    }

    /**
     *
     */
    public function handle()
    {

        $config = [
            'name'     => $this->option('name'),
            'user'     => $this->option('user'),
            'command'  => $this->option('command'),
            'logfile'  => $this->option('logfile'),
            'numprocs' => $this->option('numprocs')
        ];

        if ($this->option('environment')) {
            $config['environment'] = $this->option('environment');
        }

        $this->supervisor->setCommand($this);
        $this->supervisor->generateProgram($config);
    }
}
