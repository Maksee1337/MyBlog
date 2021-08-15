<?php


namespace App\Controller;

use App\Entity\News;
use App\Form\PostForm;
use http\Env\Response;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Request;
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
            'H1' => 'Create a new post',
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
            //проверяем это обновление поста или удаление
            //если в после есть ключ Submit то обновляем и перекидываем на страницу поста
            if ( array_key_exists('Submit', $request->request->get('post_form'))) {
                $post->setDateTime(new \DateTime());
                $post->setViews(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                return $this->redirectToRoute('News_ShowPost', [
                    'post' => $post->getId(),
                ]);
            } else {
                //если в после есть ключ Delete то удаляем пост и переходим на главную страницу
                if (array_key_exists('Delete', $request->request->get('post_form'))) {

                    $em = $this->getDoctrine()->getManager();
                    $em->remove($post);
                    $em->flush();

                    return $this->redirectToRoute('Default_Index');
                }
            }
        }

        return $this->render('News/NewPost.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'H1' => 'Edit a post',
        ]);
    }

    /**
     * ShowPost выводит пост на страницу. сам пост формируется автоматически из аргуметов метода
     * @Route("/ShowPost/{post}", name="News_ShowPost")
     * @param News $post
     * @return Response
     */
    public function ShowPost(News $post)
    {
        return $this->render('News/ShowPost.html.twig', [
            'post' => $post,
        ]);
    }
}
