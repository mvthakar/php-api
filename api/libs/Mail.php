<?php

require pathOf('libs/PHPMailer/src/Exception.php');
require pathOf('libs/PHPMailer/src/PHPMailer.php');
require pathOf('libs/PHPMailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public static function send($to, $subjet, $body)
    {
        $mail = new PHPMailer(true);

        try 
        {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();

            $mail->Host = 'smtp-mail.outlook.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'whatever_whatever_@outlook.com';
            $mail->Password = 'WhateverWhatever@121223';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            $mail->setFrom('whatever_whatever_@outlook.com', 'API');
            $mail->addAddress($to);
        
            $mail->isHTML(true);
            $mail->Subject = $subjet;
            $mail->Body = $body;
        
            $mail->send();
        } 
        catch (Exception $e) 
        {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}