<?php //>

use PHPMailer\PHPMailer\PHPMailer;

return function ($options) {
    $mailer = new PHPMailer();

    $mailer->CharSet = 'utf-8';
    $mailer->From = $options['username'];
    $mailer->Host = 'smtp.gmail.com';
    $mailer->Password = $options['password'];
    $mailer->Port = 465;
    $mailer->SMTPAuth = true;
    $mailer->SMTPSecure = 'ssl';
    $mailer->Username = $options['username'];

    $mailer->isHTML(true);
    $mailer->isSMTP();

    $mailer->FromName = $options['from'];
    $mailer->Subject = render($options['subject'], $options);
    $mailer->Body = render($options['content'], $options);

    foreach (preg_split('/[\s;,]/', $options['to'], 0, PREG_SPLIT_NO_EMPTY) as $to) {
        $mailer->AddAddress($to);
    }

    return $mailer->Send();
};
