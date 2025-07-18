<?php
/**
 * Plugin Name: LLMS.txt Generator by Dante Testa
 * Plugin URI: https://dantetesta.com.br/plugins/llms-txt-generator
 * Description: Plugin WordPress que gera e gerencia o arquivo llms.txt, permitindo controlar como modelos de IA acessam seu site. Oferece geração automática de descrições técnicas para posts, páginas e CPTs, com suporte à integração com OpenAI e DeepSeek. Ideal para melhorar como as IAs compreendem e representam seu conteúdo.
 * Version: 2.0.0
 * Requires at least: 5.6
 * Requires PHP: 7.0
 * Author: Dante Testa
 * Author URI: https://dantetesta.com.br
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: llms-txt-generator
 * Domain Path: /languages
 *
 * @package LLMS_Txt_Generator
 * @author Dante Testa (https://dantetesta.com.br)
 *
 * LLMS.txt Generator
 * ==================
 * Este plugin permite que proprietários de sites WordPress controlem com precisão
 * como modelos de IA podem acessar e utilizar o conteúdo do seu site. Semelhante
 * ao robots.txt para mecanismos de busca, o arquivo llms.txt fornece diretrizes
 * para sistemas de IA como ChatGPT, Claude, Gemini e outros.
 *
 * Recursos:
 * - Geração automática de descrições técnicas para posts e páginas
 * - Suporte completo a tipos de post personalizados (CPTs)
 * - Configuração granular de fontes de conteúdo para CPTs (post_content, post_excerpt, campos personalizados)
 * - Integração com OpenAI e DeepSeek para geração avançada de descrições
 * - Interface administrativa intuitiva com Tailwind CSS
 * - Controle por post/página para incluir ou excluir do arquivo llms.txt
 * - Meta box para personalização de descrições específicas
 * - Compatível com Gutenberg e Editor Clássico
 * - Otimizado para performance e segurança
 * - Geração em massa de descrições via Admin Columns
 *
 * Copyright (c) 2025 Dante Testa. Todos os direitos reservados.
 */

// Evitar acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes do plugin
define('LLMS_TXT_GENERATOR_VERSION', '1.0.0');
define('LLMS_TXT_GENERATOR_FILE', __FILE__);
define('LLMS_TXT_GENERATOR_PATH', plugin_dir_path(__FILE__));
define('LLMS_TXT_GENERATOR_URL', plugin_dir_url(__FILE__));

/**
 * Função de carregamento do plugin
 * 
 * @since 1.0.0
 */
function llms_txt_generator_load() {
    // Verificar se as classes já foram carregadas
    if (class_exists('LLMS_Txt_Generator')) {
        return;
    }
    
    // Carregar arquivos de classes
    require_once LLMS_TXT_GENERATOR_PATH . 'includes/class-llms-txt-i18n.php';
    require_once LLMS_TXT_GENERATOR_PATH . 'includes/class-llms-txt-meta-box.php';
    require_once LLMS_TXT_GENERATOR_PATH . 'includes/class-llms-txt-file.php';
    require_once LLMS_TXT_GENERATOR_PATH . 'includes/class-llms-txt-admin.php';
    require_once LLMS_TXT_GENERATOR_PATH . 'includes/class-llms-txt-generator.php';
    require_once LLMS_TXT_GENERATOR_PATH . 'includes/class-llms-txt-bulk-generator.php';
    
    // Inicializar o plugin
    LLMS_Txt_Generator::get_instance();
}

// Carregar o plugin
add_action('plugins_loaded', 'llms_txt_generator_load');

/**
 * Cria diretórios necessários na ativação
 * 
 * @since 1.0.0
 */
function llms_txt_generator_activate() {
    // Garantir que o diretório de assets existe
    $assets_dir = LLMS_TXT_GENERATOR_PATH . 'assets';
    
    // Verificar e criar diretórios de assets se necessário
    if (!file_exists($assets_dir . '/js')) {
        wp_mkdir_p($assets_dir . '/js');
    }
    
    if (!file_exists($assets_dir . '/css')) {
        wp_mkdir_p($assets_dir . '/css');
    }
}
register_activation_hook(__FILE__, 'llms_txt_generator_activate');
