<?php

namespace App\Event;

use App\Service\EmailSender;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UserNotifierSubscriber
 * @package App\Event
 */
class UserNotifierSubscriber implements EventSubscriberInterface
{
    const ADMIN_EMAIL = '______@gmail.com';
    /**
     * @var EmailSender EmailSender
     */
    protected $emailSender;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UserNotifierSubscriber constructor.
     * @param EmailSender $emailSender
     * @param ContainerInterface $container
     * @param UrlGeneratorInterface $router
     */
    public function __construct(EmailSender $emailSender,
                                ContainerInterface $container,
                                UrlGeneratorInterface $router
    )
    {
        $this->router = $router;
        $this->container = $container;
        $this->emailSender = $emailSender;
    }

    /**
     * @return \array[][]
     */
    public static function getSubscribedEvents()
    {

        return [
            PostDeletedEvent::NAME => [
                ['postDeleted', 1],
            ],
            PostEditedEvent::NAME => [
                ['postEdited', 1],
            ],
            PostCreatedEvent::NAME => [
                ['postCreated', 1],
            ],
        ];
    }

    /**
     * @param PostDeletedEvent $event
     */
    public function postDeleted(PostDeletedEvent $event)
    {
        $html = sprintf('Post ID:%s deleted!', $event->getPost()->getId());
        $this->emailSender->send("Post Deleted", $html, self::ADMIN_EMAIL);
        //////////////////

        // тут должен быть код отправки в телеграм, которую не получилось сделать
    }

    /**
     * @param PostEditedEvent $event
     */
    public function postEdited(PostEditedEvent $event)
    {
        // тут должен быть код отправки в телеграм, которую не получилось сделать
       // echo 'postEdited'; die;
    }

    /**
     * @param PostCreatedEvent $event
     */
    public function postCreated(PostCreatedEvent $event)
    {
        $context = $this->router->getContext();
        $context->setBaseUrl(sprintf('%s://%s:8000', $context->getScheme(), $context->getHost()));
        $locate = $this->container->get('translator')->getLocale();
        $link = $this->router->generate('News_ShowPost', ['_locale' => $locate, 'post' => $event->getPost()->getId()]);
        $html = sprintf('New post: <a href="%s">Link</a>', $link);
        $this->emailSender->send("New post", $html, self::ADMIN_EMAIL);
    }


}