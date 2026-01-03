<?php
/**
 * Classe para gerenciar a internacionalização do plugin LLMS.txt Generator
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
 * Classe responsável por gerenciar a internacionalização do plugin
 * 
 * @since 1.0.0
 */
class LLMS_Txt_I18n {

    /**
     * Instância única da classe (padrão Singleton)
     *
     * @since 1.0.0
     * @var LLMS_Txt_I18n
     */
    private static $instance = null;

    /**
     * Obtém a instância única da classe
     *
     * @since 1.0.0
     * @return LLMS_Txt_I18n
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe
     * Registra os hooks necessários para a internacionalização
     *
     * @since 1.0.0
     */
    private function __construct() {
        // Carregar arquivos de tradução
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    /**
     * Carrega os arquivos de tradução
     * 
     * Idioma padrão: Português Brasileiro (pt_BR)
     * Segundo idioma: Inglês Americano (en_US)
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {
        // Definir português brasileiro como idioma padrão
        $locale = determine_locale();
        
        // Se o locale não for português brasileiro, carregar tradução apropriada
        if ($locale !== 'pt_BR') {
            load_plugin_textdomain(
                'llms-txt-generator',
                false,
                dirname(plugin_basename(LLMS_TXT_GENERATOR_FILE)) . '/languages/'
            );
        }
        
        // Para português brasileiro, as strings já estão no código em português
        // Não é necessário carregar arquivo de tradução adicional
    }

    /**
     * Localiza scripts para JavaScript
     *
     * @since 1.0.0
     * @param string $hook Hook atual do WordPress
     */
    public function localize_admin_scripts($hook) {
        // Localizar script da página de administração
        if ('settings_page_llms-txt-generator' === $hook) {
            wp_localize_script('llms-txt-admin', 'llms_txt_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('llms_txt_ajax_nonce'),
                'validating_key' => __('Validando chave...', 'llms-txt-generator'),
                'updating_file' => __('Atualizando arquivo...', 'llms-txt-generator'),
                'error_message' => __('Ocorreu um erro. Por favor, tente novamente.', 'llms-txt-generator'),
                'connection_error' => __('Erro de conexão. Verifique sua internet.', 'llms-txt-generator'),
                'show_key' => __('Mostrar', 'llms-txt-generator'),
                'hide_key' => __('Ocultar', 'llms-txt-generator'),
                'file_updated' => __('Arquivo atualizado com sucesso!', 'llms-txt-generator'),
                'file_error' => __('Erro ao atualizar o arquivo.', 'llms-txt-generator'),
                'confirm_regenerate' => __('Tem certeza que deseja regenerar o arquivo llms.txt? Isso substituirá o arquivo atual.', 'llms-txt-generator')
            ));
        }

        // Localizar script da meta box
        if (in_array($hook, array('post.php', 'post-new.php'))) {
            wp_localize_script('llms-txt-meta-box', 'llms_txt_meta_box', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('llms_txt_meta_box_nonce'),
                'characters_remaining' => __('%d caracteres restantes', 'llms-txt-generator'),
                'post_id_missing' => __('ID do post não encontrado', 'llms-txt-generator'),
                'generate_error' => __('Erro ao gerar descrição', 'llms-txt-generator'),
                'generating' => __('Gerando descrição...', 'llms-txt-generator'),
                'description_too_long' => __('A descrição deve ter no máximo 150 caracteres.', 'llms-txt-generator')
            ));
        }
    }
}
