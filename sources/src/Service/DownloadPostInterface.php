<?php


namespace App\Service;
use App\Entity\News;

/**
 * Interface DownloadPostInterface
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 *
 * @package App\Service
 */
interface DownloadPostInterface
{
    /**
     * @param News $post
     * @return array
     */
    public function getDataFromPost(News $post);
}