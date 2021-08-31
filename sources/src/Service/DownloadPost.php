<?php

namespace App\Service;

use App\Entity\News;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class DownloadPost - класс-сервис для скачивания поста
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 * @package App\Service
 */
class DownloadPost
{
    /**
     * @var string $filename
     */
    protected $filename = '';

    /**
     * @var string $fileContent
     */
    protected $fileContent = '';

    /**
     * download - метод для скачивания файла
     * @param News   $post
     * @param string $type
     * @return Response
     */
    public function download(News $post, $type)
    {
        if ($type == 'text') {
            $this->getText($post);
        } elseif ($type == 'html') {
            $this->getHtml($post);
        } else {
            return new Response('Type error');
        }

        $response = new \Symfony\Component\HttpFoundation\Response($this->fileContent);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->filename
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    /**
     * @param News $post
     * @return void
     */
    protected function getText(News $post)
    {
        $this->filename = $post->getId() . '.txt';
        $this->fileContent = $post->getShort();
        $this->fileContent .= "\n\n".$post->getText();
    }

    /**
     * @param News $post
     * @return void
     */
    protected function getHtml(News $post)
    {
        $this->filename = $post->getId() . '.html';
        $this->fileContent = $post->getShort();
        $this->fileContent .= '<br><br>'.$post->getText();
    }
}
