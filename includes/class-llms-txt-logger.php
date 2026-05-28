<?php
/**
 * Sistema de logs do plugin LLMS.txt Generator.
 *
 * Grava eventos em wp-content/uploads/llms-txt-generator/plugin.log com:
 *   - Sanitização automática de contexto (chaves contendo "api_key", "token",
 *     "secret", "password" são redacted)
 *   - Rotação simples quando o arquivo passa de 500 KB
 *   - Proteção do diretório via .htaccess + index.html
 *
 * Níveis: info, warning, error, debug (este último só grava quando WP_DEBUG).
 *
 * @package LLMS_Txt_Generator
 * @since 2.3.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class LLMS_Txt_Logger
{
    const MAX_FILE_SIZE = 524288;
    const LOG_DIR_NAME  = 'llms-txt-generator';
    const LOG_FILE_NAME = 'plugin.log';

    private static $log_dir;
    private static $log_path;

    public static function get_log_dir()
    {
        if (self::$log_dir === null) {
            $uploads = wp_upload_dir(null, false);
            $base = isset($uploads['basedir']) && $uploads['basedir']
                ? $uploads['basedir']
                : WP_CONTENT_DIR . '/uploads';
            self::$log_dir = trailingslashit($base) . self::LOG_DIR_NAME;
        }
        return self::$log_dir;
    }

    public static function get_log_path()
    {
        if (self::$log_path === null) {
            self::$log_path = trailingslashit(self::get_log_dir()) . self::LOG_FILE_NAME;
        }
        return self::$log_path;
    }

    private static function ensure_log_dir()
    {
        $dir = self::get_log_dir();

        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }

        $htaccess = trailingslashit($dir) . '.htaccess';
        if (!file_exists($htaccess)) {
            @file_put_contents($htaccess, "Order deny,allow\nDeny from all\n");
        }

        $index = trailingslashit($dir) . 'index.html';
        if (!file_exists($index)) {
            @file_put_contents($index, '');
        }

        return is_dir($dir) && is_writable($dir);
    }

    public static function log($level, $message, $context = array())
    {
        if (!self::ensure_log_dir()) {
            return false;
        }

        $path = self::get_log_path();

        if (file_exists($path) && filesize($path) > self::MAX_FILE_SIZE) {
            @rename($path, $path . '.old');
        }

        $context = self::sanitize_context($context);

        $timestamp = function_exists('wp_date') ? wp_date('Y-m-d H:i:s') : date('Y-m-d H:i:s');

        $line = sprintf(
            "[%s] [%s] %s%s\n",
            $timestamp,
            strtoupper($level),
            (string) $message,
            !empty($context) ? ' | ' . wp_json_encode($context) : ''
        );

        return (bool) @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
    }

    public static function info($message, $context = array())
    {
        return self::log('info', $message, $context);
    }

    public static function warning($message, $context = array())
    {
        return self::log('warning', $message, $context);
    }

    public static function error($message, $context = array())
    {
        return self::log('error', $message, $context);
    }

    public static function debug($message, $context = array())
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return self::log('debug', $message, $context);
        }
        return false;
    }

    public static function read_last_lines($limit = 500)
    {
        $path = self::get_log_path();
        if (!file_exists($path)) {
            return '';
        }
        $content = @file_get_contents($path);
        if ($content === false) {
            return '';
        }
        $lines = explode("\n", rtrim($content, "\n"));
        $limit = max(1, (int) $limit);
        $slice = array_slice($lines, -$limit);
        return implode("\n", $slice);
    }

    public static function file_size()
    {
        $path = self::get_log_path();
        return file_exists($path) ? (int) filesize($path) : 0;
    }

    public static function clear()
    {
        $path = self::get_log_path();
        if (file_exists($path)) {
            return (bool) @unlink($path);
        }
        return true;
    }

    public static function snapshot_environment()
    {
        return array(
            'plugin_version' => defined('LLMS_TXT_GENERATOR_VERSION') ? LLMS_TXT_GENERATOR_VERSION : 'unknown',
            'php'            => PHP_VERSION,
            'wp'             => function_exists('get_bloginfo') ? get_bloginfo('version') : 'unknown',
            'locale'         => function_exists('get_locale') ? get_locale() : 'unknown',
            'multisite'      => function_exists('is_multisite') ? is_multisite() : false,
            'openssl'        => extension_loaded('openssl'),
            'mbstring'       => extension_loaded('mbstring'),
            'json'           => extension_loaded('json'),
            'curl'           => extension_loaded('curl'),
            'memory_limit'   => ini_get('memory_limit'),
            'abspath_writable' => defined('ABSPATH') ? is_writable(ABSPATH) : false,
            'upload_writable'  => self::upload_dir_writable(),
            'user_id'        => function_exists('get_current_user_id') ? get_current_user_id() : 0,
        );
    }

    private static function upload_dir_writable()
    {
        if (!function_exists('wp_upload_dir')) {
            return false;
        }
        $uploads = wp_upload_dir(null, false);
        return isset($uploads['basedir']) && is_writable($uploads['basedir']);
    }

    private static function sanitize_context($context)
    {
        if (!is_array($context)) {
            return $context;
        }

        $blocked = array('api_key', 'apikey', 'token', 'secret', 'password', 'authorization', 'auth');

        foreach ($context as $k => $v) {
            $kl = strtolower((string) $k);
            $hit = false;
            foreach ($blocked as $needle) {
                if (strpos($kl, $needle) !== false) {
                    $context[$k] = '[REDACTED]';
                    $hit = true;
                    break;
                }
            }
            if (!$hit && is_array($v)) {
                $context[$k] = self::sanitize_context($v);
            }
        }

        return $context;
    }
}
