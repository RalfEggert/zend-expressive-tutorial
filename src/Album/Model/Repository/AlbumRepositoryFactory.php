<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;

/**
 * Class AlbumRepositoryFactory
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
        $adapter = $container->get(AdapterInterface::class);

        $resultSetPrototype = new HydratingResultSet(
            new ArraySerializable(), new AlbumEntity()
        );

        $gateway = new TableGateway(
            'album', $adapter, null, $resultSetPrototype
        );

        return new AlbumRepository($gateway);
    }
}
