<?php
/**
 * Classe para gerenciar o arquivo llms.txt
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
 * Classe responsável por gerenciar o arquivo llms.txt
 * 
 * @since 1.0.0
 */
class LLMS_Txt_File {

    /**
     * Instância única da classe (padrão Singleton)
     *
     * @since 1.0.0
     * @var LLMS_Txt_File
     */
    private static $instance = null;

    /**
     * Caminho para o arquivo llms.txt
     *
     * @since 1.0.0
     * @var string
     */
    private $file_path;

    /**
     * URL do arquivo llms.txt
     *
     * @since 1.0.0
     * @var string
     */
    private $file_url;

    /**
     * Obtém a instância única da classe
     *
     * @since 1.0.0
     * @return LLMS_Txt_File
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe
     * Registra os hooks necessários para gerenciar o arquivo llms.txt
     *
     * @since 1.0.0
     */
    private function __construct() {
        // Definir caminho e URL do arquivo
        $this->file_path = ABSPATH . 'llms.txt';
        $this->file_url = home_url('/llms.txt');
        
        // Adicionar hooks
        add_action('init', array($this, 'maybe_serve_file'));
        add_action('llms_txt_regenerate_file', array($this, 'regenerate_file'));
        add_action('admin_init', array($this, 'check_file_exists'));
        
        // Hooks para regenerar o arquivo quando as configurações são salvas
        add_action('update_option_llms_txt_settings', array($this, 'regenerate_file'));
        add_action('add_option_llms_txt_settings', array($this, 'regenerate_file'));
        
        // Adicionar AJAX handlers
        add_action('wp_ajax_llms_txt_get_preview', array($this, 'ajax_get_preview'));
        add_action('wp_ajax_llms_txt_regenerate_file', array($this, 'ajax_regenerate_file'));
    }

    /**
     * Verifica se o arquivo llms.txt existe e o cria se necessário
     *
     * @since 1.0.0
     */
    public function check_file_exists() {
        $settings = get_option('llms_txt_settings', array());
        
        // Verificar se o plugin está habilitado
        if (isset($settings['enabled']) && $settings['enabled'] === '1') {
            // Verificar se o arquivo existe
            if (!file_exists($this->file_path)) {
                $this->regenerate_file();
            }
        }
    }

    /**
     * Serve o arquivo llms.txt quando solicitado
     *
     * @since 1.0.0
     */
    public function maybe_serve_file() {
        // Verificar se estamos acessando o arquivo llms.txt
        if (isset($_SERVER['REQUEST_URI']) && '/llms.txt' === $_SERVER['REQUEST_URI']) {
            // Verificar se o arquivo existe
            if (file_exists($this->file_path)) {
                // Definir headers
                header('Content-Type: text/plain');
                header('X-Robots-Tag: noindex, follow');
                
                // Enviar conteúdo do arquivo
                readfile($this->file_path);
                exit;
            } else {
                // Arquivo não existe, retornar 404
                status_header(404);
                nocache_headers();
                include(get_query_template('404'));
                exit;
            }
        }
    }

