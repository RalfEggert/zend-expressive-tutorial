<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;
use Album\Model\Storage\AlbumStorageInterface;

/**
 * Class ZendDbAlbumRepository
 *
 * @package Album\Model\Repository
 */
class AlbumRepository implements AlbumRepositoryInterface
{
    /**
     * @var AlbumStorageInterface
     */
    private $albumStorage;

    /**
     * AlbumRepository constructor.
     *
     * @param AlbumStorageInterface $albumStorage
     */
    public function __construct(AlbumStorageInterface $albumStorage)
    {
        $this->albumStorage = $albumStorage;
    }

    /**
     * Fetch all albums
     *
     * @return AlbumEntity[]
     */
    public function fetchAllAlbums()
    {
        return $this->albumStorage->fetchAlbumList();
    }

    /**
     * Fetch a single album
     *
     * @param $id
     *
     * @return AlbumEntity|null
     */
    public function fetchSingleAlbum($id)
    {
        return $this->albumStorage->fetchAlbumById($id);
    }

    /**
     * Save album
     *
     * @param AlbumEntity $album
     *
     * @return boolean
     */
    public function saveAlbum(AlbumEntity $album)
    {
        if (!$album->getId()) {
            return $this->albumStorage->insertAlbum($album);
        } else {
            return $this->albumStorage->updateAlbum($album);
        }
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
        return $this->albumStorage->deleteAlbum($album);
    }
}
