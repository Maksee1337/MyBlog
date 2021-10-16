<?php

namespace App\Event;

use App\Entity\News;
use Symfony\Contracts\EventDispatcher\Event;

class PostEditedEvent extends Event
{
    const NAME = 'post.edited';

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