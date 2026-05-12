<?php

namespace App\Classe;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mail
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($to_email, $to_name, $subject, $content)
    {
        $htmlContent = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
                    <h1 style="color: white; margin: 0;">Maisons d\'hôtes</h1>
                </div>
                <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
                    <h2 style="color: #333;">Réinitialisation de mot de passe</h2>
                    ' . $content . '
                </div>
            </div>
        ';

        $email = (new Email())
            ->from('bahrihajer14@gmail.com')
            ->to($to_email)
            ->subject($subject)
            ->html($htmlContent)
            ->text(strip_tags($content));

        $this->mailer->send($email);
    }
}
