<?php

namespace App\Services;

class CsvImporter
{
    /**
     * Maps data from a CSV file into an associative array.
     *
     * @param string $filename Path to the CSV file to import
     *
     * @return array Associative array of CSV data
     */
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

    /**
     * Processes an uploaded CSV file and returns the data as an array.
     *
     * @param array $csvFile $_FILES array containing uploaded file information
     *
     * @return array Array of imported data
     */
    public function process(array $csvFile): array
    {
        $books = [];
        $uploadDir = __DIR__ . '/uploads/';
        $uploadFile = $uploadDir . basename((string)$csvFile['name']);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (move_uploaded_file($csvFile['tmp_name'], $uploadFile)) {
            $books = static::map($uploadFile);
        }
        return $books;
    }
}
