<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class ZendDbAlbumRepository
 *
 * @package Album\Model\Repository
 */
class ZendDbAlbumRepository implements AlbumRepositoryInterface
{
    /** @var TableGateway */
    private $gateway;

    /**
     * ZendDbAlbumRepository constructor.
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array
     */
    public function fetchAllAlbums()
    {
        $select = $this->gateway->getSql()->select();

        $collection = array();

        /** @var AlbumEntity $entity */
        foreach ($this->gateway->selectWith($select) as $entity) {
            $collection[$entity->getId()] = $entity;
        }

        return $collection;
    }

    /**
     * @param $id
     *
     * @return AlbumEntity
     */
    public function fetchSingleAlbum($id)
    {
        $select = $this->gateway->getSql()->select();
        $select->where->equalTo('id', $id);

        return $this->gateway->selectWith($select)->current();
    }

    /**
     * @param AlbumEntity $album
     */
    public function saveAlbum(AlbumEntity $album)
    {
        if ($album->getId()) {
            $this->updateAlbum($album);
        }
        $this->insertAlbum($album);
    }

    /**
     * @param AlbumEntity $album
     */
    private function insertAlbum(AlbumEntity $album)
    {
        $insertData = $album->getArrayCopy();

        $insert = $this->gateway->getSql()->insert();
        $insert->values($insertData);

        $this->gateway->insertWith($insert);
    }

    /**
     * @param AlbumEntity $album
     */
    private function updateAlbum(AlbumEntity $album)
    {
        $updateData = $album->getArrayCopy();

        $update = $this->gateway->getSql()->update();
        $update->set($updateData);
        $update->where->equalTo('id', $album->getId());

        $this->gateway->updateWith($update);
    }

    /**
     * @param AlbumEntity $album
     */
    public function deleteAlbum(AlbumEntity $album)
    {
        $delete = $this->gateway->getSql()->delete();
        $delete->where->equalTo('id', $album->getId());

        $this->gateway->deleteWith($delete);
    }
}
