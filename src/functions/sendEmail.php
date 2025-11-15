<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function sendMail($receiverMail, $receiverName, $subject, $htmlBody, $plainBody) {
    $smtpPassword = $_ENV['smtpPassword'];

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.zeptomail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'emailappsmtp.2dd0b785541be1af'; 
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('noreply@rendercores.com', 'EnderDeploy - Noreply');
        $mail->addAddress(htmlspecialchars($receiverMail), htmlspecialchars($receiverName));

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("SEND_MAIL_ERROR: Fallo al enviar a {$receiverMail}. PHPMailer Error: {$mail->ErrorInfo}");
        throw $e;
        return false;
    }
}
?>