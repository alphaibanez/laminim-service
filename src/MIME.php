<?php

namespace Lkt;

class MIME
{
    /**
     * Application MIME
     */
    const JSON = 'application/json';
    const JSONLD = 'application/ld+json';
    const OCTET_STREAM = 'application/octet-stream';
    const ABIWORD = 'application/x-abiword';
    const ARCHIVE_DOCUMENT = 'application/x-freearc';
    const KINDLE_EBOOK = 'application/vnd.amazon.ebook';
    const BZIP = 'application/x-bzip';
    const BZIP2 = 'application/x-bzip2';
    const CDA = 'application/x-cdf';
    const CSH = 'application/x-csh';
    const MS_DOC = 'application/msword';
    const MS_DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    const MS_EOT = 'application/vnd.ms-fontobject';
    const EPUB = 'application/epub+zip';
    const GZIP = 'application/gzip';
    const JAR = 'application/java-archive';
    const APPLE_INSTALLER_PACKAGE = 'application/vnd.apple.installer+xml';
    const ODP = 'application/vnd.oasis.opendocument.presentation';
    const ODS = 'application/vnd.oasis.opendocument.spreadsheet';
    const ODT = 'application/vnd.oasis.opendocument.text';
    const PDF = 'application/pdf';
    const PHP = 'application/x-httpd-php';
    const PPT = 'application/vnd.ms-powerpoint';
    const PPTX = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    const RAR = 'application/vnd.rar';
    const RTF = 'application/rtf';
    const SH = 'application/x-sh';
    const TAR = 'application/x-tar';
    const MS_VISIO = 'application/vnd.visio';
    const XHTML = 'application/xhtml+xml';
    const XLS = 'application/vnd.ms-excel';
    const XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    const XML = 'application/xml';
    const ZIP = 'application/zip';
    const SEVEN_ZIP = 'application/x-7z-compressed';


    /**
     * Audio MIME
     */
    const AAC = 'audio/aac';
    const MID = 'audio/midi';
    const MIDI = 'audio/x-midi';
    const MP3 = 'audio/mpeg';
    const OGA = 'audio/ogg';
    const OPUS = 'audio/opus';
    const WAV = 'audio/wav';
    const WEBA = 'audio/webm';

    /**
     * Font MIME
     */
    const OTF = 'font/otf';
    const TTF = 'font/ttf';
    const WOFF = 'font/woff';
    const WOFF2 = 'font/woff2';

    /**
     * Image MIME
     */
    const GIF = 'image/gif';
    const PNG = 'image/png';
    const JPEG = 'image/jpeg';
    const SVG = 'image/svg+xml';
    const WEBP = 'image/webp';
    const AVIF = 'image/avif';
    const BMP = 'image/bmp';
    const TIFF = 'image/tiff';
    const MS_ICO = 'image/vnd.microsoft.icon';

    /**
     * Text MIME
     */
    const CSS = 'text/css';
    const HTML = 'text/html';
    const PLAIN = 'text/plain';
    const CSV = 'text/csv';
    const ICS = 'text/calendar';
    const JS = 'text/javascript';

    /**
     * Video MIME
     */
    const AVI = 'video/x-msvideo';
    const MP4 = 'video/mp4';
    const MPEG = 'video/mpeg';
    const OGG = 'video/ogg';
    const MPEG_TS = 'video/mp2t';
    const WEBM = 'video/webm';

    const IMAGES = [
        self::GIF,
        self::PNG,
        self::JPEG,
        self::SVG,
        self::WEBP,
        self::AVIF,
        self::BMP,
        self::TIFF,
        self::MS_ICO,
    ];

    const VIDEOS = [
        self::AVI,
        self::MP4,
        self::MPEG,
        self::OGG,
        self::MPEG_TS,
        self::WEBM,
    ];

    const FONTS = [
        self::OTF,
        self::TTF,
        self::WOFF,
        self::WOFF2,
        self::MS_EOT,
    ];

    const AUDIOS = [
        self::MP3,
        self::OPUS,
        self::OGA,
        self::AAC,
        self::WEBA,
        self::CDA,
        self::MID,
        self::MIDI,
        self::WAV,
    ];

