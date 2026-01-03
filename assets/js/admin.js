/**
 * JavaScript para a página de administração do LLMS.txt Generator
 *
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @author Dante Testa (https://dantetesta.com.br)
 */

(function($) {
    'use strict';

    /**
     * Inicialização quando o DOM estiver pronto
     */
    $(document).ready(function() {
        // Elementos da página
        const $apiKeyInput = $('#llms_txt_openai_api_key');
        const $toggleKeyBtn = $('#llms_txt_toggle_key');
        const $validateKeyBtn = $('#llms_txt_validate_api_key');
        const $apiKeyStatus = $('#llms_txt_api_key_status');
        const $previewBtn = $('#llms_txt_preview_file');
        const $regenerateBtn = $('#llms_txt_regenerate_file');
        const $previewContainer = $('#llms_txt_preview_container');
        const $previewContent = $('#llms_txt_preview_content');

        /**
         * Alterna a visibilidade da chave da API
         */
        $toggleKeyBtn.on('click', function() {
            const inputType = $apiKeyInput.attr('type');
            
            if (inputType === 'password') {
                $apiKeyInput.attr('type', 'text');
                $toggleKeyBtn.text(llms_txt_admin.hide_key);
            } else {
                $apiKeyInput.attr('type', 'password');
                $toggleKeyBtn.text(llms_txt_admin.show_key);
            }
        });

        /**
         * Valida a chave da API OpenAI
         */
        $validateKeyBtn.on('click', function() {
            const apiKey = $apiKeyInput.val().trim();
            
            if (!apiKey) {
                showApiKeyStatus('error', llms_txt_admin.error_message);
                return;
            }
            
            // Desabilitar botão e mostrar status de carregamento
            $validateKeyBtn.prop('disabled', true);
            showApiKeyStatus('loading', llms_txt_admin.validating_key);
            
            // Enviar requisição AJAX
            $.ajax({
                url: llms_txt_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'llms_txt_validate_api_key',
                    nonce: llms_txt_admin.nonce,
                    api_key: apiKey
                },
                success: function(response) {
                    if (response.success) {
                        showApiKeyStatus('success', response.data.message);
                    } else {
                        showApiKeyStatus('error', response.data.message);
                    }
                },
                error: function() {
                    showApiKeyStatus('error', llms_txt_admin.connection_error);
                },
                complete: function() {
                    $validateKeyBtn.prop('disabled', false);
                }
            });
        });

        /**
         * Exibe o status da validação da chave da API
         * 
         * @param {string} type Tipo de status (success, error, loading)
         * @param {string} message Mensagem a ser exibida
         */
        function showApiKeyStatus(type, message) {
            $apiKeyStatus.removeClass('text-green-500 text-red-500 text-blue-500');
            
            if (type === 'success') {
                $apiKeyStatus.addClass('text-green-500');
            } else if (type === 'error') {
                $apiKeyStatus.addClass('text-red-500');
            } else if (type === 'loading') {
                $apiKeyStatus.addClass('text-blue-500');
            }
            
            $apiKeyStatus.text(message);
        }

        /**
         * Visualiza o conteúdo do arquivo llms.txt
         */
        $previewBtn.on('click', function() {
            // Desabilitar botão e mostrar status de carregamento
            $previewBtn.prop('disabled', true);
            $previewContent.text(llms_txt_admin.updating_file);
            $previewContainer.removeClass('hidden');
            
            // Enviar requisição AJAX
            $.ajax({
                url: llms_txt_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'llms_txt_get_preview',
                    nonce: llms_txt_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $previewContent.text(response.data.content);
                    } else {
                        $previewContent.text(response.data.message);
                    }
                },
                error: function() {
                    $previewContent.text(llms_txt_admin.connection_error);
                },
                complete: function() {
                    $previewBtn.prop('disabled', false);
                }
            });
        });

        /**
         * Regenera o arquivo llms.txt
         */
        $regenerateBtn.on('click', function() {
            // Confirmar antes de regenerar
            if (!confirm(llms_txt_admin.confirm_regenerate)) {
                return;
            }
            
            // Desabilitar botão e mostrar status de carregamento
            $regenerateBtn.prop('disabled', true);
            $previewContent.text(llms_txt_admin.updating_file);
            $previewContainer.removeClass('hidden');
            
            // Enviar requisição AJAX
            $.ajax({
                url: llms_txt_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'llms_txt_regenerate_file',
                    nonce: llms_txt_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $previewContent.text(response.data.content);
                        alert(llms_txt_admin.file_updated);
                    } else {
                        $previewContent.text(response.data.message);
                        alert(llms_txt_admin.file_error);
                    }
                },
                error: function() {
                    $previewContent.text(llms_txt_admin.connection_error);
                    alert(llms_txt_admin.connection_error);
                },
                complete: function() {
                    $regenerateBtn.prop('disabled', false);
                }
            });
        });
    });
})(jQuery);
