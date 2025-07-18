<?php
/**
 * Classe para gerenciar a interface administrativa do LLMS.txt Generator
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
 * Classe responsável por gerenciar a interface administrativa do plugin
 * 
 * @since 1.0.0
 */
class LLMS_Txt_Admin {

    /**
     * Instância única da classe (padrão Singleton)
     *
     * @since 1.0.0
     * @var LLMS_Txt_Admin
     */
    private static $instance = null;

    /**
     * Obtém a instância única da classe
     *
     * @since 1.0.0
     * @return LLMS_Txt_Admin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe
     * Registra os hooks necessários para a interface administrativa
     *
     * @since 1.0.0
     */
    private function __construct() {
        // Adicionar página de configurações
        add_action('admin_menu', array($this, 'add_settings_page'));
        
        // Registrar configurações
        add_action('admin_init', array($this, 'register_settings'));
        
        // Adicionar links de ação no plugin
        add_filter('plugin_action_links_' . plugin_basename(LLMS_TXT_GENERATOR_FILE), array($this, 'add_action_links'));
        
        // Registrar scripts e estilos
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Adicionar AJAX handler para validação da API
        add_action('wp_ajax_llms_txt_validate_api_key', array($this, 'ajax_validate_api_key'));
    }

    /**
     * Adiciona a página de configurações ao menu administrativo
     *
     * @since 1.0.0
     */
    public function add_settings_page() {
        add_options_page(
            __('LLMS.txt Generator', 'llms-txt-generator'),
            __('LLMS.txt Generator', 'llms-txt-generator'),
            'manage_options',
            'llms-txt-generator',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Renderiza a página de configurações
     *
     * @since 1.0.0
     */
    public function render_settings_page() {
        // Incluir o template da página de configurações
        include plugin_dir_path(dirname(__FILE__)) . 'templates/admin-page.php';
    }

    /**
     * Registra as configurações do plugin
     *
     * @since 1.0.0
     */
    public function register_settings() {
        register_setting(
            'llms_txt_settings',
            'llms_txt_settings',
            array($this, 'sanitize_settings')
        );
        
        add_settings_section(
            'llms_txt_section_general',
            __('Configurações Gerais', 'llms-txt-generator'),
            array($this, 'section_general_callback'),
            'llms_txt_settings'
        );
        
        add_settings_field(
            'enabled',
            __('Habilitar LLMS.txt', 'llms-txt-generator'),
            array($this, 'field_enabled_callback'),
            'llms_txt_settings',
            'llms_txt_section_general'
        );
        
        add_settings_field(
            'site_description',
            __('Descrição do Site', 'llms-txt-generator'),
            array($this, 'field_site_description_callback'),
            'llms_txt_settings',
            'llms_txt_section_general'
        );
        
        add_settings_field(
            'include_posts',
            __('Incluir Posts', 'llms-txt-generator'),
            array($this, 'field_include_posts_callback'),
            'llms_txt_settings',
            'llms_txt_section_general'
        );
        
        add_settings_field(
            'include_pages',
            __('Incluir Páginas', 'llms-txt-generator'),
            array($this, 'field_include_pages_callback'),
            'llms_txt_settings',
            'llms_txt_section_general'
        );
        
        add_settings_field(
            'post_types',
            __('Tipos de Post Adicionais', 'llms-txt-generator'),
            array($this, 'field_post_types_callback'),
            'llms_txt_settings',
            'llms_txt_section_general'
        );
        
        add_settings_field(
            'custom_content',
            __('Conteúdo Personalizado', 'llms-txt-generator'),
            array($this, 'field_custom_content_callback'),
            'llms_txt_settings',
            'llms_txt_section_general'
        );
        
        add_settings_section(
            'llms_txt_section_ai',
            __('Integração com IA', 'llms-txt-generator'),
            array($this, 'section_ai_callback'),
            'llms_txt_settings'
        );
        
        add_settings_field(
            'ai_provider',
            __('Provedor de IA', 'llms-txt-generator'),
            array($this, 'field_ai_provider_callback'),
            'llms_txt_settings',
            'llms_txt_section_ai'
        );
        
        add_settings_field(
            'openai_api_key',
            __('Chave da API OpenAI', 'llms-txt-generator'),
            array($this, 'field_openai_api_key_callback'),
            'llms_txt_settings',
            'llms_txt_section_ai'
        );
        
        add_settings_field(
            'deepseek_api_key',
            __('Chave da API DeepSeek V3', 'llms-txt-generator'),
            array($this, 'field_deepseek_api_key_callback'),
            'llms_txt_settings',
            'llms_txt_section_ai'
        );
        
        add_settings_field(
            'auto_generate',
            __('Geração Automática', 'llms-txt-generator'),
            array($this, 'field_auto_generate_callback'),
            'llms_txt_settings',
            'llms_txt_section_ai'
        );
    }

    /**
     * Sanitiza as configurações antes de salvar
     *
     * @since 1.0.0
     * @param array $input Configurações a serem sanitizadas
     * @return array Configurações sanitizadas
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        // Habilitado ou desabilitado
        $sanitized['enabled'] = isset($input['enabled']) ? '1' : '0';
        
        // Descrição do site
        if (isset($input['site_description'])) {
            $sanitized['site_description'] = sanitize_text_field($input['site_description']);
        }
        
        // Incluir posts - garante que o valor seja processado corretamente
        // Se o valor for enviado como 1, significa que o checkbox está marcado
        // Se o valor for enviado como 0, significa que o checkbox não está marcado
        // O campo oculto garante que sempre teremos um valor, mesmo quando o checkbox não está marcado
        $sanitized['include_posts'] = isset($input['include_posts']) && $input['include_posts'] === '1' ? '1' : '0';
        
        // Incluir páginas - mesma lógica do campo anterior
        $sanitized['include_pages'] = isset($input['include_pages']) && $input['include_pages'] === '1' ? '1' : '0';
        
        // Log para debug
        error_log('LLMS.txt: Salvando configurações - include_posts: ' . $sanitized['include_posts'] . ', include_pages: ' . $sanitized['include_pages']);
        
        // Tipos de post adicionais
        if (isset($input['post_types']) && is_array($input['post_types'])) {
            $sanitized['post_types'] = array_map('sanitize_text_field', $input['post_types']);
            
            // Processar fonte de conteúdo e campos personalizados para cada CPT
            if (isset($input['cpt_content_source']) && is_array($input['cpt_content_source'])) {
                $sanitized['cpt_content_source'] = array();
                
                // Para cada CPT selecionado, salvar sua fonte de conteúdo
                foreach ($sanitized['post_types'] as $post_type) {
                    if (isset($input['cpt_content_source'][$post_type])) {
                        $source = sanitize_text_field($input['cpt_content_source'][$post_type]);
                        $valid_sources = array('post_content', 'post_excerpt', 'custom_fields');
                        
                        // Verificar se a fonte é válida
                        if (in_array($source, $valid_sources)) {
                            $sanitized['cpt_content_source'][$post_type] = $source;
                        } else {
                            $sanitized['cpt_content_source'][$post_type] = 'post_content'; // Valor padrão
                        }
                    } else {
                        // Se não foi especificado, usar o padrão
                        $sanitized['cpt_content_source'][$post_type] = 'post_content';
                    }
                    
                    // Se a fonte for campos personalizados, processar a lista de campos
                    if (isset($sanitized['cpt_content_source'][$post_type]) && 
                        $sanitized['cpt_content_source'][$post_type] === 'custom_fields' && 
                        isset($input['cpt_custom_fields'][$post_type])) {
                        
                        $custom_fields = sanitize_text_field($input['cpt_custom_fields'][$post_type]);
                        $sanitized['cpt_custom_fields'][$post_type] = $custom_fields;
                    }
                }
                
                // Log para debug
                error_log('LLMS.txt: Salvando fontes de conteúdo para CPTs: ' . print_r($sanitized['cpt_content_source'], true));
                if (isset($sanitized['cpt_custom_fields'])) {
                    error_log('LLMS.txt: Salvando campos personalizados para CPTs: ' . print_r($sanitized['cpt_custom_fields'], true));
                }
            }
        } else {
            $sanitized['post_types'] = array(); // Se não houver CPTs selecionados, inicializar como array vazio
        }
        
        // Conteúdo personalizado
        if (isset($input['custom_content'])) {
            $sanitized['custom_content'] = sanitize_textarea_field($input['custom_content']);
        }
        
        // Provedor de IA
        if (isset($input['ai_provider'])) {
            $sanitized['ai_provider'] = sanitize_text_field($input['ai_provider']);
        }
        
        // Chave da API OpenAI
        if (isset($input['openai_api_key'])) {
            $sanitized['openai_api_key'] = sanitize_text_field($input['openai_api_key']);
        }
        
        // Chave da API DeepSeek
        if (isset($input['deepseek_api_key'])) {
            $sanitized['deepseek_api_key'] = sanitize_text_field($input['deepseek_api_key']);
        }
        
        // Geração automática
        $sanitized['auto_generate'] = isset($input['auto_generate']) ? '1' : '0';
        
        // Manter a data da última atualização
        $settings = get_option('llms_txt_settings', array());
        if (isset($settings['last_updated'])) {
            $sanitized['last_updated'] = $settings['last_updated'];
        }
        
        // Adicionar flag para mostrar mensagem de sucesso
        add_settings_error(
            'llms_txt_settings',
            'settings_updated',
            __('Configurações salvas com sucesso!', 'llms-txt-generator'),
            'updated'
        );
        
        // Definir um transient para mostrar um toast personalizado
        set_transient('llms_txt_settings_updated', true, 30);
        
        return $sanitized;
    }

    /**
     * Adiciona links de ação na página de plugins
     *
     * @since 1.0.0
     * @param array $links Links existentes
     * @return array Links modificados
     */
    public function add_action_links($links) {
        $plugin_links = array(
            '<a href="' . admin_url('options-general.php?page=llms-txt-generator') . '">' . __('Configurações', 'llms-txt-generator') . '</a>'
        );
        
        return array_merge($plugin_links, $links);
    }

    /**
     * Registra scripts e estilos para a interface administrativa
     *
     * @since 1.0.0
     * @param string $hook Hook atual do WordPress
     */
    public function enqueue_scripts($hook) {
        // Debug para verificar qual página estamos carregando
        error_log('LLMS Txt Generator: Hook atual: ' . $hook);
        error_log('LLMS Txt Generator: GET page: ' . (isset($_GET['page']) ? $_GET['page'] : 'não definido'));
        
        // Verifica se estamos em qualquer página relacionada ao plugin
        $is_plugin_page = false;
        
        // Detecta todas as páginas relacionadas ao plugin
        if ('settings_page_llms-txt-generator' === $hook || // Página principal de configurações
            strpos($hook, 'llms-txt') !== false || // Qualquer página com llms-txt no hook
            (isset($_GET['page']) && strpos($_GET['page'], 'llms-txt') !== false) || // Parâmetro page na URL
            (isset($_GET['page']) && $_GET['page'] === 'llms-txt-generator') || // Página específica do plugin
            (isset($_GET['post_type']) && strpos($_GET['post_type'], 'llms-txt') !== false)) { // Parâmetro post_type
            $is_plugin_page = true;
            error_log('LLMS Txt Generator: Página do plugin detectada');
        }
        
        // Forçar carregamento se estivermos na página de configurações do WordPress
        if (strpos($hook, 'settings_page') !== false && isset($_GET['page']) && $_GET['page'] === 'llms-txt-generator') {
            $is_plugin_page = true;
            error_log('LLMS Txt Generator: Página de configurações detectada via GET parameter');
        }
        
        // TEMPORÁRIO: Forçar carregamento em todas as páginas admin para debug
        if (is_admin()) {
            $is_plugin_page = true;
            error_log('LLMS Txt Generator: Forçando carregamento em todas as páginas admin');
        }
        
        // Sempre carregar os estilos em todas as páginas admin (para garantir que funcione em qualquer contexto)
        // O CSS está isolado com prefixos então não vai afetar outras partes do admin
        
        // Carregar Tailwind CSS via CDN - necessário para as classes utilizadas no template
        wp_enqueue_style(
            'tailwindcss',
            'https://cdn.tailwindcss.com',
            array(),
            '3.4.0'
        );
        error_log('LLMS Txt Generator: Tailwind CSS carregado via CDN');
        
        // Enqueue do CSS principal - necessário em todas as páginas do admin
        wp_enqueue_style(
            'llms-txt-admin-css',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin-page.css',
            array('tailwindcss'), // Dependência do Tailwind
            LLMS_TXT_GENERATOR_VERSION . '.' . time() // Força recarregar o CSS durante o desenvolvimento
        );
        error_log('LLMS Txt Generator: CSS admin carregado');
        
        // Enqueue do CSS para toast notifications
        wp_enqueue_style(
            'llms-txt-toast-css',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/toast.css',
            array('tailwindcss'), // Dependência do Tailwind
            LLMS_TXT_GENERATOR_VERSION . '.' . time() // Força recarregar o CSS durante o desenvolvimento
        );
        error_log('LLMS Txt Generator: CSS toast carregado');
        
        // Se for página específica do plugin, carregar scripts adicionais
        if ($is_plugin_page) {
            // Registrar e enfileirar o JavaScript principal
            wp_register_script(
                'llms-txt-admin',
                plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin-page.js',
                array('jquery'),
                LLMS_TXT_GENERATOR_VERSION,
                true
            );
            wp_enqueue_script('llms-txt-admin');
            error_log('LLMS Txt Generator: JS admin carregado');
            
            // Localizar script via classe de internacionalização
            LLMS_Txt_I18n::get_instance()->localize_admin_scripts($hook);
        }
    }

    /**
     * Obtém todos os tipos de post disponíveis
     *
     * @since 1.0.0
     * @return array Tipos de post disponíveis
     */
    public function get_available_post_types() {
        $post_types = get_post_types(array(
            'public' => true,
            'show_ui' => true
        ), 'objects');
        
        // Remover tipos de post que não fazem sentido proteger
        $excluded = array('attachment', 'nav_menu_item', 'revision', 'custom_css', 'customize_changeset', 'oembed_cache');
        
        $available = array();
        foreach ($post_types as $post_type) {
            if (!in_array($post_type->name, $excluded)) {
                $available[$post_type->name] = $post_type->labels->name;
            }
        }
        
        return $available;
    }

    /**
     * Obtém as configurações atuais do plugin
     *
     * @since 1.0.0
     * @return array Configurações do plugin
     */
    public function get_settings() {
        $defaults = array(
            'enabled' => '0',
            'custom_rules' => '',
            'include_posts' => '1', // Posts nativos habilitados por padrão
            'include_pages' => '0', // Páginas desabilitadas por padrão
            'protected_post_types' => array('post', 'page'),
            'ai_provider' => 'openai',
            'openai_api_key' => '',
            'deepseek_api_key' => '',
            'auto_generate' => '0',
            'last_updated' => ''
        );
        
        $settings = get_option('llms_txt_settings', array());
        
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Handler AJAX para validação da chave da API
     *
     * @since 1.0.0
     * @updated 1.1.0 Adicionado suporte para validação da API DeepSeek
     */
    /**
     * Valida a chave da API via AJAX
     * 
     * @since 1.0.0
     * @updated 1.1.0 Adicionado suporte para validação da API DeepSeek via OpenRouter
     * @updated 1.1.1 Melhorado o tratamento de erros e resposta para OpenRouter
     */
    public function ajax_validate_api_key() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'llms_txt_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Erro de segurança. Por favor, recarregue a página.', 'llms-txt-generator')));
        }
        
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'llms-txt-generator')));
        }
        
        // Verificar se a chave foi fornecida
        if (!isset($_POST['api_key']) || empty($_POST['api_key'])) {
            wp_send_json_error(array('message' => __('Por favor, insira uma chave de API válida.', 'llms-txt-generator')));
        }
        
        $api_key = sanitize_text_field($_POST['api_key']);
        $api_provider = isset($_POST['api_provider']) ? sanitize_text_field($_POST['api_provider']) : 'openai';
        
        // Configurar os parâmetros da requisição com base no provedor
        if ($api_provider === 'openai') {
            $endpoint = 'https://api.openai.com/v1/models';
            $headers = array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            );
        } else if ($api_provider === 'deepseek') {
            $endpoint = 'https://openrouter.ai/api/v1/models';
            $headers = array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
                'HTTP_X_TITLE' => 'LLMS.txt Generator WordPress Plugin',
            );
        } else {
            wp_send_json_error(array('message' => __('Provedor de API inválido.', 'llms-txt-generator')));
            return;
        }
        
        // Fazer a requisição
        $response = wp_remote_get($endpoint, array(
            'headers' => $headers,
            'timeout' => 15,
            'sslverify' => true,
        ));
        
        // Verificar erro na requisição
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => __('Erro de conexão: ', 'llms-txt-generator') . $response->get_error_message()));
            return;
        }
        
        // Verificar código de resposta
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Tratamento específico para cada provedor
        if ($response_code === 200) {
            // Verificação adicional para OpenRouter (DeepSeek)
            if ($api_provider === 'deepseek') {
                // Verificar se a resposta contém modelos DeepSeek
                $has_deepseek_model = false;
                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as $model) {
                        if (isset($model['id']) && strpos($model['id'], 'deepseek') !== false) {
                            $has_deepseek_model = true;
                            break;
                        }
                    }
                }
                
                if (!$has_deepseek_model) {
                    wp_send_json_error(array('message' => __('A chave da API OpenRouter é válida, mas não tem acesso aos modelos DeepSeek. Verifique sua conta.', 'llms-txt-generator')));
                    return;
                }
            }
            
            // Chave válida
            wp_send_json_success(array('message' => __('Chave da API validada com sucesso!', 'llms-txt-generator')));
        } else {
            // Extrair mensagem de erro da resposta
            $error_message = __('A chave da API parece ser inválida.', 'llms-txt-generator');
            
            if ($api_provider === 'openai') {
                // Formato de erro da OpenAI
                if (isset($data['error']['message'])) {
                    $error_message = $data['error']['message'];
                }
            } else if ($api_provider === 'deepseek') {
                // Formato de erro da OpenRouter
                if (isset($data['error'])) {
                    if (is_string($data['error'])) {
                        $error_message = $data['error'];
                    } else if (isset($data['error']['message'])) {
                        $error_message = $data['error']['message'];
                    }
                } else if (isset($data['message'])) {
                    $error_message = $data['message'];
                }
            }
            
            wp_send_json_error(array('message' => $error_message));
        }
    }
    
    /**
     * Callback para a seção geral
     *
     * @since 1.0.0
     */
    public function section_general_callback() {
        echo '<p>' . __('Configure as opções gerais para o arquivo llms.txt. Este arquivo ajuda as Inteligências Artificiais a entenderem o conteúdo do seu site.', 'llms-txt-generator') . '</p>';
    }
    
    /**
     * Callback para a seção de Integração com IA
     *
     * @since 1.1.0
     */
    public function section_ai_callback() {
        echo '<p>' . __('Configure a integração com APIs de Inteligência Artificial para geração automática de descrições técnicas.', 'llms-txt-generator') . '</p>';
    }
    
    /**
     * Callback para o campo de habilitação do arquivo llms.txt
     *
     * @since 1.0.0
     */
    public function field_enabled_callback() {
        $settings = $this->get_settings();
        $checked = isset($settings['enabled']) && $settings['enabled'] === '1' ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" name="llms_txt_settings[enabled]" id="llms_txt_enabled" value="1" <?php echo $checked; ?>>
            <?php _e('Habilitar arquivo llms.txt', 'llms-txt-generator'); ?>
        </label>
        <p class="description"><?php _e('Se marcado, o arquivo llms.txt será gerado e disponibilizado no seu site.', 'llms-txt-generator'); ?></p>
        <?php
    }
    
    /**
     * Callback para o campo de descrição do site
     *
     * @since 1.0.0
     */
    public function field_site_description_callback() {
        $settings = $this->get_settings();
        $value = isset($settings['site_description']) ? $settings['site_description'] : '';
        ?>
        <textarea name="llms_txt_settings[site_description]" id="llms_txt_site_description" class="large-text" rows="3"><?php echo esc_textarea($value); ?></textarea>
        <p class="description"><?php _e('Adicione uma descrição personalizada para o seu site no arquivo llms.txt. Esta descrição será exibida logo após o título do site.', 'llms-txt-generator'); ?></p>
        <?php
    }
    
    /**
     * Callback para o campo de incluir posts
     *
     * @since 1.2.0
     */
    public function field_include_posts_callback() {
        $settings = $this->get_settings();
        $checked = isset($settings['include_posts']) ? $settings['include_posts'] === '1' : true; // Padrão é habilitado
        $checked = $checked ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" name="llms_txt_settings[include_posts]" id="llms_txt_include_posts" value="1" <?php echo $checked; ?>>
            <?php _e('Incluir posts no arquivo llms.txt', 'llms-txt-generator'); ?>
        </label>
        <p class="description"><?php _e('Se marcado, os posts publicados serão incluídos no arquivo llms.txt e o metabox será exibido na edição de posts.', 'llms-txt-generator'); ?></p>
        <?php
    }
    
    /**
     * Callback para o campo de incluir páginas
     *
     * @since 1.0.0
     */
    public function field_include_pages_callback() {
        $settings = $this->get_settings();
        $checked = isset($settings['include_pages']) && $settings['include_pages'] === '1' ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" name="llms_txt_settings[include_pages]" id="llms_txt_include_pages" value="1" <?php echo $checked; ?>>
            <?php _e('Incluir páginas no arquivo llms.txt', 'llms-txt-generator'); ?>
        </label>
        <p class="description"><?php _e('Se marcado, as páginas publicadas também serão incluídas no arquivo llms.txt e o metabox será exibido na edição de páginas.', 'llms-txt-generator'); ?></p>
        <?php
    }
    
    /**
     * Callback para o campo de tipos de post adicionais
     *
     * @since 1.0.0
     */
    public function field_post_types_callback() {
        $settings = $this->get_settings();
        $post_types = $this->get_available_post_types();
        $selected = isset($settings['post_types']) ? $settings['post_types'] : array();
        
        // Remover post e page, pois já são tratados separadamente
        unset($post_types['post']);
        unset($post_types['page']);
        
        if (empty($post_types)) {
            echo '<p>' . __('Não há tipos de post adicionais disponíveis.', 'llms-txt-generator') . '</p>';
            return;
        }
        
        echo '<p>' . __('Selecione os tipos de post adicionais que deseja incluir no arquivo llms.txt:', 'llms-txt-generator') . '</p>';
        
        foreach ($post_types as $name => $label) {
            $checked = in_array($name, $selected) ? 'checked' : '';
            echo '<label style="display: block; margin-bottom: 5px;">';
            echo '<input type="checkbox" name="llms_txt_settings[post_types][]" value="' . esc_attr($name) . '" ' . $checked . '> ';
            echo esc_html($label);
            echo '</label>';
        }
    }
    
    /**
     * Callback para o campo de conteúdo personalizado
     *
     * @since 1.0.0
     */
    public function field_custom_content_callback() {
        $settings = $this->get_settings();
        $value = isset($settings['custom_content']) ? $settings['custom_content'] : '';
        ?>
        <textarea name="llms_txt_settings[custom_content]" id="llms_txt_custom_content" class="large-text code" rows="10"><?php echo esc_textarea($value); ?></textarea>
        <p class="description"><?php _e('Adicione conteúdo personalizado para o arquivo llms.txt. Use o formato Markdown.', 'llms-txt-generator'); ?></p>
        <?php
    }
    
    /**
     * Callback para o campo de seleção do provedor de IA
     *
     * @since 1.1.0
     */
    public function field_ai_provider_callback() {
        $settings = $this->get_settings();
        $value = isset($settings['ai_provider']) ? $settings['ai_provider'] : 'openai';
        ?>
        <div class="flex flex-col">
            <select name="llms_txt_settings[ai_provider]" id="llms_txt_ai_provider" class="regular-text">
                <option value="openai" <?php selected($value, 'openai'); ?>><?php _e('OpenAI (ChatGPT)', 'llms-txt-generator'); ?></option>
                <option value="deepseek" <?php selected($value, 'deepseek'); ?>><?php _e('DeepSeek V3 (Gratuito via OpenRouter)', 'llms-txt-generator'); ?></option>
            </select>
        </div>
        <p class="description"><?php _e('Selecione qual provedor de IA usar para gerar descrições automáticas.', 'llms-txt-generator'); ?></p>
        <div class="mt-2">
            <p class="text-sm">
                <strong><?php _e('Links úteis:', 'llms-txt-generator'); ?></strong><br>
                <a href="https://platform.openai.com/docs/guides/text-generation" target="_blank" class="text-blue-600 hover:underline"><?php _e('Manual da API OpenAI', 'llms-txt-generator'); ?></a> | 
                <a href="https://openrouter.ai/docs" target="_blank" class="text-blue-600 hover:underline"><?php _e('Manual da API OpenRouter (DeepSeek)', 'llms-txt-generator'); ?></a>
            </p>
        </div>
        <?php
    }

    /**
     * Callback para o campo da chave da API OpenAI
     *
     * @since 1.0.0
     */
    public function field_openai_api_key_callback() {
        $settings = $this->get_settings();
        $value = isset($settings['openai_api_key']) ? $settings['openai_api_key'] : '';
        ?>
        <div class="flex flex-col openai-api-fields" data-provider="openai">
            <input type="password" name="llms_txt_settings[openai_api_key]" id="llms_txt_openai_api_key" class="regular-text" value="<?php echo esc_attr($value); ?>">
            <button type="button" id="llms_txt_validate_openai_api_key" class="button button-secondary mt-2 w-40"><?php _e('Validar Chave', 'llms-txt-generator'); ?></button>
            <div id="llms_txt_openai_api_validation_result" class="mt-2"></div>
        </div>
        <p class="description openai-api-fields" data-provider="openai"><?php _e('Insira sua chave da API OpenAI para habilitar a geração automática de descrições técnicas.', 'llms-txt-generator'); ?></p>
        <p class="text-sm openai-api-fields" data-provider="openai"><a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline"><?php _e('Obter chave da API OpenAI', 'llms-txt-generator'); ?></a></p>
        <?php
    }
    
    /**
     * Callback para o campo da chave da API DeepSeek V3
     *
     * @since 1.1.0
     */
    public function field_deepseek_api_key_callback() {
        $settings = $this->get_settings();
        $value = isset($settings['deepseek_api_key']) ? $settings['deepseek_api_key'] : '';
        ?>
        <div class="flex flex-col deepseek-api-fields" data-provider="deepseek">
            <input type="password" name="llms_txt_settings[deepseek_api_key]" id="llms_txt_deepseek_api_key" class="regular-text" value="<?php echo esc_attr($value); ?>">
            <button type="button" id="llms_txt_validate_deepseek_api_key" class="button button-secondary mt-2 w-40"><?php _e('Validar Chave', 'llms-txt-generator'); ?></button>
            <div id="llms_txt_deepseek_api_validation_result" class="mt-2"></div>
        </div>
        <p class="description deepseek-api-fields" data-provider="deepseek"><?php _e('Insira sua chave da API OpenRouter para usar o modelo DeepSeek V3 (gratuito).', 'llms-txt-generator'); ?></p>
        <p class="text-sm deepseek-api-fields" data-provider="deepseek"><a href="https://openrouter.ai/keys" target="_blank" class="text-blue-600 hover:underline"><?php _e('Obter chave gratuita da API OpenRouter', 'llms-txt-generator'); ?></a></p>
        <?php
    }
    
    /**
     * Callback para o campo de geração automática
     *
     * @since 1.0.0
     */
    public function field_auto_generate_callback() {
        $settings = $this->get_settings();
        $checked = isset($settings['auto_generate']) && $settings['auto_generate'] === '1' ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" name="llms_txt_settings[auto_generate]" id="llms_txt_auto_generate" value="1" <?php echo $checked; ?>>
            <?php _e('Gerar descrições técnicas automaticamente', 'llms-txt-generator'); ?>
        </label>
        <p class="description"><?php _e('Se marcado, o plugin tentará gerar automaticamente descrições técnicas para posts e páginas que não possuem uma descrição personalizada.', 'llms-txt-generator'); ?></p>
        <?php
    }
}
