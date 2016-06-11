# Part 5: Updating and deleting albums

In this part of the tutorial we will handle the updating and deleting of
existing albums. Similar to the album creation action we will create all 
actions and factories needed. For the update we can reuse the album data 
form, for deletion we will create a new delete form.

## Update album configuration

First, we will update the album configuration in the 
`/config/autoload/album.global.php` file to add new middleware actions, 
a form and some routes. 

* In the `dependencies` section four new middleware with its factories are
  added. These will be created in the next steps.
   
   * The `AlbumUpdateFormAction` will show the update form for an album.
   * The `AlbumUpdateHandleAction` will handle the update form processing.
   * The `AlbumDeleteFormAction` will show the delete form for an album.
   * The `AlbumDeleteHandleAction` will handle the update form processing.
   
* Additionally, a new delete album form is also registered for the DI 
  Container. The form will be created as well in the next steps.
  
* The the `routes` section four new routes will be added for the four new
  middleware actions. Some are only processed for GET requests, some only
  for POST requests.
   

```php
<?php
return [
    'dependencies' => [
        'factories' => [
            /* ... */
            
            Album\Action\AlbumUpdateFormAction::class =>
                Album\Action\AlbumUpdateFormFactory::class,
            Album\Action\AlbumUpdateHandleAction::class =>
                Album\Action\AlbumUpdateHandleFactory::class,
            Album\Action\AlbumDeleteFormAction::class =>
                Album\Action\AlbumDeleteFormFactory::class,
            Album\Action\AlbumDeleteHandleAction::class =>
                Album\Action\AlbumDeleteHandleFactory::class,

            /* ... */

            Album\Form\AlbumDeleteForm::class =>
                Album\Form\AlbumDeleteFormFactory::class,

            /* ... */
        ],
    ],

    'routes' => [
        /* ... */

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

    /* ... */
];
```

## Add links to the album list page

Next, you need to add a link to the update and the delete page for each
album in the album list. Please open the `/templates/album/list.phtml` and
update the `foreach()` loop. You can generate the URLs with the `url` view
helper and display them in the table at the end of each row.

```php
    <?php foreach ($this->albumList as $albumEntity) : ?>
        <?php
        $urlParams = ['id' => $albumEntity->getId()];
        $updateUrl = $this->url('album-update', $urlParams);
        $deleteUrl = $this->url('album-delete', $urlParams);
        ?>
        <tr>
            <td><?php echo $albumEntity->getId(); ?></td>
            <td><?php echo $albumEntity->getArtist(); ?></td>
            <td><?php echo $albumEntity->getTitle(); ?></td>
            <td>
                <a href="<?php echo $updateUrl; ?>" class="btn btn-success">
                    <i class="fa fa-pencil"></i>
                </a>
                <a href="<?php echo $deleteUrl; ?>" class="btn btn-success">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
```

