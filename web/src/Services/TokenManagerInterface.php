<?php

namespace App\Services;

interface TokenManagerInterface
{
    public static function generateToken(): string;

}