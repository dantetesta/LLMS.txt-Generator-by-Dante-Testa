<?php
/**
 * Classe para gerenciar as meta boxes do LLMS.txt Generator
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
 * Classe responsável por gerenciar as meta boxes do plugin
 * 
 * @since 1.0.0
 */
class LLMS_Txt_Meta_Box {

    /**
     * Instância única da classe (padrão Singleton)
     *
     * @since 1.0.0
     * @var LLMS_Txt_Meta_Box
     */
    private static $instance = null;

    /**
     * Obtém a instância única da classe
     *
     * @since 1.0.0
     * @return LLMS_Txt_Meta_Box
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe
     * Registra os hooks necessários para as meta boxes
     *
     * @since 1.0.0
     */
    private function __construct() {
        // Adicionar meta box
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        
        // Salvar dados da meta box
        add_action('save_post', array($this, 'save_meta_box_data'));
        
        // Registrar scripts e estilos
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Adicionar AJAX handlers
        add_action('wp_ajax_llms_txt_generate_description', array($this, 'ajax_generate_description'));
    }

    /**
     * Adiciona a meta box aos tipos de post selecionados
     *
     * @since 1.0.0
     */
    public function add_meta_box() {
        // Obter configurações
        $settings = get_option('llms_txt_settings', array());
        
        // Verificar se o plugin está habilitado
        if (!isset($settings['enabled']) || $settings['enabled'] !== '1') {
            return;
        }
        
        // Inicializar array de tipos de post para adicionar metabox
        $post_types_to_add = array();
        
        // Verificar configuração para posts nativos
        if (isset($settings['include_posts']) && $settings['include_posts'] === '1') {
            $post_types_to_add[] = 'post';
        }
        
        // Verificar configuração para páginas
        if (isset($settings['include_pages']) && $settings['include_pages'] === '1') {
            $post_types_to_add[] = 'page';
        }
        
        // Obter tipos de post personalizados selecionados
        if (isset($settings['post_types']) && is_array($settings['post_types'])) {
            $post_types_to_add = array_merge($post_types_to_add, $settings['post_types']);
        }
        
        // Adicionar meta box aos tipos de post selecionados
        foreach ($post_types_to_add as $post_type) {
            add_meta_box(
                'llms_txt_meta_box',
                __('LLMS.txt - Descrição LLMS', 'llms-txt-generator'),
                array($this, 'render_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * Renderiza o conteúdo da meta box
     *
     * @since 1.0.0
     * @param WP_Post $post Post atual
     */
    public function render_meta_box($post) {
        // Adicionar nonce para verificação
        wp_nonce_field('llms_txt_meta_box', 'llms_txt_meta_box_nonce');
        
        // Obter valores salvos
        $protected = get_post_meta($post->ID, '_llms_txt_protected', true);
        $description = get_post_meta($post->ID, '_llms_txt_description', true);
        
        // Incluir o template da meta box
        include plugin_dir_path(dirname(__FILE__)) . 'templates/meta-box.php';
    }

    /**
     * Salva os dados da meta box
     *
     * @since 1.0.0
     * @param int $post_id ID do post
     */
    public function save_meta_box_data($post_id) {
        // Verificar se é um autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Verificar nonce
        if (!isset($_POST['llms_txt_meta_box_nonce']) || !wp_verify_nonce($_POST['llms_txt_meta_box_nonce'], 'llms_txt_meta_box')) {
            return;
        }
        
        // Verificar permissões
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Salvar dados
        $protected = isset($_POST['llms_txt_protected']) ? '1' : '0';
        update_post_meta($post_id, '_llms_txt_protected', $protected);
        
        // Salvar opção de exclusão do post do arquivo llms.txt
        $exclude = isset($_POST['llms_txt_exclude']) ? '1' : '0';
        update_post_meta($post_id, '_llms_txt_exclude', $exclude);
        
        if (isset($_POST['llms_txt_description'])) {
            $description = sanitize_text_field($_POST['llms_txt_description']);
            update_post_meta($post_id, '_llms_txt_description', $description);
        }
        
        // Regenerar arquivo llms.txt
        $file_manager = LLMS_Txt_File::get_instance();
        $file_manager->regenerate_file();
    }

    /**
     * Registra scripts e estilos para a meta box
     *
     * @since 1.0.0
     * @param string $hook Hook atual do WordPress
     */
    public function enqueue_scripts($hook) {
        // Verificar se estamos na página de edição de post
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        // Verificar se o tipo de post atual tem a meta box
        $settings = get_option('llms_txt_settings', array());
        $post_types = isset($settings['protected_post_types']) ? $settings['protected_post_types'] : array('post', 'page');
        $screen = get_current_screen();
        
        if (!in_array($screen->post_type, $post_types)) {
            return;
        }
        
        // Registrar estilo personalizado para meta box
        wp_register_style(
            'llms-txt-meta-box-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/meta-box.css',
            array(),
            LLMS_TXT_VERSION
        );
        
        // Carregar apenas o estilo da meta box (que já tem o Tailwind como dependência)
        wp_enqueue_style('llms-txt-meta-box-style');
        
        // Adicionar estilo inline para garantir isolamento
        $wrapper_css = ".editor-styles-wrapper .llms-txt-wrapper { all: revert; }"; // Reset para o editor
        wp_add_inline_style('llms-txt-meta-box-style', $wrapper_css);
        
        $custom_css = "
            .classic-editor .llms-txt-meta-box {
                background-color: #fff;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                box-sizing: border-box;
            }
            
            .classic-editor .llms-txt-meta-box textarea {
                width: 100% !important;
                border: 1px solid #ddd;
                padding: 8px;
                border-radius: 4px;
                box-sizing: border-box;
                font-family: inherit;
                font-size: 14px;
                resize: vertical;
            }
            
            .classic-editor .switch {
                position: relative;
                display: inline-block;
                width: 48px;
                height: 24px;
                margin-right: 8px;
                vertical-align: middle;
            }
            
            .classic-editor .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            
            .classic-editor .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 34px;
            }
            
            .classic-editor .slider:before {
                position: absolute;
                content: \"\";
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }
            
            .classic-editor input:checked + .slider {
                background-color: #3b82f6;
            }
            
            .classic-editor input:checked + .slider:before {
                transform: translateX(24px);
            }
        ";
        wp_add_inline_style('llms-txt-meta-box-compat', $custom_css);
        
        // Registrar e enfileirar scripts
        wp_register_script(
            'llms-txt-meta-box',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/meta-box.js',
            array('jquery'),
            LLMS_TXT_GENERATOR_VERSION,
            true
        );
        wp_enqueue_script('llms-txt-meta-box');
        
        // Adicionar CSS inline para o switcher
        $switcher_css = '
        /* Estilo do container do switcher */
        .switch {
            display: inline-block;
            position: relative;
            width: 48px;
            height: 24px;
        }
        
        /* Esconder o checkbox padrão */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        /* Estilo do slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }
        
        /* Estilo do botão do slider */
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
        }
        
        /* Estilo quando o checkbox está marcado */
        input:checked + .slider {
            background-color: #3b82f6;
        }
        
        input:focus + .slider {
            box-shadow: 0 0 1px #3b82f6;
        }
        
        /* Movimento do botão quando marcado */
        input:checked + .slider:before {
            transform: translateX(24px);
        }
        
        /* Estilo arredondado */
        .slider.round {
            border-radius: 24px;
        }
        
        .slider.round:before {
            border-radius: 50%;
        }
        ';
        
        wp_add_inline_style('tailwindcss', $switcher_css);
        
        // Localizar script via classe de internacionalização
        LLMS_Txt_I18n::get_instance()->localize_admin_scripts($hook);
    }

    /**
     * Obtém todos os posts protegidos
     *
     * @since 1.0.0
     * @return array Posts protegidos
     */
    public function get_protected_posts() {
        // Obter configurações
        $settings = get_option('llms_txt_settings', array());
        
        // Verificar se o plugin está habilitado
        if (!isset($settings['enabled']) || $settings['enabled'] !== '1') {
            return array();
        }
        
        // Obter tipos de post protegidos
        $post_types = isset($settings['protected_post_types']) ? $settings['protected_post_types'] : array('post', 'page');
        
        // Consultar posts protegidos
        $args = array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_llms_txt_protected',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        
        $query = new WP_Query($args);
        
        return $query->posts;
    }

    /**
     * Obtém a descrição técnica de um post
     *
     * @since 1.0.0
     * @param int $post_id ID do post
     * @return string Descrição técnica
     */
    public function get_post_description($post_id) {
        return get_post_meta($post_id, '_llms_txt_description', true);
    }

    /**
     * Handler AJAX para geração automática de descrição técnica
     *
     * @since 1.0.0
     * @updated 1.1.0 Adicionado suporte para API DeepSeek R1 via OpenRouter
     */
    public function ajax_generate_description() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'llms_txt_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Erro de segurança. Por favor, recarregue a página.', 'llms-txt-generator')));
        }
        
        // Verificar permissões
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'llms-txt-generator')));
        }
        
        $post_id = intval($_POST['post_id']);
        
        // Verificar se o post existe
        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error(array('message' => __('Post não encontrado.', 'llms-txt-generator')));
        }
        
        // Obter configurações e verificar API selecionada
        $settings = get_option('llms_txt_settings', array());
        $api_provider = isset($settings['ai_provider']) ? $settings['ai_provider'] : 'openai';
        
        // Verificar se a chave da API está configurada
        if ($api_provider === 'openai') {
            if (!isset($settings['openai_api_key']) || empty($settings['openai_api_key'])) {
                wp_send_json_error(array('message' => __('Chave da API OpenAI não configurada.', 'llms-txt-generator')));
            }
            $api_key = $settings['openai_api_key'];
        } elseif ($api_provider === 'deepseek') {
            if (!isset($settings['deepseek_api_key']) || empty($settings['deepseek_api_key'])) {
                wp_send_json_error(array('message' => __('Chave da API OpenRouter (DeepSeek) não configurada.', 'llms-txt-generator')));
            }
            $api_key = $settings['deepseek_api_key'];
        } else {
            wp_send_json_error(array('message' => __('Provedor de API inválido.', 'llms-txt-generator')));
        }
        
        // Obter conteúdo do post
        $content = $post->post_title . ' ' . strip_tags($post->post_content);
        $content = substr($content, 0, 2000); // Limitado a 2000 caracteres para melhor contexto
        
        // Prompt para geração de descrição técnica
      // Prompt para geração de descrição para llms.txt