Now you can browse to [http://localhost:8080/album](http://localhost:8080/album) 
to see if the links for update and delete are shown correctly. Don't click 
on the yet since we have no update and delete action yet 

![Screenshot of album list with links](images/screen-album-list-links.png)

## Add update action to show form

Next, you need to create the `AlbumUpdateFormAction.php` file in the 
existing `/src/Album/Action/` path. Please note the following:

* The `AlbumUpdateFormAction` has three dependencies to the template 
  renderer, the album repository and the album form. All of these 
  dependencies can be injected with the constructor.
  
* The `__invoke()` method is run when the middleware is processed.
  
  * First the `id` is taken from the routing to read the current 
    `AlbumEntity` to update. 
  
  * If the form validation was started and failed, then the form has some
    messages set. In that case an appropriate message is set for the form.
    
  * If the form validation was not run, then a different message is set for
    the form. Additionally, the `AlbumEntity` instance is bound to the 
    form. When this is done the form uses the injected hydrator to extract
    the data from the entity and passes it to the form elements to set
    their values.
    
  * Next, the `$data` array is built with the form, the entity and the 
    message.
     
  * Finally, the update template is rendered and a `HtmlResponse` is
    passed back.

```php
<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumUpdateFormAction
 *
 * @package Album\Action
 */
class AlbumUpdateFormAction
{
    /**
     * @var TemplateRendererInterface
     */
    private $template;

    /**
     * @var AlbumRepositoryInterface
     */
    private $albumRepository;

    /**
     * @var AlbumDataForm
     */
    private $albumForm;

    /**
     * AlbumUpdateFormAction constructor.
     *
     * @param TemplateRendererInterface $template
     * @param AlbumRepositoryInterface  $albumRepository
     * @param AlbumDataForm             $albumForm
     */
    public function __construct(
        TemplateRendererInterface $template,
        AlbumRepositoryInterface $albumRepository,
        AlbumDataForm $albumForm
    ) {
        $this->template        = $template;
        $this->albumRepository = $albumRepository;
        $this->albumForm       = $albumForm;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     *
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        $id = $request->getAttribute('id');

        $album = $this->albumRepository->fetchSingleAlbum($id);

        if ($this->albumForm->getMessages()) {
            $message = 'Please check your input!';
        } else {
            $message = 'Please change the album!';

            $this->albumForm->bind($album);
        }

        $data = [
            'albumForm'   => $this->albumForm,
            'albumEntity' => $album,
            'message'     => $message,
        ];

        return new HtmlResponse(
            $this->template->render('album::update', $data)
        );
    }
}
```

The corresponding factory will be created in the new 
`AlbumUpdateFormFactory.php` file. It looks much similar to the
`AlbumCreateFormFactory` and requests the three dependencies from the DI
container to pass them to the constructor of the `AlbumUpdateFormAction`.

```php
<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumUpdateFormFactory
 *
 * @package Album\Action
 */
class AlbumUpdateFormFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumUpdateFormAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template        = $container->get(TemplateRendererInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $albumForm       = $container->get(AlbumDataForm::class);

        return new AlbumUpdateFormAction(
            $template, $albumRepository, $albumForm
        );
    }
}
```

## Add update action for form handling

Now you have to create the `AlbumUpdateHandleAction.php` file in the 
existing `/src/Album/Action/` path to handle the update form processing. 
Please note the following: 

* The `AlbumUpdateHandleAction` has three dependencies to the router, the 
  album repository and the album form. All of these dependencies can be 
  injected with the constructor.
  
* The `__invoke()` method is run when the form is processed.

  * The `id` is read from the request attributes.
  
  * The POST data is also read from the request.
  
  * Then the POST data is passed to the form and the form is validated.
  
  * If the validation was successful...
    
    * The `AlbumEntity` is fetched from the repository and the POST data
      is passed to it.
      
    * The album is saved and a redirect to the album list is made.
    
  * If the form validation failed...
  
    * The next middleware is processed which is the `AlbumUpdateFormAction` 
      to show the update form.
  
```php
<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumUpdateHandleAction
 *
 * @package Album\Action
 */
class AlbumUpdateHandleAction
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AlbumRepositoryInterface
     */
    private $albumRepository;

    /**
     * @var AlbumDataForm
     */
    private $albumForm;

    /**
     * AlbumUpdateHandleAction constructor.
     *
     * @param RouterInterface           $router
     * @param AlbumRepositoryInterface           $albumRepository
     * @param AlbumDataForm             $albumForm
     */
    public function __construct(
        RouterInterface $router,
        AlbumRepositoryInterface $albumRepository,
        AlbumDataForm $albumForm
    ) {
        $this->router = $router;
        $this->albumRepository = $albumRepository;
        $this->albumForm = $albumForm;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     *
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        $id = $request->getAttribute('id');

        $postData = $request->getParsedBody();

        $this->albumForm->setData($postData);

        if ($this->albumForm->isValid()) {
            $postData['id'] = $id;

            $album = $this->albumRepository->fetchSingleAlbum($id);
            $album->exchangeArray($postData);

            $this->albumRepository->saveAlbum($album);

            return new RedirectResponse(
                $this->router->generateUri('album')
            );
        }

        return $next($request, $response);
    }
}
```

The needed factory will be created in the new 
`AlbumUpdateHandleFactory.php` file. It looks much similar to the
`AlbumCreateHandleFactory` and requests the three needed dependencies from 
the DI container to pass them to the constructor of the 
`AlbumUpdateHandleAction`.

```php
<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumUpdateHandleFactory
 *
 * @package Album\Action
 */
class AlbumUpdateHandleFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumUpdateHandleAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $router          = $container->get(RouterInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $albumForm       = $container->get(AlbumDataForm::class);

        return new AlbumUpdateHandleAction(
            $router, $albumRepository, $albumForm
        );
    }
}
```

## Create update template

Now you need to create the `update.phtml` file in the `/templates/album/` 
path. In this template you need to setup the form with an form action and
display it. Again the form is rendered by using the `form`, the 
`formLabel`, the `formElement` and the `formElementErrors` view helpers 
for the form elements and the submit button.

```php
<?php
use Album\Form\AlbumDataForm;
use Album\Model\Entity\AlbumEntity;

/** @var AlbumEntity $album */
$album = $this->albumEntity;

/** @var AlbumDataForm $form */
$form = $this->albumForm;
$form->setAttribute(
    'action', $this->url('album-update-handle', ['id' => $album->getId()])
);

$this->headTitle('Edit album');
?>

<div class="jumbotron">
    <h1>Edit album</h1>
</div>

<div class="alert alert-danger">
    <strong><?php echo $this->message; ?></strong>
</div>

<div class="well">
    <?php echo $this->form()->openTag($form); ?>
    <div class="form-group">
        <?php echo $this->formLabel($form->get('artist')); ?>
        <div class="col-sm-10">
            <?php echo $this->formElement($form->get('artist')); ?>
            <?php echo $this->formElementErrors($form->get('artist')); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $this->formLabel($form->get('title')); ?>
        <div class="col-sm-10">
            <?php echo $this->formElement($form->get('title')); ?>
            <?php echo $this->formElementErrors($form->get('title')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?php echo $this->formElement($form->get('save_album')); ?>
        </div>
    </div>
    <?php echo $this->form()->closeTag(); ?>
</div>

<p>
    <a href="<?php echo $this->url('album') ?>" class="btn btn-success">
        Back to album list
    </a>
</p>
```

Now you can browse to 
[http://localhost:8080/album/update/1](http://localhost:8080/album/update/1) 
to see if the update form works correctly. Try to change the album and save
it.

![Screenshot of album update](images/screen-album-update.png)

## Add delete form

To delete an album we will create another form with two submit buttons. 
Please create the new file `AlbumDeleteForm.php` in the `/src/Album/Form/`
path. This form just adds two submit buttons. One to confirm the deletion
and on to cancel it.

```php
<?php
namespace Album\Form;

use Zend\Form\Form;

/**
 * Class AlbumDeleteForm
 *
 * @package Album\Form
 */
class AlbumDeleteForm extends Form
{
    /**
     * Init form
     */
    public function init()
    {
        $this->setName('album_delete_form');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'name'       => 'delete_album_yes',
                'type'       => 'Submit',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => 'Yes',
                    'id'    => 'delete_album_yes',
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'delete_album_no',
                'type'       => 'Submit',
                'attributes' => [
                    'class' => 'btn btn-default',
                    'value' => 'No',
                    'id'    => 'delete_album_no',
                ],
            ]
        );
    }
}
```

The `AlbumDeleteFormFactory` for this form is created in the 
`AlbumDeleteFormFactory.php` file and quite simple. It needs no 
dependencies to inject and just runs the `init()` method of the form.

```php
<?php
namespace Album\Form;

use Interop\Container\ContainerInterface;
use Zend\Form\Form;

/**
 * Class AlbumDeleteFormFactory
 *
 * @package Album\Form
 */
class AlbumDeleteFormFactory extends Form
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumDeleteForm
     */
    public function __invoke(ContainerInterface $container)
    {
        $form = new AlbumDeleteForm();
        $form->init();

        return $form;
    }
}
```

## Add delete action to show form

Again we will need a middleware action to show the form. This is done
in the `AlbumDeleteFormAction.php` file. This middleware is very similar
to the `AlbumUpdateFormAction` above. The main difference is that it uses
the new delete form and sets a different message.

```php
<?php
namespace Album\Action;

use Album\Form\AlbumDeleteForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumDeleteFormAction
 *
 * @package Album\Action
 */
class AlbumDeleteFormAction
{
    /**
     * @var TemplateRendererInterface
     */
    private $template;

    /**
     * @var AlbumRepositoryInterface
     */
    private $albumRepository;

    /**
     * @var AlbumDeleteForm
     */
    private $albumForm;

    /**
     * AlbumDeleteFormAction constructor.
     *
     * @param TemplateRendererInterface $template
     * @param AlbumRepositoryInterface  $albumRepository
     * @param AlbumDeleteForm           $albumForm
     */
    public function __construct(
        TemplateRendererInterface $template,
        AlbumRepositoryInterface $albumRepository,
        AlbumDeleteForm $albumForm
    ) {
        $this->template        = $template;
        $this->albumRepository = $albumRepository;
        $this->albumForm       = $albumForm;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     *
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        $id = $request->getAttribute('id');

        $album = $this->albumRepository->fetchSingleAlbum($id);

        $message = 'Do you want to delete this album?';

        $this->albumForm->bind($album);

        $data = [
            'albumEntity' => $album,
            'albumForm'   => $this->albumForm,
            'message'     => $message,
        ];

        return new HtmlResponse(
            $this->template->render('album::delete', $data)
        );
    }
}
```

The `AlbumDeleteFormFactory` is also quite similar to the 
`AlbumUpdateFormFactory` above. The only difference is the injection of 
the delete from during instantiation of `AlbumDeleteFormAction`. 

```php
<?php
namespace Album\Action;

use Album\Form\AlbumDeleteForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumDeleteFormFactory
 *
 * @package Album\Action
 */
class AlbumDeleteFormFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumDeleteFormAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template        = $container->get(TemplateRendererInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $albumForm       = $container->get(AlbumDeleteForm::class);

        return new AlbumDeleteFormAction(
            $template, $albumRepository, $albumForm
        );
    }
}
```

## Add delete action for form handling

For the deletion handling you need to create the `AlbumDeleteFormAction.php` 
which works different than the form handling middleware actions for album
creation and updating. In the `__invoke()` method it checks if the 
deletion confirm button named `delete_album_yes` was send to delete the 
album. No matter which submit button was sent, a redirect to the album 
list is created at the end.

```php
<?php
namespace Album\Action;

use Album\Form\AlbumDeleteForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class AlbumDeleteFormAction
 *
 * @package Album\Action
 */
class AlbumDeleteFormAction
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AlbumRepositoryInterface
     */
    private $albumRepository;

    /**
     * AlbumDeleteHandleAction constructor.
     *
     * @param RouterInterface          $router
     * @param AlbumRepositoryInterface $albumRepository
     */
    public function __construct(
        RouterInterface $router,
        AlbumRepositoryInterface $albumRepository
    ) {
        $this->router          = $router;
        $this->albumRepository = $albumRepository;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     *
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        $id = $request->getAttribute('id');

        $album = $this->albumRepository->fetchSingleAlbum($id);

        $postData = $request->getParsedBody();

        if (isset($postData['delete_album_yes'])) {
            $this->albumRepository->deleteAlbum($album);
        }

        return new RedirectResponse(
            $this->router->generateUri('album')
        );
    }
}
```

The `AlbumDeleteHandleFactory` just requests the router and the album 
repository and injects them into the instantiation of the 
`AlbumDeleteHandleAction`. 

```php
<?php
namespace Album\Action;

use Album\Form\AlbumDeleteForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class AlbumDeleteHandleFactory
 *
 * @package Album\Action
 */
class AlbumDeleteHandleFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumDeleteHandleAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $router          = $container->get(RouterInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);

        return new AlbumDeleteHandleAction(
            $router, $albumRepository
        );
    }
}
```

## Create delete templates

Finally, the `delete.phtml` template file in the `/templates/album/` path
is needed to setup the form action and to display the delete form.

```php
<?php
use Album\Form\AlbumDataForm;
use Album\Model\Entity\AlbumEntity;

/** @var AlbumEntity $album */
$album = $this->albumEntity;

/** @var AlbumDataForm $form */
$form = $this->albumForm;
$form->setAttribute(
    'action', $this->url('album-delete-handle', ['id' => $album->getId()])
);

$this->headTitle('Delete album');
?>

<div class="jumbotron">
    <h1>Delete album</h1>
</div>

<table class="table table-bordered">
    <tr>
        <th>Id</th>
        <td><?php echo $album->getId(); ?></td>
    </tr>
    <tr>
        <th>Artist</th>
        <td><?php echo $album->getArtist(); ?></td>
    </tr>
    <tr>
        <th>Title</th>
        <td><?php echo $album->getTitle(); ?></td>
    </tr>
</table>

<div class="alert alert-danger">
    <strong><?php echo $this->message; ?></strong>
</div>

<div class="well">
    <?php echo $this->form()->openTag($form); ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?php echo $this->formElement($form->get('delete_album_yes')); ?>
            <?php echo $this->formElement($form->get('delete_album_no')); ?>
        </div>
    </div>
    <?php echo $this->form()->closeTag(); ?>
</div>

<p>
    <a href="<?php echo $this->url('album') ?>" class="btn btn-success">
        Back to album list
    </a>
</p>
```

Now you can browse to 
[http://localhost:8080/album/delete/1](http://localhost:8080/album/delete/1) 
to see if the delete form works correctly. Try to delete the album.

![Screenshot of album delete](images/screen-album-delete.png)

## Compare with example repository branch `part5`

You can easily compare your code with the example repository when looking 
at the branch `part5`. If you want you can even clone it and have a deeper
look.

[https://github.com/RalfEggert/zend-expressive-tutorial/tree/part5](https://github.com/RalfEggert/zend-expressive-tutorial/tree/part5)
