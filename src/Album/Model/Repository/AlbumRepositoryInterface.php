<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;

/**
 * Interface AlbumRepositoryInterface
 *
 * @package Album\Model\Repository
 */
interface AlbumRepositoryInterface
{
    /**
     * Fetch all albums
     *
     * @return AlbumEntity[]
     */
    public function fetchAllAlbums();

    /**
     * Fetch a single album
     *
     * @param $id
     *
     * @return AlbumEntity|null
     */
    public function fetchSingleAlbum($id);

    /**
     * Save album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function saveAlbum(AlbumEntity $album);

    /**
     * Delete an album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function deleteAlbum(AlbumEntity $album);
}