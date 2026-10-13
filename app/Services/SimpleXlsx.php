<?php

namespace App\Services;

/**
 * Minimal, dependency-free XLSX reader/writer.
 * Uses only ext-zip + SimpleXML (available on standard shared hosting).
 */
class SimpleXlsx
{
    /**
     * Build a simple .xlsx binary (single sheet, all cells as inline strings).
     */
    public static function build(array $headers, array $rows = [], string $sheetName = 'Feuille1'): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('Extension PHP zip غير متوفرة على الخادم.');
        }

        $sheetXml = self::buildSheetXml($headers, $rows);

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';

        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::esc($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip = new \ZipArchive();

        if ($zip->open($tmp, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('تعذر إنشاء ملف Excel.');
        }

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rootRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        $content = file_get_contents($tmp);
        @unlink($tmp);

        return $content;
    }

    /**
     * Read the first worksheet of a .xlsx file. Returns an array of rows,
     * each row being a 0-indexed array of string values.
     */
    public static function read(string $path): array
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('Extension PHP zip غير متوفرة على الخادم.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('تعذر فتح الملف (ليس ملف Excel صالحاً).');
        }

        // Shared strings (optional)
        $shared = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $sx = @simplexml_load_string(self::stripNs($sharedXml));
            if ($sx !== false) {
                foreach ($sx->si as $si) {
                    $shared[] = self::siText($si);
                }
            }
        }

        $sheetPath = self::firstSheetPath($zip);
        $sheetXml  = $sheetPath ? $zip->getFromName($sheetPath) : false;
        $zip->close();

        if ($sheetXml === false) {
            throw new \RuntimeException('لم يتم العثور على ورقة العمل داخل الملف.');
        }

        $sx = @simplexml_load_string(self::stripNs($sheetXml));
        if ($sx === false) {
            throw new \RuntimeException('تعذر تحليل محتوى الملف.');
        }

        $rows = [];
        if (isset($sx->sheetData)) {
            foreach ($sx->sheetData->row as $row) {
                $cols = [];
                $auto = 0;
                foreach ($row->c as $c) {
                    $ref  = (string) $c['r'];
                    $type = (string) $c['t'];

                    if ($ref !== '' && preg_match('/^([A-Z]+)/', strtoupper($ref), $m)) {
                        $idx = self::colIndex($m[1]);
                    } else {
                        $idx = $auto;
                    }
                    $auto = $idx + 1;

                    if ($type === 'inlineStr') {
                        $val = self::siText($c->is);
                    } elseif ($type === 's') {
                        $val = $shared[(int) $c->v] ?? '';
                    } else {
                        $val = (string) $c->v;
                    }

                    $cols[$idx] = $val;
                }

                $max  = $cols ? max(array_keys($cols)) : -1;
                $line = [];
                for ($i = 0; $i <= $max; $i++) {
                    $line[] = $cols[$i] ?? '';
                }
                $rows[] = $line;
            }
        }

        return $rows;
    }

    private static function buildSheetXml(array $headers, array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>';

        $allRows = array_merge([$headers], $rows);
        $rowNum  = 1;

        foreach ($allRows as $row) {
            $xml .= '<row r="' . $rowNum . '">';
            $colNum = 1;
            foreach ($row as $value) {
                $ref = self::colLetter($colNum) . $rowNum;
                if ($value === null || $value === '') {
                    $xml .= '<c r="' . $ref . '"/>';
                } else {
                    $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t xml:space="preserve">'
                        . self::esc((string) $value) . '</t></is></c>';
                }
                $colNum++;
            }
            $xml .= '</row>';
            $rowNum++;
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private static function firstSheetPath(\ZipArchive $zip): ?string
    {
        if ($zip->locateName('xl/worksheets/sheet1.xml') !== false) {
            return 'xl/worksheets/sheet1.xml';
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('#^xl/worksheets/[^/]+\.xml$#', $name)) {
                return $name;
            }
        }

        return null;
    }

    private static function siText($node): string
    {
        if ($node === null) {
            return '';
        }

        $text = '';
        foreach ($node->t as $t) {
            $text .= (string) $t;
        }
        foreach ($node->r as $r) {
            foreach ($r->t as $t) {
                $text .= (string) $t;
            }
        }

        return $text;
    }

    private static function stripNs(string $xml): string
    {
        return preg_replace('/\s+xmlns(:[A-Za-z0-9_]+)?="[^"]*"/', '', $xml);
    }

    private static function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function colLetter(int $n): string
    {
        $s = '';
        while ($n > 0) {
            $mod = ($n - 1) % 26;
            $s   = chr(65 + $mod) . $s;
            $n   = intdiv($n - 1, 26);
        }

        return $s;
    }

    private static function colIndex(string $letters): int
    {
        $n = 0;
        foreach (str_split($letters) as $ch) {
            $n = $n * 26 + (ord($ch) - 64);
        }

        return $n - 1;
    }
}
