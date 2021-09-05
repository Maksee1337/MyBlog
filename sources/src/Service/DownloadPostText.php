<?php

namespace App\Service;

use App\Entity\News;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class DownloadPostText - класс-сервис для скачивания поста
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 * @package App\Service
 */
class DownloadPostText implements DownloadPostInterface
{
    /**
     * @param News $post
     * @return array
     */
    public function getDataFromPost(News $post)
    {
        $fileName = $post->getId() . '.txt';
        $fileContent = $post->getShort();
        $fileContent .= "\n\n".$post->getText();

        return ['fileName' => $fileName,
                'fileContent' => $fileContent,
        ];
    }
}
