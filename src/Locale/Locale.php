<?php

namespace Lkt\Locale;

use Lkt\Locale\Enums\CountryLangCode;
use Lkt\Locale\Enums\LangCode;

class Locale
{
    protected static LangCode|string $langCode = LangCode::English;
    protected static CountryLangCode|string $countryLangCode = CountryLangCode::EnglishFromGreatBritain;

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
}