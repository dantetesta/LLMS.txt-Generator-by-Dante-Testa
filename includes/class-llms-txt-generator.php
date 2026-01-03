<?php
/**
 * Classe principal do plugin LLMS.txt Generator
 *
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @author Dante Testa (https://dantetesta.com.br)
 * @updated 2026-01-03 - Compatibilidade PHP 8.2+ e segurança
 */

// Evitar acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal do plugin LLMS.txt Generator
 * 
 * @since 1.0.0
 */
class LLMS_Txt_Generator {

    /**
     * Instância única da classe (padrão Singleton)
     *
     * @since 1.0.0
     * @var LLMS_Txt_Generator|null
     */
    private static ?LLMS_Txt_Generator $instance = null;

    /**
     * Obtém a instância única da classe
     *
     * @since 1.0.0
     * @return LLMS_Txt_Generator
     */
    public static function get_instance(): LLMS_Txt_Generator {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe
     * Inicializa o plugin e carrega todas as dependências
     *
     * @since 1.0.0
     */
    private function __construct() {
        // Carregar internacionalização
        $this->load_i18n();
        
        // Inicializar componentes do plugin
        $this->init_components();
        
        // Registrar hooks de ativação e desativação
        register_activation_hook(LLMS_TXT_GENERATOR_FILE, array($this, 'activate'));
        register_deactivation_hook(LLMS_TXT_GENERATOR_FILE, array($this, 'deactivate'));
        
        // Adicionar hook de desinstalação
        register_uninstall_hook(LLMS_TXT_GENERATOR_FILE, array('LLMS_Txt_Generator', 'uninstall'));
    }

    /**
     * Carrega os arquivos de tradução
     *
     * @since 1.0.0
     * @return void
     */
    private function load_i18n(): void {
        // Inicializar classe de internacionalização
        LLMS_Txt_I18n::get_instance();
    }

    /**
     * Inicializa os componentes do plugin
     *
     * @since 1.0.0
     * @return void
     */
    private function init_components(): void {
        // Carregar componentes administrativos apenas no admin
        if (is_admin()) {
            // Inicializar interface administrativa
            LLMS_Txt_Admin::get_instance();
            
            // Inicializar meta box para posts e páginas
            LLMS_Txt_Meta_Box::get_instance();
        }
        
        // Inicializar gerenciador do arquivo llms.txt
        LLMS_Txt_File::get_instance();
    }

    /**
     * Método executado na ativação do plugin
     *
     * @since 1.0.0
     * @return void
     */
    public function activate(): void {
        // Verificar versão mínima do PHP (atualizado para 8.0)
        if (version_compare(PHP_VERSION, '8.0', '<')) {
            deactivate_plugins(plugin_basename(LLMS_TXT_GENERATOR_FILE));
            wp_die(
                __('O plugin LLMS.txt Generator requer PHP 8.0 ou superior.', 'llms-txt-generator'),
                __('Erro de ativação', 'llms-txt-generator'),
                array('back_link' => true)
            );
        }
        
        // Verificar versão mínima do WordPress
        if (version_compare(get_bloginfo('version'), '5.6', '<')) {
            deactivate_plugins(plugin_basename(LLMS_TXT_GENERATOR_FILE));
            wp_die(
                __('O plugin LLMS.txt Generator requer WordPress 5.6 ou superior.', 'llms-txt-generator'),
                __('Erro de ativação', 'llms-txt-generator'),
                array('back_link' => true)
            );
        }
        
        // Configurações padrão
        $default_settings = array(
            'enabled' => '1',
            'custom_rules' => '',
            'protected_post_types' => array('post', 'page'),
            'openai_api_key' => '',
            'auto_generate' => '0',
            'last_updated' => ''
        );
        
        // Adicionar configurações padrão apenas se não existirem
        if (!get_option('llms_txt_settings')) {
            add_option('llms_txt_settings', $default_settings);
        }
        
        // Gerar o arquivo llms.txt inicial
        $file_manager = LLMS_Txt_File::get_instance();
        $file_manager->regenerate_file();
        
        // Limpar cache de regras de reescrita
        flush_rewrite_rules();
    }

    /**
     * Método executado na desativação do plugin
     *
     * @since 1.0.0
     * @return void
     */
    public function deactivate(): void {
        // Remover o arquivo llms.txt
        $file_path = ABSPATH . 'llms.txt';
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
        
        // Limpar cache de regras de reescrita
        flush_rewrite_rules();
    }

    /**
     * Método estático executado na desinstalação do plugin
     *
     * @since 1.0.0
     * @return void
     */
    public static function uninstall(): void {
        // Remover todas as opções do plugin
        delete_option('llms_txt_settings');
        
        // Remover metadados de posts usando queries seguras com prepare()
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
                '_llms_txt_protected'
            )
        );
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
                '_llms_txt_description'
            )
        );
        
        // Remover meta de exclusão também
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
                '_llms_txt_exclude'
            )
        );
        
        // Remover o arquivo llms.txt
        $file_path = ABSPATH . 'llms.txt';
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }
}
