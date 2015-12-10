<?php
namespace Album\Model\Table;

use Album\Model\Entity\AlbumEntity;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Exception\ErrorException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class AlbumTable
 *
 * @package Album\Model\Table
 * @method ResultSet selectWith($select)
 */
class AlbumTable extends TableGateway
{
    /**
     * AlbumTable constructor.
     *
     * @param AdapterInterface $adapter
     * @param ResultSetInterface $resultSetPrototype
     */
    public function __construct(
        AdapterInterface $adapter, ResultSetInterface $resultSetPrototype
    ) {
        parent::__construct('album', $adapter, null, $resultSetPrototype);
    }

    /**
     * @return array
     */
    public function fetchAllAlbums()
    {
        $select = $this->getSql()->select();

        $collection = array();

        /** @var AlbumEntity $entity */
        foreach ($this->selectWith($select) as $entity) {
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
        $select = $this->getSql()->select();
        $select->where->equalTo('id', $id);

        return $this->selectWith($select)->current();
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

        $insert = $this->getSql()->insert();
        $insert->values($insertData);

        return $this->insertWith($insert) > 0;
    }

    /**
     * @param AlbumEntity $album
     *
     * @return bool
     */
    private function updateAlbum(AlbumEntity $album)
    {
        $updateData = $album->getArrayCopy();

        $update = $this->getSql()->update();
        $update->set($updateData);
        $update->where->equalTo('id', $album->getId());

        return $this->updateWith($update) > 0;
    }

    /**
     * @param AlbumEntity $album
     *
     * @return bool
     */
    public function deleteAlbum(AlbumEntity $album)
    {
        $delete = $this->getSql()->delete();
        $delete->where->equalTo('id', $album->getId());

        return $this->deleteWith($delete) > 0;
    }
}
