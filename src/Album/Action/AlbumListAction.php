<?php
namespace Album\Action;

use Album\Model\Table\AlbumTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumListAction
 *
 * @package Album\Action
 */
class AlbumListAction
{
    /**
     * @var TemplateRendererInterface
     */
    private $template;

    /**
     * @var AlbumTable
     */
    private $albumTable;

    /**
     * AlbumListAction constructor.
     *
     * @param TemplateRendererInterface $template
     * @param AlbumTable                $albumTable
     */
    public function __construct(
        TemplateRendererInterface $template, AlbumTable $albumTable
    ) {
        $this->template = $template;
        $this->albumTable = $albumTable;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     *
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request, ResponseInterface $response,
        callable $next = null
    ) {
        $data = [
            'albumList' => $this->albumTable->fetchAllAlbums(),
        ];

        return new HtmlResponse(
            $this->template->render('album::list', $data)
        );
    }
}