    public static function getByExtension(string $extension): string
    {
        return match ($extension) {
            // Image
            'jpg', 'jpeg' => static::JPEG,
            'png' => static::PNG,
            'svg' => static::SVG,
            'webp' => static::WEBP,
            'avif' => static::AVIF,
            'gif' => static::GIF,
            'ico' => static::MS_ICO,
            'bmp' => static::BMP,
            'tif', 'tiff' => static::TIFF,

            // Office
            'csv' => static::CSV,
            'txt' => static::PLAIN,
            'pdf' => static::PDF,
            'docx' => static::MS_DOCX,
            'doc' => static::MS_DOC,
            'odp' => static::ODP,
            'ods' => static::ODS,
            'odt' => static::ODT,
            'pptx' => static::PPTX,
            'ppt' => static::PPT,
            'xlsx' => static::XLSX,
            'xls' => static::XLS,
            'rtf' => static::RTF,
            'ics' => static::ICS,
            'vsd' => static::MS_VISIO,
            'abw' => static::ABIWORD,

            // Video
            'mp4' => static::MP4,
            'ogg' => static::OGG,
            'webm' => static::WEBM,
            'avi' => static::AVI,
            'mpeg' => static::MPEG,
            'ts' => static::MPEG_TS,

            // Audio
            'mp3' => static::MP3,
            'opus' => static::OPUS,
            'oga' => static::OGA,
            'aac' => static::AAC,
            'weba' => static::WEBA,
            'cda' => static::CDA,
            'mid' => static::MID,
            'midi' => static::MIDI,
            'wav' => static::WAV,

            // Archive
            'zip' => static::ZIP,
            'rar' => static::RAR,
            '7z' => static::SEVEN_ZIP,
            'tar' => static::TAR,
            'gzip' => static::GZIP,
            'bz' => static::BZIP,
            'bz2' => static::BZIP2,
            'arc' => static::ARCHIVE_DOCUMENT,

            // Code
            'html', 'htm' => static::HTML,
            'css' => static::CSS,
            'js', 'mjs' => static::JS,
            'json' => static::JSON,
            'jsonld' => static::JSONLD,
            'xml' => static::XML,
            'xhtml' => static::XHTML,
            'php' => static::PHP,
            'sh' => static::SH,
            'csh' => static::CSH,
            'jar' => static::JAR,

            // Font
            'otf' => static::OTF,
            'ttf' => static::TTF,
            'woff' => static::WOFF,
            'woff2' => static::WOFF2,
            'eot' => static::MS_EOT,

            // Ebook
            'epub' => static::EPUB,
            'azw' => static::KINDLE_EBOOK,

            // Extra
            'mpkg' => static::APPLE_INSTALLER_PACKAGE,

            default => static::OCTET_STREAM,
        };
    }

    public static function getExtensionByMime(string $mime): string
    {
        return match ($mime) {
            // Image
            static::JPEG => 'jpeg',
            static::PNG => 'png',
            static::SVG => 'svg',
            static::WEBP => 'webp',
            static::AVIF => 'avif',
            static::GIF => 'gif',
            static::MS_ICO => 'ico',
            static::BMP => 'bmp',
            static::TIFF => 'tif', 'tiff',

            // Office
            static::CSV => 'csv',
            static::PLAIN => 'txt',
            static::PDF => 'pdf',
            static::MS_DOCX => 'docx',
            static::MS_DOC => 'doc',
            static::ODP => 'odp',
            static::ODS => 'ods',
            static::ODT => 'odt',
            static::PPTX => 'pptx',
            static::PPT => 'ppt',
            static::XLSX => 'xlsx',
            static::XLS => 'xls',
            static::RTF => 'rtf',
            static::ICS => 'ics',
            static::MS_VISIO => 'vsd',
            static::ABIWORD => 'abw',

            // Video
            static::MP4 => 'mp4',
            static::OGG => 'ogg',
            static::WEBM => 'webm',
            static::AVI => 'avi',
            static::MPEG => 'mpeg',
            static::MPEG_TS => 'ts',

            // Audio
            static::MP3 => 'mp3',
            static::OPUS => 'opus',
            static::OGA => 'oga',
            static::AAC => 'aac',
            static::WEBA => 'weba',
            static::CDA => 'cda',
            static::MID => 'mid',
            static::MIDI => 'midi',
            static::WAV => 'wav',

            // Archive
            static::ZIP => 'zip',
            static::RAR => 'rar',
            static::SEVEN_ZIP => '7z',
            static::TAR => 'tar',
            static::GZIP => 'gzip',
            static::BZIP => 'bz',
            static::BZIP2 => 'bz2',
            static::ARCHIVE_DOCUMENT => 'arc',

            // Code
            static::HTML => 'html', 'htm',
            static::CSS => 'css',
            static::JS => 'js', 'mjs',
            static::JSON => 'json',
            static::JSONLD => 'jsonld',
            static::XML => 'xml',
            static::XHTML => 'xhtml',
            static::PHP => 'php',
            static::SH => 'sh',
            static::CSH => 'csh',
            static::JAR => 'jar',

            // Font
            static::OTF => 'otf',
            static::TTF => 'ttf',
            static::WOFF => 'woff',
            static::WOFF2 => 'woff2',
            static::MS_EOT => 'eot',

            // Ebook
            static::EPUB => 'epub',
            static::KINDLE_EBOOK => 'azw',

            // Extra
            static::APPLE_INSTALLER_PACKAGE => 'mpkg',

            default => '',
        };
    }

