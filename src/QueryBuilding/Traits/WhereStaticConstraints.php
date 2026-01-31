<?php

namespace Lkt\QueryBuilding\Traits;

use Lkt\QueryBuilding\Constraints\ConcatBeginsLikeConstraint;
use Lkt\QueryBuilding\Constraints\ConcatEndsLikeConstraint;
use Lkt\QueryBuilding\Constraints\ConcatEqualConstraint;
use Lkt\QueryBuilding\Constraints\ConcatLikeConstraint;
use Lkt\QueryBuilding\Constraints\ConcatNotBeginsLikeConstraint;
use Lkt\QueryBuilding\Constraints\ConcatNotConstraint;
use Lkt\QueryBuilding\Constraints\ConcatNotEndsLikeConstraint;
use Lkt\QueryBuilding\Constraints\ConcatNotLikeConstraint;
use Lkt\QueryBuilding\DateIntervals\AbstractInterval;

trait WhereStaticConstraints
{
    public static function booleanFalse(string $column)
    {
        return (new static())->andBooleanFalse($column);
    }

    public static function booleanTrue(string $column)
    {
        return (new static())->andBooleanTrue($column);
    }

    public static function datetimeBetween(string $column, $from, $to)
    {
        return (new static())->andDatetimeBetween($column, $from, $to);
    }

    public static function datetimeEqual(string $column, $value)
    {
        return (new static())->andDatetimeEqual($column, $value);
    }

    public static function datetimeNot(string $column, $value)
    {
        return (new static())->andDatetimeNot($column, $value);
    }

    public static function datetimeGreaterOrEqualThan(string $column, $value, AbstractInterval $interval = null)
    {
        return (new static())->andDatetimeGreaterOrEqualThan($column, $value, $interval);
    }

    public static function datetimeGreaterOrEqualThanNow(string $column, AbstractInterval $interval = null)
    {
        return (new static())->andDatetimeGreaterOrEqualThanNow($column, $interval);
    }

    public static function datetimeGreaterThan(string $column, $value, AbstractInterval $interval = null)
    {
        return (new static())->andDatetimeGreaterThan($column, $value, $interval);
    }

    public static function datetimeGreaterThanNow(string $column, AbstractInterval $interval = null)
    {
        return (new static())->andDatetimeGreaterThanNow($column, $interval);
    }

    public static function datetimeLowerOrEqualThan(string $column, $value, AbstractInterval $interval = null)
    {
        return (new static())->andDatetimeLowerOrEqualThan($column, $value, $interval);
    }

    public static function datetimeLowerOrEqualThanNow(string $column, AbstractInterval $interval = null)
    {
        return (new static())->andDatetimeLowerOrEqualThanNow($column, $interval);
    }

    public static function datetimeLowerThan(string $column, $value, AbstractInterval $interval = null)
    {
        return (new static())->andDatetimeLowerThan($column, $value, $interval);
    }

    public static function datetimeLowerThanNow(string $column, AbstractInterval $interval = null)
    {
        return (new static())->andDatetimeLowerThanNow($column, $interval);
    }

    public static function datetimeLike(string $column, string $value)
    {
        return (new static())->andDatetimeLike($column, $value);
    }

    public static function datetimeBeginsLike(string $column, string $value)
    {
        return (new static())->andDatetimeBeginsLike($column, $value);
    }

    public static function datetimeEndsLike(string $column, string $value)
    {
        return (new static())->andDatetimeEndsLike($column, $value);
    }

    public static function datetimeNotLike(string $column, string $value)
    {
        return (new static())->andDatetimeNotLike($column, $value);
    }

    public static function datetimeNotBeginsLike(string $column, string $value)
    {
        return (new static())->andDatetimeNotBeginsLike($column, $value);
    }

    public static function datetimeNotEndsLike(string $column, string $value)
    {
        return (new static())->andDatetimeNotEndsLike($column, $value);
    }

    public static function decimalBetween(string $column, $from, $to)
    {
        return (new static())->andDecimalBetween($column, $from, $to);
    }

    public static function decimalEqual(string $column, float $value)
    {
        return (new static())->andDecimalEqual($column, $value);
    }

    public static function decimalGreaterOrEqualThan(string $column, float $value)
    {
        return (new static())->andDecimalGreaterOrEqualThan($column, $value);
    }

    public static function decimalGreaterThan(string $column, float $value)
    {
        return (new static())->andDecimalGreaterThan($column, $value);
    }

    public static function decimalIn(string $column, array $value)
    {
        return (new static())->andDecimalIn($column, $value);
    }

    public static function decimalLowerOrEqualThan(string $column, float $value)
    {
        return (new static())->andDecimalLowerOrEqualThan($column, $value);
    }

    public static function decimalLowerThan(string $column, float $value)
    {
        return (new static())->andDecimalLowerThan($column, $value);
    }

    public static function decimalNot(string $column, float $value)
    {
        return (new static())->andDecimalNot($column, $value);
    }

    public static function decimalNotIn(string $column, array $value)
    {
        return (new static())->andDecimalNotIn($column, $value);
    }

    public static function foreignKeysContains(string $column, $value)
    {
        return (new static())->andForeignKeysContains($column, $value);
    }

    public static function integerBetween(string $column, $from, $to)
    {
        return (new static())->andIntegerBetween($column, $from, $to);
    }

