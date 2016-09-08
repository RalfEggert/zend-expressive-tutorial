# Part 3: Model and database

In this part of the tutorial, we will setup a database and implement the 
model layer for our application. At the end of this chapter, the album list 
page will show the data from the database.

## Setup the database and the database connection

First, we need to setup the database. We will use MySQL for this tutorial.

Create a new database called `album-tutorial`, and then run the following SQL 
statements to create the `album` table, along with some test data.

```sql
CREATE TABLE album (
    id int(11) NOT NULL auto_increment,
    artist varchar(100) NOT NULL,
    title varchar(100) NOT NULL,
    PRIMARY KEY (id)
);
INSERT INTO album (artist, title)
    VALUES  ('The  Military  Wives',  'In  My  Dreams');
INSERT INTO album (artist, title)
    VALUES  ('Adele',  '21');
INSERT INTO album (artist, title)
    VALUES  ('Bruce  Springsteen',  'Wrecking Ball (Deluxe)');
INSERT INTO album (artist, title)
    VALUES  ('Lana  Del  Rey',  'Born  To  Die');
INSERT INTO album (artist, title)
    VALUES  ('Gotye',  'Making  Mirrors');
```

## Install `Zend\Db` component

Next, we'll add the [zend-db](https://github.com/zendframework/zend-db)
component to the application, using composer:
 
```
$ composer require zendframework/zend-db
```

When you run this installation via Composer the `Zend\ComponentInstaller`
steps in here now and asks you if you want to inject the 
`Zend\Db\ConfigProvider` into your config file. You should select it with 
the choice of `1` and also remember your decision with `y`. It should look 
like this:

![Install Zend\Db component](images/install-zend-db.png)

Please note that your `/config/config.php` should be updated as well by 
adding the `Zend\Db\ConfigProvider`:

```php

use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\PhpFileProvider;

$configManager = new ConfigManager([
    \Zend\Db\ConfigProvider::class,
    Zend\Filter\ConfigProvider::class,
    Zend\I18n\ConfigProvider::class,
    Zend\Router\ConfigProvider::class,
    Zend\Validator\ConfigProvider::class,
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
]);

return new ArrayObject($configManager->getMergedConfig());
```

To configure database access, create the file
`/config/autoload/database.global.php` with the following contents:

```php
<?php
return [
    'db' => [
        'driver'  => 'pdo',
        'dsn'     => 'mysql:dbname=album-tutorial;host=localhost;charset=utf8',
        'user'    => 'album',
        'pass'    => 'album',
    ],
];
```

The `db` configuration section defines the database connection. We will
use the PDO driver with a MySQL database, and the database table and user
we created above. 

> ### Store credentials in `*.local.php` files
>
> The above example stores the database credentials in a "global" configuration
> file. **DO NOT DO THIS.**
>
> In your global configuration files, put in empty credentials. Then, in a file
> named `database.local.php`, add the same structure, and provide the
> credentials:
> 
> ```php
> <?php
> return [
>     'db' => [
>         'driver'  => 'pdo',
>         'dsn'     => 'mysql:dbname=album-tutorial;host=localhost;charset=utf8',
>         'user'    => 'album',
>         'pass'    => 'album',
>     ],
> ];
> ```
>
> "local" configuration files are merged *after* global configuration files,
> which means they will have precedence. Additionally, they are omitted from
> version control by default (via a `.gitignore` rule in the project root),
> ensuring they will not be checked in to your repository. This also allows you
> to have separate credentials and configuration per location where you deploy,
> whether that's development, staging, QA, or production.

At this point, we have setup the database, and provided a database adapter to
our application.

## Create an album entity

To represent the data of the albums, we will create an entity class. Create
the directory `src/Album/Model/Entity/`; under it, create an `AlbumEntity.php`
file with the following contents:

