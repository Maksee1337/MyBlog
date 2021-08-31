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
     * @var string $fileName
     */
    protected$fileName = '';

    /**
     * @var string $fileContent
     */
    protected $fileContent = '';

    /**
     * GetContent - метод для скачивания файла
     * @param News   $post
     * @param string $type
     * @return string
     */
    public function GetContent(News $post, $type)
    {
        if ($type == 'text') {
            $this->getText($post);
        } elseif ($type == 'html') {
            $this->getHtml($post);
        } else {
            return false;
        }

        return [
            'fileName' => $this->fileName,
            'fileContent' => $this->fileContent,
            ];
    }

    /**
     * @param News $post
     * @return void
     */
    protected function getText(News $post)
    {
        $this->fileName = $post->getId() . '.txt';
        $this->fileContent = $post->getShort();
        $this->fileContent .= "\n\n".$post->getText();
    }

    /**
     * @param News $post
     * @return void
     */
    protected function getHtml(News $post)
    {
        $this->fileName = $post->getId() . '.html';
        $this->fileContent = $post->getShort();
        $this->fileContent .= '<br><br>'.$post->getText();
    }
}
