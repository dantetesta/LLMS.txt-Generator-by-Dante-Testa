<?php
/**
 * Template para a meta box do LLMS.txt Generator
 *
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @updated 1.1.0 Adicionado suporte para DeepSeek V3
 * @author Dante Testa (https://dantetesta.com.br)
 */

// Evitar acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obter configurações
$settings = get_option('llms_txt_settings', array());
$api_provider = isset($settings['ai_provider']) ? $settings['ai_provider'] : 'openai';

// Verificar se a chave da API está configurada
$has_api_key = false;
if ($api_provider === 'openai') {
    $has_api_key = isset($settings['openai_api_key']) && !empty($settings['openai_api_key']);
    $provider_name = 'OpenAI';
    $provider_color = 'bg-green-500';
} elseif ($api_provider === 'deepseek') {
    $has_api_key = isset($settings['deepseek_api_key']) && !empty($settings['deepseek_api_key']);
    $provider_name = 'DeepSeek V3';
    $provider_color = 'bg-blue-500';
}

// Detectar se estamos usando o editor clássico
// Verificamos várias condições para identificar corretamente o editor clássico
$using_classic_editor = false;

// Verifica se o Gutenberg está ativo
if (function_exists('is_gutenberg_page')) {
    $using_classic_editor = !is_gutenberg_page();
} 

// Verifica se o post tem blocks (blocos Gutenberg)
if (!$using_classic_editor && function_exists('has_blocks') && isset($post->ID)) {
    $using_classic_editor = !has_blocks($post->ID) && !wp_is_block_theme();
}

// Verifica se o plugin Classic Editor está ativo e substituindo o Gutenberg
if (!$using_classic_editor && function_exists('classic_editor_init_actions')) {
    $classic_editor_settings = get_option('classic-editor-settings');
    if ($classic_editor_settings && isset($classic_editor_settings['editor']) && $classic_editor_settings['editor'] === 'classic') {
        $using_classic_editor = true;
    }
}

// Classe adicional para aplicar estilos específicos ao editor clássico
$editor_class = $using_classic_editor ? 'classic-editor' : 'block-editor';
?>

<style>
    /* Estilos específicos para o editor clássico */
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
    
    /* Correção para o switch */
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
        content: "";
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
    
    /* Estilo do contador de caracteres e botão */
    .classic-editor .char-count-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 5px;
        margin-bottom: 10px;
    }
    
    .classic-editor .provider-badge {
        display: inline-block;
        padding: 2px 6px;
        margin-left: 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        color: white;
        text-align: center;
    }
    
    .classic-editor .blue-badge {
        background-color: #3b82f6;
    }
    
    .classic-editor .green-badge {
        background-color: #10b981;
    }
    
    .classic-editor .generate-button {
        padding: 4px 8px;
        border: none;
        border-radius: 4px;
        color: white;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
    }
    
    .classic-editor .generate-button:hover {
        opacity: 0.9;
    }
    
    .classic-editor .help-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        position: relative;
        margin-left: 4px;
        vertical-align: middle;
    }
    
    .classic-editor .tooltip {
        visibility: hidden;
        background-color: #333;
        color: white;
        text-align: center;
        padding: 5px 8px;
        border-radius: 4px;
        position: absolute;
        z-index: 1;
        width: 200px;
        left: -100px;
        top: 25px;
        font-size: 11px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .classic-editor .help-icon:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }
</style>