    public static function getImageByExtension(string $extension): string
    {
        return match ($extension) {
            'jpg', 'jpeg' => static::JPEG,
            'png' => static::PNG,
            'svg' => static::SVG,
            'webp' => static::WEBP,
            'avif' => static::AVIF,
            'gif' => static::GIF,
            'ico' => static::MS_ICO,
            'bmp' => static::BMP,
            'tif', 'tiff' => static::TIFF,
            default => static::OCTET_STREAM,
        };
    }

    public static function getVideoByExtension(string $extension): string
    {
        return match ($extension) {
            'mp4' => static::MP4,
            'ogg' => static::OGG,
            'webm' => static::WEBM,
            'avi' => static::AVI,
            'mpeg' => static::MPEG,
            'ts' => static::MPEG_TS,
            default => static::OCTET_STREAM,
        };
    }

    public static function getEbookByExtension(string $extension): string
    {
        return match ($extension) {
            'epub' => static::EPUB,
            'azw' => static::KINDLE_EBOOK,
            default => static::OCTET_STREAM,
        };
    }

    public static function getAudioByExtension(string $extension): string
    {
        return match ($extension) {
            'mp3' => static::MP3,
            'opus' => static::OPUS,
            'oga' => static::OGA,
            'aac' => static::AAC,
            'weba' => static::WEBA,
            'cda' => static::CDA,
            'mid' => static::MID,
            'midi' => static::MIDI,
            'wav' => static::WAV,
            default => static::OCTET_STREAM,
        };
    }

    public static function getArchiveByExtension(string $extension): string
    {
        return match ($extension) {
            'zip' => static::ZIP,
            'rar' => static::RAR,
            '7z' => static::SEVEN_ZIP,
            'tar' => static::TAR,
            'gzip' => static::GZIP,
            'bz' => static::BZIP,
            'bz2' => static::BZIP2,
            'arc' => static::ARCHIVE_DOCUMENT,
            default => static::OCTET_STREAM,
        };
    }

    public static function getOfficeByExtension(string $extension): string
    {
        return match ($extension) {
            'csv' => static::CSV,
            'txt' => static::PLAIN,
            'pdf' => static::PDF,
            'docx' => static::MS_DOCX,
            'doc' => static::MS_DOC,
            'odp' => static::ODP,
            'ods' => static::ODS,
            'odt' => static::ODT,
            'pptx' => static::PPTX,
            'ppt' => static::PPT,
            'xlsx' => static::XLSX,
            'xls' => static::XLS,
            'rtf' => static::RTF,
            'ics' => static::ICS,
            'vsd' => static::MS_VISIO,
            'abw' => static::ABIWORD,
            default => static::OCTET_STREAM,
        };
    }

    public static function getCodeByExtension(string $extension): string
    {
        return match ($extension) {
            'html', 'htm' => static::HTML,
            'css' => static::CSS,
            'js', 'mjs' => static::JS,
            'json' => static::JSON,
            'jsonld' => static::JSONLD,
            'xml' => static::XML,
            'xhtml' => static::XHTML,
            'php' => static::PHP,
            'sh' => static::SH,
            'csh' => static::CSH,
            'jar' => static::JAR,
            default => static::OCTET_STREAM,
        };
    }

    public static function getFontByExtension(string $extension): string
    {
        return match ($extension) {
            'otf' => static::OTF,
            'ttf' => static::TTF,
            'woff' => static::WOFF,
            'woff2' => static::WOFF2,
            'eot' => static::MS_EOT,
            default => static::OCTET_STREAM,
        };
    }

    public static function isImage(string $mime): bool
    {
        return in_array($mime, static::IMAGES);
    }

    public static function isVideo(string $mime): bool
    {
        return in_array($mime, static::VIDEOS);
    }

    public static function isFont(string $mime): bool
    {
        return in_array($mime, static::FONTS);
    }

    public static function isAudio(string $mime): bool
    {
        return in_array($mime, static::AUDIOS);
    }
}


