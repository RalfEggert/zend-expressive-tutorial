<?php
return [
    'dependencies' => [
        'factories' => [
            Album\Action\AlbumListAction::class =>
                Album\Action\AlbumListFactory::class,

            Album\Model\Table\AlbumTable::class =>
                Album\Model\Table\AlbumTableFactory::class,
        ],
    ],

    'routes' => [
        [
            'name' => 'album',
            'path' => '/album',
            'middleware' => Album\Action\AlbumListAction::class,
            'allowed_methods' => ['GET'],
        ],
    ],

    'templates' => [
        'paths' => [
            'album'    => ['templates/album'],
        ],
    ],
];