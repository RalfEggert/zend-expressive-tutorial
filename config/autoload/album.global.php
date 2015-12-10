<?php
return [
    'dependencies' => [
        'factories' => [
            Album\Action\AlbumListAction::class =>
                Album\Action\AlbumListFactory::class,
            Album\Action\AlbumCreateAction::class =>
                Album\Action\AlbumCreateFactory::class,
            Album\Action\AlbumUpdateAction::class =>
                Album\Action\AlbumUpdateFactory::class,
            Album\Action\AlbumDeleteAction::class =>
                Album\Action\AlbumDeleteFactory::class,

            Album\Form\AlbumForm::class =>
                Album\Form\AlbumFormFactory::class,
            Album\Form\AlbumDeleteForm::class =>
                Album\Form\AlbumDeleteFormFactory::class,

            Album\Model\Table\AlbumTable::class =>
                Album\Model\Table\AlbumTableFactory::class,
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
        [
            'name'            => 'album-update',
            'path'            => '/album/update/:id',
            'middleware'      => Album\Action\AlbumUpdateAction::class,
            'allowed_methods' => ['GET', 'POST'],
            'options'         => [
                'constraints' => [
                    'id' => '[0-9]+',
                ],
            ],
        ],
        [
            'name'            => 'album-delete',
            'path'            => '/album/delete/:id',
            'middleware'      => Album\Action\AlbumDeleteAction::class,
            'allowed_methods' => ['GET', 'POST'],
            'options'         => [
                'constraints' => [
                    'id' => '[0-9]+',
                ],
            ],
        ],
    ],

    'templates' => [
        'paths' => [
            'album'    => ['templates/album'],
        ],
    ],
];