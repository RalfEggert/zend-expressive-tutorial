<?php
namespace Album\Action;

use Album\Form\AlbumForm;
use Album\Model\Entity\AlbumEntity;
use Album\Model\Table\AlbumTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\Http\Request;

/**
 * Class AlbumCreateAction
 *
 * @package Album\Action
 */
class AlbumCreateAction
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
     * @var AlbumForm
     */
    private $albumForm;

    /**
     * AlbumCreateAction constructor.
     *
     * @param TemplateRendererInterface $template
     * @param AlbumTable                $albumTable
     * @param AlbumForm                 $albumForm
     */
    public function __construct(
        TemplateRendererInterface $template, AlbumTable $albumTable,
        AlbumForm $albumForm
    ) {
        $this->template = $template;
        $this->albumTable = $albumTable;
        $this->albumForm = $albumForm;
    }

    /**
     * @param ServerRequestInterface|Request $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     *
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request, ResponseInterface $response,
        callable $next = null
    ) {
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();

            $album = new AlbumEntity();
            $album->exchangeArray($postData);

            var_dump($album);
            exit;
        }

        $data = [
            'albumList' => $this->albumTable->fetchAllAlbums(),
            'albumForm' => $this->albumForm,
        ];

        return new HtmlResponse(
            $this->template->render('album::create', $data)
        );
    }
}
