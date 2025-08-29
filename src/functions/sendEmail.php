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
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'administrador@endercores.com'; 
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('administrador@endercores.com', 'EnderDeploy - Noreply');
        $mail->addAddress(htmlspecialchars($receiverMail), htmlspecialchars($receiverName));

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainBody;

        $mail->send();
        echo 'Correo enviado correctamente.';
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>