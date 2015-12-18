<?php
namespace Album\Action;

use Album\Form\AlbumDeleteForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumDeleteFactory
 *
 * @package Album\Action
 */
class AlbumDeleteFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumDeleteAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template        = $container->get(TemplateRendererInterface::class);
        $router          = $container->get(RouterInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $albumForm       = $container->get(AlbumDeleteForm::class);

        return new AlbumDeleteAction(
            $template, $router, $albumRepository, $albumForm
        );
    }
}
