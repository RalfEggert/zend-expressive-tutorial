<?php
namespace Album\Form;

use Zend\Form\Form;

/**
 * Class AlbumDeleteForm
 *
 * @package Album\Form
 */
class AlbumDeleteForm extends Form
{
    /**
     * Init form
     */
    public function init()
    {
        $this->setName('album_delete_form');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'name'       => 'delete_album_yes',
                'type'       => 'Submit',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => 'Yes',
                    'id'    => 'delete_album_yes',
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'delete_album_no',
                'type'       => 'Submit',
                'attributes' => [
                    'class' => 'btn btn-default',
                    'value' => 'No',
                    'id'    => 'delete_album_no',
                ],
            ]
        );
    }
}
