<?php
namespace Album\Model\Table;

use Album\Model\Entity\AlbumEntity;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class AlbumTable
 *
 * @package Album\Model\Table
 */
class AlbumTable
{
    /** @var TableGateway */
    private $gateway;

    /**
     * AlbumTable constructor.
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
     * @return mixed
     */
    public function fetchSingleAlbum($id)
    {
        $select = $this->gateway->getSql()->select();
        $select->where->equalTo('id', $id);

        return $this->gateway->selectWith($select)->current();
    }

    /**
     * @param AlbumEntity $album
     *
     * @return bool
     */
    public function saveAlbum(AlbumEntity $album)
    {
        if ($album->getId()) {
            return $this->updateAlbum($album);
        } else {
            return $this->insertAlbum($album);
        }
    }

    /**
     * @param AlbumEntity $album
     *
     * @return bool
     */
    private function insertAlbum(AlbumEntity $album)
    {
        $insertData = $album->getArrayCopy();

        $insert = $this->gateway->getSql()->insert();
        $insert->values($insertData);

        return $this->gateway->insertWith($insert) > 0;
    }

    /**
     * @param AlbumEntity $album
     *
     * @return bool
     */
    private function updateAlbum(AlbumEntity $album)
    {
        $updateData = $album->getArrayCopy();

        $update = $this->gateway->getSql()->update();
        $update->set($updateData);
        $update->where->equalTo('id', $album->getId());

        return $this->gateway->updateWith($update) > 0;
    }

    /**
     * @param AlbumEntity $album
     *
     * @return bool
     */
    public function deleteAlbum(AlbumEntity $album)
    {
        $delete = $this->gateway->getSql()->delete();
        $delete->where->equalTo('id', $album->getId());

        return $this->gateway->deleteWith($delete) > 0;
    }
}
