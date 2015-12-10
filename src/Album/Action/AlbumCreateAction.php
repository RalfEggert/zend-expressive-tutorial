<?php
namespace Album\Action;

use Album\Form\AlbumDataForm;
use Album\Model\Entity\AlbumEntity;
use Album\Model\Table\AlbumTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AlbumTable
     */
    private $albumTable;

    /**
     * @var AlbumDataForm
     */
    private $albumForm;

    /**
     * AlbumCreateAction constructor.
     *
     * @param TemplateRendererInterface $template
     * @param RouterInterface           $router
     * @param AlbumTable                $albumTable
     * @param AlbumDataForm             $albumForm
     */
    public function __construct(
        TemplateRendererInterface $template, RouterInterface $router,
        AlbumTable $albumTable, AlbumDataForm $albumForm
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
        $message = 'Please enter the new album!';

        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();

            $this->albumForm->setData($postData);

            if ($this->albumForm->isValid()) {
                $album = new AlbumEntity();
                $album->exchangeArray($postData);

                if ($this->albumTable->saveAlbum($album)) {
                    return new RedirectResponse(
                        $this->router->generateUri('album')
                    );
                } else {
                    $message = 'The new album could not be saved!';
                }
            } else {
                $message = 'Please check your input!';
            }
        }

        $data = [
            'albumForm' => $this->albumForm,
            'message'   => $message,
        ];

        return new HtmlResponse(
            $this->template->render('album::create', $data)
        );
    }
}
