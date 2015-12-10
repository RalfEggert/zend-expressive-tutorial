<?php
namespace Album\Form;

use Zend\Form\Form;

/**
 * Class AlbumDataForm
 *
 * @package Album\Form
 */
class AlbumDataForm extends Form
{
    /**
     * Init form
     */
    public function init()
    {
        $this->setName('album_form');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'name'       => 'artist',
                'type'       => 'Text',
                'attributes' => [
                    'class' => 'form-control',
                ],
                'options'    => [
                    'label'            => 'Artist',
                    'label_attributes' => [
                        'class' => 'col-sm-2 control-label',
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'title',
                'type'       => 'Text',
                'attributes' => [
                    'class' => 'form-control',
                ],
                'options'    => [
                    'label' => 'Title',
                    'label_attributes' => [
                        'class' => 'col-sm-2 control-label',
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'save_album',
                'type'       => 'Submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => 'Save Album',
                    'id'    => 'save_album',
                ],
            ]
        );
    }
}
