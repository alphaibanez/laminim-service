<?php

namespace Lkt\Instances;

use Lkt\Enums\AccessTokenDuration;
use Lkt\Enums\AccessTokenPurpose;
use Lkt\Factory\Schemas\Schema;
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
        $schema = Schema::get(static::COMPONENT);
        $query = $schema->getQueryBuilder()
            ->andStringEqual('token', $token);

        if ($purpose) {
            $query->andIntegerEqual('purpose', $purpose->value);
        }

        return static::getOne($query);
    }

    public static function fromUser(LktUser $user, AccessTokenPurpose|null $purpose = null): ?static
    {
        $schema = Schema::get(static::COMPONENT);
        $query = $schema->getQueryBuilder()
            ->andIntegerEqual('user_id', $user->getId());

        if ($purpose) {
            $query->andIntegerEqual('purpose', $purpose->value);
        }

        return static::getOne($query);
    }

    public static function createChangePasswordAccessToken(LktUser $user, \DateTime $expiresAt): static
    {
        $schema = Schema::get(static::COMPONENT);
        $previousToken = static::getOne(
            $schema->getQueryBuilder()
                ->andIntegerEqual('user_id', $user->getId())
                ->andIntegerEqual('purpose', AccessTokenPurpose::ChangePassword->value)
                ->andIntegerEqual('duration', AccessTokenDuration::Temporary->value)
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
        $schema = Schema::get(static::COMPONENT);
        $previousToken = static::getOne(
            $schema->getQueryBuilder()
                ->andIntegerEqual('user_id', $user->getId())
                ->andIntegerEqual('purpose', AccessTokenPurpose::Identifier->value)
                ->andIntegerEqual('duration', AccessTokenDuration::Temporary->value)
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

    public static function getActiveIdentifierAccessToken(LktUser $user, \DateTime $expiresAt): static
    {
        $schema = Schema::get(static::COMPONENT);
        $previousToken = static::getOne(
            $schema->getQueryBuilder()
                ->andIntegerEqual('user_id', $user->getId())
                ->andIntegerEqual('purpose', AccessTokenPurpose::Identifier->value)
                ->andIntegerEqual('duration', AccessTokenDuration::Temporary->value)
        );

        if ($previousToken) {
            return $previousToken
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