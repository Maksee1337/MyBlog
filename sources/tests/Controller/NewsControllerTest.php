<?php

namespace App\Tests\Controller;

use App\Entity\News;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


/**
 * Class NewsControllerTest
 * @author Maks Voytenko <m.voytenko1991@gmail.com>
 * @package App\Tests\Controller
 */
class NewsControllerTest extends WebTestCase
{
    /**
     * @return int
     */
    private function getLastPostId(): int
    {
        $em = self::bootKernel()->getContainer()->get('doctrine')->getManager();
        $post = $em->getRepository(News::class)->findOneBy([], ['id' => 'desc']);

        return $post->getId();
    }

    /**
     * @param int $id
     * @return mixed
     */
    private function getPostById(int $id)
    {
        $em = self::bootKernel()->getContainer()->get('doctrine')->getManager();
        $post = $em->getRepository(News::class)->findOneBy(['id' => $id]);

        return $post;
    }

    /** assertDatabaseHas - проверяет базу на наличие конткретной записи в поле
     * @param int    $id
     * @param string $field
     * @param string $value
     * @return void
     */
    private function assertDatabaseHas(int $id , string $field, string $value) : void
    {
        $em = self::bootKernel()->getContainer()->get('doctrine')->getManager();
        $post = $em->getRepository(News::class)->findOneBy(['id' => $id, $field => $value]);

        $this->assertTrue(NULL != $post);
    }

    public function test_NewPost()
    {
        $client = static::createClient();

        $short = uniqid();
        $text = uniqid();
        $author = uniqid();

        $crawler = $client->request('GET', '/NewPost');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Submit')->form();
        $form['post_form[Short]'] = $short;
        $form['post_form[Text]'] = $text;
        $form['post_form[Author]'] = $author;

        $lastPostId = $this->getLastPostId();
        $client->submit($form);
        $currentPostId = $this->getLastPostId();
        $this->assertTrue($lastPostId < $currentPostId); // проверка что айди увеличился

        // проверка что данные записалить правильно
        $this->assertDatabaseHas($currentPostId,'Short', $short);
        $this->assertDatabaseHas($currentPostId,'Text', $text);
        $this->assertDatabaseHas($currentPostId,'Author', $author);

        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        // проверка на наличие записи на главной странице, актуально пока нет пагинации
        $this->assertSelectorTextContains('body', $short);
        $this->assertSelectorTextContains('body', $author);
    }
    public function test_EditPost()
    {
        $client = static::createClient();

        $short = uniqid();
        $text = uniqid();
        $author = uniqid();

        $lastPostId = $this->getLastPostId();
        $crawler = $client->request('GET', '/EditPost/'.$lastPostId);
        $this->assertResponseIsSuccessful();

        //отправляем обновленные данные
        $form = $crawler->selectButton('Submit')->form();
        $form['post_form[Short]'] = $short;
        $form['post_form[Text]'] = $text;
        $form['post_form[Author]'] = $author;
        $client->submit($form);

        // проверка что данные изменились правильно
        $this->assertDatabaseHas($lastPostId,'Short', $short);
        $this->assertDatabaseHas($lastPostId,'Text', $text);
        $this->assertDatabaseHas($lastPostId,'Author', $author);

        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        // проверка записи на главной странице, актуально пока нет пагинации
        $this->assertSelectorTextContains('body', $short);
        $this->assertSelectorTextContains('body', $author);
    }
    public function test_ShowPost()
    {
        $client = static::createClient();

        $lastPostId = $this->getLastPostId();
        $crawler = $client->request('GET', '/ShowPost/'.$lastPostId);
        $this->assertResponseIsSuccessful();

        $post = $this->getPostById($lastPostId);

        $this->assertSelectorTextContains('body', $post->getShort());
        $this->assertSelectorTextContains('body', $post->getText());
        $this->assertSelectorTextContains('body', $post->getAuthor());
    }
    public function test_DeletePost()
    {
        $client = static::createClient();

        $lastPostId = $this->getLastPostId();
        $crawler = $client->request('GET', '/DeletePost/'.$lastPostId);
        $this->assertResponseRedirects('/');

        // проверяем есть ли пост в базе
        $this->assertTrue(NULL==$this->getPostById($lastPostId));
    }

}
