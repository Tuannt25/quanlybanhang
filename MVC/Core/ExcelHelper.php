<?php
class ExcelHelper
{
    /* =====================================================
       EXPORT CSV (DB → CSV)
       ❌ KHÔNG convert encoding
       ✅ UTF-8 BOM để Excel đọc đúng
    ===================================================== */
 public static function export($filename, $headers, $rows)
{
    if (ob_get_length()) ob_end_clean();

    $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);

    // ✅ BẮT BUỘC: text/csv
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // UTF-8 BOM cho Excel Windows
    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');

    fputcsv($out, $headers);

    foreach ($rows as $row) {
        fputcsv($out, $row);
    }

    fclose($out);
    exit;
}

    /* =====================================================
       IMPORT CSV (CSV → PHP)
       ✅ Convert CP1252 / ANSI → UTF-8
       ✅ Bỏ header mặc định
    ===================================================== */
    public static function import($fileTmpPath, $skipHeader = true)
    {
        if (!file_exists($fileTmpPath)) return [];

        $rows = [];
        $fp = fopen($fileTmpPath, 'r');
        if (!$fp) return [];

        $isFirst = true;

        while (($data = fgetcsv($fp)) !== false) {

            // bỏ dòng header
            if ($isFirst && $skipHeader) {
                $isFirst = false;
                continue;
            }
            $isFirst = false;

            // bỏ dòng trống
            $allEmpty = true;
            foreach ($data as $v) {
                if (trim((string)$v) !== '') {
                    $allEmpty = false;
                    break;
                }
            }
            if ($allEmpty) continue;

            // convert từng cột về UTF-8
            $row = [];
            foreach ($data as $v) {
                $row[] = self::toUtf8($v);
            }

            $rows[] = $row;
        }

        fclose($fp);
        return $rows;
    }

    /* =====================================================
       CONVERT TO UTF-8 (AN TOÀN – KHÔNG DOUBLE ENCODE)
    ===================================================== */
    private static function toUtf8($value)
    {
        if (!is_string($value)) return $value;

        // remove UTF-8 BOM
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);

        // nếu đã UTF-8 thì giữ nguyên
        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        // Excel ANSI / CP1252 → UTF-8
        $converted = @iconv('CP1252', 'UTF-8//IGNORE', $value);

        return $converted !== false ? $converted : $value;
    }
}