```php
<?php
namespace Album\Model\Entity;

use DomainException;
use Zend\Stdlib\ArraySerializableInterface;

class AlbumEntity implements ArraySerializableInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $artist;

    /**
     * @var string
     */
    private $title;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        foreach ($array as $key => $value) {
            $setter = 'set' . ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            }
        }
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $data = [];

        foreach (get_object_vars($this) as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }


    /**
     * @param int $id
     */
    private function setId($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            throw new DomainException(
                'Album id must be a positive integer!'
            );
        }

        $this->id = $id;
    }

    /**
     * @param string $artist
     */
    private function setArtist($artist)
    {
        $artist = (string) $artist;

        if (empty($artist) || strlen($artist) > 100) {
            throw new DomainException(
                'Album artist must be between 1 and 100 chars!'
            );
        }

        $this->artist = $artist;
    }

    /**
     * @param string $title
     */
    private function setTitle($title)
    {
        $title = (string) $title;

        if (empty($title) || strlen($title) > 100) {
            throw new DomainException(
                'Album title must be between 1 and 100 chars!'
            );
        }

        $this->title = $title;
    }
}
```

There are a few things to note:

- `AlbumEntity` implements `Zend\Stdlib\ArraySerializableInterface`,
  which provides the methods `exchangeArray()` and `getArrayCopy()`, allowing
  array de/serialization. This allows us to bind the entity to a form, as well
  as to handle the exchange of the data coming from the database.

- The three private properties only allow access via the implemented
  methods. To get the values for `id`, `artist`, and `title`, you need to use the
  four getter methods. To change the data, you need to use the 
  `exchangeArray()` method.

- Within the `exchangeArray()` method, the injected array is looped. For 
  each key, we build a setter method name and check if the method exists. 
  The value is only set for this key if that check was successful.
  
- Within the `getArrayCopy()` method, we look through all the properties of
  the current object and build a `$data` array that gets returned at the 
  end.

- Each property has a private setter method which casts and validates the value,
  raising an exception for invalid data.

## Create a storage interface

To access the data from the database table, we will use the
`Zend\Db\TableGateway` subcomponent. But before we do that, we have a little
preparation to do first.

If we use `Zend\Db\TableGateway` directly, we're binding our model to a specific
data access layer, and more generally to relational databases. This means that
if any changes happen to the `Zend\Db\TableGateway` implementation, we will need
to change our code; if we decide to move to a NoSQL database later, we will need
to change our code.

To prevent the need for such changes, we will create a storage interface
modeling our low layer data access needs.
 
Create the path `src/Album/Model/Storage/` and then create the file
`AlbumStorageInterface.php` beneath it. This interface defines methods for
reading a list of albums, reading a single album, inserting an album, updating
an album, and deleting albums. 

```php
<?php
namespace Album\Model\Storage;

use Album\Model\Entity\AlbumEntity;

interface AlbumStorageInterface
{
    /**
     * Fetch a list of albums.
     *
     * @return AlbumEntity[]
     */
    public function fetchAlbumList();

    /**
     * Fetch an album by identifer.
     *
     * @param int $id
     * @return AlbumEntity|null
     */
    public function fetchAlbumById($id);

    /**
     * Insert an album into storage.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function insertAlbum(AlbumEntity $album);

    /**
     * Update an album.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function updateAlbum(AlbumEntity $album);

    /**
     * Delete an album.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function deleteAlbum(AlbumEntity $album);
}
```

Any time you want to use a different data storage &mdash; e.g., a NoSQL
database, a web service, etc. &mdash; you can create a new class implementing
this interface. You would then only need to swap which storage implementation
you use.

## Install `Zend\Hydrator` component

