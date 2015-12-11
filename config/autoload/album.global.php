<?php
return [
    'dependencies' => [
        'factories' => [
            Album\Action\AlbumListAction::class =>
                Album\Action\AlbumListFactory::class,

            Album\Model\Repository\AlbumRepositoryInterface::class =>
                Album\Model\Repository\ZendDbAlbumRepositoryFactory::class,
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
