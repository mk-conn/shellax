# Shellax

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Laravel package to ease deployments - running tasks after deploy/install like cache cleaning, adding supervisor programms ... 

## Install

Via Composer

``` bash
$ composer require mk-conn/shellax
```

## Usage

In your `config/app.php` add the Shellax service provider like so:

```php
'providers' => // ... other providers 

    MkConn\Shellax\Providers\ShellaxServiceProvider::class,
    
```

Publish the shellax config 
```bash
php artisan vendor:publish --provider="MkConn\Shellax\Providers\ShellaxServiceProvider"
```

Available artisan commands
```bash
php artisan shellax:postintall
php artisan shellax:supervisor-register
```

### Configuration
Example configuration
```php
<?php

$dir = __DIR__;
$dir = realpath($dir . '/..');

return [
    // post install tasks - e.g. cache clearing, running migrations, etc...
    'postinstall' => [
        'artisan' => [
            'shellax:supervisor-register' => [
                '--name'     => 'your-fancy-name-here',
                '--user'     => 'nginx', // user to run the following command
                '--command'  => "/usr/bin/php {$dir}/artisan queue:work --tries=3 --timeout=10",
                '--logfile'  => '/var/log/laravel-queue.log',
                '--numprocs' => '4', // number of processes to run by supervisor
            ]
        ],
        'shell' => [
            '/etc/whatever-should-run -arg1'   
        ]
    ],
    'supervisor'  => [
        'config_dir'         => env('SUPERVISOR_CONFIG_DIR', '/etc/supervisor.d'),
        'config_ext'         => env('SUPERVISOR_CONFIG_EXT', '.conf'),
        'supervisor_bin_dir' => env('SUPERVISOR_BIN_DIR', '/usr/bin')
    ]
];
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [mk-conn][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package_name/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/:vendor/:package_name.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/:vendor/:package_name.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/:vendor/:package_name.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-travis]: https://travis-ci.org/:vendor/:package_name
[link-scrutinizer]: https://scrutinizer-ci.com/g/:vendor/:package_name/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/:vendor/:package_name
[link-downloads]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username
[link-contributors]: ../../contributors
