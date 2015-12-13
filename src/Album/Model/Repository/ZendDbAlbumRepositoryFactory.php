<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;

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
     * @return AlbumRepositoryInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        $adapter = $container->get(AdapterInterface::class);

        $resultSetPrototype = new HydratingResultSet(
            new ArraySerializable(), new AlbumEntity()
        );

        $gateway = new TableGateway(
            'album', $adapter, null, $resultSetPrototype
        );

        return new ZendDbAlbumRepository($gateway);
    }
}
