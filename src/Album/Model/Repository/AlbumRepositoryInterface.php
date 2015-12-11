<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;

interface AlbumRepositoryInterface
{
    /**
     * @return array
     */
    public function fetchAllAlbums();

    /**
     * @param $id
     *
     * @return AlbumEntity
     */
    public function fetchSingleAlbum($id);

    /**
     * @param AlbumEntity $album
     *
     * @return bool
     */
    public function saveAlbum(AlbumEntity $album);

    /**
     * @param AlbumEntity $album
     *
     * @return bool
     */
    public function deleteAlbum(AlbumEntity $album);
}
