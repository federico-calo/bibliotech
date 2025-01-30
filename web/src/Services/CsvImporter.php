<?php

namespace App\Services;

class CsvImporter
{

    public static function map(string $filename): array
    {
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            $headers = fgetcsv($handle, 1000, ',');
            if ($headers !== false) {
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    public function process($csvFile): array
    {
        $books = [];
        $uploadDir = __DIR__ . '/uploads/';
        $uploadFile = $uploadDir . basename((string) $csvFile['name']);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (move_uploaded_file($csvFile['tmp_name'], $uploadFile)) {
            $books = static::map($uploadFile);
        }

        return $books;
    }

}