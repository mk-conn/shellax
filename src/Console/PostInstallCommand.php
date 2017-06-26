<?php
/**
 * Created by PhpStorm.
 * User: mkruege
 * Date: 26.06.17
 * Time: 13:46
 */

namespace MkConn\Shellax\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
        $artisanCommands = config('shellax.postinstall.artisan');

        foreach ($artisanCommands as $artisanCommand => $args) {
            if (!is_array($args)) {
                $args = [];
            }

            Artisan::call($artisanCommand, $args);
        }
    }

}
