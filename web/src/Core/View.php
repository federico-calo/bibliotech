<?php

namespace App\Core;

class View
{
    private const string PAGE_TEMPLATE_NAME = 'layout';

    /**
     * @throws \Exception
     */
    public static function render(string $template, array $variables = []): void
    {
        $pageTemplate = Settings::getTemplatePath(static::PAGE_TEMPLATE_NAME);
        $template = file_exists($template) ? $template : Settings::getTemplatePath($template);
        $message = Message::getMessage();
        $messageType = $message['messageType'] ?? '';
        $messageText = $message['messageText'] ?? '';
        $content = static::renderTemplate(
            $template,
            $variables
        );
        if (!file_exists($pageTemplate)) {
            throw new \RuntimeException("Error: Layout file not found: static::PAGE_TEMPLATE");
        }

        include $pageTemplate;
    }

    /**
     * @param  string $template
     * @param  array  $variables
     * @return string
     */
    protected static function renderTemplate(string $template, array $variables): string
    {
        extract($variables);
        ob_start();
        include $template;
        return ob_get_clean();
    }
}
