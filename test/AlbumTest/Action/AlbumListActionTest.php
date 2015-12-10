<?php

namespace AlbumTest\Action;

use Album\Action\AlbumListAction;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListActionTest extends PHPUnit_Framework_TestCase
{
    /** @var ServerRequestInterface */
    private $request;

    /** @var ResponseInterface */
    private $response;

    /** @var callable */
    private $next;

    public function setUp()
    {
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->response = $this->prophesize(ResponseInterface::class);
        $this->next = function () {};
    }

    public function testActionRendersAlbumList()
    {
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $renderer->render('album::list', [])->shouldBeCalled()->willReturn('BODY');
        $action = new AlbumListAction($renderer->reveal());
        $response = $action($this->request->reveal(), $this->response->reveal(), $this->next);
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals('BODY', $response->getBody());
    }
}
