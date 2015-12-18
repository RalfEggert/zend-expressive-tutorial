<?php
namespace Album\Model\Repository;

use Album\Db\AlbumTableGatewayInterface;
use Interop\Container\ContainerInterface;

/**
 * Class ZendDbAlbumRepositoryFactory
 *
 * @package Album\Model\Repository
 */
class ZendDbAlbumRepositoryFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ZendDbAlbumRepository
     */
    public function __invoke(ContainerInterface $container)
    {
        $tableGateway = $container->get(AlbumTableGatewayInterface::class);

        return new ZendDbAlbumRepository($tableGateway);
    }
}
