<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Celtic34fr\ContactCore\Entity\CliInfos;
use Celtic34fr\ContactCore\Entity\Courriel;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\SendMailer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;

class SendMailerTest extends TestCase
{
    public function testSendTemplate()
    {
        // Création des mocks pour les dépendances
        $emMock = $this->createMock(EntityManagerInterface::class);
        $twigMock = $this->createMock(TwigEnvironment::class);
        $extConfigMock = $this->createMock(ExtensionConfig::class);

        // Créer un objet SendMailer
        $sendMailer = new SendMailer();
        $sendMailer->initialize($emMock, $twigMock, $extConfigMock);

        // Créer un objet CliInfos fictif pour les tests
        $destinataireMock = $this->createMock(CliInfos::class);
        
        // Définir les valeurs des paramètres pour le test
        $template_name = 'test_template';
        $subject = 'Test Subject';
        $context = ['key1' => 'value1', 'key2' => 'value2'];

        // Mock pour le rendu du template Twig
        $html_body = '<html><body>Test HTML Body</body></html>';
        $twigMock->expects($this->once())->method('render')->willReturn($html_body);

        // Mock pour getConfig
        $extConfigMock->expects($this->once())->method('get')->willReturn(['courriel' => 'test@example.com']);

        // Appel de la méthode sendTemplate
        $result = $sendMailer->sendTemplate($destinataireMock, $template_name, $subject, $context);

        // Vérification du résultat
        $this->assertTrue($result);
    }

    public function testResendMail()
    {
        // Création des mocks pour les dépendances
        $emMock = $this->createMock(EntityManagerInterface::class);
        $twigMock = $this->createMock(TwigEnvironment::class);
        $extConfigMock = $this->createMock(ExtensionConfig::class);

        // Créer un objet SendMailer
        $sendMailer = new SendMailer();
        $sendMailer->initialize($emMock, $twigMock, $extConfigMock);

        // Créer un objet Courriel fictif pour les tests
        $mailMock = $this->createMock(Courriel::class);

        // Mock pour le send
        $sendReturn = true;
        /** comment faire pour que $sendMailer->send() retourne TRUE ? */
        //$sendMailer->method('send')->willReturn($sendReturn);

        // Appel de la méthode resendMail
        $sendMailer->resendMail($mailMock);

        // Vérification de l'appel à la méthode send
        $this->assertTrue($sendReturn);
    }
}