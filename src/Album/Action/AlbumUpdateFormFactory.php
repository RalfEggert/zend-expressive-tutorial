<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumUpdateFormFactory
 *
 * @package Album\Action
 */
class AlbumUpdateFormFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumUpdateFormAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template        = $container->get(TemplateRendererInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $albumForm       = $container->get(AlbumDataForm::class);

        return new AlbumUpdateFormAction(
            $template, $albumRepository, $albumForm
        );
    }
}
