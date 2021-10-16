<?php

namespace App\Event;

use App\Entity\News;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PostCreatedEvent extends Event
{
    const NAME = 'post.created';

    /**
     * @var News
     */
    private $post;

    public function __construct(News $post)
    {
        $this->post = $post;
    }

    public function getPost()
    {
        return $this->post;
    }
}