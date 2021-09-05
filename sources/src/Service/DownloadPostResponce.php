<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class DownloadPostResponce
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 * @package App\Service
 */
class DownloadPostResponce
{
    public function getResponce($data)
    {
        $response = new Response($data['fileContent']);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $data['fileName'],
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }
}