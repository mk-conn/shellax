<?php

return [
    // post install tasks - e.g. cache clearing, running migrations, etc...
    'postinstall' => [
        'register:supervisor' => [
            'name'     => '',
            'user'     => '',
            'command'  => '',
            'logfile'  => '',
            'numprocs' => '',
        ]
    ],
    'supervisor'  => [
        'config_dir' => env('SUPERVISOR_CONFIG_DIR', '/etc/supervisor.d'),
        'config_ext' => env('SUPERVISOR_CONFIG_EXT', '.conf')
    ]
];
