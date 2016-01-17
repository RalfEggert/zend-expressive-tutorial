# Part 4: Forms and input filter

In this part of the tutorial we will create an input filter and a form
to allow the user of the album application to add new albums. We will also
need to create some new middleware actions for the form display and 
handling. 

## Add more Zend Framework components

To make sure that the needed Zend Framework components are installed, you 
need to update the `composer.json` to require the 
[`Zend\Form`](https://github.com/zendframework/zend-form) and the 
[`Zend\InputFilter`](https://github.com/zendframework/zend-inputfilter) 
components of the Zend Framework.
 
```
{
    // ... 

    "require": {
        // ...
         
        "zendframework/zend-db": "^2.5",
        "zendframework/zend-form": "^2.5",
        "zendframework/zend-inputfilter": "^2.5"    },

    // ... 
}
```

Now you need to do a composer update to install the `Zend\Db`:

```
$ composer update
```

## Create the album input filter

First, we need to create the album input filter. The `Zend\InputFilter` 
component can be used to filter and validate generic sets of input data.
This input filter can work together with the form we will create in the 
next step.
 
Please create a new path `/src/Album/Model/InputFilter/` and place the new
`AlbumInputFilter.php` file in there. The `AlbumInputFilter` defines two input 
elements, one for the artist and one for the title. Both input elements are
mandatory and get a set of filters and validators defined. The id does not
need an input element.

```php
<?php
namespace Album\Model\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Class AlbumInputFilter
 *
 * @package Album\Model\InputFilter
 */
class AlbumInputFilter extends InputFilter
{
    /**
     * Init input filter
     */
    public function init()
    {
        $this->add([
            'name'     => 'artist',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 100,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'     => 'title',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 100,
                    ],
                ],
            ],
        ]);
    }
}
```

Please note that the adding of the input elements is done in the 
`init()` method. This method is automatically called when the input filter
is instantiated through the input filter manager. The input filter manager
is a specialized service-manager just for input filter classes. We won't 
use the input filter manager in this tutorial. By implementing the `init()` 
method it will be much easier to setup the input filter manager in your
project at a later time.
 
Of course the `AlbumInputFilter` will need a factory as will. So please 
create another `AlbumInputFilterFactory.php` file in the same path. The 
factory is just instantiating the `AlbumInputFilter` and running the
`init()` method. If you need to add further configuration like some valid 
options for another input element you can inject that after instantiation
and before the call of the `init()` method.

```php
<?php
namespace Album\Model\InputFilter;

use Interop\Container\ContainerInterface;

/**
 * Class AlbumInputFilterFactory
 *
 * @package Album\Model\InputFilter
 */
class AlbumInputFilterFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumInputFilter
     */
    public function __invoke(ContainerInterface $container)
    {
        $inputFilter = new AlbumInputFilter();
        $inputFilter->init();

        return $inputFilter;
    }
}
```

## Create the album form

Next we will need to create a form for the album data. The `Zend\Form` 
component can be used to structure and display forms. It can work
together with the album input filter we just created.

Please create another new path `/src/Album/Form/` and place a new file 
`AlbumDataForm.php` in there. The `AlbumDataForm` extends the class 
`Zend\Form\Form` and defines two form elements (one for the artist and one 
for the title) and a submit button. The form elements are setup as text 
inputs and named by a label. All elements get some CSS classes defined to 
be used by Bootstrap again.

```php
<?php
namespace Album\Form;

use Zend\Form\Form;

/**
 * Class AlbumDataForm
 *
 * @package Album\Form
 */
class AlbumDataForm extends Form
{
    /**
     * Init form
     */
    public function init()
    {
        $this->setName('album_form');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'name'       => 'artist',
                'type'       => 'Text',
                'attributes' => [
                    'class' => 'form-control',
                ],
                'options'    => [
                    'label'            => 'Artist',
                    'label_attributes' => [
                        'class' => 'col-sm-2 control-label',
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'title',
                'type'       => 'Text',
                'attributes' => [
                    'class' => 'form-control',
                ],
                'options'    => [
                    'label' => 'Title',
                    'label_attributes' => [
                        'class' => 'col-sm-2 control-label',
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'save_album',
                'type'       => 'Submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => 'Save Album',
                    'id'    => 'save_album',
                ],
            ]
        );
    }
}
```

Please note that the adding of the form elements is also done in the 
`init()` method. This method is automatically called when the form is 
instantiated through the form element manager. The form element manager
is a specialized service-manager just for form elements and forms. We won't 
use the form element manager in this tutorial. By implementing the `init()` 
method it will be much easier to setup the form element manager in your
project at a later time.

The `AlbumDataForm` also needs a factory which is created in the 
`AlbumDataFormFactory.php` file in the same path. This factory instantiates
the `AlbumDataForm` form and injects an instance of the 
`Zend\Hydrator\ArraySerializable` and the album input filter we just 
created.

```php
<?php
namespace Album\Form;

use Album\Model\InputFilter\AlbumInputFilter;
use Interop\Container\ContainerInterface;
use Zend\Form\Form;
use Zend\Hydrator\ArraySerializable;

/**
 * Class AlbumDataFormFactory
 *
 * @package Album\Form
 */
class AlbumDataFormFactory extends Form
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumDataForm
     */
    public function __invoke(ContainerInterface $container)
    {
        $hydrator    = new ArraySerializable();
        $inputFilter = $container->get(AlbumInputFilter::class);

        $form = new AlbumDataForm();
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->init();

        return $form;
    }
}
```

Please note that the injection of the `Zend\Hydrator\ArraySerializable` is
done for a special reason. We can now bind an `AlbumEntity` instance to 
the form and the hydrator helps to extract the data from the entity and 
fill the form elements with these values. 

## Update album configuration

to be written...

## Create album form create action

to be written...

## Create album form handling action

to be written...

## Add form view helpers to helper plugin manager

to be written...

## Create album creation template

to be written...

## Add link to the album creation page

to be written...

Now you can browse to 
[http://localhost:8080/album/create](http://localhost:8080/album/cerate) 
to see if the album form is shown as expected. 

![Screenshot of album create form](images/screen-album-create-form.png)

## Compare with example repository branch `part4`

You can easily compare your code with the example repository when looking 
at the branch `part4`. If you want you can even clone it and have a deeper
look.

[https://github.com/RalfEggert/zend-expressive-tutorial/tree/part4](https://github.com/RalfEggert/zend-expressive-tutorial/tree/part4)