    /**
     * Regenera o arquivo llms.txt
     *
     * @since 1.0.0
     * @return bool Verdadeiro se o arquivo foi gerado com sucesso, falso caso contrário
     */
    public function regenerate_file() {
        $settings = get_option('llms_txt_settings', array());
        
        // Verificar se o plugin está habilitado
        if (!isset($settings['enabled']) || $settings['enabled'] !== '1') {
            // Plugin desabilitado, remover arquivo se existir
            if (file_exists($this->file_path)) {
                @unlink($this->file_path);
            }
            return false;
        }
        
        // Gerar conteúdo do arquivo
        $content = $this->generate_content();
        
        // Garantir que o conteúdo esteja em UTF-8
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content));
        }
        
        // Adicionar BOM (Byte Order Mark) para garantir que o arquivo seja reconhecido como UTF-8
        $content = "\xEF\xBB\xBF" . $content;
        
        // Tentar escrever o arquivo com codificação UTF-8
        $result = @file_put_contents($this->file_path, $content);
        
        // Atualizar timestamp da última atualização
        if ($result !== false) {
            $settings['last_updated'] = time();
            update_option('llms_txt_settings', $settings);
            return true;
        }
        
        return false;
    }

    /**
     * Gera o conteúdo do arquivo llms.txt
     *
     * @since 1.0.0
     * @return string Conteúdo do arquivo
     */
    public function generate_content() {
        // Título do site
        $content = "# " . get_bloginfo('name') . "\n\n";
        
        // Obter configurações
        $settings = get_option('llms_txt_settings', array());
        
        // Priorizar a descrição personalizada do site, se existir
        // Caso contrário, usar a descrição padrão do WordPress
        if (!empty($settings['site_description'])) {
            $content .= "> " . $settings['site_description'];
        } else {
            $description = get_bloginfo('description');
            if (!empty($description)) {
                $content .= "> " . $description;
            }
        }
        
        // Adicionar quebra de linha após as descrições
        $content .= "\n\n";
        
        // Adicionar seção de posts se configurado
        if (!isset($settings['include_posts']) || $settings['include_posts'] === '1') {
            $content .= "## Posts\n\n";
            
            // Obter posts publicados
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => -1, // Sem limite - incluir todos os posts
                'orderby' => 'date',
                'order' => 'DESC'
            );
            
            $query = new WP_Query($args);
            
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    
                    $post_id = get_the_ID();
                    
                    // Verificar se o post deve ser excluído do arquivo llms.txt
                    $exclude = get_post_meta($post_id, '_llms_txt_exclude', true);
                    if ($exclude === '1') {
                        continue; // Pular este post
                    }
                    
                    $title = get_the_title();
                    $permalink = get_permalink();
                    $description = $this->get_post_description_for_llms($post_id);
                    
                    $content .= "- [" . $title . "](" . $permalink . "): " . $description . "\n";
                }
                wp_reset_postdata();
            } else {
                $content .= "- Nenhum post encontrado\n";
            }
        }
        
        // Adicionar seção de páginas, se configurado
        if (isset($settings['include_pages']) && $settings['include_pages'] === '1') {
            $content .= "\n## Páginas\n\n";
            
            $args = array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'posts_per_page' => -1, // Sem limite - incluir todas as páginas
                'orderby' => 'title',
                'order' => 'ASC'
            );
            
            $query = new WP_Query($args);
            
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    
                    $post_id = get_the_ID();
                    
                    // Verificar se a página deve ser excluída do arquivo llms.txt
                    $exclude = get_post_meta($post_id, '_llms_txt_exclude', true);
                    if ($exclude === '1') {
                        continue; // Pular esta página
                    }
                    
                    $title = get_the_title();
                    $permalink = get_permalink();
                    $description = $this->get_post_description_for_llms($post_id);
                    
                    $content .= "- [" . $title . "](" . $permalink . "): " . $description . "\n";
                }
                wp_reset_postdata();
            } else {
                $content .= "- Nenhuma página encontrada\n";
            }
        }
        
        // Adicionar seções para cada tipo de post personalizado selecionado
        if (!empty($settings['post_types']) && is_array($settings['post_types'])) {
            foreach ($settings['post_types'] as $post_type) {
                // Obter informações sobre este tipo de post
                $post_type_obj = get_post_type_object($post_type);
                if (!$post_type_obj) {
                    continue;
                }
                
                // Obter o nome plural para o título da seção
                $post_type_label = $post_type_obj->labels->name;
                $content .= "\n## " . $post_type_label . "\n\n";
                
                // Obter configurações de fonte de conteúdo para este CPT
                $content_source = isset($settings['cpt_content_source'][$post_type]) ? 
                    $settings['cpt_content_source'][$post_type] : 'post_content';
                    
                // Verificar se estamos usando campos personalizados
                $custom_fields = array();
                if ($content_source === 'custom_fields' && 
                    isset($settings['cpt_custom_fields'][$post_type]) && 
                    !empty($settings['cpt_custom_fields'][$post_type])) {
                    // Converter string de campos separados por vírgula em array
                    $custom_fields = array_map('trim', explode(',', $settings['cpt_custom_fields'][$post_type]));
                }
                
                // Log para depuração
                error_log('LLMS.txt: Processando CPT ' . $post_type . ' com fonte: ' . $content_source);
                if (!empty($custom_fields)) {
                    error_log('LLMS.txt: Campos personalizados para ' . $post_type . ': ' . print_r($custom_fields, true));
                }
                
                // Obter posts deste tipo
                $args = array(
                    'post_type' => $post_type,
                    'post_status' => 'publish',
                    'posts_per_page' => -1, // Sem limite - incluir todos os posts do CPT
                    'orderby' => 'title',
                    'order' => 'ASC'
                );
                
                $query = new WP_Query($args);
                
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        
                        $post_id = get_the_ID();
                        $post = get_post($post_id);
                        
                        // Verificar se o post deve ser excluído do arquivo llms.txt
                        $exclude = get_post_meta($post_id, '_llms_txt_exclude', true);
                        if ($exclude === '1') {
                            continue; // Pular este post
                        }
                        
                        $title = get_the_title();
                        $permalink = get_permalink();
                        
                        // Verificar se existe uma descrição técnica personalizada (meta box)
                        $meta_box = LLMS_Txt_Meta_Box::get_instance();
                        $custom_description = $meta_box->get_post_description($post_id);
                        $description = '';
                        
                        // Se já temos uma descrição personalizada, usá-la
                        if (!empty($custom_description)) {
                            $description = $custom_description;
                        } else {
                            // Caso contrário, obter conteúdo conforme configuração do CPT
                            switch ($content_source) {
                                case 'post_excerpt':
                                    if (!empty($post->post_excerpt)) {
                                        $description = wp_strip_all_tags($post->post_excerpt);
                                    }
                                    break;
                                    
                                case 'custom_fields':
                                    // Concatenar valores de todos os campos personalizados
                                    $meta_values = array();
                                    foreach ($custom_fields as $field) {
                                        $meta_value = get_post_meta($post_id, $field, true);
                                        if (!empty($meta_value)) {
                                            // Converter array para string se necessário
                                            if (is_array($meta_value)) {
                                                $meta_value = implode(', ', $meta_value);
                                            }
                                            $meta_values[] = wp_strip_all_tags($meta_value);
                                        }
                                    }
                                    
                                    if (!empty($meta_values)) {
                                        $description = implode(' | ', $meta_values);
                                    }
                                    break;
                                    
                                case 'post_content':
                                default:
                                    // Usar o início do conteúdo como descrição
                                    $content_text = wp_strip_all_tags($post->post_content);
                                    $content_text = preg_replace('/\s+/', ' ', $content_text); // Remover quebras de linha e espaços extras
                                    
                                    if (strlen($content_text) > 350) {
                                        $description = substr($content_text, 0, 347) . '...';
                                    } else {
                                        $description = $content_text;
                                    }
                                    break;
                            }
                        }
                        
                        // Se ainda não temos descrição, usar texto genérico
                        if (empty($description)) {
                            $description = __('Sem descrição disponível', 'llms-txt-generator');
                        }
                        
                        $content .= "- [" . $title . "](" . $permalink . "): " . $description . "\n";
                    }
                    wp_reset_postdata();
                } else {
                    $content .= "- Nenhum item encontrado para este tipo de post\n";
                }
            }
        }
        
        // Adicionar informações personalizadas, se existirem
        if (!empty($settings['custom_content'])) {
            $content .= "\n" . $settings['custom_content'] . "\n";
        }
        
        // Adicionar rodapé
        $content .= "\n---\n";
        $content .= "Gerado por LLMS.txt Generator do Dante Testa www.dantetesta.com.br v" . LLMS_TXT_GENERATOR_VERSION . " | " . date('Y-m-d H:i:s') . "\n";
        
        return $content;
    }

    /**
     * Obtém a descrição de um post para o arquivo llms.txt
     *
     * @since 1.0.0
     * @param int $post_id ID do post
     * @return string Descrição do post
     */
    private function get_post_description_for_llms($post_id) {
        // Verificar se existe uma descrição técnica personalizada
        $meta_box = LLMS_Txt_Meta_Box::get_instance();
        $custom_description = $meta_box->get_post_description($post_id);
        
        if (!empty($custom_description)) {
            return $custom_description;
        }
        
        // Verificar se existe uma meta description (SEO)
        $seo_description = '';
        
        // Compatibilidade com plugins SEO populares
        // Yoast SEO
        $yoast_description = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if (!empty($yoast_description)) {
            return $yoast_description;
        }
        
        // Rank Math
        $rank_math_description = get_post_meta($post_id, 'rank_math_description', true);
        if (!empty($rank_math_description)) {
            return $rank_math_description;
        }
        
        // All in One SEO Pack
        $aioseo_description = get_post_meta($post_id, '_aioseop_description', true);
        if (!empty($aioseo_description)) {
            return $aioseo_description;
        }
        
        // Se não houver descrição personalizada ou meta description, usar o resumo ou início do conteúdo
        $post = get_post($post_id);
        
        if (!empty($post->post_excerpt)) {
            return wp_strip_all_tags($post->post_excerpt);
        }
        
        // Usar o início do conteúdo como descrição
        $content = wp_strip_all_tags($post->post_content);
        $content = preg_replace('/\s+/', ' ', $content); // Remover quebras de linha e espaços extras
        
        // Ajustado para 350 caracteres (padrão de 1-3 frases)
        if (strlen($content) > 350) {
            return substr($content, 0, 347) . '...';
        }
        
        return $content;
    }

    /**
     * Obtém o conteúdo atual do arquivo llms.txt
     *
     * @since 1.0.0
     * @return string|bool Conteúdo do arquivo ou falso se o arquivo não existir
     */
    public function get_file_content() {
        if (file_exists($this->file_path)) {
            return file_get_contents($this->file_path);
        }
        
        return false;
    }

    /**
     * Verifica se o arquivo llms.txt existe
     *
     * @since 1.0.0
     * @return bool Verdadeiro se o arquivo existe, falso caso contrário
     */
    public function file_exists() {
        return file_exists($this->file_path);
    }

    /**
     * Obtém a URL do arquivo llms.txt
     *
     * @since 1.0.0
     * @return string URL do arquivo
     */
    public function get_file_url() {
        return $this->file_url;
    }

    /**
     * Obtém a data da última atualização do arquivo
     *
     * @since 1.0.0
     * @return string Data da última atualização ou string vazia se não houver data
     */
    public function get_last_updated() {
        $settings = get_option('llms_txt_settings', array());
        
        if (isset($settings['last_updated'])) {
            return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $settings['last_updated']);
        }
        
        return '';
    }

    /**
     * Handler AJAX para obter a visualização do arquivo
     *
     * @since 1.0.0
     */
    public function ajax_get_preview() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'llms_txt_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Erro de segurança. Por favor, recarregue a página.', 'llms-txt-generator')));
        }
        
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'llms-txt-generator')));
        }
        
        // Gerar conteúdo de visualização
        $content = $this->generate_content();
        
        // Retornar conteúdo
        wp_send_json_success(array('content' => $content));
    }

    /**
     * Handler AJAX para regenerar o arquivo
     *
     * @since 1.0.0
     */
    public function ajax_regenerate_file() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'llms_txt_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Erro de segurança. Por favor, recarregue a página.', 'llms-txt-generator')));
        }
        
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'llms-txt-generator')));
        }
        
        // Regenerar arquivo
        $result = $this->regenerate_file();
        
        if ($result) {
            // Retornar sucesso
            wp_send_json_success(array(
                'message' => __('O arquivo llms.txt foi regenerado com sucesso!', 'llms-txt-generator'),
                'content' => $this->get_file_content(),
                'last_updated' => $this->get_last_updated()
            ));
        } else {
            // Retornar erro
            wp_send_json_error(array('message' => __('Ocorreu um erro ao regenerar o arquivo llms.txt. Verifique as permissões de escrita.', 'llms-txt-generator')));
        }
    }
}
