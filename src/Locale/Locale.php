<?php

namespace Lkt\Locale;

use Lkt\Locale\Enums\CountryLangCode;
use Lkt\Locale\Enums\CurrencyCode;
use Lkt\Locale\Enums\LangCode;

class Locale
{
    protected static LangCode|string $langCode = LangCode::English;
    protected static CountryLangCode|string $countryLangCode = CountryLangCode::EnglishFromGreatBritain;

    protected static $availableLangCodes = [];
    protected static $availableCurrencyCodes = [];
    public static CurrencyCode|string $baseCurrency = CurrencyCode::Euro;

    /**
     * @param string $langCode
     * @return void
     */
    public static function setLangCode(string|LangCode $langCode)
    {
        if (is_string($langCode)) {
            $attempt = LangCode::tryFrom($langCode);
            if ($attempt) {
                static::$langCode = $attempt;
                return;
            }
        }
        static::$langCode = $langCode;
    }

    public static function getLangCode(): string
    {
        if (is_string(static::$langCode)) return static::$langCode;
        return static::$langCode->value;
    }

    public static function setCountryLangCode(string $langCode): void
    {
        static::$countryLangCode = $langCode;
    }

    public static function getCountryLangCode(): string
    {
        if (is_string(static::$countryLangCode)) return static::$countryLangCode;
        return static::$countryLangCode->value;
    }

    public static function detectLangCodeByCountry(string $countryCode): LangCode|null
    {
        $q = strtolower($countryCode);
        $locale = LangCode::tryFrom($q);
        if ($locale) return $locale;

        $country = CountryLangCode::tryFrom($q);
        if ($country) {
            $lang = explode('-', $country->value)[0];
            $locale = LangCode::tryFrom($lang);
            if ($locale) return $locale;
        }
        return null;
    }

    public static function setAvailableLangCodes(array $codes)
    {
        static::$availableLangCodes = [];
        foreach ($codes as $code) {
            if (is_string($code)) {
                $attempt = LangCode::tryFrom($code);
                if ($attempt) {
                    static::$availableLangCodes[] = $attempt;
                    return;
                }
            } elseif ($code instanceof LangCode) {
                static::$availableLangCodes[] = $code;
            }
        }
    }

    /**
     * @return LangCode[]
     */
    public static function getAvailableLangCodes(): array
    {
        return static::$availableLangCodes;
    }

    public static function setAvailableCurrencyCodes(array $codes)
    {
        static::$availableCurrencyCodes = [];
        foreach ($codes as $code) {
            if (is_string($code)) {
                $attempt = CurrencyCode::tryFrom($code);
                if ($attempt) {
                    static::$availableCurrencyCodes[] = $attempt;
                    return;
                }
            } elseif ($code instanceof CurrencyCode) {
                static::$availableCurrencyCodes[] = $code;
            }
        }
    }

    /**
     * @return CurrencyCode[]
     */
    public static function getAvailableCurrencyCodes(): array
    {
        return static::$availableCurrencyCodes;
    }
}