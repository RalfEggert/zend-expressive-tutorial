<?php
namespace Album\Model\Repository;

use Album\Model\Storage\AlbumStorageInterface;
use Interop\Container\ContainerInterface;

/**
 * Class ZendDbAlbumRepositoryFactory
 *
 * @package Album\Model\Repository
 */
class AlbumRepositoryFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumRepository
     */
    public function __invoke(ContainerInterface $container)
    {
        $albumStorage = $container->get(AlbumStorageInterface::class);

        return new AlbumRepository($albumStorage);
    }
}
