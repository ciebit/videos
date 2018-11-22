<?php

$fileSettings = __DIR__.'/settings.ini';
if (is_readable($fileSettings) == false) {
    exit('Arquivo de configuracao nao existe ou esta sem permissao de leitura');
}
$settings = parse_ini_file($fileSettings, true);

return [
    'paths' => [
        'migrations' => __DIR__.'/database/migrations'
    ],
    'environments' => [
        'default_database' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $settings['database']['host'],
            'name' => $settings['database']['name'],
            'user' => $settings['database']['username'],
            'pass' => $settings['database']['password'],
            'port' => $settings['database']['port'],
            'charset' => $settings['database']['charset'],
        ]
    ]
];
