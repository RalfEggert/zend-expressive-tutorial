<?php
return [
    'dependencies' => [
        'factories' => [
            Album\Action\AlbumListAction::class         =>
                Album\Action\AlbumListFactory::class,
            Album\Action\AlbumCreateFormAction::class   =>
                Album\Action\AlbumCreateFormFactory::class,
            Album\Action\AlbumCreateHandleAction::class =>
                Album\Action\AlbumCreateHandleFactory::class,

            Album\Form\AlbumDataForm::class =>
                Album\Form\AlbumDataFormFactory::class,

            Album\Model\InputFilter\AlbumInputFilter::class =>
                Album\Model\InputFilter\AlbumInputFilterFactory::class,

            Album\Model\Repository\AlbumRepositoryInterface::class =>
                Album\Model\Repository\AlbumRepositoryFactory::class,

            Album\Model\Storage\AlbumStorageInterface::class =>
                Album\Db\AlbumTableGatewayFactory::class,
        ],
    ],

    'routes' => [
        [
            'name'            => 'album',
            'path'            => '/album',
            'middleware'      => Album\Action\AlbumListAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name'            => 'album-create',
            'path'            => '/album/create',
            'middleware'      => Album\Action\AlbumCreateFormAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name'            => 'album-create-handle',
            'path'            => '/album/create/handle',
            'middleware'      => [
                Album\Action\AlbumCreateHandleAction::class,
                Album\Action\AlbumCreateFormAction::class,
            ],
            'allowed_methods' => ['POST'],
        ],
    ],

    'templates' => [
        'paths' => [
            'album' => ['templates/album'],
        ],
    ],
];
