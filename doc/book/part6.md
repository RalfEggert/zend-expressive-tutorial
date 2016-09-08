# Part 6: Refactor application structure

In this last and very short part we will refactor the application 
structure a little bit. We will delete the old home page and ping actions 
and remove all unused files. After this the album page should be shown as 
the new home page.

## Delete unused files

Please delete the following file paths and all of the files in there:

* `/src/App/Action/`
* `/templates/app/`
* `/test/AppTest/Action/`

## Remove unused configuration

Please remove the following unused configuration:

* The `App\\` line in the `autoload` section from the `composer.json` 
  file.

* The `AppTest\\` line in the `autoload-dev` section from the 
  `composer.json` file.

* The `App\Action\PingAction` and `App\Action\HomePageAction` from the 
  `dependencies` section of the `/config/autoload/routes.global.php` file.
  
* The two routes from the `routes` section of the 
  `/config/autoload/routes.global.php` file.
  
* The `app` path from the `paths` of the `templates` section of the 
  `/config/autoload/templates.global.php` file.

## Update templates

In the `/templates/error/404.phtml` file please change the link to the 
home page to:

```php
<?php echo $this->url('album') ?>">Album</a>
```

In the `/templates/layout/default.phtml` file please change the link to the 
logo to:

```php
<?php echo $this->url('home') ?>
```

In the navbar you can delete the three menu options with the links to the 
`Docs`, the `Contribute` and the `Ping Test`. 

## Finish

Now you are done with your first PSR-7 middleware. You have created a 
lightweight application to handle albums with `Zend\Expressive`. Please
have a closer look at the generated code now and try to understand 
everything you have done in the six parts of this tutorial.

## Compare with example repository branch `part6`

You can easily compare your code with the example repository when looking 
at the branch `part6`. If you want you can even clone it and have a deeper
look.

[https://github.com/RalfEggert/zend-expressive-tutorial/tree/part6](https://github.com/RalfEggert/zend-expressive-tutorial/tree/part6)
