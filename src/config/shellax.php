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
            'whoami',
            'ls' => ['-l']
        ]
    ],
    'supervisor'  => [
        'config_dir' => env('SUPERVISOR_CONFIG_DIR', '/etc/supervisor.d'),
        'config_ext' => env('SUPERVISOR_CONFIG_EXT', '.conf')
    ]
];
