<?php
namespace Album\Action;

use Album\Model\Repository\AlbumRepository;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumListFactory
 *
 * @package Album\Action
 */
class AlbumListFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AlbumListAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $template = $container->get(TemplateRendererInterface::class);
        $albumRepository = $container->get(AlbumRepository::class);

        return new AlbumListAction($template, $albumRepository);
    }
}