Before you continue, we'll add the 
[zend-hydrator](https://github.com/zendframework/zend-hydrator)
component to the application, using composer:
 
```
$ composer require zendframework/zend-hydrator
```

When you run this installation via Composer the `Zend\ComponentInstaller`
steps in here again and asks you if you want to inject the 
`Zend\Hydrator\ConfigProvider` into your config file. You should select it 
with the choice of `1` and also remember your decision with `y`. It should 
look like this:

![Install Zend\Hydrator component](images/install-zend-hydrator.png)

Please note that your `/config/config.php` should be updated as well by 
adding the `Zend\Hydrator\ConfigProvider`:

```php

use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\PhpFileProvider;

$configManager = new ConfigManager([
    \Zend\Hydrator\ConfigProvider::class,
    \Zend\Db\ConfigProvider::class,
    Zend\Filter\ConfigProvider::class,
    Zend\I18n\ConfigProvider::class,
    Zend\Router\ConfigProvider::class,
    Zend\Validator\ConfigProvider::class,
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
]);

return new ArrayObject($configManager->getMergedConfig());
```

## Create a table gateway

A [table data gateway](http://martinfowler.com/eaaCatalog/tableDataGateway.html)
represents the data of a single table in your database, and allows reading and
writing access to this data.
[`Zend\Db\TableGateway`](http://framework.zend.com/manual/current/en/modules/zend.db.table-gateway.html)
implements this pattern.
 
Because storage is not part of the domain model and implementation can be
swapped (because we defined a storage interface!), we'll place our table gateway
in a separate path. Create the directory `src/Album/Db/`, and place the
`AlbumTableGateway.php` file in it. Our table gateway will implement the
`AlbumStorageInterface` interface defined in the previous section, and extend
`Zend\Db\TableGateway\TableGateway`. 

```php
<?php
namespace Album\Db;

use Album\Model\Entity\AlbumEntity;
use Album\Model\Storage\AlbumStorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\TableGateway\TableGateway;

class AlbumTableGateway extends TableGateway implements AlbumStorageInterface
{
    /**
     * @param AdapterInterface   $adapter
     * @param ResultSetInterface $resultSet
     */
    public function __construct(AdapterInterface $adapter, ResultSetInterface $resultSet)
    {
        parent::__construct('album', $adapter, null, $resultSet);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAlbumList()
    {
        $select = $this->getSql()->select();

        $collection = [];

        /** @var AlbumEntity $entity */
        foreach ($this->selectWith($select) as $entity) {
            $collection[$entity->getId()] = $entity;
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAlbumById($id)
    {
        $select = $this->getSql()->select();
        $select->where->equalTo('id', $id);

        return $this->selectWith($select)->current();
    }

    /**
     * {@inheritDoc}
     */
    public function insertAlbum(AlbumEntity $album)
    {
        $insertData = $album->getArrayCopy();

        $insert = $this->getSql()->insert();
        $insert->values($insertData);

        return $this->insertWith($insert) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function updateAlbum(AlbumEntity $album)
    {
        $updateData = $album->getArrayCopy();

        $update = $this->getSql()->update();
        $update->set($updateData);
        $update->where->equalTo('id', $album->getId());

        return $this->updateWith($update) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAlbum(AlbumEntity $album)
    {
        $delete = $this->getSql()->delete();
        $delete->where->equalTo('id', $album->getId());

        return $this->deleteWith($delete) > 0;
    }
}
```

Notes:

- The constructor defines parameters for the database adapter and a
  pre-configured result set prototype. This prototype is used for all the
  selects from the database to represent the data. Within the constructor, the
  name of the database table is set and the adapter and the prototype are passed
  to the parent constructor.
  
- Within the `fetchAlbumList()` method, a `Select` object is created based on
  `Zend\Db\Sql`. The data of all albums is fetched from the database and 
  placed in an array collection with the id of the album as the key.

- Within the `fetchAlbumById()` method, a `Select` object is created as well. 
  The selection is limited to the album with the id that was passed to this
  method. This method just returns the fetched album. 

- Within the `insertAlbum()` method, an `Insert` object based on `Zend\Db\Sql`
  is created.  The data of the album is extracted and passed to the `Insert`
  instance, and the insertion is executed. If a new row was created, the method
  returns `true`, otherwise it returns `false`.

- Within the `updateAlbum()` method, an `Update` object based on `Zend\Db\Sql`
  is created. The data of the album is extracted and passed to the `Update`
  instance. Updates are limited to the album passed to the method. When the
  update is executed, method returns `true` if an update occurred, and otherwise
  returns `false`.

- Within the `deleteAlbum()` method, a `Delete` object based on `Zend\Db\Sql` is
  created. Deletion is limited to the album passed to the method. When the
  deletion is executed, the method will return `true` if any rows were deleted,
  and otherwise returns `false`.

Please note that all of these methods either return an `AlbumEntity` or an array
collection of `AlbumEntity` instances, and, if any parameters are accepted, they
typically only accept an `AlbumEntity` instance (with the exception of
`fetchAlbumById()`). There is no need to pass arrays to the command methods or
to handle arrays returned passed from the query methods.

To get our `AlbumTableGateway` configured properly, we will also need a factory
in the same path. The `AlbumTableGatewayFactory` requests the instance of the
database adapter via the service container (zend-servicemanager in our case),
and then creates a [hydrating](http://zendframework.github.io/zend-hydrator/quick-start/#usage)
result set prototype using `Zend\Hydrator\ArraySerializable` and an
`AlbumEntity` instance. Both the adapter and the prototype are injected into the
constructor of the `AlbumTableGateway`.

```php
<?php
namespace Album\Db;

use Album\Model\Entity\AlbumEntity;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator\ArraySerializable;

class AlbumTableGatewayFactory
{
    /**
     * @param ContainerInterface $container
     * @return AlbumTableGateway
     */
    public function __invoke(ContainerInterface $container)
    {
        $resultSetPrototype = new HydratingResultSet(
            new ArraySerializable(),
            new AlbumEntity()
        );

        return new AlbumTableGateway(
            $container->get(AdapterInterface::class),
            $resultSetPrototype
        );
    }
}
```

Please note that [zend-hydrator](https://github.com/zendframework/zend-hydrator)
is used to provide de/serialization between `AlbumEntity` instances and the
array data read from the database. The concrete `ArraySerializable` hydrator
uses the methods `exchangeArray()` and `getArrayCopy()` defined in
`Zend\Stdlib\ArraySerializableInterface` and implemented in the `AlbumEntity` .

## Create an album repository

When creating our domain model, we need something to mediate between the domain
objects &mdash; our entities &mdash; and the storage layer. This is generally
achieved by a [repository](http://martinfowler.com/eaaCatalog/repository.html).

A repository accepts and returns domain objects, and decides whether or not
storage operations are necessary. Often, they will cache results in order to
reduce overhead on subsequent requests to the same methods, though this is not a
strict requirement.

We'll now create a repository for the album. The repository will be used within
our middleware actions, and consume an `AlbumStorageInterface` implementation as
developed in the previous section. This will allow us to switch from a database
to a web service, or to use a different implementation than the table gateway,
without needing to change any application code.

As with storage, we'll start by creating an interface.
Create the directory `src/Album/Model/Repository/` and place the file
`AlbumRepositoryInterface.php` it. This interface is similar to the
`AlbumStorageInterface`, but combines insert and update operations into a single
"save" method.

```php
<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;

interface AlbumRepositoryInterface
{
    /**
     * Fetch all albums.
     *
     * @return AlbumEntity[]
     */
    public function fetchAllAlbums();

    /**
     * Fetch a single album by identifier.
     *
     * @param int $id
     * @return AlbumEntity|null
     */
    public function fetchSingleAlbum($id);

    /**
     * Save an album.
     *
     * Should insert a new album if no identifier is present in the entity, and
     * update an existing album otherwise.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function saveAlbum(AlbumEntity $album);

    /**
     * Delete an album.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function deleteAlbum(AlbumEntity $album);
}
```

This may seem like duplication of effort, as many methods are duplicated between
this and the `AlbumStorageInterface`. However, the separation provides a number
of benefits:

- Separation of concerns. We can add entity validation, caching, etc. as part of
  the repository, keeping them separate from storage.
- If our middleware consumes only the repository, we can mock the repository
  during testing to validate behavior.

Let's create a reference implementation of the interface as well.  In the same
path, create the file `AlbumRepository.php`. The class it defines will implement
the `AlbumRepositoryInterface`, and compose an `AlbumStorageInterface`
instance, provided to the constructor.
 
```php
<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;
use Album\Model\Storage\AlbumStorageInterface;

class AlbumRepository implements AlbumRepositoryInterface
{
    /**
     * @var AlbumStorageInterface
     */
    private $albumStorage;

    /**
     * AlbumRepository constructor.
     *
     * @param AlbumStorageInterface $albumStorage
     */
    public function __construct(AlbumStorageInterface $albumStorage)
    {
        $this->albumStorage = $albumStorage;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAllAlbums()
    {
        return $this->albumStorage->fetchAlbumList();
    }

    /**
     * {@inheritDoc}
     * Fetch a single album
     */
    public function fetchSingleAlbum($id)
    {
        return $this->albumStorage->fetchAlbumById($id);
    }

    /**
     * {@inheritDoc}
     */
    public function saveAlbum(AlbumEntity $album)
    {
        if (! $album->getId()) {
            return $this->albumStorage->insertAlbum($album);
        }

        return $this->albumStorage->updateAlbum($album);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAlbum(AlbumEntity $album)
    {
        return $this->albumStorage->deleteAlbum($album);
    }
}
```

Most methods of this class proxy directly to the appropriate methods of the
storage; only the `saveAlbum()` method does any extra work (to determine whether
an insert or update operation is warranted).

The `AlbumRepository` needs a factory.  Create the file
`AlbumRepositoryFactory.php` within the same directory; in this factory, we'll
request the album storage from the service container, and pass it to the
constructor of the repository.

```php
<?php
namespace Album\Model\Repository;

use Album\Model\Storage\AlbumStorageInterface;
use Interop\Container\ContainerInterface;

class AlbumRepositoryFactory
{
    /**
     * @param ContainerInterface $container
     * @return AlbumRepository
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AlbumRepository(
            $container->get(AlbumStorageInterface::class)
        );
    }
}
```

## Update the album configuration

Now that we have storage and our repository sorted, we need to add dependency
configuration to the application.  Edit the file
`config/autoload/album.global.php` and add the following configuration to the
`dependencies` section.

```php
<?php
return [
    'dependencies' => [
        'factories' => [
            /* ... */

            Album\Model\Repository\AlbumRepositoryInterface::class =>
                Album\Model\Repository\AlbumRepositoryFactory::class,

            Album\Model\Storage\AlbumStorageInterface::class =>
                Album\Db\AlbumTableGatewayFactory::class,
        ],
    ],
    
    /* ... */
];
```

For both the repository and the storage we use the interface names as the
identifier and the factories for the instantiation.

## Update the album list middleware

Now that we have our domain models, repository, and storage created, we can
update our middleware to use them.

Edit the file `src/Album/Action/AlbumListAction.php` and implement the following
changes:

```php
<?php
namespace Album\Action;

use Album\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListAction
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
     * @param TemplateRendererInterface $template
     * @param AlbumRepositoryInterface  $albumRepository
     */
    public function __construct(
        TemplateRendererInterface $template,
        AlbumRepositoryInterface $albumRepository
    ) {
        $this->template        = $template;
        $this->albumRepository = $albumRepository;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        $data = [
            'albumList' => $this->albumRepository->fetchAllAlbums(),
        ];

        return new HtmlResponse(
            $this->template->render('album::list', $data)
        );
    }
}
```

The changes in the above include:

- Adding another private property, `$albumRepository`, to hold an
  `AlbumRepositoryInterface` instance.
  
- Changing the constructor to add a second parameter, `$albumRepository`,
  accepting an `AlbumRepositoryInterface` instance and assigning it to the
  `$albumRepository` property.

- Filling the `$data` array within the `invoke()` method with a list of
  albums fetched from the repository.

Because we've added a new constructor argument, we will need to
update the `AlbumListFactory`:

```php
<?php
namespace Album\Action;

use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumListAction
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AlbumListAction(
            $container->get(TemplateRendererInterface::class),
            $container->get(AlbumRepositoryInterface::class)
        );
    }
}
```

## Update the album list template

Finally, now that our middleware is passing albums to the template, we need to
update the template to display them.

The list is presented within a table styled by
[Bootstrap](http://getbootstrap.com). We loop through all albums and echo the
id, artist, and title by accessing the getter methods of the `AlbumEntity`. 

```php
<?php
use Album\Model\Entity\AlbumEntity;

$this->headTitle('Albums');
?>

<div class="jumbotron">
    <h1>Album list</h1>
</div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Id</th>
        <th>Artist</th>
        <th>Title</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var AlbumEntity $albumEntity */ ?>
    <?php foreach ($this->albumList as $albumEntity) : ?>
        <tr>
            <td><?php echo $albumEntity->getId(); ?></td>
            <td><?php echo $albumEntity->getArtist(); ?></td>
            <td><?php echo $albumEntity->getTitle(); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
```

Now you can browse to 
[http://localhost:8080/album](http://localhost:8080/album) to see if the 
album list is shown as expected. 

![Screenshot of album list with data](images/screen-album-list-with-data.png)

## Update tests for album list middleware

Let's test that everything works as expected.

Edit `test/AlbumTest/Action/AlbumListActionTest` to add the injection of
the `AlbumRepositoryInterface` instance, and to mock its call to
`fetchAllAlbums()`.

```php
<?php
namespace AppTest\Action;

/* ... */

class AlbumListActionTest extends PHPUnit_Framework_TestCase
{
    /* ... */

    /**
     * Test if action renders the album list
     */
    public function testActionRendersAlbumList()
    {
        $albumRepository = $this->prophesize(
            AlbumRepositoryInterface::class
        );
        $albumRepository->fetchAllAlbums()->shouldBeCalled()->willReturn([
            'album1',
            'album2'
        ]);

        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $renderer->render(
            'album::list',
            ['albumList' => ['album1', 'album2']]
        )->shouldBeCalled()->willReturn('BODY');

        $action = new AlbumListAction(
            $renderer->reveal(),
            $albumRepository->reveal()
        );

        $response = $action(
            $this->request->reveal(),
            $this->response->reveal(),
            $this->next
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);

        $this->assertEquals('BODY', $response->getBody());
    }
}
```

The factory test case also needs to test the injection of the `AlbumRepository`:

```php
<?php
namespace AppTest\Action;

/* ... */

class AlbumListFactoryTest extends \PHPUnit_Framework_TestCase
{
    /* ... */

    /**
     * Test if factory returns the correct action
     */
    public function testFactory()
    {
        $factory = new AlbumListFactory();

        $this->container
            ->get(TemplateRendererInterface::class)
            ->willReturn(
                $this->prophesize(TemplateRendererInterface::class)->reveal()
            );

        $this->container
            ->get(AlbumRepositoryInterface::class)
            ->willReturn(
                $this->prophesize(AlbumRepositoryInterface::class)->reveal()
            );

        $action = $factory($this->container->reveal());

        $this->assertTrue($action instanceof AlbumListAction);
    }
}
```

Now run the tests from your project root:

```bash
$ phpunit
```

## Compare with example repository branch `part3`

You can easily compare your code with the example repository when looking 
at the branch `part3`. If you want you can even clone it and have a deeper
look.

[https://github.com/RalfEggert/zend-expressive-tutorial/tree/part3](https://github.com/RalfEggert/zend-expressive-tutorial/tree/part3)
