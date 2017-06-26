<?php
/**
 * Created by PhpStorm.
 * User: mkruege
 * Date: 23.06.17
 * Time: 14:31
 */

namespace Mdc\Shellax;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class Supervisor
 *
 * @package Mdc\Supervisor
 */
class Supervisor
{

    /**
     *
     */
    const SUPERVISOR_CONF_DIR = '/etc/supervisor.d';

    public function __construct()
    {
    }


    /**
     * @param $config
     */
    public function generateProgram($config)
    {


        $replace = [
            '/__command__/'  => $config['command'],
            '/__name__/'     => $config['name'],
            '/__user__/'     => $config['user'],
            '/__numprocs__/' => $config['numprocs'],
            '/__logfile__/'  => $config['logfile']
        ];

        $stub = file_get_contents(__DIR__ . '/stubs/supervisor-program.conf');

        $programConfig = preg_replace(array_keys($replace), array_values($replace), $stub);

        // write to /etc/shellax.d/<name>.conf
        $supervisorConfDir = env('SUPERVISOR_CONF_DIR', self::SUPERVISOR_CONF_DIR);

        if (is_dir($supervisorConfDir)) {
            $ext = env('SUPERVISOR_CONF_EXT', '.conf');
            $filename = $supervisorConfDir . '/' . $config['name'] . $ext;
            file_put_contents($filename, $programConfig);
        }

        $this->addProgramm($config['name']);
    }

    /**
     * @param $name
     */
    protected function addProgramm($name)
    {
        $commands = [
            'supervisorctl reread',
            'supervisorctl add ' . $name,
            'supervisorctl update',
            'supervisorctl restart all'
        ];

        $process = new Process($commands);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
}
