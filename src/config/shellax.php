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
    ]
];
