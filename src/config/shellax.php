<?php

return [
    // post install tasks - e.g. cache clearing, running migrations, etc...
    'postinstall' => [
        'artisan' => [
            'shellax:supervisor-register' => [
                '--name'     => '',
                '--user'     => '',
                '--command'  => '',
                '--logfile'  => '',
                '--numprocs' => '4',
            ]
        ],
        'shell'   => [
            'whoami'
        ]
    ],
    'supervisor'  => [
        'config_dir'         => env('SUPERVISOR_CONFIG_DIR', '/etc/supervisor.d'),
        'config_ext'         => env('SUPERVISOR_CONFIG_EXT', '.conf'),
        'supervisor_bin_dir' => env('SUPERVISOR_BIN_DIR', '/usr/bin')
    ],
    'cron'        => [
        'dirs' => [
            'hourly'  => env('CRON_DIR_DAILY', '/etc/cron.daily'),
            'daily'   => env('CRON_DIR_DAILY', '/etc/cron.hourly'),
            'monthly' => env('CRON_DIR_MONTHLY', '/etc/cron.monthly'),
            'default' => env('CRON_DIR_DEFAULT', '/etc/cron.d')
        ]
    ]
];
