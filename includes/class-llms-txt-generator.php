<?php
/**
 * Classe principal do plugin LLMS.txt Generator
 *
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @author Dante Testa (https://dantetesta.com.br)
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
     * @var LLMS_Txt_Generator
     */
    private static $instance = null;

    /**
     * Obtém a instância única da classe
     *
     * @since 1.0.0
     * @return LLMS_Txt_Generator
     */
    public static function get_instance() {
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
     */
    private function load_i18n() {
        // Inicializar classe de internacionalização
        LLMS_Txt_I18n::get_instance();
    }

    /**
     * Inicializa os componentes do plugin
     *
     * @since 1.0.0
     */
    private function init_components() {
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
     */
    public function activate() {
        // Verificar versão mínima do PHP
        if (version_compare(PHP_VERSION, '7.0', '<')) {
            deactivate_plugins(plugin_basename(LLMS_TXT_GENERATOR_FILE));
            wp_die(
                __('O plugin LLMS.txt Generator requer PHP 7.0 ou superior.', 'llms-txt-generator'),
                __('Erro de ativação', 'llms-txt-generator'),
                array('back_link' => true)
            );
        }
        
        // Verificar versão mínima do WordPress
        if (version_compare(get_bloginfo('version'), '5.0', '<')) {
            deactivate_plugins(plugin_basename(LLMS_TXT_GENERATOR_FILE));
            wp_die(
                __('O plugin LLMS.txt Generator requer WordPress 5.0 ou superior.', 'llms-txt-generator'),
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
     */
    public function deactivate() {
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
     */
    public static function uninstall() {
        // Remover todas as opções do plugin
        delete_option('llms_txt_settings');
        
        // Remover metadados de posts
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_llms_txt_protected'");
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_llms_txt_description'");
        
        // Remover o arquivo llms.txt
        $file_path = ABSPATH . 'llms.txt';
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }
}