// Prompt para geração de descrição para llms.txt
$system_prompt = 'Analise o [post_title] e [post_content] fornecidos e crie uma descrição objetiva de 1 a 3 frases (máximo de 350 caracteres) para sistemas de IA. '
               . 'Estruture a descrição seguindo: [Descrição direta do que o conteúdo oferece]. [Especificação técnica ou método utilizado]. [Público-alvo definido] que [problema específico resolvido]. [Benefícios práticos principais]. [Contexto adicional relevante] [...] '
               . 'Diretrizes: Inicie com declaração clara do que o conteúdo oferece, inclua especificações técnicas quando relevantes, '
               . 'defina o público-alvo objetivamente, mencione benefícios práticos específicos, use terminologia precisa para facilitar indexação, '
               . 'evite perguntas retóricas ou linguagem persuasiva, mantenha tom informativo e descritivo. '
               . 'Exemplo: "Sistema completo de cardápio digital com integração WhatsApp usando apenas HTML e Google Sheets. Solução prática para comerciantes que buscam simplicidade sem abrir mão de visual profissional e funcionalidade moderna. Elimina necessidade de conhecimento técnico avançado e reduz custos operacionais [...]" '
               . 'Importante: Retorne apenas o texto da descrição sem aspas ou delimitadores.';

        // Preparar requisição de acordo com o provedor selecionado
        if ($api_provider === 'openai') {
            // Requisição para a API OpenAI
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
                            'content' => $system_prompt
                        ),
                        array(
                            'role' => 'user',
                            'content' => $content
                        )
                    ),
                    'max_tokens' => 150, // Ajustado para o limite de 350 caracteres (1-3 frases)
                    'temperature' => 0.5, // Mantido para respostas precisas e técnicas
                ))
            ));
        } else {
            // Requisição para a API DeepSeek R1 via OpenRouter
            $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => site_url(), // Requerido pela OpenRouter
                    'X-Title' => 'LLMS.txt Generator' // Nome da aplicação
                ),
                'timeout' => 30,
                'body' => json_encode(array(
                    'model' => 'deepseek/deepseek-chat-v3-0324:free',
                    'messages' => array(
                        array(
                            'role' => 'system',
                            'content' => $system_prompt
                        ),
                        array(
                            'role' => 'user',
                            'content' => $content
                        )
                    ),
                    'max_tokens' => 150,
                    'temperature' => 0.5,
                ))
            ));
        }
        
        // Verificar erro na requisição
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        // Verificar código de resposta
        $response_code = wp_remote_retrieve_response_code($response);
        
        if ($response_code !== 200) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : __('Erro ao comunicar com a API.', 'llms-txt-generator');
            
            wp_send_json_error(array('message' => $error_message));
        }
        
        // Processar resposta
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            $description = trim($data['choices'][0]['message']['content']);
            
            // Limitar a 350 caracteres (padrão de 1-3 frases)
            if (strlen($description) > 350) {
                $description = substr($description, 0, 347) . '...';
            }
            
            // Salvar a descrição como meta dado
            update_post_meta($post_id, '_llms_txt_description', $description);
            
            wp_send_json_success(array(
                'message' => __('Descrição técnica gerada com sucesso!', 'llms-txt-generator'),
                'description' => $description
            ));
        } else {
            wp_send_json_error(array('message' => __('Erro ao processar a resposta da API.', 'llms-txt-generator')));
        }
    }
}
