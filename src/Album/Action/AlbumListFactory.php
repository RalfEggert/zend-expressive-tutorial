<?php
namespace Album\Action;

use Album\Model\Table\AlbumTable;
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
        $albumTable = $container->get(AlbumTable::class);

        return new AlbumListAction($template, $albumTable);
    }
}
