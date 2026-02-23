<?php

namespace Lkt\Factory\Export\DTO;

use Lkt\Locale\Enums\LangCode;
use Lkt\Locale\Locale;
use Lkt\Translations\Translations;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class TranslationsDataExport extends AbstractDataExport
{
    readonly public array $header;
    readonly public array $rows;

    /**
     * @param array[] $items
     */
    protected function __construct(array $items)
    {
        $header = array_keys($items);
        $rows = [];
        $keys = [];
        $languages = Locale::getAvailableLangCodes();
        $langKeys = array_map(function (LangCode $lang) { return $lang->value; }, $languages);

        foreach ($items as $content){
            $arrayKeys = array_keys($content);
            foreach ($arrayKeys as $arrayKey){
                if (!in_array($arrayKey, $keys, true)){
                    $keys[] = $arrayKey;
                }
            }
        }

        sort($keys);

        foreach ($keys as $key){
            $row = [$key];
            foreach ($langKeys as $lang) $row[] = trim($items[$lang][$key]);
            $rows[] = $row;
        }


        $this->rows = $rows;
        $this->header = count($header) === 0 ? [] : ['key', ...$langKeys];
    }

    public function getSpreadsheet(Properties|null $properties = null): Spreadsheet
    {
        if (!$properties) $properties = new Properties();

        if ($properties->getTitle() === 'Untitled Spreadsheet') {
            $moment = date('Y-m-d H:i:s');
            $properties->setTitle("Translations at {$moment}");
        }

        return parent::getSpreadsheet($properties);
    }

    /**
     * @return static
     */
    public static function all(): static
    {
        return new static(Translations::export());
    }

    /**
     * @return static
     */
    public static function missing(): static
    {
        return new static(Translations::getMissedTranslations());
    }
}