<?php
/**
 * Created by PhpStorm.
 * User: mkruege
 * Date: 23.06.17
 * Time: 14:31
 */

namespace MkConn\Shellax;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Console\Exception\CommandNotFoundException;
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
     * Check, if supervisor is installed
     */
    protected function preCheck()
    {

        $process = new Process('supervisord --version');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new CommandNotFoundException(
                'Supervisor is not installed, but required to run supervisor related tasks.');
        }

        echo 'Running with supervisor ' . $process->getOutput();
    }


    /**
     * @param $config
     */
    public function generateProgram($config)
    {

        $this->preCheck();

        $replace = [
            '/__command__/' => $config['command'],
            '/__name__/' => $config['name'],
            '/__user__/' => $config['user'],
            '/__numprocs__/' => $config['numprocs'],
            '/__logfile__/' => $config['logfile']
        ];

        $stub = file_get_contents(__DIR__ . '/stubs/supervisor-program.conf');

        $programConfig = preg_replace(array_keys($replace), array_values($replace), $stub);

        // write to /etc/supervisor.d/<name>.conf
        $supervisorConfDir = config('shellax.supervisor.config_dir');
        $configExt = config('shellax.supervisor.config_ext');

        if (!is_dir($supervisorConfDir)) {
            throw new FileNotFoundException(
                'Directory ' . $supervisorConfDir . ' does not exist but its needed to store the config.');
        }

        $filename = $supervisorConfDir . '/' . $config['name'] . $configExt;
        file_put_contents($filename, $programConfig);

        $this->addProgramm($config['name']);
    }

    /**
     * @param $name
     */
    protected function addProgramm($name)
    {
        $binDir = config('shellax.supervisor.supervisor_bin_dir');

        $commands = [
            $binDir . '/supervisorctl reread',
            $binDir . '/supervisorctl add ' . $name,
            $binDir . '/supervisorctl update',
            $binDir . '/supervisorctl restart all'
        ];

        foreach ($commands as $command) {
            $process = new Process($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            echo $process->getOutput();
        }
    }
}
