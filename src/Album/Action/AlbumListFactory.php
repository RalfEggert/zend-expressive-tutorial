<?php
namespace Album\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumListFactory
 *
 * @package Album\Action
 */
class AlbumListFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumListAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;

        return new AlbumListAction($template);
    }
}
