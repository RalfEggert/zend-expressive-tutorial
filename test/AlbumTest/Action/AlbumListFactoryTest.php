<?php
namespace AlbumTest\Action;

use Album\Action\AlbumListAction;
use Album\Action\AlbumListFactory;
use Album\Model\Repository\AlbumRepositoryInterface;
use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListFactoryTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Setup test case
     */
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    /**
     * Test if factory returns the correct action
     */
    public function testFactoryReturnsAlbumListAction()
    {
        $this->container
            ->get(TemplateRendererInterface::class)
            ->willReturn(
                $this->prophesize(TemplateRendererInterface::class)
                    ->reveal()
            );

        $this->container
            ->get(AlbumRepositoryInterface::class)
            ->willReturn(
                $this->prophesize(AlbumRepositoryInterface::class)
            );

        $factory = new AlbumListFactory();
        $this->assertTrue($factory instanceof AlbumListFactory);

        $action = $factory($this->container->reveal());
        $this->assertTrue($action instanceof AlbumListAction);
    }
}
