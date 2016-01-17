<?php
namespace Album\Model\Entity;

use DomainException;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class AlbumEntity
 *
 * @package Album\Model\Entity
 */
class AlbumEntity implements ArraySerializableInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $artist;

    /**
     * @var string
     */
    private $title;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        foreach ($array as $key => $value) {
            $setter = 'set' . ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            }
        }
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $data = [];

        foreach (get_object_vars($this) as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }


    /**
     * @param $id
     */
    private function setId($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            throw new DomainException(
                'Album id must be a positive integer!'
            );
        }

        $this->id = $id;
    }

    /**
     * @param $artist
     */
    private function setArtist($artist)
    {
        $artist = (string)$artist;

        if (empty($artist) || strlen($artist) > 100) {
            throw new DomainException(
                'Album artist must be between 1 and 100 chars!'
            );
        }

        $this->artist = $artist;
    }

    /**
     * @param $title
     */
    private function setTitle($title)
    {
        $title = (string)$title;

        if (empty($title) || strlen($title) > 100) {
            throw new DomainException(
                'Album title must be between 1 and 100 chars!'
            );
        }

        $this->title = $title;
    }
}
