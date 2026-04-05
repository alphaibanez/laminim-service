<?php

namespace Lkt\Instances;

use donatj\UserAgent\UserAgentParser;
use Lkt\Config\Settings\ContactSettings;
use Lkt\Enums\TimeInSeconds;
use Lkt\Exceptions\SilentHttpException;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Generated\GeneratedLktContactRequest;
use Lkt\Http\Networking\Networking;
use function Lkt\Tools\Parse\clearInput;

class LktContactRequest extends GeneratedLktContactRequest
{
    const COMPONENT = 'lkt-contact-request';

    protected function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        $networking = Networking::getInstance();

        if (is_numeric(ContactSettings::$maxRequestPerIp) && ContactSettings::$maxRequestPerIp > 0) {
            $now = new \DateTime();
            $time = ContactSettings::$maxRequestPeriod ?? TimeInSeconds::OneDay->value;
            $now->sub(\DateInterval::createFromDateString("{$time} seconds"));

            $dateLimit = $now->format('Y-m-d H:i:s');

            $counterQuery = static::getQueryCaller()
                ->andClientIPAddressEqual($networking->remoteAddress)
                ->andClientUserAgentEqual($networking->userAgent)
                ->andCreatedAtGreaterOrEqualThan($dateLimit);

            $previousAttempts = count(static::getMany($counterQuery));

            if ($previousAttempts >= ContactSettings::$maxRequestPerIp) {
                throw SilentHttpException::getInstance('max-contact-requests-reached');
            }
        }

        $parser = new UserAgentParser();
        $ua = $parser->parse();

        $name = clearInput($data['name']);
        $email = clearInput($data['email']);
        $message = clearInput($data['message']);
        $contactReason = (int)$data['contactReason'];

        $this->setAccessPolicy('create', AccessPolicyEndOfLife::UntilNextWrite);

        return [
            'createdAt' => time(),
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'contactReason' => $contactReason,
            'clientProtocol' => $networking->httpProtocol,
            'clientIPAddress' => $networking->remoteAddress,
            'clientUserAgent' => $networking->userAgent,
            'clientBrowser' => $ua->browser(),
            'clientBrowserVersion' => $ua->browserVersion(),
            'clientOS' => $ua->platform(),
        ];
    }
}