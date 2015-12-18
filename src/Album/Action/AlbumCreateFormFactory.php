<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumCreateFormFactory
 *
 * @package Album\Action
 */
class AlbumCreateFormFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumCreateFormAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template  = $container->get(TemplateRendererInterface::class);
        $albumForm = $container->get(AlbumDataForm::class);

        return new AlbumCreateFormAction(
            $template, $albumForm
        );
    }
}
