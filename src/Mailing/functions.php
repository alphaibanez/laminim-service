<?php

namespace Lkt\Mailing;

use Lkt\Connectors\MailConnector;

function sendMail(
    string|array $to,
    string $subject = '',
    string $text = '',
    string $from = '',
    string $connectorName  ='default'
): bool
{
    $connector = MailConnector::get($connectorName);

    if (!is_array($to)) $to = [$to];
    $status = true;
    foreach ($to as $email) {
        $firstAttempt = $connector->mailFromPHPMailer($email, $subject, $text, $from)->deliveryIsFailWithPHPMailer();

        if (!$firstAttempt) {
            $secondAttempt = $connector->mailFromServer($email, $subject, $text, $from)->deliveryIsSuccessWithServer();

            if (!$secondAttempt) $status = false;
        }
    }
    return $status;
}