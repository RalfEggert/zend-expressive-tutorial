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
