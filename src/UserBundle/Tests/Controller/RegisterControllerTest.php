<?php

namespace EventBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    public function testRegister()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Register', $response->getContent());

        $usernameVal = $crawler
            ->filter('#user_register_username')
            ->attr('value');

        $this->assertEquals('put your username here', $usernameVal);

        $form = $crawler->selectButton('Register')->form();
        $form['user_register[username]'] = 'user65';
        $form['user_register[email]'] = 'user65@mail.com';
        $form['user_register[plainPassword][first]'] = 'passUser65';
        $form['user_register[plainPassword][second]'] = 'passUser65';
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
        $this->assertContains(
            'Congratulations you are now registered',
            $client->getResponse()->getContent()
        );
    }
}
