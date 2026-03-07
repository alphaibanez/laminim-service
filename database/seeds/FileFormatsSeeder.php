<?php

use Phinx\Seed\AbstractSeed;
use Lkt\Instances\LktFileFormat;

class FileFormatsSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run() : void
    {
        LktFileFormat::createIfMissing([
            'name' => [
                'es' => 'CSV', 'en' => 'CSV',
            ],
            'description' => [
                'es' => 'En informática, un archivo de valores separados por comas (CSV) almacena datos tabulares (números y texto) en texto plano. Cada línea del archivo es un registro de datos. Cada registro consta de uno o más campos separados por comas. El uso de la coma como separador de campos es el origen del nombre de este formato de archivo.',
                'en' => 'In computing, a comma-separated values (CSV) file stores tabular data (numbers and text) in plain text. Each line of the file is a data record. Each record consists of one or more fields, separated by commas. The use of the comma as a field separator is the source of the name for this file format.',
            ]
        ]);

        LktFileFormat::createIfMissing([
            'name' => [
                'es' => 'ODS', 'en' => 'ODS',
            ],
            'description' => [
                'es' => 'Los archivos con extensión ODS corresponden al formato OpenDocument Spreadsheet Document, que puede ser editado por el usuario. Los datos se almacenan dentro del archivo ODF en filas y columnas. Es un formato basado en XML y es uno de los varios subtipos dentro de la familia de Open Document Formats (ODF). El formato está especificado como parte de las especificaciones ODF 1.2, publicadas y mantenidas por OASIS. Diversas aplicaciones en Windows y en otros sistemas operativos pueden abrir archivos ODS para su edición y manipulación, entre ellas Microsoft Excel, NeoOffice y LibreOffice. Los archivos ODS también pueden convertirse a otros formatos de hoja de cálculo, como XLS, XLSX y otros, mediante diferentes aplicaciones.',
                'en' => 'Files with ODS extension stand for OpenDocument Spreadsheet Document format that are editable by user. Data is stored inside ODF file into rows and columns. It is XML-based format and is one of the several subtypes in the Open Document Formats (ODF) family. The format is specified as part of the ODF 1.2 specifications published and maintained by OASIS. A number of applications on Windows as well as other operating systems can open ODS files for editing and manipulation including Microsoft Excel, NeoOffice and LibreOffice. ODS files can also be converted into other spreadsheet formats as well like XLS, XLSX and others by different applications.',
            ]
        ]);

        LktFileFormat::createIfMissing([
            'name' => [
                'es' => 'XLS', 'en' => 'XLS',
            ],
            'description' => [
                'es' => 'XLS es un archivo binario de libro de trabajo de MS Excel que contiene información sobre todas las hojas de cálculo de un libro, incluyendo tanto el contenido como el formato (formato de números, colores, formato condicional, etc.). También puede incluir elementos adicionales como gráficos, imágenes, etc. Los archivos XLS solo pueden ser leídos por aplicaciones que hayan sido desarrolladas específicamente para interpretar su formato, y únicamente pueden escribirse de la misma manera.',
                'en' => 'XLS is an MS Excel workbook binary file, which holds information about all the worksheets in a workbook, comprising both content and formatting (number masking, colouring, conditional formatting, etc), and can also hold additionals like charts, images, etc. XLS files cannonly be read by applications that have been especially written to read their format, and can only be written in the same way.',
            ]
        ]);

        LktFileFormat::createIfMissing([
            'name' => [
                'es' => 'XLSX', 'en' => 'XLSX',
            ],
            'description' => [
                'es' => 'XLSX es un formato muy conocido para documentos de Microsoft Excel que fue introducido por Microsoft con el lanzamiento de Microsoft Office 2007. Basado en una estructura organizada según las Open Packaging Conventions, tal como se describe en la Parte 2 del estándar OOXML ECMA-376, el nuevo formato es un paquete ZIP que contiene varios archivos XML. La estructura y los archivos internos pueden examinarse simplemente descomprimiendo el archivo .xlsx.',
                'en' => 'XLSX is well-known format for Microsoft Excel documents that was introduced by Microsoft with the release of Microsoft Office 2007. Based on structure organized according to the Open Packaging Conventions as outlined in Part 2 of the OOXML standard ECMA-376, the new format is a zip package that contains a number of XML files. The underlying structure and files can be examined by simply unzipping the .xlsx file.',
            ]
        ]);
    }
}