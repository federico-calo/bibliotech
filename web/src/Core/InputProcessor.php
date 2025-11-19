<?php

namespace App\Core;

class InputProcessor
{
    /**
     * @return array
     */
    public function processServerInput(): array
    {
        $input = match ($_SERVER['REQUEST_METHOD']) {
            'POST' => filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [],
            'GET' => filter_input_array(INPUT_GET, FILTER_DEFAULT) ?? [],
            default => []
        };

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES)) {
            $input['files'] = $this->sanitizeFiles($_FILES);
        }

        $input['headers'] = getallheaders() ?: [];

        return $input;
    }

    /**
     * @param  array $files
     * @return array
     */
    private function sanitizeFiles(array $files): array
    {
        $sanitizedFiles = [];
        foreach ($files as $key => $file) {
            $sanitizedFiles[$key] = is_array($file['name'])
                ? $this->normalizeMultipleFiles($file)
                : $this->sanitizeSingleFile($file);
        }

        return $sanitizedFiles;
    }

    /**
     * @param  array $file
     * @return array
     */
    private function sanitizeSingleFile(array $file): array
    {
        return [
            'name' => htmlspecialchars((string)$file['name'], ENT_QUOTES, 'UTF-8'),
            'type' => htmlspecialchars((string)$file['type'], ENT_QUOTES, 'UTF-8'),
            'tmp_name' => $file['tmp_name'],
            'error' => $file['error'],
            'size' => $file['size']
        ];
    }

    /**
     * @param  array $file
     * @return array
     */
    private function normalizeMultipleFiles(array $file): array
    {
        $normalized = [];
        foreach ($file['name'] as $index => $name) {
            $normalized[] = [
                'name' => htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8'),
                'type' => htmlspecialchars((string)$file['type'][$index], ENT_QUOTES, 'UTF-8'),
                'tmp_name' => $file['tmp_name'][$index],
                'error' => $file['error'][$index],
                'size' => $file['size'][$index]
            ];
        }

        return $normalized;
    }
}
