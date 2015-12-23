<?php
namespace Album\Db;

use Album\Model\Entity\AlbumEntity;
use Album\Model\Storage\AlbumStorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class AlbumTableGateway
 *
 * @package Album\Db
 */
class AlbumTableGateway extends TableGateway implements AlbumStorageInterface
{
    /**
     * AlbumTableGateway constructor.
     *
     * @param AdapterInterface   $adapter
     * @param ResultSetInterface $resultSet
     */
    public function __construct(AdapterInterface $adapter, ResultSetInterface $resultSet)
    {
        parent::__construct('album', $adapter, null, $resultSet);
    }

    /**
     * Fetch album list
     *
     * @return AlbumEntity[]
     */
    public function fetchAlbumList()
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
     * Fetch an album by id
     *
     * @param int $id
     *
     * @return AlbumEntity|null
     */
    public function fetchAlbumById($id)
    {
        $select = $this->getSql()->select();
        $select->where->equalTo('id', $id);

        return $this->selectWith($select)->current();
    }

    /**
     * Insert album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function insertAlbum(AlbumEntity $album)
    {
        $insertData = $album->getArrayCopy();

        $insert = $this->getSql()->insert();
        $insert->values($insertData);

        return $this->insertWith($insert) > 0;
    }

    /**
     * Update album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function updateAlbum(AlbumEntity $album)
    {
        $updateData = $album->getArrayCopy();

        $update = $this->getSql()->update();
        $update->set($updateData);
        $update->where->equalTo('id', $album->getId());

        return $this->updateWith($update) > 0;
    }

    /**
     * Delete an album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function deleteAlbum(AlbumEntity $album)
    {
        $delete = $this->getSql()->delete();
        $delete->where->equalTo('id', $album->getId());

        return $this->deleteWith($delete) > 0;
    }
}
