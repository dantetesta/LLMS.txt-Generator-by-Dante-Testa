<?php
/**
 * Página admin para visualizar / baixar / limpar o log do plugin.
 *
 * Registra um submenu em Configurações → LLMS.txt Logs e expõe:
 *   - viewer das últimas N linhas
 *   - download como text/plain
 *   - botão para limpar (com nonce + capability)
 *
 * @package LLMS_Txt_Generator
 * @since 2.3.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class LLMS_Txt_Logs_Page
{
    const MENU_SLUG = 'llms-txt-logs';
    const NONCE_ACTION = 'llms_txt_logs_action';

    public static function init()
    {
        add_action('admin_menu', array(__CLASS__, 'register_menu'));
        add_action('admin_init', array(__CLASS__, 'handle_actions'));
    }

    public static function register_menu()
    {
        add_submenu_page(
            'options-general.php',
            __('LLMS.txt — Logs', 'llms-txt-generator'),
            __('LLMS.txt Logs', 'llms-txt-generator'),
            'manage_options',
            self::MENU_SLUG,
            array(__CLASS__, 'render')
        );
    }

    public static function handle_actions()
    {
        if (!isset($_GET['page']) || $_GET['page'] !== self::MENU_SLUG) {
            return;
        }
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['llms_action']) && isset($_GET['_wpnonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));
            if (!wp_verify_nonce($nonce, self::NONCE_ACTION)) {
                wp_die(__('Erro de segurança. Recarregue a página.', 'llms-txt-generator'));
            }

            $action = sanitize_key(wp_unslash($_GET['llms_action']));

            if ($action === 'clear') {
                LLMS_Txt_Logger::clear();
                LLMS_Txt_Logger::info('Log limpo manualmente pelo admin', array(
                    'user_id' => get_current_user_id(),
                ));
                wp_safe_redirect(add_query_arg(array('page' => self::MENU_SLUG, 'cleared' => '1'), admin_url('options-general.php')));
                exit;
            }

            if ($action === 'download') {
                $path = LLMS_Txt_Logger::get_log_path();
                if (!file_exists($path)) {
                    wp_die(__('Nenhum log para baixar.', 'llms-txt-generator'));
                }
                nocache_headers();
                header('Content-Type: text/plain; charset=UTF-8');
                header('Content-Disposition: attachment; filename="llms-txt-generator.log"');
                header('Content-Length: ' . filesize($path));
                readfile($path);
                exit;
            }
        }
    }

    public static function render()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta página.', 'llms-txt-generator'));
        }

        $log_content = LLMS_Txt_Logger::read_last_lines(500);
        $log_size = LLMS_Txt_Logger::file_size();
        $log_path = LLMS_Txt_Logger::get_log_path();

        $clear_url = wp_nonce_url(
            add_query_arg(array('page' => self::MENU_SLUG, 'llms_action' => 'clear'), admin_url('options-general.php')),
            self::NONCE_ACTION
        );
        $download_url = wp_nonce_url(
            add_query_arg(array('page' => self::MENU_SLUG, 'llms_action' => 'download'), admin_url('options-general.php')),
            self::NONCE_ACTION
        );

        $cleared = isset($_GET['cleared']) && $_GET['cleared'] === '1';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('LLMS.txt Generator — Logs do plugin', 'llms-txt-generator'); ?></h1>

            <?php if ($cleared) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Log limpo com sucesso.', 'llms-txt-generator'); ?></p>
                </div>
            <?php endif; ?>

            <p>
                <?php esc_html_e('Este painel registra eventos de ativação, desativação, chamadas a APIs externas, gravação do arquivo llms.txt e falhas de criptografia. Útil para diagnosticar problemas de instalação ou comportamento inesperado.', 'llms-txt-generator'); ?>
            </p>

            <table class="widefat" style="max-width:720px;margin-bottom:1em;">
                <tbody>
                    <tr>
                        <th style="width:200px;"><?php esc_html_e('Arquivo de log', 'llms-txt-generator'); ?></th>
                        <td><code><?php echo esc_html($log_path); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Tamanho atual', 'llms-txt-generator'); ?></th>
                        <td><?php echo esc_html(size_format($log_size)); ?> <?php echo $log_size === 0 ? '<em>(' . esc_html__('vazio', 'llms-txt-generator') . ')</em>' : ''; ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Versão do plugin', 'llms-txt-generator'); ?></th>
                        <td><?php echo esc_html(defined('LLMS_TXT_GENERATOR_VERSION') ? LLMS_TXT_GENERATOR_VERSION : '?'); ?></td>
                    </tr>
                </tbody>
            </table>

            <p>
                <a href="<?php echo esc_url($download_url); ?>" class="button button-secondary">
                    ⬇️ <?php esc_html_e('Baixar log', 'llms-txt-generator'); ?>
                </a>
                <a href="<?php echo esc_url($clear_url); ?>" class="button button-secondary"
                    onclick="return confirm('<?php echo esc_js(__('Tem certeza que deseja apagar o log?', 'llms-txt-generator')); ?>');">
                    🗑️ <?php esc_html_e('Limpar log', 'llms-txt-generator'); ?>
                </a>
                <a href="<?php echo esc_url(add_query_arg('page', self::MENU_SLUG, admin_url('options-general.php'))); ?>" class="button">
                    🔄 <?php esc_html_e('Atualizar', 'llms-txt-generator'); ?>
                </a>
            </p>

            <h2><?php esc_html_e('Últimas 500 linhas', 'llms-txt-generator'); ?></h2>

            <?php if (empty($log_content)) : ?>
                <p><em><?php esc_html_e('Nenhum evento registrado ainda. Ative ou use o plugin para começar a gerar logs.', 'llms-txt-generator'); ?></em></p>
            <?php else : ?>
                <textarea readonly style="width:100%;height:480px;font-family:Menlo,Monaco,Consolas,monospace;font-size:12px;background:#1d2327;color:#f0f0f1;border:1px solid #2c3338;padding:12px;"><?php echo esc_textarea($log_content); ?></textarea>
            <?php endif; ?>

            <p style="margin-top:1em;color:#666;font-size:12px;">
                <?php esc_html_e('Chaves de API, tokens e senhas são automaticamente sanitizados como [REDACTED] antes da gravação.', 'llms-txt-generator'); ?>
            </p>
        </div>
        <?php
    }
}
