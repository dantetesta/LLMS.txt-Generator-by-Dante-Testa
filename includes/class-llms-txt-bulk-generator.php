<?php
/**
 * Classe para gerenciar a geração em massa de descrições LLMS
 *
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @author Dante Testa (https://dantetesta.com.br)
 */

// Evitar acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe para gerenciar a geração em massa de descrições técnicas
 * 
 * Permite a geração individual e em lote de descrições técnicas para posts,
 * páginas e Custom Post Types habilitados no plugin.
 */
class LLMS_Txt_Bulk_Generator {

    /**
     * Instância única da classe (padrão singleton)
     *
     * @var self
     */
    private static $instance = null;

    /**
     * Tipos de post habilitados para geração em massa
     *
     * @var array
     */
    private $post_types = [];

    /**
     * Construtor
     */
    private function __construct() {
        // Registrar hooks
        add_action('admin_init', array($this, 'register_admin_columns'));
        add_action('admin_init', array($this, 'register_bulk_actions'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Endpoints AJAX
        add_action('wp_ajax_llms_txt_generate_single_description', array($this, 'ajax_generate_single_description'));
        add_action('wp_ajax_llms_txt_bulk_process', array($this, 'ajax_bulk_process'));
        
        // Manipulação do bulk action redirecionamento
        add_filter('wp_redirect', array($this, 'handle_bulk_redirect'), 10, 2);
        
        // Carregar tipos de post habilitados
        $this->load_post_types();
    }

    /**
     * Retorna instância única da classe (padrão singleton)
     *
     * @return self
     */
    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Carrega os tipos de post habilitados para geração
     */
    private function load_post_types() {
        // Obter configurações do plugin
        $settings = get_option('llms_txt_settings', array());
        
        // Tipos padrão (sempre habilitados)
        $this->post_types = array('post', 'page');
        
        // Adicionar CPTs habilitados nas configurações
        if (isset($settings['enabled_post_types']) && is_array($settings['enabled_post_types'])) {
            $this->post_types = array_merge($this->post_types, $settings['enabled_post_types']);
        }
        
        // Filtrar para permitir extensão via código
        $this->post_types = apply_filters('llms_txt_generator_bulk_post_types', $this->post_types);
    }
    
    /**
     * Registra colunas administrativas para os tipos de post habilitados
     */
    public function register_admin_columns() {
        foreach ($this->post_types as $post_type) {
            // Adiciona a coluna
            add_filter("manage_{$post_type}_posts_columns", array($this, 'add_llms_column'));
            
            // Preenche a coluna com o conteúdo
            add_action("manage_{$post_type}_posts_custom_column", array($this, 'populate_llms_column'), 10, 2);
        }
    }
    
    /**
     * Adiciona a coluna "Descrição LLMS" na lista de posts
     *
     * @param array $columns Colunas existentes
     * @return array Colunas modificadas
     */
    public function add_llms_column($columns) {
        $new_columns = array();
        
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            // Adiciona a coluna após a coluna de título
            if ($key === 'title') {
                $new_columns['llms_txt_description'] = __('Descrição LLMS', 'llms-txt-generator');
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Preenche a coluna com o status da descrição técnica
     *
     * @param string $column_name Nome da coluna
     * @param int $post_id ID do post
     */
    public function populate_llms_column($column_name, $post_id) {
        if ($column_name !== 'llms_txt_description') {
            return;
        }
        
        // Verificar se o post está excluído do LLMS.txt
        $is_excluded = get_post_meta($post_id, '_llms_txt_exclude', true);
        if ($is_excluded) {
            echo '<div class="llms-txt-status" data-post-id="' . esc_attr($post_id) . '">';
            echo '<span class="dashicons dashicons-hidden llms-txt-icon-pending" title="' . esc_attr__('Excluído do LLMS.txt', 'llms-txt-generator') . '"></span>';
            echo '</div>';
            return;
        }
        
        // Verificar se já tem descrição técnica
        $description = get_post_meta($post_id, '_llms_txt_description', true);
        $nonce = wp_create_nonce('llms_txt_generate_description_' . $post_id);
        
        echo '<div class="llms-txt-status" data-post-id="' . esc_attr($post_id) . '">';
        
        if (!empty($description)) {
            // Já possui descrição - mostrar ícone de sucesso e opção para atualizar
            echo '<span class="dashicons dashicons-yes-alt llms-txt-icon-success" title="' . esc_attr__('Descrição técnica gerada', 'llms-txt-generator') . '"></span>';
            echo ' <a href="#" class="llms-txt-generate-single" data-post-id="' . esc_attr($post_id) . '" data-nonce="' . esc_attr($nonce) . '" title="' . esc_attr__('Regenerar descrição', 'llms-txt-generator') . '">';
            echo '<span class="dashicons dashicons-update"></span>';
            echo '</a>';
        } else {
            // Sem descrição - mostrar ícone de erro e opção para gerar
            echo '<span class="dashicons dashicons-no llms-txt-icon-error" title="' . esc_attr__('Sem descrição técnica', 'llms-txt-generator') . '"></span>';
            echo ' <a href="#" class="llms-txt-generate-single" data-post-id="' . esc_attr($post_id) . '" data-nonce="' . esc_attr($nonce) . '" title="' . esc_attr__('Gerar descrição', 'llms-txt-generator') . '">';
            echo '<span class="dashicons dashicons-update"></span>';
            echo '</a>';
        }
        
        echo '</div>';
    }
    
    /**
     * Registra ações em massa para os tipos de post habilitados
     */
    public function register_bulk_actions() {
        foreach ($this->post_types as $post_type) {
            // Adiciona a ação em massa para o tipo de post
            add_filter("bulk_actions-edit-{$post_type}", array($this, 'add_bulk_action'));
        }
    }
    
    /**
     * Adiciona a ação em massa para geração de descrições
     *
     * @param array $actions Ações existentes
     * @return array Ações modificadas
     */
    public function add_bulk_action($actions) {
        $actions['llms_txt_generate_descriptions'] = __('Gerar descrições LLMS', 'llms-txt-generator');
        return $actions;
    }
    
    /**
     * Manipula o redirecionamento após ação em massa
     *
     * @param string $location URL de redirecionamento
     * @param string $status Código de status HTTP
     * @return string URL de redirecionamento modificada
     */
    public function handle_bulk_redirect($location, $status) {
        // Verificar se estamos em uma página de edição de posts (admin)
        if (!is_admin() || !isset($_REQUEST['post_type'])) {
            return $location;
        }
        
        // Verificar se o tipo de post está habilitado para este plugin
        $current_post_type = isset($_REQUEST['post_type']) ? sanitize_text_field($_REQUEST['post_type']) : 'post';
        if (!in_array($current_post_type, $this->post_types)) {
            return $location;
        }
        
        // Verificar se é uma ação em massa
        if (!isset($_REQUEST['action']) || $_REQUEST['action'] !== 'llms_txt_generate_descriptions') {
            if (!isset($_REQUEST['action2']) || $_REQUEST['action2'] !== 'llms_txt_generate_descriptions') {
                return $location;
            }
        }

        // Obter os IDs dos posts selecionados
        $post_ids = array();
        
        // Posts podem vir como array (checkbox múltiplos) ou como ID único
        if (isset($_REQUEST['post']) && !empty($_REQUEST['post'])) {
            $post_ids = is_array($_REQUEST['post']) ? $_REQUEST['post'] : array($_REQUEST['post']);
        }
        
        // Limitar a 500 itens por vez para evitar problemas de timeout e memória
        if (!empty($post_ids)) {
            $post_ids = array_slice($post_ids, 0, 500);
            
            // Salvar a lista de post IDs na opção para processamento assíncrono
            update_option('llms_txt_bulk_queue', $post_ids);
            
            // Adicionar flag para iniciar o processamento na página de listagem
            return add_query_arg('llms_txt_bulk_process', 'start', $location);
        }
        
        // Se não há posts selecionados
        return add_query_arg('llms_txt_bulk_error', 'no_posts', $location);
    }
    
    /**
     * Enfileira scripts e estilos para as páginas administrativas
     *
     * @param string $hook Hook atual da página administrativa
     */
    public function enqueue_assets($hook) {
        // Verificar se estamos na página de edição de posts
        if ($hook !== 'edit.php') {
            return;
        }
        
        // Verificar se o tipo de post atual está habilitado
        $current_screen = get_current_screen();
        if (!$current_screen || !in_array($current_screen->post_type, $this->post_types)) {
            return;
        }
        
        // Enfileirar estilos
        wp_enqueue_style(
            'llms-txt-bulk-generator',
            LLMS_TXT_GENERATOR_URL . 'assets/css/llms-bulk-generator.css',
            array(),
            LLMS_TXT_GENERATOR_VERSION
        );
        
        // Enfileirar script de admin columns
        wp_enqueue_script(
            'llms-txt-admin-columns',
            LLMS_TXT_GENERATOR_URL . 'assets/js/admin-columns.js',
            array('jquery'),
            LLMS_TXT_GENERATOR_VERSION,
            true
        );
        
        // Localização para admin columns
        wp_localize_script('llms-txt-admin-columns', 'llmsTxtAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'generateText' => __('Gerando...', 'llms-txt-generator'),
            'successText' => __('Descrição gerada com sucesso', 'llms-txt-generator'),
            'errorText' => __('Erro ao gerar descrição', 'llms-txt-generator')
        ));
        
        // Verificar se há um processo em massa para iniciar
        $bulk_process = isset($_GET['llms_txt_bulk_process']) ? sanitize_text_field($_GET['llms_txt_bulk_process']) : '';
        
        if ($bulk_process === 'start') {
            // Enfileirar script de processamento em massa
            wp_enqueue_script(
                'llms-txt-bulk-processor',
                LLMS_TXT_GENERATOR_URL . 'assets/js/bulk-processor.js',
                array('jquery'),
                LLMS_TXT_GENERATOR_VERSION,
                true
            );
            
            // Localização para processador em massa
            wp_localize_script('llms-txt-bulk-processor', 'llmsTxtBulk', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('llms_txt_bulk_process'),
                'processingTitle' => __('Gerando descrições LLMS', 'llms-txt-generator'),
                'completedTitle' => __('Processamento concluído', 'llms-txt-generator'),
                'cancelledTitle' => __('Processamento cancelado', 'llms-txt-generator'),
                'successText' => __('Sucesso', 'llms-txt-generator'),
                'errorText' => __('Erros', 'llms-txt-generator'),
                'cancelText' => __('Cancelar', 'llms-txt-generator'),
                'closeText' => __('Fechar', 'llms-txt-generator'),
                'delayMessage' => __('Pausa de 30 segundos para evitar bloqueio de API', 'llms-txt-generator'),
                'isListingPage' => true
            ));
            
            // Adicionar script inline para iniciar o processamento
            $queue = get_option('llms_txt_bulk_queue', array());
            
            if (!empty($queue)) {
                // Garantir que todos os IDs sejam inteiros para evitar problemas de formatação
                $queue = array_map('intval', $queue);
                $queue_json = json_encode($queue);
                
                // Registrar script com um ID para debugging
                wp_add_inline_script('llms-txt-bulk-processor', "
                    console.log('Iniciando processamento em massa LLMS.txt');
                    console.log('Posts na fila: " . count($queue) . "');
                    jQuery(document).ready(function($) {
                        // Trigger com delay para garantir que tudo esteja carregado
                        setTimeout(function() {
                            $(document).trigger('llms_txt_init_bulk_process', [" . $queue_json . "]);
                        }, 500);
                    });
                ");
                
                // Limpar a fila após iniciar o processamento
                delete_option('llms_txt_bulk_queue');
            }
        }
        
        // Mostrar notificação de erro, se aplicável
        $error = isset($_GET['llms_txt_bulk_error']) ? sanitize_text_field($_GET['llms_txt_bulk_error']) : '';
        
        if ($error) {
            $error_message = '';
            
            switch ($error) {
                case 'no_posts':
                    $error_message = __('Nenhum post foi selecionado para gerar descrições.', 'llms-txt-generator');
                    break;
                default:
                    $error_message = __('Erro desconhecido durante o processamento em massa.', 'llms-txt-generator');
            }
            
            add_action('admin_notices', function() use ($error_message) {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($error_message) . '</p></div>';
            });
        }
    }
    
    /**
     * Endpoint AJAX para geração de descrição técnica individual
     */
    public function ajax_generate_single_description() {
        // Verificar nonce e permissões
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $is_bulk = isset($_POST['is_bulk']) && $_POST['is_bulk'];
        
        // Verificar nonce - usando verificador diferente para bulk vs individual
        if (!isset($_POST['nonce'])) {
            wp_send_json_error(array('message' => __('Erro de segurança: nonce não fornecido.', 'llms-txt-generator')));
        }
        
        if ($is_bulk) {
            // Para requisições em massa, usar o nonce geral
            if (!wp_verify_nonce($_POST['nonce'], 'llms_txt_bulk_process')) {
                wp_send_json_error(array('message' => __('Erro de segurança. Recarregue a página e tente novamente.', 'llms-txt-generator')));
            }
        } else {
            // Para requisições individuais, usar o nonce específico do post
            if (!wp_verify_nonce($_POST['nonce'], 'llms_txt_generate_description_' . $post_id)) {
                wp_send_json_error(array('message' => __('Erro de segurança. Recarregue a página e tente novamente.', 'llms-txt-generator')));
            }
        }
        
        // Verificar permissões
        $post = get_post($post_id);
        if (!$post || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(array('message' => __('Você não tem permissão para editar este post.', 'llms-txt-generator')));
        }
        
        // Verificar se o post está excluído do LLMS.txt
        $is_excluded = get_post_meta($post_id, '_llms_txt_exclude', true);
        if ($is_excluded) {
            wp_send_json_error(array('message' => __('Este item está marcado como excluído do LLMS.txt.', 'llms-txt-generator')));
        }
        
        // Gerar descrição técnica
        $description = $this->generate_technical_description($post);
        
        if (is_wp_error($description)) {
            wp_send_json_error(array('message' => $description->get_error_message()));
        }
        
        // Salvar a descrição técnica
        update_post_meta($post_id, '_llms_txt_description', $description);
        
        // Resposta de sucesso
        $is_bulk = isset($_POST['is_bulk']) && $_POST['is_bulk'];
        wp_send_json_success(array(
            'message' => __('Descrição técnica gerada com sucesso.', 'llms-txt-generator'),
            'description' => $description,
            'is_bulk' => $is_bulk
        ));
    }
    
    /**
     * Endpoint AJAX para processamento em massa
     * Não utilizado diretamente - o processamento é feito via JavaScript
     */
    public function ajax_bulk_process() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'llms_txt_bulk_process')) {
            wp_send_json_error(array('message' => __('Erro de segurança. Recarregue a página e tente novamente.', 'llms-txt-generator')));
        }
        
        wp_send_json_success(array(
            'message' => __('Processamento iniciado. O JavaScript gerenciará a fila.', 'llms-txt-generator')
        ));
    }
    
