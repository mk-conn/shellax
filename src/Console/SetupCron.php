<?php

namespace MkConn\Shellax\Console;


use Cron\CronExpression;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling;

/**
 * Class SetupCron
 *
 * @package MkConn\Shellax\Console
 */
class SetupCron extends Command
{
    use Scheduling\ManagesFrequencies;

    /**
     * @var string
     */
    protected $signature = 'shellax:setup-cron
                            {--name= : Give the job a name (used as filename in cron directoy)}
                            {--schedule= : When to run this command (all Laravel frequency options are possible, expected dasherized e.g. every-five-minutes)}
                            {--at= : Specify when exactly (when scheduled daily or hourly), e.g. 13:00 or 17}
                            {--on= : Specify when exactly (when scheduled monthly), e.g. 4,15:00 -> run every month on the 4th, at 15:00}
                            {--command= : The command to run}
                            {--user= : The user who runs the command}
                            {--output= : Where to send the output}';

    /**
     * @var string
     */
    protected $description = 'Setup a cronjob';

    /**
     * @var string
     */
    protected $entry = null;

    /**
     * @var string
     */
    protected $name = null;

    public $expression = '* * * * * *';

    public $output = '/dev/null';

    /**
     *
     */
    public function handle()
    {
        $this->buildCronEntry()
             ->writeCronEntry();
    }

    /**
     * @return $this
     */
    protected function buildCronEntry()
    {
        $schedule = $this->option('schedule');
        $this->name = $this->option('name');
        $command = $this->option('command');
        $at = $this->option('at');
        $on = $this->option('on');
        $user = $this->option('user');
        $output = $this->option('output') ?: '/dev/null';

        if (!$this->name) {
            throw new \InvalidArgumentException('The --name option must be set.');
        }
        if (!$command) {
            throw new \InvalidArgumentException('The --command option must be set.');
        }

        $method = camel_case($schedule);
        $args = null;

        if ($at) {
            $method .= 'At';
            $args = $at;
        }

        if ($on) {
            $method .= 'On';
            $args = $on;
        }

        if (method_exists($this, $method)) {
            $this->{$method}($args);
        } else {
            if (CronExpression::isValidExpression($schedule)) {
                $this->cron($schedule);
            }
            throw new \InvalidArgumentException(
                "Schedule `$schedule` is not a valid frequency option or cron expression.");
        }

        $command = $user . ' ' . $command;

        $commandParts = [
            $this->expression,
            escapeshellcmd(trim($command)),
            '>>',
            $output,
            '2>&1'
        ];

        $this->entry = implode(' ', $commandParts);

        $this->comment('Cron entry: ' . $this->entry);

        return $this;
    }

    /**
     * Saves the cron file
     */
    protected function writeCronEntry()
    {
        $cronDir = config('shellax.cron.dirs.default');
        if (strlen($this->entry) > 0) {
            if (is_dir($cronDir)) {
                $filename = $cronDir . DIRECTORY_SEPARATOR . $this->name;
                file_put_contents($filename, $this->entry);

                $this->info('Saved cronjob to ' . $filename);
            }
        }
    }

}
