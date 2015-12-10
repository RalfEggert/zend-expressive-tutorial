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
