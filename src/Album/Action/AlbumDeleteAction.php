<?php
namespace Album\Action;

use Album\Form\AlbumDeleteForm;
use Album\Model\Table\AlbumTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumDeleteAction
 *
 * @package Album\Action
 */
class AlbumDeleteAction
{
    /**
     * @var TemplateRendererInterface
     */
    private $template;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AlbumTable
     */
    private $albumTable;

    /**
     * @var AlbumDeleteForm
     */
    private $albumForm;

    /**
     * AlbumDeleteAction constructor.
     *
     * @param TemplateRendererInterface $template
     * @param RouterInterface           $router
     * @param AlbumTable                $albumTable
     * @param AlbumDeleteForm           $albumForm
     */
    public function __construct(
        TemplateRendererInterface $template, RouterInterface $router,
        AlbumTable $albumTable, AlbumDeleteForm $albumForm
    ) {
        $this->template   = $template;
        $this->router     = $router;
        $this->albumTable = $albumTable;
        $this->albumForm  = $albumForm;
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
        $message = 'You you want to delete this album!';

        $id = $request->getAttribute('id');

        $album = $this->albumTable->fetchSingleAlbum($id);

        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();

            if (isset($postData['delete_album_yes'])) {
                $this->albumTable->deleteAlbum($album);
            }
            
            return new RedirectResponse(
                $this->router->generateUri('album')
            );
        } else {
            $this->albumForm->bind($album);
        }

        $data = [
            'albumEntity' => $album,
            'albumForm'   => $this->albumForm,
            'message'     => $message,
        ];

        return new HtmlResponse(
            $this->template->render('album::delete', $data)
        );
    }
}
