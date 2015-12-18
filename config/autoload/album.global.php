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
            Album\Action\AlbumUpdateFormAction::class =>
                Album\Action\AlbumUpdateFormFactory::class,
            Album\Action\AlbumUpdateHandleAction::class =>
                Album\Action\AlbumUpdateHandleFactory::class,
            Album\Action\AlbumDeleteFormAction::class =>
                Album\Action\AlbumDeleteFormFactory::class,
            Album\Action\AlbumDeleteHandleAction::class =>
                Album\Action\AlbumDeleteHandleFactory::class,

            Album\Form\AlbumDataForm::class =>
                Album\Form\AlbumDataFormFactory::class,
            Album\Form\AlbumDeleteForm::class =>
                Album\Form\AlbumDeleteFormFactory::class,

            Album\Model\InputFilter\AlbumInputFilter::class =>
                Album\Model\InputFilter\AlbumInputFilterFactory::class,

            Album\Model\Repository\AlbumRepositoryInterface::class =>
                Album\Model\Repository\ZendDbAlbumRepositoryFactory::class,

            Album\Db\AlbumTableGatewayInterface::class =>
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
        [
            'name'            => 'album-update',
            'path'            => '/album/update/:id',
            'middleware'      => Album\Action\AlbumUpdateFormAction::class,
            'allowed_methods' => ['GET'],
            'options'         => [
                'constraints' => [
                    'id' => '[1-9][0-9]*',
                ],
            ],
        ],
        [
            'name'            => 'album-update-handle',
            'path'            => '/album/update/:id/handle',
            'middleware'      => [
                Album\Action\AlbumUpdateHandleAction::class,
                Album\Action\AlbumUpdateFormAction::class,
            ],
            'allowed_methods' => ['POST'],
            'options'         => [
                'constraints' => [
                    'id' => '[1-9][0-9]*',
                ],
            ],
        ],
        [
            'name'            => 'album-delete',
            'path'            => '/album/delete/:id',
            'middleware'      => Album\Action\AlbumDeleteFormAction::class,
            'allowed_methods' => ['GET'],
            'options'         => [
                'constraints' => [
                    'id' => '[1-9][0-9]*',
                ],
            ],
        ],
        [
            'name'            => 'album-delete-handle',
            'path'            => '/album/delete/:id/handle',
            'middleware'      => Album\Action\AlbumDeleteHandleAction::class,
            'allowed_methods' => ['POST'],
            'options'         => [
                'constraints' => [
                    'id' => '[1-9][0-9]*',
                ],
            ],
        ],
    ],

    'templates' => [
        'paths' => [
            'album' => ['templates/album'],
        ],
    ],
];
