<?php

namespace Lkt\Instances;

use Lkt\Enums\AccessTokenDuration;
use Lkt\Enums\AccessTokenPurpose;
use Lkt\Generated\GeneratedLktAccessToken;

class LktAccessToken extends GeneratedLktAccessToken
{
    const COMPONENT = 'lkt-access-token';

    private static function mkToken(): string
    {
        return hash('sha256', bin2hex(random_bytes(32)));
    }

    public static function fromToken(string $token, AccessTokenPurpose|null $purpose = null): ?static
    {
        $query = static::getQueryCaller()
            ->andTokenEqual($token);

        if ($purpose) {
            $query->andPurposeEqual($purpose->value);
        }

        return static::getOne($query);
    }

    public static function fromUser(LktUser $user, AccessTokenPurpose|null $purpose = null): ?static
    {
        $query = static::getQueryCaller()
            ->andUserEqual($user->getId());

        if ($purpose) {
            $query->andPurposeEqual($purpose->value);
        }

        return static::getOne($query);
    }

    public static function createChangePasswordAccessToken(LktUser $user, \DateTime $expiresAt): static
    {
        $previousToken = static::getOne(
            static::getQueryCaller()
                ->andUserEqual($user->getId())
                ->andPurposeEqual(AccessTokenPurpose::ChangePassword->value)
                ->andDurationEqual(AccessTokenDuration::Temporary->value)
        );

        if ($previousToken) {
            return $previousToken
                ->setToken(static::mkToken())
                ->setExpiresAt($expiresAt)
                ->save();
        }

        return static::getInstance()
            ->setUserId($user->getId())
            ->setToken(static::mkToken())
            ->setExpiresAt($expiresAt)
            ->setPurpose(AccessTokenPurpose::ChangePassword->value)
            ->setDuration(AccessTokenDuration::Temporary->value)
            ->save();
    }

    public static function createIdentifierAccessToken(LktUser $user, \DateTime $expiresAt): static
    {
        $previousToken = static::getOne(
            static::getQueryCaller()
                ->andUserEqual($user->getId())
                ->andPurposeEqual(AccessTokenPurpose::Identifier->value)
                ->andDurationEqual(AccessTokenDuration::Temporary->value)
        );

        if ($previousToken) {
            return $previousToken
                ->setToken(static::mkToken())
                ->setExpiresAt($expiresAt)
                ->save();
        }

        return static::getInstance()
            ->setUserId($user->getId())
            ->setToken(static::mkToken())
            ->setExpiresAt($expiresAt)
            ->setPurpose(AccessTokenPurpose::Identifier->value)
            ->setDuration(AccessTokenDuration::Temporary->value)
            ->save();
    }
}