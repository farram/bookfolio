<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testRegister(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/signup');

        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Créez votre book gratuitement');
        $form = $button->form();
        $form['registration_form[profession]']->setValue("1");
        $form['registration_form[experience]']->setValue("");
        $form['registration_form[email]']->setValue(sprintf('foo%s@example.com', rand()));
        $form['registration_form[plainPassword]']->setValue('Gabriel2012?M');
        $form['registration_form[termsAccepted]']->tick();
        $client->submit($form);
        //$this->assertResponseRedirects('/');
    }
}
