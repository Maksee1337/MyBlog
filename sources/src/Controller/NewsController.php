<?php

namespace App\Controller;

use App\Entity\News;
use App\Event\PostDeletedEvent;
use App\Event\PostCreatedEvent;
use App\Event\PostEditedEvent;
use App\Service\DownloadPostResponce;
use App\Service\DownloadPostText;
use App\Service\DownloadPostHtml;
use App\Form\DownloadForm;
use App\Form\PostForm;
use http\Env\Response;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class NewsControllerTest
 *
 * @package App\Controller
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 */
class NewsController extends AbstractController
{
    /**
     * NewPost метод создает форму и обрабатывает запрос на создание нового поста
     * @Route("/NewPost", name="News_NewPost")
     * @param Request $request
     * @return Response
     */
    public function NewPost(Request $request, EventDispatcherInterface $dispatcher)
    {
        $post = new News();
        $form = $this->createForm(PostForm::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $post->setDateTime(new \DateTime());
            $post->setViews(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $dispatcher->dispatch(new PostCreatedEvent($post), PostCreatedEvent::NAME);

            return $this->redirectToRoute('News_ShowPost', [
                'post' => $post->getId(),
            ]);
        }
        return $this->render('News/NewPost.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * EditPost метод создает форму с данными поста. Так же через этот метод можно удалить пост.
     * @Route("/EditPost/{post}", name="News_EditPost")
     * @param Request $request
     * @param News    $post
     * @return Response
     */
    public function EditPost(Request $request, News $post, EventDispatcherInterface $dispatcher)
    {
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // проверка что пришел запрос и он валиден
                $dispatcher->dispatch(new PostEditedEvent($post), PostEditedEvent::NAME);
                $post->setDateTime(new \DateTime());
                $post->setViews(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                return $this->redirectToRoute('News_ShowPost', [
                    'post' => $post->getId(),
                ]);

        }

     //   dd($request);
        return $this->render('News/EditPost.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * DeletePost - удаляет пост
     * @Route("/DeletePost/{post}", name="News_DeletePost")
     * @param News $post
     * @return Response
     */
    public function DeletePost(News $post, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(new PostDeletedEvent($post), PostDeletedEvent::NAME);
        $em = $this->getDoctrine()->getManager();
       // $em->remove($post);
       // $em->flush();

        return $this->redirectToRoute('Default_Index');
    }

    /**
     * @Route("/DownloadFile/{post}/Text", name="DownloadFile_Text")
     * @param News                 $post
     * @param DownloadPostText     $exporterText
     * @param DownloadPostResponce $downloadPostResponce
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadText(News $post, DownloadPostText $exporterText, DownloadPostResponce $downloadPostResponce)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $downloadPostResponce->getResponce($exporterText->getDataFromPost($post));
    }

    /**
     * @Route("/DownloadFile/{post}/Html", name="DownloadFile_Html")
     * @param News                 $post
     * @param DownloadPostHtml     $exporterHtml
     * @param DownloadPostResponce $downloadPostResponce
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadHtml(News $post, DownloadPostHtml $exporterHtml, DownloadPostResponce $downloadPostResponce)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $downloadPostResponce->getResponce($exporterHtml->getDataFromPost($post));
    }
    /**
     * ShowPost выводит пост на страницу. сам пост формируется автоматически из аргуметов метода
     * @Route("/ShowPost/{post}", name="News_ShowPost")
     * @param News $post
     * @return Response
     */
    public function ShowPost(News $post)
    {
        $downloadForm = $this->createForm(DownloadForm::class, $post);

        return $this->render('News/ShowPost.html.twig', [
            'post' => $post,
            'form' => $downloadForm->createView(),
        ]);
    }
}
