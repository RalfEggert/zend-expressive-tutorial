<?php
namespace Album\Action;

use Album\Form\AlbumForm;
use Album\Model\Table\AlbumTable;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumUpdateFactory
 *
 * @package Album\Action
 */
class AlbumUpdateFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumUpdateAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template = $container->get(TemplateRendererInterface::class);
        $router = $container->get(RouterInterface::class);
        $albumTable = $container->get(AlbumTable::class);
        $albumForm = $container->get(AlbumForm::class);

        return new AlbumUpdateAction(
            $template, $router, $albumTable, $albumForm
        );
    }
}
