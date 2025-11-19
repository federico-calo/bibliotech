<?php

namespace App\Services;

interface TokenManagerInterface
{
    /**
     * @return string
     */
    public static function generateToken(): string;
}
