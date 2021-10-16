<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Subscribers;
use App\Entity\User;
use App\Form\FeedbackForm;
use App\Form\PostForm;
use App\Form\SubscribeForm;
use App\Form\UnSubscribeForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\Button\InlineKeyboardButton;
use Symfony\Component\Notifier\Bridge\Telegram\Reply\Markup\InlineKeyboardMarkup;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

/**
 * Class UserController
 *
 * @package App\Controller
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 */
class UserController extends AbstractController
{

    /**НЕ РАБОТАЕТ!!!
     * @Route("/tg", name="User_tg")
     */
    public function tg(ChatterInterface $chatter): Response
    {
        $chatMessage = new ChatMessage('Testttt');

        // Create Telegram options
        $telegramOptions = (new TelegramOptions())
            ->chatId('@max121314')
            ->parseMode('MarkdownV2')
            ->disableWebPagePreview(true)
            ->disableNotification(true)
            ->replyMarkup((new InlineKeyboardMarkup())
                ->inlineKeyboard([
                    (new InlineKeyboardButton('Visit symfony.com'))
                        ->url('https://symfony.com/'),
                ])
            );

        // Add the custom options to the chat message and send the message
        $chatMessage->options($telegramOptions);

        $chatter->send($chatMessage);

        dd($chatter);
    }

    /**
     * @Route("/Login", name="User_Login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function Login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('User/Login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => ((!is_null($error)) ? $error->getMessageKey() : false),
        ]);
    }

    /**
     * @Route("/NewPass", name="User_NewPass")
     *
     * @param Request                     $request
     * @param UserPasswordHasherInterface $hasher
     * @param MailerInterface             $mailer
     * @return Response
     */
    public function NewPass(Request $request, UserPasswordHasherInterface $hasher, MailerInterface $mailer): Response
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('User/NewPass.html.twig', [
                'error' => false,
            ]);
        } else {
            $lastUsername = $request->request->get('_username');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['login' => $lastUsername]);
            if ($user) {
                $newPass = uniqid();
                $user->setPassword($hasher->hashPassword($user, $newPass));
                $em->persist($user);
                $em->flush();

                $email = new Email();
                $email->from('burm.courses@gmail.com');
                $email->to($user->getEmail());
                $email->subject('New password');
                $email->text($lastUsername.', your new password is '. $newPass);
                $mailer->send($email);

                return $this->render('User/Login.html.twig', [
                  'last_username' => $lastUsername,
                  'error' => 'Your new password sent to '. $user->getEmail(),
                ]);
            } else {
                return $this->render('User/NewPass.html.twig', [
                   'error' => 'User "'.$lastUsername.'" is not found',
                ]);
            }
        }
    }

    /**
     * @Route("/Subscribe", name="User_Subscribe")
     * @param Request $request
     * @return Response
     */
    public function Subscribe(Request $request): Response
    {
        $subscriber = new Subscribers();
        $form = $this->createForm(SubscribeForm::class, $subscriber);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $tmp = $em->getRepository(Subscribers::class)->findOneBy(['email' => $subscriber->getEmail()]);
            if (is_null($tmp)) {
                $em->persist($subscriber);
                $em->flush();
                return $this->redirectToRoute('Default_Index');
            } else {
                return $this->render('User/Subscribe.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'You already subscribed.',
                ]);
            }

        }

        return $this->render('User/Subscribe.html.twig', [
            'form' => $form->createView(),
            'error' => false,
        ]);
    }

    /**
     * @Route("/UnSubscribe", name="User_UnSubscribe")
     * @param Request $request
     * @return Response
     */
    public function UnSubscribe(Request $request): Response
    {
        $subscriber = new Subscribers();
        $form = $this->createForm(UnSubscribeForm::class, $subscriber);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $tmp = $em->getRepository(Subscribers::class)->findOneBy(['email' => $subscriber->getEmail()]);

            if (is_null($tmp)) {
                return $this->render('User/Subscribe.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'Email not found.',
                ]);
            } else {
                $em->remove($tmp);
                $em->flush();
                return $this->redirectToRoute('Default_Index');
            }
        }
        return $this->render('User/UnSubscribe.html.twig', [
            'form' => $form->createView(),
            'error' => false,
        ]);
    }
}
