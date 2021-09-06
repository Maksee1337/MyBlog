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

        $name = uniqid();
        $text = uniqid();
        $email = uniqid().'@gmail.com';

        $form = $crawler->selectButton('Submit')->form();
        $form['feedback_form[Name]'] = $name;
        $form['feedback_form[Text]'] = $text;
        $form['feedback_form[Email]'] = $email;
        $client->submit($form);

        $this->assertResponseRedirects('/');

    }
}
