<?php
namespace Album\Form;

use Interop\Container\ContainerInterface;
use Zend\Form\Form;
use Zend\Hydrator\ArraySerializable;

/**
 * Class AlbumFormFactory
 *
 * @package Album\Form
 */
class AlbumFormFactory extends Form
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumForm
     */
    public function __invoke(ContainerInterface $container)
    {
        $hydrator = new ArraySerializable();

        $form = new AlbumForm();
        $form->setHydrator($hydrator);
        $form->init();

        return $form;
    }
}
