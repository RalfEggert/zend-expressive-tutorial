<?php
namespace AlbumTest\Action;

use Album\Action\AlbumListAction;
use Album\Model\Repository\AlbumRepositoryInterface;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumListActionTest
 *
 * @package AlbumTest\Action
 */
class AlbumListActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var callable
     */
    private $next;

    /**
     * Setup test case
     */
    public function setUp()
    {
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->response = $this->prophesize(ResponseInterface::class);

        $this->next = function () {
        };
    }

    /**
     * Test if action renders the album list
     */
    public function testActionRendersAlbumList()
    {
        $albumRepository = $this->prophesize(
            AlbumRepositoryInterface::class
        );
        $albumRepository->fetchAllAlbums()->shouldBeCalled()->willReturn(
            ['album1', 'album2']
        );

        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $renderer->render(
            'album::list', ['albumList' => ['album1', 'album2']]
        )->shouldBeCalled()->willReturn('BODY');

        $action = new AlbumListAction(
            $renderer->reveal(), $albumRepository->reveal()
        );

        $response = $action(
            $this->request->reveal(),
            $this->response->reveal(),
            $this->next
        );

        $this->assertInstanceOf(HtmlResponse::class, $response);

        $this->assertEquals('BODY', $response->getBody());
    }
}
