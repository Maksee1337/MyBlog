<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Users;
use App\Form\FeedbackForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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
    public function Feedback(Request $request, MailerInterface $mailer)
    {
        $post = $request->request->get('feedback_form');

        if (NULL != $post) {
            $email = new Email();
            $email->from('burm.courses@gmail.com');
            $email->to('m.voytenko1991@gmail.com');
            $email->subject('Feedback from '. $post['Name']);
            $email->text($post['Text'] . "\n\nAuthor: ". $post['Email']);
            $mailer->send($email);
            return $this->redirectToRoute('Default_Index');
        }

        $form = $this->createForm(FeedbackForm::class);
        return $this->render('Default/Feedback.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
