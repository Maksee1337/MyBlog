<?php


namespace App\Controller;

use App\Entity\News;
use App\Service\DownloadPost;
use App\Form\DownloadForm;
use App\Form\PostForm;
use http\Env\Response;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class NewsController
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
    public function NewPost(Request $request)
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
    public function EditPost(Request $request, News $post)
    {
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // проверка что пришел запрос и он валиден
                $post->setDateTime(new \DateTime());
                $post->setViews(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

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
     * DeletePost - удаляет пост
     * @Route("/DeletePost/{post}", name="News_DeletePost")
     * @param News $post
     * @return Response
     */
    public function DeletePost(News $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('Default_Index');
    }

    /**
     * DownloadFile - отдает данные в виде файла
     * @Route("/DownloadFile/{post}/{type}", name="News_DownloadFile")
     * @param News         $post
     * @param string       $type
     * @param DownloadPost $downloadPost
     * @return Response
     */
    public function DownloadFile(News $post, $type, DownloadPost $downloadPost)
    {
        $fileContent = $downloadPost->GetContent($post, $type);

        if($fileContent) {
            $response = new \Symfony\Component\HttpFoundation\Response($fileContent['fileContent']);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $fileContent['fileName'],
            );
            $response->headers->set('Content-Disposition', $disposition);
            return $response;
        } else {
            return new Response('Type error');
        }
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
