<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $replyTo, $subject, $body)
{
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $return = [
        "success" => false,
        "error" => ''
    ];

    try {
        //Recipients
        $mail->setFrom('kp.mail.pin@gmail.com', 'Localement Suisse');
        $mail->addAddress($to['address'], $to['name']);     //Add a recipient
        //$mail->addAddress('ellen@example.com');               //Name is optional
        $mail->addReplyTo($replyTo['address'], $replyTo['name']);

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body['html'];
        $mail->AltBody = $body['alt'];

        $mail->send();
        $return["success"] = true;
    } catch (Exception $e) {
        $return["error"] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    return $return;
}