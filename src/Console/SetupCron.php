<?php

namespace MkConn\Shellax\Console;


use Cron\CronExpression;
use Illuminate\Cache\NullStore;
use Illuminate\Cache\Repository;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling;

/**
 * Class SetupCron
 *
 * @package MkConn\Shellax\Console
 */
class SetupCron extends Command
{
    /**
     * @var string
     */
    protected $signature = 'shellax:setup-cron
                            {--name= : Give the job a name (used as filename in cron directoy)}
                            {--schedule= : When to run this command (all Laravel frequency options are possible, expected dasherized e.g. every-five-minutes)}
                            {--at= : Specify when exactly (when scheduled daily or hourly), e.g. 13:00 or 17}
                            {--on= : Specify when exactly (when scheduled monthly), e.g. 4,15:00 -> run every month on the 4th, at 15:00}
                            {--command= : The command to run}
                            {--as= : The user to run the command}
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
        $user = $this->option('as');
        $output = $this->option('output');

        if (!$user) {
            throw new \InvalidArgumentException('The --as (user) option must be set.');
        }
        if (!$this->name) {
            throw new \InvalidArgumentException('The --name option must be set.');
        }
        if (!$command) {
            throw new \InvalidArgumentException('The --command option must be set.');
        }

        $scheduleEvent = new Scheduling\Event(new Scheduling\CacheMutex(new Repository(new NullStore())), $command);
        $scheduleEvent->user($user);

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

        if (method_exists($scheduleEvent, $method)) {
            $scheduleEvent->{$method}($args);
        } else {
            if (CronExpression::isValidExpression($schedule)) {
                $scheduleEvent->cron($schedule);
            }
            throw new \InvalidArgumentException(
                "Schedule `$schedule` is not a valid frequency option or cron expression.");
        }

        if ($output) {
            $scheduleEvent->sendOutputTo($output);
        }

        $this->entry = $scheduleEvent->getExpression() . ' ' . $scheduleEvent->getSummaryForDisplay();

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