    public static function integerEqual(string $column, int $value)
    {
        return (new static())->andIntegerEqual($column, $value);
    }

    public static function integerGreaterOrEqualThan(string $column, int $value)
    {
        return (new static())->andIntegerGreaterOrEqualThan($column, $value);
    }

    public static function integerGreaterThan(string $column, int $value)
    {
        return (new static())->andIntegerGreaterThan($column, $value);
    }

    public static function integerIn(string $column, array $value)
    {
        return (new static())->andIntegerIn($column, $value);
    }

    public static function integerLowerOrEqualThan(string $column, int $value)
    {
        return (new static())->andIntegerLowerOrEqualThan($column, $value);
    }

    public static function integerLowerThan(string $column, int $value)
    {
        return (new static())->andIntegerLowerThan($column, $value);
    }

    public static function integerNot(string $column, int $value)
    {
        return (new static())->andIntegerNot($column, $value);
    }

    public static function integerNotIn(string $column, array $value)
    {
        return (new static())->andIntegerNotIn($column, $value);
    }

    public static function isNotNull(string $column)
    {
        return (new static())->andIsNotNull($column);
    }

    public static function isNull(string $column)
    {
        return (new static())->andIsNull($column);
    }

    public static function raw(string $value)
    {
        return (new static())->andRaw($value);
    }

    public static function stringBeginsLike(string $column, string $value)
    {
        return (new static())->andStringBeginsLike($column, $value);
    }

    public static function stringEndsLike(string $column, string $value)
    {
        return (new static())->andStringEndsLike($column, $value);
    }

    public static function stringEqual(string $column, string $value)
    {
        return (new static())->andStringEqual($column, $value);
    }

    public static function stringIn(string $column, array $value)
    {
        return (new static())->andStringIn($column, $value);
    }

    public static function stringLike(string $column, string $value)
    {
        return (new static())->andStringLike($column, $value);
    }

    public static function stringNotBeginsLike(string $column, string $value)
    {
        return (new static())->andStringNotBeginsLike($column, $value);
    }

    public static function stringNot(string $column, string $value)
    {
        return (new static())->andStringNot($column, $value);
    }

    public static function stringNotEndsLike(string $column, string $value)
    {
        return (new static())->andStringNotEndsLike($column, $value);
    }

    public static function stringNotIn(string $column, array $value)
    {
        return (new static())->andStringNotIn($column, $value);
    }

    public static function stringNotLike(string $column, string $value)
    {
        return (new static())->andStringNotLike($column, $value);
    }

    public static function i18nStringBeginsLike(string $column, string $value)
    {
        return (new static())->andI18nStringBeginsLike($column, $value);
    }

    public static function i18nStringEndsLike(string $column, string $value)
    {
        return (new static())->andI18nStringEndsLike($column, $value);
    }

    public static function i18nStringEqual(string $column, string $value)
    {
        return (new static())->andI18nStringEqual($column, $value);
    }

    public static function i18nStringIn(string $column, array $value)
    {
        return (new static())->andI18nStringIn($column, $value);
    }

    public static function i18nStringLike(string $column, string $value)
    {
        return (new static())->andI18nStringLike($column, $value);
    }

    public static function i18nStringNotBeginsLike(string $column, string $value)
    {
        return (new static())->andI18nStringNotBeginsLike($column, $value);
    }

    public static function i18nStringNot(string $column, string $value)
    {
        return (new static())->andI18nStringNot($column, $value);
    }

    public static function i18nStringNotEndsLike(string $column, string $value)
    {
        return (new static())->andI18nStringNotEndsLike($column, $value);
    }

    public static function i18nStringNotIn(string $column, array $value)
    {
        return (new static())->andI18nStringNotIn($column, $value);
    }

    public static function i18nStringNotLike(string $column, string $value)
    {
        return (new static())->andI18nStringNotLike($column, $value);
    }

    public function concatEqual(array $columns, string $separator, string $value): self
    {
        $this->and[] = ConcatEqualConstraint::defineConcat($columns, $separator, $value);
        return $this;
    }

    public function concatLike(array $columns, string $separator, string $value): self
    {
        $this->and[] = ConcatLikeConstraint::defineConcat($columns, $separator, $value);
        return $this;
    }

    public function concatBeginsLike(array $columns, string $separator, string $value): self
    {
        $this->and[] = ConcatBeginsLikeConstraint::defineConcat($columns, $separator, $value);
        return $this;
    }

    public function concatEndsLike(array $columns, string $separator, string $value): self
    {
        $this->and[] = ConcatEndsLikeConstraint::defineConcat($columns, $separator, $value);
        return $this;
    }

    public function concatNot(array $columns, string $separator, string $value): self
    {
        $this->and[] = ConcatNotConstraint::defineConcat($columns, $separator, $value);
        return $this;
    }

    public function concatNotLike(array $columns, string $separator, string $value): self
    {
        $this->and[] = ConcatNotLikeConstraint::defineConcat($columns, $separator, $value);
        return $this;
    }

    public function concatNotBeginsLike(array $columns, string $separator, string $value): self
    {
        $this->and[] = ConcatNotBeginsLikeConstraint::defineConcat($columns, $separator, $value);
        return $this;
    }

    public function concatNotEndsLike(array $columns, string $separator, string $value): self
    {
        $this->and[] = ConcatNotEndsLikeConstraint::defineConcat($columns, $separator, $value);
        return $this;
    }
}