    /**
     * Gera descrição técnica para um post
     *
     * @param WP_Post $post Objeto do post
     * @return string|WP_Error Descrição técnica ou objeto de erro
     */
    private function generate_technical_description($post) {
        // Verificar se algum outro plugin já forneceu uma descrição técnica
        $description = apply_filters('llms_txt_pre_generate_technical_description', '', $post);
        
        if (!empty($description)) {
            return $description;
        }
        
        // Obter configurações do plugin
        $settings = get_option('llms_txt_settings', array());
        
        // Determinar qual API usar
        $api_provider = isset($settings['ai_provider']) ? $settings['ai_provider'] : 'openai';
        
        if ($api_provider === 'deepseek') {
            // Usar DeepSeek
            $description = $this->generate_description_with_deepseek($post);
        } else {
            // Padrão: Usar OpenAI
            $description = $this->generate_description_with_openai($post);
        }
        
        if (is_wp_error($description)) {
            return $description;
        }
        
        // Limitar tamanho da descrição a 350 caracteres
        if (mb_strlen($description) > 350) {
            $description = mb_substr($description, 0, 347) . '...';
        }
        
        return apply_filters('llms_txt_generated_technical_description', $description, $post);
    }
    
    /**
     * Gera descrição técnica usando a API da OpenAI
     *
     * @param WP_Post $post Objeto do post
     * @return string|WP_Error Descrição técnica ou objeto de erro
     */
    private function generate_description_with_openai($post) {
        // Verificar se temos uma chave de API
        $settings = get_option('llms_txt_settings', array());
        $api_key = isset($settings['openai_api_key']) ? $settings['openai_api_key'] : '';
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave de API da OpenAI não configurada.', 'llms-txt-generator'));
        }
        
