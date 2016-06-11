<?php
namespace Album\Action;

use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListFactory
{
    /**
     * @param ContainerInterface $container
     * @return AlbumListAction
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AlbumListAction(
            $container->get(TemplateRendererInterface::class),
            $container->get(AlbumRepositoryInterface::class)
        );
    }
}
