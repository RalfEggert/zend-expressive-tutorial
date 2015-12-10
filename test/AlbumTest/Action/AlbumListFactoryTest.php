<?php

namespace AppTest\Action;

use Album\Action\AlbumListAction;
use Album\Action\AlbumListFactory;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerInterface */
    protected $container;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $router = $this->prophesize(RouterInterface::class);

        $this->container->get(RouterInterface::class)->willReturn($router);
    }

    public function testFactory()
    {
        $factory = new AlbumListFactory();
        $this->container
            ->get(TemplateRendererInterface::class)
            ->willReturn($this->prophesize(TemplateRendererInterface::class));

        $this->assertTrue($factory instanceof AlbumListFactory);

        $action = $factory($this->container->reveal());

        $this->assertTrue($action instanceof AlbumListAction);
    }
}
