<?php
/**
 * Created by PhpStorm.
 * User: mkruege
 * Date: 23.06.17
 * Time: 14:12
 */

namespace Mdc\Shellax\Providers;


use Illuminate\Support\ServiceProvider;
use MkConn\Shellax\Console\PostInstallCommand;
use MkConn\Shellax\Console\RegisterSupervisorCommand;
use MkConn\Shellax\Supervisor;


/**
 * Class SupervisorServiceProvider
 *
 * @package Mdc\Supervisor\Providers
 */
class ShellaxServiceProvider extends ServiceProvider
{

    public function boot()
    {

        $configPath = __DIR__ . '/../config/shellax.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('shellax.php');
        } else {
            $publishPath = base_path('config/shellax.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    public function register()
    {
        $this->app->singleton(
            'supervisor', function ($app) {
            return new Supervisor();
        });

        $this->app->singleton(
            'command.shellax.postinstall-command', function () {
            return new PostInstallCommand();
        });

        $this->app->singleton(
            'command.shellax.register-shellax-command',
            function ($app) {
                return new RegisterSupervisorCommand($app['supervisor']);
            }
        );

        $this->commands(['command.shellax.postinstall-command', 'command.shellax.register-shellax-command']);
    }
}
