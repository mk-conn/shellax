<?php
/**
 * Created by PhpStorm.
 * User: mkruege
 * Date: 23.06.17
 * Time: 14:12
 */

namespace Mdc\Shellax\Providers;


use Illuminate\Support\ServiceProvider;
use MkConn\Shellax\Console\RegisterSupervisorCommand;
use MkConn\Shellax\Supervisor;


/**
 * Class SupervisorServiceProvider
 *
 * @package Mdc\Supervisor\Providers
 */
class ShellaxServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(
            'shellax', function ($app) {
            return new Supervisor();
        });

        $this->app->singleton(
            'command.shellax.register-shellax-command',
            function ($app) {
                return new RegisterSupervisorCommand($app['shellax']);
            }
        );

        $this->commands('command.shellax.register-shellax-command');
    }
}
