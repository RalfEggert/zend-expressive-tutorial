<?php
namespace Album\Action;

use Album\Form\AlbumDeleteForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class AlbumDeleteHandleFactory
 *
 * @package Album\Action
 */
class AlbumDeleteHandleFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumDeleteHandleAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $router          = $container->get(RouterInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);

        return new AlbumDeleteHandleAction(
            $router, $albumRepository
        );
    }
}
