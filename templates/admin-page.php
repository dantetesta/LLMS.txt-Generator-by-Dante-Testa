<?php
/**
 * Template para a página de administração do LLMS.txt Generator
 *
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @author Dante Testa (https://dantetesta.com.br)
 */

// Evitar acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Obter configurações
$admin = LLMS_Txt_Admin::get_instance();
$settings = $admin->get_settings();
$file_manager = LLMS_Txt_File::get_instance();
$post_types = $admin->get_available_post_types();
?>

<style>
    /* Oculta a mensagem "Thank you for creating with WordPress" */
    #wpfooter {
        display: none !important;
    }
    
    /* Também oculta qualquer texto flutuante no meio da tela que possa ser essa mensagem */
    .update-nag, #message {
        display: none !important;
    }
</style>

<div class="wrap llms-admin-page">
    <!-- Sistema de notificações toast -->
    <div id="llms-toast-container" class="fixed top-4 right-4 z-50 w-80 max-w-full" style="transform: translateX(110%); transition: transform 0.3s ease-in-out;"></div>
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold m-0"><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="flex items-center space-x-2">
            <?php if ($file_manager->file_exists()): ?>
                <a href="<?php echo esc_url($file_manager->get_file_url()); ?>" target="_blank" class="inline-flex items-center bg-blue-600 hover:bg-blue-100 hover:shadow-lg text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <?php _e('Ver arquivo llms.txt', 'llms-txt-generator'); ?>
                </a>
            <?php endif; ?>
            
            <button type="button" id="llms_txt_regenerate_file" class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <?php _e('Regenerar arquivo', 'llms-txt-generator'); ?>
            </button>
        </div>
    </div>
    
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">

        <form method="post" action="options.php">
            <?php settings_fields('llms_txt_settings'); ?>
            
            <!-- Cabeçalho da página -->
            <div class="border-b border-gray-200 bg-gray-50 p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800"><?php _e('Configurações do LLMS.txt Generator', 'llms-txt-generator'); ?></h2>
                        <p class="mt-1 text-sm text-gray-600"><?php _e('Configure como o arquivo llms.txt será gerado para informar às Inteligências Artificiais sobre o conteúdo do seu site.', 'llms-txt-generator'); ?></p>
                    </div>
                    
                    <div class="mt-4 md:mt-0">
                        <!-- Switcher para habilitar arquivo llms.txt (corrigido) -->
                        <div class="toggle-wrapper">
                            <label for="llms_txt_enabled" class="flex items-center cursor-pointer toggle-label" role="switch" aria-checked="<?php echo ($settings['enabled'] === '1') ? 'true' : 'false'; ?>" tabindex="0">
                                <span class="relative inline-block w-14 h-8 transition duration-200 ease-in-out">
                                    <input type="checkbox" id="llms_txt_enabled" name="llms_txt_settings[enabled]" value="1" <?php checked('1', $settings['enabled']); ?> class="sr-only toggle-checkbox">
                                    <span class="block w-14 h-8 <?php echo ($settings['enabled'] === '1') ? 'bg-blue-500' : 'bg-gray-300'; ?> rounded-full shadow-inner toggle-bg"></span>
                                    <span class="toggle-dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 ease-in-out transform <?php echo ($settings['enabled'] === '1') ? 'translate-x-6' : ''; ?>"></span>
                                </span>
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    <?php _e('Habilitar arquivo llms.txt', 'llms-txt-generator'); ?>
                                </span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">
                                <?php _e('Quando habilitado, o arquivo llms.txt será gerado e disponibilizado na raiz do seu site.', 'llms-txt-generator'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Layout de duas colunas -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            
                <!-- Coluna Esquerda -->
                <div class="col-span-1 md:col-span-2">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                            <h3 class="text-lg font-medium text-gray-800"><?php _e('Descrição do Site', 'llms-txt-generator'); ?></h3>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                <?php _e('Adicione uma descrição personalizada para o seu site no arquivo llms.txt.', 'llms-txt-generator'); ?>
                            </p>
                            <textarea 
                                id="llms_txt_site_description" 
                                name="llms_txt_settings[site_description]" 
                                rows="4" 
                                class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y"
                                placeholder="<?php _e('Descreva o propósito do seu site...', 'llms-txt-generator'); ?>"
                            ><?php echo esc_textarea(isset($settings['site_description']) ? $settings['site_description'] : ''); ?></textarea>
                            <p class="text-xs text-gray-500 mt-2">
                                <?php _e('Esta descrição será exibida logo após o título do site no arquivo llms.txt.', 'llms-txt-generator'); ?>
                            </p>
                        </div>
                    </div>
            
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-6">
                        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                            <h3 class="text-lg font-medium text-gray-800"><?php _e('Conteúdo a Incluir', 'llms-txt-generator'); ?></h3>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-4">
                                <?php _e('Selecione quais tipos de conteúdo devem ser incluídos no arquivo llms.txt.', 'llms-txt-generator'); ?>
                            </p>
                            
                            <h4 class="text-md font-medium text-gray-700 mb-3 border-b border-gray-200 pb-2"><?php _e('Conteúdo a Incluir', 'llms-txt-generator'); ?></h4>
                            
                            <!-- Switcher para Posts Nativos -->
                            <div class="mb-4">
                                <!-- Campo oculto para garantir que o valor seja enviado mesmo quando não marcado -->
                                <input type="hidden" name="llms_txt_settings[include_posts]" value="0">
                                <label for="llms_txt_include_posts" class="toggle-label flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" id="llms_txt_include_posts" name="llms_txt_settings[include_posts]" value="1" <?php checked('1', isset($settings['include_posts']) ? $settings['include_posts'] : '1'); ?> class="toggle-checkbox sr-only">
                                        <div class="w-12 h-6 bg-gray-300 rounded-full shadow-inner toggle-bg"></div>
                                        <div class="toggle-dot absolute w-6 h-6 bg-white rounded-full shadow-md top-0 left-0 transition-transform duration-300 ease-in-out <?php echo (isset($settings['include_posts']) && $settings['include_posts'] === '1') ? 'transform translate-x-6' : ''; ?>"></div>
                                    </div>
                                    <div class="ml-3 text-sm font-medium text-gray-700">
                                        <?php _e('Incluir posts', 'llms-txt-generator'); ?>
                                        <p class="text-xs text-gray-500 mt-1"><?php _e('Ativa/desativa a inclusão de posts no arquivo llms.txt e remove o metabox de edição', 'llms-txt-generator'); ?></p>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Switcher para Páginas -->
                            <div class="mb-4">
                                <!-- Campo oculto para garantir que o valor seja enviado mesmo quando não marcado -->
                                <input type="hidden" name="llms_txt_settings[include_pages]" value="0">
                                <label for="llms_txt_include_pages" class="toggle-label flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" id="llms_txt_include_pages" name="llms_txt_settings[include_pages]" value="1" <?php checked('1', isset($settings['include_pages']) ? $settings['include_pages'] : '0'); ?> class="toggle-checkbox sr-only">
                                        <div class="w-12 h-6 bg-gray-300 rounded-full shadow-inner toggle-bg"></div>
                                        <div class="toggle-dot absolute w-6 h-6 bg-white rounded-full shadow-md top-0 left-0 transition-transform duration-300 ease-in-out <?php echo (isset($settings['include_pages']) && $settings['include_pages'] === '1') ? 'transform translate-x-6' : ''; ?>"></div>
                                    </div>
                                    <div class="ml-3 text-sm font-medium text-gray-700">
                                        <?php _e('Incluir páginas', 'llms-txt-generator'); ?>
                                        <p class="text-xs text-gray-500 mt-1"><?php _e('Ativa/desativa a inclusão de páginas no arquivo llms.txt e remove o metabox de edição', 'llms-txt-generator'); ?></p>
                                    </div>
                                </label>
                            </div>
                            
                            <h4 class="text-md font-medium text-gray-700 mb-3 border-b border-gray-200 pb-2"><?php _e('Tipos de Post Adicionais', 'llms-txt-generator'); ?></h4>
                            
                            <div class="space-y-6">
                                <?php 
                                // Remover post e page, pois já são tratados separadamente
                                $custom_post_types = $post_types;
                                unset($custom_post_types['post']);
                                unset($custom_post_types['page']);
                                
                                foreach ($custom_post_types as $type => $label): 
                                    $is_selected = in_array($type, isset($settings['post_types']) ? $settings['post_types'] : array());
                                    $content_source = isset($settings['cpt_content_source'][$type]) ? $settings['cpt_content_source'][$type] : 'post_content';
                                    $custom_fields = isset($settings['cpt_custom_fields'][$type]) ? $settings['cpt_custom_fields'][$type] : '';
                                ?>
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-center mb-3">
                                            <input type="checkbox" 
                                                id="llms_txt_post_type_<?php echo esc_attr($type); ?>" 
                                                name="llms_txt_settings[post_types][]" 
                                                value="<?php echo esc_attr($type); ?>" 
                                                <?php checked($is_selected); ?> 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cpt-selector"
                                                data-cpt="<?php echo esc_attr($type); ?>">
                                            <label for="llms_txt_post_type_<?php echo esc_attr($type); ?>" class="ml-2 block text-sm font-medium text-gray-700">
                                                <?php echo esc_html($label); ?>
                                            </label>
                                        </div>
                                        
                                        <div class="pl-6 space-y-3 cpt-options <?php echo $is_selected ? '' : 'hidden'; ?>" id="cpt_options_<?php echo esc_attr($type); ?>">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                                    <?php _e('Fonte de conteúdo para descrições:', 'llms-txt-generator'); ?>
                                                </label>
                                                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-4">
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" 
                                                            name="llms_txt_settings[cpt_content_source][<?php echo esc_attr($type); ?>]" 
                                                            value="post_content" 
                                                            <?php checked($content_source, 'post_content'); ?> 
                                                            class="content-source-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                            data-cpt="<?php echo esc_attr($type); ?>">
                                                        <span class="ml-2 text-xs text-gray-700"><?php _e('Conteúdo do post', 'llms-txt-generator'); ?></span>
                                                    </label>
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" 
                                                            name="llms_txt_settings[cpt_content_source][<?php echo esc_attr($type); ?>]" 
                                                            value="post_excerpt" 
                                                            <?php checked($content_source, 'post_excerpt'); ?> 
                                                            class="content-source-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                            data-cpt="<?php echo esc_attr($type); ?>">
                                                        <span class="ml-2 text-xs text-gray-700"><?php _e('Resumo (excerpt)', 'llms-txt-generator'); ?></span>
                                                    </label>
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" 
                                                            name="llms_txt_settings[cpt_content_source][<?php echo esc_attr($type); ?>]" 
                                                            value="custom_fields" 
                                                            <?php checked($content_source, 'custom_fields'); ?> 
                                                            class="content-source-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                            data-cpt="<?php echo esc_attr($type); ?>">
                                                        <span class="ml-2 text-xs text-gray-700"><?php _e('Campos personalizados', 'llms-txt-generator'); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="custom-fields-container <?php echo $content_source === 'custom_fields' ? '' : 'hidden'; ?>" id="custom_fields_container_<?php echo esc_attr($type); ?>">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                                    <?php _e('Nomes dos campos personalizados (separados por vírgula):', 'llms-txt-generator'); ?>
                                                    <span class="font-normal text-gray-500"><?php _e('Ex: _descricao, _peso, _info', 'llms-txt-generator'); ?></span>
                                                </label>
                                                <input type="text" 
                                                    name="llms_txt_settings[cpt_custom_fields][<?php echo esc_attr($type); ?>]" 
                                                    value="<?php echo esc_attr($custom_fields); ?>" 
                                                    class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md"
                                                    placeholder="<?php _e('_descricao, _info, _atributos', 'llms-txt-generator'); ?>">
                                                <p class="mt-1 text-xs text-gray-500">
                                                    <?php _e('O conteúdo desses campos será concatenado para gerar o contexto da descrição.', 'llms-txt-generator'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <script type="text/javascript">
                                jQuery(document).ready(function($) {
                                    // Controla a visibilidade das opções de CPT
                                    $('.cpt-selector').on('change', function() {
                                        const cptId = $(this).data('cpt');
                                        if (this.checked) {
                                            $(`#cpt_options_${cptId}`).removeClass('hidden');
                                        } else {
                                            $(`#cpt_options_${cptId}`).addClass('hidden');
                                        }
                                    });
                                    
                                    // Controla a visibilidade do container de campos personalizados
                                    $('.content-source-radio').on('change', function() {
                                        const cptId = $(this).data('cpt');
                                        if (this.value === 'custom_fields') {
                                            $(`#custom_fields_container_${cptId}`).removeClass('hidden');
                                        } else {
                                            $(`#custom_fields_container_${cptId}`).addClass('hidden');
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
            
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-6">
                        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                            <h3 class="text-lg font-medium text-gray-800"><?php _e('Conteúdo Personalizado', 'llms-txt-generator'); ?></h3>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                <?php _e('Adicione conteúdo personalizado para o arquivo llms.txt. Use o formato Markdown.', 'llms-txt-generator'); ?>
                            </p>
                            <div class="bg-gray-50 border border-gray-200 rounded-md p-3 mb-3">
                                <textarea 
                                    id="llms_txt_custom_content" 
                                    name="llms_txt_settings[custom_content]" 
                                    rows="8" 
                                    class="w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y font-mono text-sm"
                                    placeholder="<?php _e('## Projetos\n\n- [Projeto A](https://exemplo.com/projeto-a): Descrição do projeto A\n- [Projeto B](https://exemplo.com/projeto-b): Descrição do projeto B', 'llms-txt-generator'); ?>"
                                ><?php echo esc_textarea(isset($settings['custom_content']) ? $settings['custom_content'] : ''); ?></textarea>
                            </div>
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            <?php _e('Exemplo de formato Markdown para conteúdo personalizado:', 'llms-txt-generator'); ?>
                                            <br>
                                            <code class="text-xs bg-blue-100 px-1 py-0.5 rounded">## Projetos</code><br>
                                            <code class="text-xs bg-blue-100 px-1 py-0.5 rounded">- [Projeto A](https://exemplo.com/projeto-a): Descrição do projeto A</code><br>
                                            <code class="text-xs bg-blue-100 px-1 py-0.5 rounded">- [Projeto B](https://exemplo.com/projeto-b): Descrição do projeto B</code>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Coluna Direita -->
                <div class="col-span-1">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                            <h3 class="text-lg font-medium text-gray-800"><?php _e('Integração com IA', 'llms-txt-generator'); ?></h3>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-4">
                                <?php _e('Configure a integração com APIs de Inteligência Artificial para gerar descrições técnicas automaticamente.', 'llms-txt-generator'); ?>
                            </p>
                            
                            <!-- Seleção de Provedor de IA -->
                            <div class="mb-6 bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?php _e('Provedor de IA', 'llms-txt-generator'); ?>
                                </label>
                                <div class="flex flex-col gap-4">
                                    <!-- OpenAI Card -->
                                    <label class="provider-card relative flex flex-col bg-white p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all duration-200 <?php echo (isset($settings['ai_provider']) && $settings['ai_provider'] === 'openai') || !isset($settings['ai_provider']) ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200'; ?>">
                                        <input type="radio" name="llms_txt_settings[ai_provider]" value="openai" class="provider-radio sr-only" <?php checked(isset($settings['ai_provider']) ? $settings['ai_provider'] : 'openai', 'openai'); ?>>
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 bg-gradient-to-r from-green-400 to-blue-500 p-2 rounded-full">
                                                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <span class="block text-sm font-medium text-gray-900">OpenAI (ChatGPT)</span>
                                                <span class="block text-xs text-gray-500">API paga, resultados de alta qualidade</span>
                                            </div>
                                        </div>
                                        <!-- Indicador de seleção -->
                                        <div class="absolute top-2 right-2 w-4 h-4 bg-blue-500 rounded-full opacity-0 transition-opacity duration-200 check-indicator <?php echo (isset($settings['ai_provider']) && $settings['ai_provider'] === 'openai') || !isset($settings['ai_provider']) ? 'opacity-100' : ''; ?>"></div>
                                    </label>
                                    
                                    <!-- DeepSeek Card -->
                                    <label class="provider-card relative flex flex-col bg-white p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all duration-200 <?php echo (isset($settings['ai_provider']) && $settings['ai_provider'] === 'deepseek') ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200'; ?>">
                                        <input type="radio" name="llms_txt_settings[ai_provider]" value="deepseek" class="provider-radio sr-only" <?php checked(isset($settings['ai_provider']) ? $settings['ai_provider'] : '', 'deepseek'); ?>>
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 bg-gradient-to-r from-purple-400 to-indigo-500 p-2 rounded-full">
                                                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <span class="block text-sm font-medium text-gray-900">DeepSeek Chat v3</span>
                                                <span class="block text-xs text-gray-500">Via OpenRouter, opção gratuita</span>
                                            </div>
                                        </div>
                                        <!-- Indicador de seleção -->
                                        <div class="absolute top-2 right-2 w-4 h-4 bg-blue-500 rounded-full opacity-0 transition-opacity duration-200 check-indicator <?php echo (isset($settings['ai_provider']) && $settings['ai_provider'] === 'deepseek') ? 'opacity-100' : ''; ?>"></div>
                                    </label>
                                </div>
                                
                                <div class="mt-3 text-xs text-gray-600">
                                    <p class="mb-1">
                                        <strong><?php _e('Links úteis:', 'llms-txt-generator'); ?></strong>
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="https://platform.openai.com/docs/guides/text-generation" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <?php _e('Manual da API OpenAI', 'llms-txt-generator'); ?>
                                        </a>
                                        <span class="text-gray-400">|</span>
                                        <a href="https://openrouter.ai/docs" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <?php _e('Manual da API OpenRouter (DeepSeek Chat v3)', 'llms-txt-generator'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- OpenAI API Key -->
                            <div class="mb-6 openai-api-fields" data-provider="openai" style="<?php echo (isset($settings['ai_provider']) && $settings['ai_provider'] !== 'openai') ? 'display: none;' : ''; ?>">
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <label for="llms_txt_openai_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php _e('Chave da API OpenAI', 'llms-txt-generator'); ?>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="password" 
                                            id="llms_txt_openai_api_key" 
                                            name="llms_txt_settings[openai_api_key]" 
                                            value="<?php echo esc_attr(isset($settings['openai_api_key']) ? $settings['openai_api_key'] : ''); ?>" 
                                            class="w-full px-3 py-2 pr-16 md:pr-28 text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="<?php _e('Insira sua chave API OpenAI', 'llms-txt-generator'); ?>"
                                        >
                                        <button 
                                            type="button" 
                                            class="password-toggle absolute inset-y-0 right-0 px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none flex items-center transition-colors duration-200 bg-gray-50 border-l border-gray-300 rounded-r-md"
                                            aria-label="<?php _e('Alternar visibilidade da chave', 'llms-txt-generator'); ?>"
                                            data-target="llms_txt_openai_api_key"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span class="toggle-text whitespace-nowrap inline"><?php _e('Mostrar', 'llms-txt-generator'); ?></span>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <?php _e('Necessária para gerar descrições técnicas automaticamente com a OpenAI.', 'llms-txt-generator'); ?>
                                    </p>
                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <button 
                                            type="button" 
                                            id="llms_txt_validate_openai_api_key" 
                                            class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-3 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out"
                                            aria-label="<?php _e('Validar chave API OpenAI', 'llms-txt-generator'); ?>"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span><?php _e('Validar Chave', 'llms-txt-generator'); ?></span>
                                        </button>
                                        <span id="llms_txt_openai_api_key_status" class="text-sm font-medium"></span>
                                    </div>
                                    <div class="mt-2 text-xs">
                                        <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <?php _e('Obter chave da API OpenAI', 'llms-txt-generator'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- DeepSeek API Key -->
                            <div class="mb-6 deepseek-api-fields" data-provider="deepseek" style="<?php echo (!isset($settings['ai_provider']) || $settings['ai_provider'] !== 'deepseek') ? 'display: none;' : ''; ?>">
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <label for="llms_txt_deepseek_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php _e('Chave da API OpenRouter (DeepSeek Chat v3)', 'llms-txt-generator'); ?>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            type="password" 
                                            id="llms_txt_deepseek_api_key" 
                                            name="llms_txt_settings[deepseek_api_key]" 
                                            value="<?php echo esc_attr(isset($settings['deepseek_api_key']) ? $settings['deepseek_api_key'] : ''); ?>" 
                                            class="w-full px-3 py-2 pr-16 md:pr-28 text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="<?php _e('Insira sua chave API OpenRouter', 'llms-txt-generator'); ?>"
                                        >
                                        <button 
                                            type="button" 
                                            class="password-toggle absolute inset-y-0 right-0 px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none flex items-center transition-colors duration-200 bg-gray-50 border-l border-gray-300 rounded-r-md"
                                            aria-label="<?php _e('Alternar visibilidade da chave', 'llms-txt-generator'); ?>"
                                            data-target="llms_txt_deepseek_api_key"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span class="toggle-text whitespace-nowrap inline"><?php _e('Mostrar', 'llms-txt-generator'); ?></span>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <?php _e('Necessária para usar o modelo DeepSeek Chat v3 (gratuito).', 'llms-txt-generator'); ?>
                                    </p>
                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <button 
                                            type="button" 
                                            id="llms_txt_validate_deepseek_api_key" 
                                            class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-3 text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out"
                                            aria-label="<?php _e('Validar chave API OpenRouter', 'llms-txt-generator'); ?>"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span><?php _e('Validar Chave', 'llms-txt-generator'); ?></span>
                                        </button>
                                        <span id="llms_txt_deepseek_api_key_status" class="text-sm font-medium"></span>
                                    </div>
                                    <div class="mt-2 text-xs">
                                        <a href="https://openrouter.ai/keys" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <?php _e('Obter chave gratuita da API OpenRouter', 'llms-txt-generator'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <!-- Botão de salvar (verde) -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-6">
                        <div class="p-4">
                            <!-- Informação da última atualização -->
                            <?php if (!empty($settings['last_updated'])): ?>
                                <div class="flex items-center mb-4 text-sm text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?php printf(__('Última atualização do arquivo: %s', 'llms-txt-generator'), $file_manager->get_last_updated()); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <button type="submit" class="w-full inline-flex items-center justify-center bg-gradient-to-r from-green-600 to-green-800 hover:from-green-700 hover:to-green-900 text-white font-medium py-3 px-8 rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300 ease-in-out transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-base"><?php _e('Salvar Configurações', 'llms-txt-generator'); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Sobre o plugin -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden mt-6">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <h3 class="text-lg font-medium text-gray-800"><?php _e('Sobre o LLMS.txt Generator', 'llms-txt-generator'); ?></h3>
        </div>
        <div class="p-6">
            <!-- Container para os 3 cards ocupando 100% da largura -->
            <div class="w-full text-left">
                <!-- Flex container para distribuir os 3 cards perfeitamente em 33.33% cada -->
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:justify-between md:gap-2">
                    <!-- Coluna 1: O que é o llms.txt -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 p-5 text-center w-full md:w-[calc(33.33%-0.5rem)]">
                        <div class="mx-auto w-16 h-16 mb-4 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4.75V6.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M17.1266 6.87347L16.0659 7.93413" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M19.25 12L17.75 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M17.1266 17.1265L16.0659 16.0659" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M12 17.75V19.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M7.9342 16.0659L6.87354 17.1265" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M6.25 12L4.75 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M7.9342 7.93413L6.87354 6.87347" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12C10 13.1046 10.8954 14 12 14Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2"><?php _e('O que é', 'llms-txt-generator'); ?></h3>
                        <p class="text-gray-600 text-sm">
                            <?php _e('O arquivo llms.txt é um formato que permite informar às Inteligências Artificiais sobre o conteúdo do seu site de forma estruturada.', 'llms-txt-generator'); ?>
                        </p>
                    </div>
                    
                    <!-- Coluna 2: Para que serve -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 p-5 text-center w-full md:w-[calc(33.33%-0.5rem)]">
                        <div class="mx-auto w-16 h-16 mb-4 bg-gradient-to-r from-green-400 to-emerald-600 rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9371 6.06294 22.75 12 22.75C17.9371 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25ZM16.5303 10.0303C16.8232 9.73744 16.8232 9.26256 16.5303 8.96967C16.2374 8.67678 15.7626 8.67678 15.4697 8.96967L10.75 13.6893L8.53033 11.4697C8.23744 11.1768 7.76256 11.1768 7.46967 11.4697C7.17678 11.7626 7.17678 12.2374 7.46967 12.5303L10.2197 15.2803C10.5126 15.5732 10.9874 15.5732 11.2803 15.2803L16.5303 10.0303Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2"><?php _e('Para que serve', 'llms-txt-generator'); ?></h3>
                        <p class="text-gray-600 text-sm">
                            <?php _e('Similar ao robots.txt, ele permite que você especifique quais partes do seu site podem ou não ser acessadas por IAs para treinamento ou geração de conteúdo.', 'llms-txt-generator'); ?>
                        </p>
                    </div>
                    
                    <!-- Coluna 3: Mais informações -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 p-5 text-center w-full md:w-[calc(33.33%-0.5rem)]">
                        <div class="mx-auto w-16 h-16 mb-4 bg-gradient-to-r from-purple-400 to-purple-700 rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 5L21 12M21 12L14 19M21 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2"><?php _e('Mais informações', 'llms-txt-generator'); ?></h3>
                        <p class="text-gray-600 text-sm">
                            <?php _e('Para mais informações, visite', 'llms-txt-generator'); ?> <a href="https://llmstxt.org" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">llmstxt.org</a>.
                        </p>
                    </div>
                </div>

               
            </div>
                <!-- Seção de links recomendados -->
                <div class="flex-shrink-1 mt-6 lg:mt-5">
                    <!-- Box único com duas colunas -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-base font-medium text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <?php _e('Links Recomendados - Use nossos links e ajude esse plugin a se manter!', 'llms-txt-generator'); ?>
                            </h4>
                        </div>
                        <div class="p-5 text-left">
                            <!-- Categorias de links -->
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-700 mb-3 border-b border-gray-100 pb-1"><?php _e('Compras & Hospedagem', 'llms-txt-generator'); ?></h5>
                                <!-- Links em formato mais respirável -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                    <!-- Amazon -->
                                    <a href="https://amzn.to/4eXEnLS" target="_blank" class="group flex flex-col p-4 hover:bg-blue-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-red-600 rounded-md flex items-center justify-center mr-2 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Amazon</span>
                                        </div>
                                        <span class="text-xs text-gray-600"><?php _e('Compre com nosso link de afiliado', 'llms-txt-generator'); ?></span>
                                    </a>
                                    
                                    <!-- Shopee -->
                                    <a href="https://s.shopee.com.br/9f9y4jO8Jz" target="_blank" class="group flex flex-col p-4 hover:bg-blue-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md">
                                        <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-red-600 rounded-md flex items-center justify-center mr-2 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Shopee</span>
                                        </div>
                                        <span class="text-xs text-gray-600"><?php _e('Descontos exclusivos para você', 'llms-txt-generator'); ?></span>
                                    </a>
                                    
                                    <!-- Napoleon -->
                                    <a href="https://dantetesta.com.br/napoleon" target="_blank" class="group flex flex-col p-4 hover:bg-blue-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-indigo-600 rounded-md flex items-center justify-center mr-2 shadow-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Napoleon</span>
                                        </div>
                                        <span class="text-xs text-gray-600"><?php _e('Hospedagem de sites', 'llms-txt-generator'); ?></span>
                                    </a>
                                    
                                    <!-- Cloudways -->
                                    <a href="https://dantetesta.com.br/cloudways" target="_blank" class="group flex flex-col p-4 hover:bg-blue-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md">
                                        <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-indigo-600 rounded-md flex items-center justify-center mr-2 shadow-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Cloudways</span>
                                        </div>
                                        <span class="text-xs text-gray-600"><?php _e('Hospedagem Cloud Gerenciada', 'llms-txt-generator'); ?></span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Ferramentas WordPress -->
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-3 border-b border-gray-100 pb-1"><?php _e('Ferramentas WordPress', 'llms-txt-generator'); ?></h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                    <!-- ZIPWP -->
                                    <a href="https://dantetesta.com.br/zipwp" target="_blank" class="group flex flex-col p-4 hover:bg-blue-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-gradient-to-r from-green-300 to-green-600 rounded-md flex items-center justify-center mr-2 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-700">ZipWP</span>
                                        </div>
                                        <span class="text-xs text-gray-600"><?php _e('IA que Cria Sites WordPress', 'llms-txt-generator'); ?></span>
                                    </a>


                                    <!-- Crocoblock -->
                                    <a href="https://dantetesta.com.br/crocoblock" target="_blank" class="group flex flex-col p-4 hover:bg-blue-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md">
                                        <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-gradient-to-r from-green-300 to-green-600 rounded-md flex items-center justify-center mr-2 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Crocoblock</span>
                                        </div>
                                        <span class="text-xs text-gray-600"><?php _e('Plugins Avançados para WordPress', 'llms-txt-generator'); ?></span>
                                    </a>


                                    <!-- Elementor -->
                                    <a href="https://dantetesta.com.br/elementorpro" target="_blank" class="group flex flex-col p-4 hover:bg-blue-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-md flex items-center justify-center mr-2 shadow-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9.504 1.132a1 1 0 01.992 0l1.75 1a1 1 0 11-.992 1.736L10 3.152l-1.254.716a1 1 0 11-.992-1.736l1.75-1zM5.618 4.504a1 1 0 01-.372 1.364L5.016 6l.23.132a1 1 0 11-.992 1.736L4 7.723V8a1 1 0 01-2 0V6a.996.996 0 01.52-.878l1.734-.99a1 1 0 011.364.372zm8.764 0a1 1 0 011.364-.372l1.733.99A1.002 1.002 0 0118 6v2a1 1 0 11-2 0v-.277l-.254.145a1 1 0 11-.992-1.736l.23-.132-.23-.132a1 1 0 01-.372-1.364zm-7 4a1 1 0 011.364-.372L10 8.848l1.254-.716a1 1 0 11.992 1.736L11 10.58V12a1 1 0 11-2 0v-1.42l-1.246-.712a1 1 0 01-.372-1.364zM3 11a1 1 0 011 1v1.42l1.246.712a1 1 0 11-.992 1.736l-1.75-1A1 1 0 012 14v-2a1 1 0 011-1zm14 0a1 1 0 011 1v2a1 1 0 01-.504.868l-1.75 1a1 1 0 11-.992-1.736L16 13.42V12a1 1 0 011-1zm-9.618 5.504a1 1 0 011.364-.372l.254.145V16a1 1 0 112 0v.277l.254-.145a1 1 0 11.992 1.736l-1.735.992a.995.995 0 01-1.022 0l-1.735-.992a1 1 0 01-.372-1.364z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Elementor</span>
                                        </div>
                                        <span class="text-xs text-gray-600"><?php _e('PageBuilder para WordPress', 'llms-txt-generator'); ?></span>
                                    </a>
                                    
                                    <!-- Inner AI -->
                                    <a href="https://dantetesta.com.br/innerai" target="_blank" class="group flex flex-col p-4 hover:bg-blue-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-md flex items-center justify-center mr-2 shadow-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9.504 1.132a1 1 0 01.992 0l1.75 1a1 1 0 11-.992 1.736L10 3.152l-1.254.716a1 1 0 11-.992-1.736l1.75-1zM5.618 4.504a1 1 0 01-.372 1.364L5.016 6l.23.132a1 1 0 11-.992 1.736L4 7.723V8a1 1 0 01-2 0V6a.996.996 0 01.52-.878l1.734-.99a1 1 0 011.364.372zm8.764 0a1 1 0 011.364-.372l1.733.99A1.002 1.002 0 0118 6v2a1 1 0 11-2 0v-.277l-.254.145a1 1 0 11-.992-1.736l.23-.132-.23-.132a1 1 0 01-.372-1.364zm-7 4a1 1 0 011.364-.372L10 8.848l1.254-.716a1 1 0 11.992 1.736L11 10.58V12a1 1 0 11-2 0v-1.42l-1.246-.712a1 1 0 01-.372-1.364zM3 11a1 1 0 011 1v1.42l1.246.712a1 1 0 11-.992 1.736l-1.75-1A1 1 0 012 14v-2a1 1 0 011-1zm14 0a1 1 0 011 1v2a1 1 0 01-.504.868l-1.75 1a1 1 0 11-.992-1.736L16 13.42V12a1 1 0 011-1zm-9.618 5.504a1 1 0 011.364-.372l.254.145V16a1 1 0 112 0v.277l.254-.145a1 1 0 11.992 1.736l-1.735.992a.995.995 0 01-1.022 0l-1.735-.992a1 1 0 01-.372-1.364z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Inner AI</span>
                                        </div>
                                        <span class="text-xs text-gray-600"><?php _e('Plataforma de IA tudo em um', 'llms-txt-generator'); ?></span>
                                    </a>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sobre o Autor -->
    <div class="mt-8 mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 shadow-sm">
        <div class="text-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php _e('Sobre o Desenvolvedor', 'llms-txt-generator'); ?></h3>
            <div class="w-16 h-1 bg-gradient-to-r from-blue-400 to-indigo-500 mx-auto rounded-full"></div>
        </div>
        
        <div class="max-w-3xl mx-auto">
            <!-- Perfil com imagem -->
            <div class="flex flex-col md:flex-row items-center mb-6">
                <!-- Gravatar do desenvolvedor -->
                <div class="mb-4 md:mb-0 md:mr-6">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg">
                        <img src="https://www.gravatar.com/avatar/<?php echo md5('dante.testa@gmail.com'); ?>?s=256&d=mp" 
                             alt="Dante Testa" 
                             class="w-full h-full object-cover">
                    </div>
                </div>
                
                <!-- Biografia -->
                <div class="flex-1">
                    <p class="text-gray-700 leading-relaxed text-left">
                        <?php _e('Dante Testa é um desenvolvedor web especializado em WordPress e entusiasta da tecnologia. Apaixonado por criar soluções que unem inovação e criatividade, dedica-se a desenvolver plugins e temas que simplificam a vida de seus usuários. Como educador, compartilha conhecimento através de cursos, mentorias e conteúdo gratuito, ajudando profissionais a dominarem o ecossistema WordPress. Sua missão é democratizar o acesso a ferramentas de qualidade e conhecimento técnico, tornando a web mais acessível para todos.', 'llms-txt-generator'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Redes sociais e contato -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Email (PIX e PayPal) -->
                <div class="flex flex-col items-center justify-center bg-white rounded-lg px-6 py-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                    <div class="text-blue-500 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1"><?php _e('Email (PIX e PayPal)', 'llms-txt-generator'); ?></p>
                    <p class="text-md font-semibold text-indigo-600 select-all">dante.testa@gmail.com</p>
                    <p class="text-xs text-gray-600 mt-2"><?php _e('Pague um café pra mim se você gostou da solução! 😊', 'llms-txt-generator'); ?></p>
                </div>
                
                <!-- Canal do YouTube -->
                <a href="https://www.youtube.com/@dantetesta" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center justify-center bg-white rounded-lg px-6 py-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 group">
                    <div class="text-red-500 mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1"><?php _e('Canal no YouTube', 'llms-txt-generator'); ?></p>
                    <p class="text-md font-semibold text-red-600 group-hover:text-red-700 transition-colors duration-300"><?php _e('Inscreva-se!', 'llms-txt-generator'); ?></p>
                </a>
            </div>
            
            <!-- Site pessoal -->
            <div class="mt-4 text-center">
                <a href="https://dantetesta.com.br" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                    <span class="mr-1"><?php _e('Visite meu site', 'llms-txt-generator'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Rodapé -->
    <div class="mt-6 mb-4 text-center">
        <div class="inline-flex items-center justify-center bg-gray-100 rounded-full px-4 py-2">
            <span class="text-sm text-gray-600">
                <?php printf(__('LLMS.txt Generator v%s', 'llms-txt-generator'), LLMS_TXT_GENERATOR_VERSION); ?>
            </span>
        </div>
    </div>
</div>

<!-- Script para notificações toast -->
<script>
    // Localize os dados para o script admin-page.js
    var llms_txt_admin = {
        nonce: '<?php echo wp_create_nonce("llms_txt_ajax_nonce"); ?>'
    };
</script>
<script src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin-page.js'); ?>" defer></script>

<!-- Container para notificações toast -->
<div id="llms-txt-toast-container" class="fixed top-4 right-4 z-50 flex flex-col items-end space-y-2 transform transition-transform duration-300" style="transform: translateX(110%)"></div>

