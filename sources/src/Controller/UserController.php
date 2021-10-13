<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\User;
use App\Form\FeedbackForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 *
 * @package App\Controller
 *
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 */
class UserController extends AbstractController
{
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
}
