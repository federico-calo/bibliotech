<?php

namespace App\Core;

class ErrorHandler
{
    /**
     * @throws \Exception
     */
    public static function handleException(\Throwable $exception): void
    {
        static::logError($exception);
        if (php_sapi_name() === 'cli') {
            static::renderCliError($exception);
        } else {
            static::renderErrorPage($exception);
        }
    }

    /**
     * @throws \Exception
     */
    private static function renderCliError(\Throwable $exception): void
    {
        $isDebug = Settings::get('debug');
        if ($isDebug) {
            $errorMessage = static::formatErrorMessage(
                $exception,
                "\033[31m[%s] Error:\033[0m %s: %s in %s on line %d\n"
            );
            $trace = $exception->getTraceAsString();
        } else {
            $errorMessage = "\033[31mAn unexpected error occurred. Please check error log for details.\033[0m\n";
            $trace = "";
        }

        fwrite(STDERR, $errorMessage);
        if ($trace) {
            fwrite(STDERR, "\nStack trace:\n$trace\n");
        }
        exit(1);
    }

    /**
     * @throws \Exception
     */
    private static function renderErrorPage(\Throwable $exception): void
    {
        $isDebug = Settings::get('debug');
        if ($isDebug) {
            $errorMessage = static::formatErrorMessage(
                $exception,
                "[%s] Error: %s: %s in %s on line %d\n"
            );
            $trace = $exception->getTraceAsString();
        } else {
            $errorMessage = "An unexpected error occurred. Please check error log for details.";
            $trace = "";
        }

        http_response_code(500);
        View::render(
            'errors/500',
            [
            'errorMessage' => $errorMessage,
            'trace' => $trace,
            'isDebug' => $isDebug
            ]
        );
        exit();
    }


    /**
     * @param  \Throwable $exception
     * @return void
     */
    private static function logError(\Throwable $exception): void
    {
        $logMessage = static::formatErrorMessage(
            $exception,
            "[%s] Error: %s: %s in %s on line %d\n"
        );
        error_log($logMessage, 3, __DIR__ . '/../../../logs/error.log');
    }

    /**
     * @param  $level
     * @param  $message
     * @param  $file
     * @param  $line
     * @return void
     * @throws \Exception
     */
    public static function handleError($level, $message, $file, $line)
    {
        $exception = new \ErrorException($message, 0, $level, $file, $line);
        self::handleException($exception);
    }

    /**
     * @param  \Throwable $exception
     * @param  string     $format
     * @return string
     */
    private static function formatErrorMessage(\Throwable $exception, string $format): string
    {
        return sprintf(
            $format,
            date('Y-m-d H:i:s'),
            $exception::class,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
    }
}
