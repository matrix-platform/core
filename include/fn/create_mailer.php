<?php //>

use PHPMailer\PHPMailer\PHPMailer;

return function ($receiver, $subject, $body, $options) {
    $mailer = new PHPMailer();

    $mailer->Host = $options['host'];
    $mailer->Port = $options['port'];
    $mailer->SMTPAuth = true;
    $mailer->Username = $options['username'];
    $mailer->Password = $options['password'];

    if ($options['secure']) {
        $mailer->SMTPSecure = $options['secure'];
    } else {
        $mailer->SMTPAutoTLS = false;
    }

    $mailer->isHTML(true);
    $mailer->isSMTP();

    $mailer->CharSet = 'utf-8';
    $mailer->From = $options['username'];
    $mailer->FromName = $options['from'];
    $mailer->Subject = $subject;
    $mailer->Body = $body;

    foreach (preg_split('/[\s;,]/', $receiver, 0, PREG_SPLIT_NO_EMPTY) as $to) {
        $mailer->addBCC($to);
    }

    return $mailer;
};
