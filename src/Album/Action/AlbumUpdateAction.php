<?php
namespace Album\Action;

use Album\Form\AlbumForm;
use Album\Model\Table\AlbumTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class AlbumUpdateAction
 *
 * @package Album\Action
 */
class AlbumUpdateAction
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
     * @var AlbumForm
     */
    private $albumForm;

    /**
     * AlbumUpdateAction constructor.
     *
     * @param TemplateRendererInterface $template
     * @param RouterInterface           $router
     * @param AlbumTable                $albumTable
     * @param AlbumForm                 $albumForm
     */
    public function __construct(
        TemplateRendererInterface $template, RouterInterface $router,
        AlbumTable $albumTable, AlbumForm $albumForm
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
        $message = 'Please change the album!';

        $id = $request->getAttribute('id');

        $album = $this->albumTable->fetchSingleAlbum($id);

        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();

            $this->albumForm->setData($postData);

            if ($this->albumForm->isValid()) {
                $postData['id'] = $id;

                $album->exchangeArray($postData);

                if ($this->albumTable->saveAlbum($album)) {
                    return new RedirectResponse(
                        $this->router->generateUri('album')
                    );
                } else {
                    $message = 'The album was not changed!';
                }
            } else {
                $message = 'Please check your input!';
            }
        } else {
            $this->albumForm->bind($album);
        }

        $data = [
            'albumForm' => $this->albumForm,
            'message'   => $message,
        ];

        return new HtmlResponse(
            $this->template->render('album::update', $data)
        );
    }
}
