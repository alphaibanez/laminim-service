<?php

namespace Lkt\Factory\Export\DTO;

use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

abstract class AbstractDataExport
{
    readonly public array $header;
    readonly public array $rows;

    public function getSpreadsheet(Properties|null $properties = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        if ($properties) $spreadsheet->setProperties($properties);

        $data = [...[$this->header], ...$this->rows];

        foreach ($data as $i => $row) {
            $index = $i + 1;

            $j = 0;
            $l = -1;

            foreach ($row as $item) {
                if (trim($item) === '') continue;
                if ($j > 25) {
                    ++$l;
                    $j = 0;
                }

                $pointer = [];
                if ($l >= 0) $pointer[] = chr(65 + $l);
                $pointer[] = chr(65 + $j);
                $pointer[] = $index;
                $pointer = implode('', $pointer);

                $spreadsheet
                    ->setActiveSheetIndex(0)
                    ->setCellValue($pointer, mb_convert_encoding($item, 'UTF-8', 'UTF-8'));
                ++$j;
            }
        }

        return $spreadsheet;
    }

    public function getCsvWriter(Properties|null $properties = null, Spreadsheet|null $spreadsheet = null): Csv
    {
        if (!$spreadsheet) $spreadsheet = $this->getSpreadsheet($properties);
        return new Csv($spreadsheet);
    }
}