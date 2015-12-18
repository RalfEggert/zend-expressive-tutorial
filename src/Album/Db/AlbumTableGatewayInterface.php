<?php
namespace Album\Db;

use Album\Model\Entity\AlbumEntity;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * Interface AlbumTableGatewayInterface
 *
 * @package Album\Db
 */
interface AlbumTableGatewayInterface extends TableGatewayInterface
{
    /**
     * Fetch album list
     *
     * @return AlbumEntity[]
     */
    public function fetchAlbumList();

    /**
     * Fetch an album by id
     *
     * @param int $id
     *
     * @return AlbumEntity|null
     */
    public function fetchAlbumById($id);

    /**
     * Insert album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function insertAlbum(AlbumEntity $album);

    /**
     * Update album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function updateAlbum(AlbumEntity $album);

    /**
     * Delete an album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function deleteAlbum(AlbumEntity $album);
}