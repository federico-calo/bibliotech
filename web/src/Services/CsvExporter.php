<?php

namespace App\Services;

class CsvExporter
{
    /**
     * @param  array  $data
     * @param  string $filename
     * @return void
     */
    public static function export(array $data, string $filename): void
    {
        \header('Content-Type: text/csv');
        \header('Content-Disposition: attachment;filename="' . $filename . '"');
        $output = \fopen('php://output', 'w');
        \fputcsv($output, ['id', 'title', 'author', 'isbn', 'summary', 'tags']);
        foreach ($data as $row) {
            if (isset($row['tags']) && \is_array($row['tags'])) {
                $row['tags'] = \implode(' | ', $row['tags']);
            }
            \fputcsv($output, $row);
        }
        \fclose($output);
        exit;
    }
}
