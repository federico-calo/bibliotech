<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenLibraryClient
{
    private const string BASE_API_URL = 'https://openlibrary.org/api/books';

    /**
     * @param Client $httpClient
     */
    public function __construct(private readonly Client $httpClient)
    {
    }

    /**
     * @param  string $isbn
     * @return array
     */
    public function getBookLink(string $isbn): array
    {
        $url = self::BASE_API_URL;
        $queryParams = [
            'bibkeys' => "isbn:$isbn",
            'jscmd' => 'details',
            'format' => 'json',
        ];

        try {
            $response = $this->httpClient->request(
                'GET',
                $url,
                [
                'query' => $queryParams,
                ]
            );

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (!$data) {
                return [];
            }

            $bookData = reset($data);
            return [
                'link_title' => $bookData['details']['title'] ?? '',
                'link_url' => $bookData['info_url'] ?? '',
            ];
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Erreur lors de la récupération des données : " . $e->getMessage());
        }
    }
}
