<?php

namespace Celtic34fr\ContactCore\Service;

use Celtic34fr\ContactCore\Entity\CliInfos;
use Celtic34fr\ContactCore\Entity\Courriel;
use Celtic34fr\ContactCore\Entity\PieceJointe;
use Celtic34fr\ContactCore\Enum\CourrielEnums;
use Celtic34fr\ContactCore\Enum\StatusCourrielEnums;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/** classe en charge de la gestion des envois de courriels */
class SendMailer
{
    private ?Mailer $mailer;
    private EntityManagerInterface $em;
    private TwigEnvironment $twig;
    private ExtensionConfig $extConfig;

    /**
     * @param EntityManagerInterface $em
     * @param TwigEnvironment $twig
     * @param ExtensionConfig $extConfig
     * @return void
     */
    public function initialize(EntityManagerInterface $em, TwigEnvironment $twig, ExtensionConfig $extConfig)
    {
        $mailerParms = $extConfig->get("celtic34fr-ContactCore/mailer");
        $dsn = "smtp://" .
            ($mailerParms['user'] ? $mailerParms['user'] . ($mailerParms['pwd'] ? ":" . $mailerParms['pwd'] : "") : "");
        $dsn .= "@" . $mailerParms['host'] . ":" . $mailerParms['port'];
        $dsn .= ($mailerParms['encryption'] ?? "");
        $this->mailer = new Mailer(Transport::fromDsn($dsn));
        $this->em = $em;
        $this->twig = $twig;
        $this->extConfig = $extConfig;
    }

    /**
     * @param RawMessage $message
     * @param Envelope|null $envelope
     * @return boolean
     */
    private function send(RawMessage $message, Envelope $envelope = null): bool
    {
        try {
            if (!$this->mailer) return false;
            $this->mailer->send($message, $envelope);
        } catch (TransportExceptionInterface $e) {
            return false;
        }
        return true;
    }

    /**
     * @param CliInfos $destinataire
     * @param string $template_name
     * @param string $subject
     * @param array $context
     * @param bool $resend
     * @return bool
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendTemplate(CliInfos $destinataire, string $template_name, string $subject, array $context): bool
    {
        $html_body = $this->twig->render($template_name, array_merge($context, ["destinataire" => $destinataire]));
        $info_company = $this->extConfig->get('celtic34fr-contact-core/entreprise');
        $from_contact = $info_company['courriel'] ?? $this->extConfig->get('celtic34fr-contact-core/courriel_reponse');
        $email = (new Email())
            ->from(new Address($from_contact))
            ->to(new Address($destinataire->getClient()->getCourriel(), $destinataire->getFullName()))
            ->subject($subject)->html($html_body)->text(strip_tags($html_body));
        if (array_key_exists('attachments', $context)) {
            $nb_elt = 0;
            foreach ($context['attachments'] as $attachment) {
                $nb_elt++;
                /** @var PieceJointe $file */
                $file = $this->em->getRepository(PieceJointe::class)->find($attachment);
                $email->attach($file->getFileContent(), "pj_" . $nb_elt, $file->getFileMime());
            }
        }
        $res = $this->send($email);
        /** enregistrement systématique des mails envoyés réunion du 2022/08/18 */
        /** resend : true si revoi de courriel, false si envoi initial */
        $mail = new Courriel();
        $mail->setContextCourriel($context)->setSujet($subject)->setTemplateCourriel($template_name)
            ->setDestinataire($destinataire)->setCreatedAt(new DateTimeImmutable('now'))
            ->setSendTimes(1)->setNature(CourrielEnums::Contact->_toString());
        if ($res) {
            $mail->setSendAt(new DateTimeImmutable('now'));
            $mail->setSendStatus(StatusCourrielEnums::Send->_toString());
        } else {
            $mail->setSendStatus(StatusCourrielEnums::Error->_toString());
        }
        $this->em->persist($mail);
        $this->em->flush();
        return $res;
    }

    /**
     * @param Courriel $mail
     * @return void
     */
    public function resendMail(Courriel $mail)
    {
        $destinataire = $mail->getDestinataire();

        $email = (new Email())
            ->from(new Address('no-reply@example.com'))
            ->to(new Address($destinataire->getClient()->getCourriel(), $destinataire->getFullName()))
            ->subject($mail->getSujet())
            ->htmlTemplate($mail->getTemplateCourriel())
            ->context(array_merge($mail->getContextCourriel(), ["destinataire" => $destinataire]));

        if ($this->send($email)) {
            $mail->setSendAt(new DateTimeImmutable());
        }
    }
}
