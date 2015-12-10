<?php
namespace Album\Model\Table;

use Album\Model\Entity\AlbumEntity;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator\ArraySerializable;

/**
 * Class AlbumTableFactory
 *
 * @package Album\Model\Table
 */
class AlbumTableFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumTable
     */
    public function __invoke(ContainerInterface $container)
    {
        $adapter = $container->get(AdapterInterface::class);

        $resultSetPrototype = new HydratingResultSet(
            new ArraySerializable(), new AlbumEntity()
        );

        return new AlbumTable($adapter, $resultSetPrototype);
    }
}
