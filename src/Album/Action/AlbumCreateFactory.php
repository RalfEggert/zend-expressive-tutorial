<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Album\Model\Repository\AlbumRepository;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumCreateFactory
 *
 * @package Album\Action
 */
class AlbumCreateFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumCreateAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template        = $container->get(TemplateRendererInterface::class);
        $router          = $container->get(RouterInterface::class);
        $albumRepository = $container->get(AlbumRepository::class);
        $albumForm       = $container->get(AlbumDataForm::class);

        return new AlbumCreateAction(
            $template, $router, $albumRepository, $albumForm
        );
    }
}
