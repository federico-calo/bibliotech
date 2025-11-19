<?php

namespace App\Core;

class Settings
{
    /**
     * @var array
     */
    protected static array $settings = [];

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        static::setSettings($settings);
    }

    /**
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        static::$settings[$key] = $value;
    }

    /**
     * @throws \Exception
     */
    public static function get(string $key): mixed
    {
        if (!array_key_exists($key, static::$settings)) {
            throw new \Exception("No $key is bound in the container");
        }
        return static::$settings[$key];
    }

    /**
     * @param  array $settings
     * @return array
     */
    public static function setSettings(array $settings): array
    {
        foreach ($settings as $key => $setting) {
            self::set($key, $setting);
        }
        return self::getSettings();
    }

    /**
     * @return array
     */
    public static function getSettings(): array
    {
        return static::$settings;
    }

    /**
     * @throws \Exception
     */
    public static function getTemplatePath($templateName): string
    {
        return static::get('templatePath') . $templateName . static::get('templateExtension');
    }
}
