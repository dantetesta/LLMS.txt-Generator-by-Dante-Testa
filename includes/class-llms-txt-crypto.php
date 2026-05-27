<?php
/**
 * Helper para criptografia simétrica das chaves de API armazenadas em wp_options.
 *
 * Usa AES-256-CBC com IV randômico por valor. A chave é derivada de AUTH_KEY e
 * SECURE_AUTH_SALT (ou wp_salt como fallback). Compatível com instalações antigas:
 * valores sem o prefixo são tratados como legado em texto plano e re-criptografados
 * no próximo salvamento das configurações.
 *
 * @package LLMS_Txt_Generator
 * @since 2.3.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class LLMS_Txt_Crypto
{
    const PREFIX = 'llmsenc:v1:';

    private static function get_key()
    {
        $material = (defined('AUTH_KEY') ? AUTH_KEY : '')
            . (defined('SECURE_AUTH_SALT') ? SECURE_AUTH_SALT : '');

        if (empty($material) && function_exists('wp_salt')) {
            $material = wp_salt('auth') . wp_salt('secure_auth');
        }

        return hash('sha256', $material, true);
    }

    public static function is_encrypted($value)
    {
        return is_string($value) && strpos($value, self::PREFIX) === 0;
    }

    public static function encrypt($plain)
    {
        if (!is_string($plain) || $plain === '') {
            return $plain;
        }
        if (self::is_encrypted($plain)) {
            return $plain;
        }
        if (!function_exists('openssl_encrypt')) {
            return $plain;
        }

        $iv = openssl_random_pseudo_bytes(16);
        $cipher = openssl_encrypt($plain, 'aes-256-cbc', self::get_key(), OPENSSL_RAW_DATA, $iv);

        if ($cipher === false) {
            return $plain;
        }

        return self::PREFIX . base64_encode($iv . $cipher);
    }

    public static function decrypt($value)
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }
        if (!self::is_encrypted($value)) {
            return $value;
        }
        if (!function_exists('openssl_decrypt')) {
            return '';
        }

        $payload = base64_decode(substr($value, strlen(self::PREFIX)), true);
        if ($payload === false || strlen($payload) < 17) {
            return '';
        }

        $iv = substr($payload, 0, 16);
        $cipher = substr($payload, 16);
        $plain = openssl_decrypt($cipher, 'aes-256-cbc', self::get_key(), OPENSSL_RAW_DATA, $iv);

        return $plain === false ? '' : $plain;
    }
}
