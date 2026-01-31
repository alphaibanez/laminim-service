<?php

namespace Lkt\Instances;

use Lkt\Connectors\MailConnector;
use Lkt\Generated\GeneratedLktPendingMail;
use Lkt\Mailing\Enums\QueuePriority;
use function Lkt\Tools\Parse\clearInput;
use function Lkt\Tools\Strings\sanitizeMailContent;

class LktPendingMail extends GeneratedLktPendingMail
{
    const COMPONENT = 'lkt-pending-mail';

    public function send(MailConnector $connector, string $from = ''): bool
    {
        if ($connector->mailFromPHPMailer($this->getEmail(), $this->getSubject(), $this->getMessage(), $from)->deliveryIsFailWithPHPMailer()) {
            return $connector->mailFromServer($this->getEmail(), $this->getSubject(), $this->getSubject(), $from)->deliveryIsSuccessWithServer();
        }
        return true;
    }


    /**
     * @param string|array $to
     * @param string $subject
     * @param string $message
     * @return static[]|static|null
     */
    protected static function addMail(QueuePriority $priority,string|array $to, string $subject, string $message = ''): static|array|null
    {
        if (!is_array($to)) {
            $to = [$to];
        }

        $subject = clearInput($subject);
        $message = sanitizeMailContent(clearInput($message));

        $response = [];
        foreach ($to as $target) {
            $email = clearInput($target);

            $instance = static::getInstance();

            $instance
                ->setPriority($priority->value)
                ->setEmail($email)
                ->setSubject($subject)
                ->setMessage($message)
                ->save();

            $response[] = $instance;
        }

        $l = count($response);

        switch ($l) {
            case 0:
                return null;

            case 1:
                return $response[0];
        }

        return $response;
    }


    /**
     * @param string|array $to
     * @param string $subject
     * @param string $message
     * @return static[]|static|null
     */
    public static function lowPriority(string|array $to, string $subject, string $message = ''): static|array|null
    {
        return static::addMail(QueuePriority::Low, $to, $subject, $message);
    }


    /**
     * @param string|array $to
     * @param string $subject
     * @param string $message
     * @return static[]|static|null
     */
    public static function mediumPriority(string|array $to, string $subject, string $message = ''): static|array|null
    {
        return static::addMail(QueuePriority::Medium, $to, $subject, $message);
    }


    /**
     * @param string|array $to
     * @param string $subject
     * @param string $message
     * @return static[]|static|null
     */
    public static function highPriority(string|array $to, string $subject, string $message = ''): static|array|null
    {
        return static::addMail(QueuePriority::High, $to, $subject, $message);
    }

    /**
     * @param string|array $to
     * @param string $subject
     * @param string $message
     * @return static[]|static|null
     */
    public static function urgentPriority(string|array $to, string $subject, string $message = ''): static|array|null
    {
        return static::addMail(QueuePriority::Urgent, $to, $subject, $message);
    }
}