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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function defaultLocate()
    {
        return $this->redirectToRoute('Default_Index');
    }

    /**
     * @Route("/", name="Default_Index")
     *
     * @return Response
     */
    public function Index()
    {
       // $user = $this->getUser();
      //  dd($user);
        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository(News::class)->findAll();
        $news = array_reverse($news);   // перевернул массив чтоб сначала были новые записи
        return $this->render('Default/Index.html.twig', ['news' => $news]);
    }

    /**
     * @Route("/About", name="Default_About")
     *
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function About(TranslatorInterface $translator)
    {
        return $this->render('Default/About.html.twig', ['text' => $translator->trans('About')]);
    }

    /**
     * @Route("/Feedback", name="Default_Feedback")
     *
     * @param Request         $request
     * @param MailerInterface $mailer
     *
     * @return Response
     */
    public function Feedback(Request $request, MailerInterface $mailer)
    {
        $post = $request->request->get('feedback_form');

        if (null != $post) {
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
