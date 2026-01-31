<?php

namespace Lkt\Instances;

use donatj\UserAgent\UserAgentParser;
use Lkt\Generated\GeneratedLktAuthenticationLog;
use Lkt\Users\Enums\PerformedAuthAction;

class LktAuthenticationLog extends GeneratedLktAuthenticationLog
{
    const COMPONENT = 'lkt-authentication-log';


    final public static function logInvalidSignInAttempt(string $attemptedCredential, string $attemptedPassword, LktUser|null $user = null): static
    {
        $now = new \DateTime();
        $now->sub(\DateInterval::createFromDateString('10 minutes'));

        $dateLimit = $now->format('Y-m-d H:i:s');
        $counterQuery = static::getQueryCaller()->andCreatedAtGreaterOrEqualThan($dateLimit);

        $previousAttempts = static::getMany($counterQuery);

        $parser = new UserAgentParser();
        $ua = $parser->parse();

        $r = static::getInstance()
            ->setCreatedAt(time())
            ->setPerformedAction(PerformedAuthAction::SignIn)
            ->setAttemptedSuccessfully(false)
            ->setClientProtocol($_SERVER['SERVER_PROTOCOL'])
            ->setClientIPAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setClientBrowser($ua->browser())
            ->setClientBrowserVersion($ua->browserVersion())
            ->setClientOS($ua->platform())
            ->setAttemptedCredential($attemptedCredential)
            ->setAttemptedPassword($attemptedPassword)
            ->setAttemptsCounter(count($previousAttempts));

        if ($user instanceof LktUser) {
            $r
                ->setUserId($user->getId())
                ->setUserStatus($user->getStatus());
        }

        return $r->save();
    }

    final public static function logSuccessSignInAttempt(string $attemptedCredential, LktUser|null $user = null): static
    {
        $parser = new UserAgentParser();
        $ua = $parser->parse();

        $r = static::getInstance()
            ->setCreatedAt(time())
            ->setPerformedAction(PerformedAuthAction::SignIn)
            ->setAttemptedSuccessfully(true)
            ->setClientProtocol($_SERVER['SERVER_PROTOCOL'])
            ->setClientIPAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setClientBrowser($ua->browser())
            ->setClientBrowserVersion($ua->browserVersion())
            ->setClientOS($ua->platform())
            ->setAttemptedCredential($attemptedCredential)
            ->setAttemptedPassword('');

        if ($user) $r->setUserId($user->getId());

        return $r->save();
    }

    final public static function logRememberPassword(string $attemptedCredential, LktUser|null $user = null): static
    {
        $parser = new UserAgentParser();
        $ua = $parser->parse();

        $r = static::getInstance()
            ->setCreatedAt(time())
            ->setPerformedAction(PerformedAuthAction::RememberPassword)
            ->setAttemptedSuccessfully(is_object($user))
            ->setClientProtocol($_SERVER['SERVER_PROTOCOL'])
            ->setClientIPAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setClientBrowser($ua->browser())
            ->setClientBrowserVersion($ua->browserVersion())
            ->setClientOS($ua->platform())
            ->setAttemptedCredential($attemptedCredential)
            ->setAttemptedPassword('');

        if ($user) $r->setUserId($user->getId());

        return $r->save();
    }

    final public static function logSignUp(string $attemptedCredential, LktUser|null $user = null): static
    {
        $parser = new UserAgentParser();
        $ua = $parser->parse();

        $r = static::getInstance()
            ->setCreatedAt(time())
            ->setPerformedAction(PerformedAuthAction::SignUp)
            ->setAttemptedSuccessfully(!is_object($user))
            ->setClientProtocol($_SERVER['SERVER_PROTOCOL'])
            ->setClientIPAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setClientBrowser($ua->browser())
            ->setClientBrowserVersion($ua->browserVersion())
            ->setClientOS($ua->platform())
            ->setAttemptedCredential($attemptedCredential)
            ->setAttemptedPassword('');

        if ($user) $r->setUserId($user->getId());

        return $r->save();
    }
}