<?php

namespace App\Tests\Controller;

use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package App\Tests\Controller
 */
class DefaultControllerTest extends WebTestCase
{
    public function test_Index()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
     //   echo $client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Homepage');
    }

    public function test_About()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/About');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Homepage');
    }

    public function test_Feedback()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/Feedback');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Homepage');
    }
}
