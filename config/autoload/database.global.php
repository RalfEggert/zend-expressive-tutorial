<?php
return [
    'dependencies' => [
        'factories' => [
            Zend\Db\Adapter\AdapterInterface::class =>
                Zend\Db\Adapter\AdapterServiceFactory::class,
        ],
    ],

    'db' => [
        'driver'  => 'pdo',
        'dsn'     => 'mysql:dbname=album-tutorial;host=localhost;charset=utf8',
        'user'    => 'album',
        'pass'    => 'album',
    ],
];
