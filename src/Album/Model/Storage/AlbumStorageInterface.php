<?php
namespace Album\Model\Storage;

use Album\Model\Entity\AlbumEntity;

/**
 * Interface AlbumStorageInterface
 *
 * @package Album\Model\Storage
 */
interface AlbumStorageInterface
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