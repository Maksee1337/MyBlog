<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package App\Controller
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="Default_Index")
     *
     * @return Response
     */
    public function Index()
    {
        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository(News::class)->findAll();
        $news = array_reverse($news);   // перевернул массив чтоб сначала были новые записи
        return $this->render('Default/Index.html.twig', ['news' => $news]);
    }

    /**
     * @Route("/About", name="Default_About")
     *
     * @return Response
     */
    public function About()
    {
        return $this->render('Default/About.html.twig', ['text' => '222some text']);
    }

    /**
     * @Route("/Feedback", name="Default_Feedback")
     *
     * @return Response
     */
    public function Feedback()
    {
        return $this->render('Default/Feedback.html.twig');
    }
}
