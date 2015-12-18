<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumCreateHandleFactory
 *
 * @package Album\Action
 */
class AlbumCreateHandleFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumCreateHandleAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template        = $container->get(TemplateRendererInterface::class);
        $router          = $container->get(RouterInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $albumForm       = $container->get(AlbumDataForm::class);

        return new AlbumCreateHandleAction(
            $template, $router, $albumRepository, $albumForm
        );
    }
}
