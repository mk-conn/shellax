<?php
/**
 * Created by PhpStorm.
 * User: mkruege
 * Date: 23.06.17
 * Time: 14:31
 */

namespace MkConn\Shellax;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Style\OutputStyle;
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
     * @var OutputStyle
     */
    protected $command = null;

    /**
     * @param $output
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

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

        $this->inform('Running with supervisor ' . $process->getOutput());
    }

    /**
     * @param $message
     */
    public function inform($message)
    {
        if ($this->command) {
            $this->command->info($message);
        } else {
            echo $message;
        }
    }

    /**
     * @param $config
     */
    public function generateProgram($config)
    {

        $this->preCheck();

        $config['autostart'] = 'true';
        $config['autorestart'] = 'true';
        $config['redirect_stderr'] = 'true';

        $stub = file_get_contents(__DIR__ . '/stubs/supervisor-program.conf');

        $replace = [
            '/__name__/'     => $config['name'],
        ];

        $stub = file_get_contents(__DIR__ . '/stubs/supervisor-program.conf');
        $programConfig = $stub . "\n";
        $programConfig = preg_replace(array_keys($replace), array_values($replace), $stub);

        foreach ($config as $key => $value) {
            $programConfig .= "$key=$value\n";
        }

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

            $this->inform($process->getOutput());
        }
    }
}