<div class="llms-txt-meta-box <?php echo $editor_class; ?>">
    <div class="bg-white p-4 rounded-lg">
        <div class="mb-4 flex items-center justify-between" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="flex: 1;">
                <label for="llms_txt_description" class="block text-sm font-medium text-gray-700 mb-1" style="display: block; font-weight: 500; margin-bottom: 0.25rem; color: #4b5563;">
                    <?php _e('Descrição para IAs', 'llms-txt-generator'); ?>
                </label>
                <p class="text-xs text-gray-500 mb-2" style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem;">
                    <?php _e('Adicione uma descrição deste conteúdo para ser exibida no arquivo llms.txt.', 'llms-txt-generator'); ?>
                </p>
            </div>
            <div class="flex items-center" style="display: flex; align-items: center;">
                <label class="switch relative inline-block w-12 h-6 mr-2" style="position: relative; display: inline-block; width: 48px; height: 24px; margin-right: 0.5rem;">
                    <input type="checkbox" id="llms_txt_exclude" name="llms_txt_exclude" value="1" <?php checked('1', get_post_meta($post->ID, '_llms_txt_exclude', true)); ?>>
                    <span class="slider round" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;"></span>
                </label>
                <label for="llms_txt_exclude" class="text-sm text-gray-700" style="font-size: 0.875rem; color: #4b5563;">
                    <?php _e('Excluir do llms.txt', 'llms-txt-generator'); ?>
                </label>
                <span class="ml-1 group relative help-icon" style="margin-left: 0.25rem; position: relative; display: inline-block;">
                    <svg class="h-4 w-4 text-gray-400 cursor-help" style="height: 1rem; width: 1rem; color: #9ca3af; cursor: help;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 absolute z-10 -ml-28 mt-2 w-64 bg-black text-white text-xs rounded py-1 px-2 pointer-events-none tooltip" style="position: absolute; z-index: 10; width: 250px; background-color: #000; color: #fff; font-size: 0.75rem; border-radius: 0.25rem; padding: 0.25rem 0.5rem; pointer-events: none; left: -100px;">
                        <?php _e('Ative esta opção para não incluir este conteúdo no arquivo llms.txt, tornando-o invisível para IAs.', 'llms-txt-generator'); ?>
                    </div>
                </span>
            </div>
        </div>
        
        <div class="mb-2" style="margin-bottom: 0.5rem;">
            <textarea id="llms_txt_description" name="llms_txt_description" rows="3" maxlength="350" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:shadow-outline resize-y" style="width: 100%; padding: 0.5rem 0.75rem; color: #4b5563; border: 1px solid #e5e7eb; border-radius: 0.375rem; resize: vertical; box-sizing: border-box;"><?php echo esc_textarea($description); ?></textarea>
            <div class="flex justify-between items-center mt-1 char-count-container" style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                <div class="flex items-center" style="display: flex; align-items: center;">
                    <span id="llms_txt_char_count" class="text-xs text-gray-500" style="font-size: 0.75rem; color: #6b7280;">
                        <?php printf(__('%d caracteres restantes', 'llms-txt-generator'), 350 - mb_strlen($description)); ?>
                    </span>
                    
                    <?php if ($has_api_key): ?>
                    <span id="llms_txt_provider_badge" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?php echo $provider_color; ?> text-white provider-badge <?php echo $api_provider === 'deepseek' ? 'blue-badge' : 'green-badge'; ?>" style="margin-left: 0.5rem; display: inline-flex; align-items: center; padding: 0 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 500; color: white;">
                        <?php echo $provider_name; ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($has_api_key): ?>
                    <button type="button" id="llms_txt_generate_description" class="<?php echo $provider_color; ?> hover:opacity-90 text-white text-xs font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out generate-button" style="background-color: <?php echo $api_provider === 'deepseek' ? '#3b82f6' : '#10b981'; ?>; color: white; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.25rem; border: none; cursor: pointer;">
                        <?php _e('Gerar automaticamente', 'llms-txt-generator'); ?>
                    </button>
                <?php endif; ?>
            </div>
            
            <p class="text-xs text-gray-500 mb-0" style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0;">
                <?php _e('Esta descrição será exibida junto ao link para este conteúdo no arquivo llms.txt.', 'llms-txt-generator'); ?>
            </p>
        </div>
        
        <div id="llms_txt_feedback" class="mt-3 hidden">
            <div id="llms_txt_loading" class="hidden">
                <div class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-blue-500"><?php _e('Gerando descrição...', 'llms-txt-generator'); ?></span>
                </div>
            </div>
            
            <div id="llms_txt_success" class="hidden">
                <div class="flex items-center">
                    <svg class="h-4 w-4 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-green-500"><?php _e('Descrição gerada com sucesso!', 'llms-txt-generator'); ?></span>
                </div>
            </div>
            
            <div id="llms_txt_error" class="hidden">
                <div class="flex items-center">
                    <svg class="h-4 w-4 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span id="llms_txt_error_message" class="text-sm text-red-500"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="llms_txt_post_id" value="<?php echo esc_attr($post->ID); ?>">

<?php
// Adicionar dados para o JavaScript
wp_localize_script('llms-txt-meta-box', 'llms_txt_meta_box', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'ajax_nonce' => wp_create_nonce('llms_txt_ajax_nonce'),
    'characters_remaining' => __('%d caracteres restantes', 'llms-txt-generator'),
    'post_id_missing' => __('ID do post não encontrado.', 'llms-txt-generator'),
    'generate_error' => __('Erro ao gerar descrição. Por favor, tente novamente.', 'llms-txt-generator'),
    'provider' => $api_provider,
    'provider_name' => $provider_name
));
