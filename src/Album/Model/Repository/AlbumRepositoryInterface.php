<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;

interface AlbumRepositoryInterface
{
    /**
     * Fetch all albums.
     *
     * @return AlbumEntity[]
     */
    public function fetchAllAlbums();

    /**
     * Fetch a single album by identifier.
     *
     * @param int $id
     * @return AlbumEntity|null
     */
    public function fetchSingleAlbum($id);

    /**
     * Save an album.
     *
     * Should insert a new album if no identifier is present in the entity, and
     * update an existing album otherwise.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function saveAlbum(AlbumEntity $album);

    /**
     * Delete an album.
     *
     * @param AlbumEntity $album
     * @return bool
     */
    public function deleteAlbum(AlbumEntity $album);
}
