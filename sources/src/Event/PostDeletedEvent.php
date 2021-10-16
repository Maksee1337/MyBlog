<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\News;

class PostDeletedEvent extends Event
{
    const NAME = 'post.deleted';

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