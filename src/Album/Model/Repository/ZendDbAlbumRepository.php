<?php
namespace Album\Model\Repository;

use Album\Model\Entity\AlbumEntity;
use Album\Db\AlbumTableGatewayInterface;

/**
 * Class ZendDbAlbumRepository
 *
 * @package Album\Model\Repository
 */
class ZendDbAlbumRepository implements AlbumRepositoryInterface
{
    /**
     * @var AlbumTableGatewayInterface
     */
    private $albumSource;

    /**
     * AlbumRepository constructor.
     *
     * @param AlbumTableGatewayInterface $albumSource
     */
    public function __construct(AlbumTableGatewayInterface $albumSource)
    {
        $this->albumSource = $albumSource;
    }

    /**
     * Fetch all albums
     *
     * @return AlbumEntity[]
     */
    public function fetchAllAlbums()
    {
        return $this->albumSource->fetchAlbumList();
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
        return $this->albumSource->fetchAlbumById($id);
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
            return $this->albumSource->insertAlbum($album);
        } else {
            return $this->albumSource->updateAlbum($album);
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
        return $this->albumSource->deleteAlbum($album);
    }
}
