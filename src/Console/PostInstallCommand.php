<?php
/**
 * Created by PhpStorm.
 * User: mkruege
 * Date: 26.06.17
 * Time: 13:46
 */

namespace MkConn\Shellax\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class PostInstallCommand
 *
 * @package MkConn\Shellax\Console
 */
class PostInstallCommand extends Command
{

    /**
     * @var string
     */
    protected $name = 'shellax:postinstall';

    /**
     * @var string
     */
    protected $description = "Runs commands defined in your shellax.php config";

    /**
     *
     */
    public function handle()
    {
        $postinstall = config('shellax.postinstall');

        foreach ($postinstall as $type => $commands) {
            $method = 'run' . ucfirst($type) . 'Commands';

            $this->$method($commands);
        }
    }

    /**
     * @param array $artisanCommands
     *
     * @return $this
     */
    protected function runArtisanCommands(array $artisanCommands = [])
    {
        foreach ($artisanCommands as $artisanCommand => $args) {

            if (!is_array($args)) {
                $artisanCommand = $args;
                $args = [];
            }

            if (is_array($args) && !Arr::isAssoc($args)) {
                foreach ($args as $commands) {
                    $this->comment($artisanCommand . ' ' . implode(' ', $commands));
                    $this->call($artisanCommand, $commands);
                }
            } else {
                $this->comment($artisanCommand . ' ' . implode(' ', $args));
                $this->call($artisanCommand, $args);
            }
        }

        return $this;
    }

    /**
     * @param array $shellCommands
     *
     * @return $this
     */
    protected function runShellCommands(array $shellCommands = [])
    {
        foreach ($shellCommands as $shellCommand) {

            $process = new Process($shellCommand);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            echo $process->getOutput();
        }

        return $this;
    }

}
