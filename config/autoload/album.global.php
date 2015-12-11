<?php
return [
    'dependencies' => [
        'factories' => [
            Album\Action\AlbumListAction::class =>
                Album\Action\AlbumListFactory::class,
            Album\Action\AlbumCreateAction::class =>
                Album\Action\AlbumCreateFactory::class,

            Album\Form\AlbumDataForm::class =>
                Album\Form\AlbumDataFormFactory::class,

            Album\Model\Repository\AlbumRepository::class =>
                Album\Model\Repository\AlbumRepositoryFactory::class,
            Album\Model\InputFilter\AlbumInputFilter::class =>
                Album\Model\InputFilter\AlbumInputFilterFactory::class,
        ],
    ],

    'routes' => [
        [
            'name' => 'album',
            'path' => '/album',
            'middleware' => Album\Action\AlbumListAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name'            => 'album-create',
            'path'            => '/album/create',
            'middleware'      => Album\Action\AlbumCreateAction::class,
            'allowed_methods' => ['GET', 'POST'],
        ],
    ],

    'templates' => [
        'paths' => [
            'album'    => ['templates/album'],
        ],
    ],
];