        // Obter conteúdo do post
        $content = $post->post_content;
        
        // Limpar o conteúdo para enviar à API
        $content = wp_strip_all_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Limitar o conteúdo para não exceder os limites da API
        if (mb_strlen($content) > 4000) {
            $content = mb_substr($content, 0, 4000);
        }
        
        // Criar prompt para a API
        $prompt = sprintf(
            'Crie uma descrição técnica objetiva e concisa para o seguinte conteúdo. A descrição deve ter no máximo 350 caracteres (1-3 frases) e ser focada nos aspectos técnicos do assunto. Não use linguagem promocional. Título: "%s". Conteúdo: "%s"',
            $post->post_title,
            $content
        );
        
        // Requisição para a API da OpenAI
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'timeout' => 30,
            'body' => json_encode(array(
                'model' => 'gpt-3.5-turbo',
                'messages' => array(
                    array(
                        'role' => 'system',
                        'content' => 'Você é um assistente especializado em criar descrições técnicas concisas para arquivos llms.txt.'
                    ),
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'temperature' => 0.3,
                'max_tokens' => 150,
            ))
        ));
        
        // Verificar erros na resposta
        if (is_wp_error($response)) {
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : __('Erro desconhecido da API OpenAI.', 'llms-txt-generator');
            return new WP_Error('api_error', $error_message);
        }
        
        // Extrair descrição da resposta
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!isset($data['choices'][0]['message']['content'])) {
            return new WP_Error('invalid_response', __('Resposta inválida da API OpenAI.', 'llms-txt-generator'));
        }
        
        return trim($data['choices'][0]['message']['content']);
    }
    
    /**
     * Gera descrição técnica usando a API do DeepSeek via OpenRouter
     *
     * @param WP_Post $post Objeto do post
     * @return string|WP_Error Descrição técnica ou objeto de erro
     */
    private function generate_description_with_deepseek($post) {
        // Verificar se temos uma chave de API
        $settings = get_option('llms_txt_settings', array());
        $api_key = isset($settings['deepseek_api_key']) ? $settings['deepseek_api_key'] : '';
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave de API do DeepSeek não configurada.', 'llms-txt-generator'));
        }
        
        // Obter conteúdo do post
        $content = $post->post_content;
        
        // Limpar o conteúdo para enviar à API
        $content = wp_strip_all_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Limitar o conteúdo para não exceder os limites da API
        if (mb_strlen($content) > 4000) {
            $content = mb_substr($content, 0, 4000);
        }
        
        // Criar prompt para a API
        $prompt = sprintf(
            'Crie uma descrição técnica objetiva e concisa para o seguinte conteúdo. A descrição deve ter no máximo 350 caracteres (1-3 frases) e ser focada nos aspectos técnicos do assunto. Não use linguagem promocional. Título: "%s". Conteúdo: "%s"',
            $post->post_title,
            $content
        );
        
        // Requisição para a API do DeepSeek via OpenRouter
        $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => site_url(),
                'X-Title' => 'LLMS.txt Generator'
            ),
            'timeout' => 30,
            'body' => json_encode(array(
                'model' => 'deepseek/deepseek-chat-v3-0324:free',
                'messages' => array(
                    array(
                        'role' => 'system',
                        'content' => 'Você é um assistente especializado em criar descrições técnicas concisas para arquivos llms.txt.'
                    ),
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'temperature' => 0.3,
                'max_tokens' => 150,
            ))
        ));
        
        // Verificar erros na resposta
        if (is_wp_error($response)) {
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : __('Erro desconhecido da API DeepSeek.', 'llms-txt-generator');
            return new WP_Error('api_error', $error_message);
        }
        
        // Extrair descrição da resposta
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!isset($data['choices'][0]['message']['content'])) {
            return new WP_Error('invalid_response', __('Resposta inválida da API DeepSeek.', 'llms-txt-generator'));
        }
        
        return trim($data['choices'][0]['message']['content']);
    }
}

// Inicializar a classe
LLMS_Txt_Bulk_Generator::get_instance();
