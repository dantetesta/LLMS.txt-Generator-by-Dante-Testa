/**
 * Script para gerenciamento das colunas administrativas e geração individual de descrições
 * 
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @author Dante Testa (https://dantetesta.com.br)
 */
(function($) {
    'use strict';

    /**
     * Inicializa os eventos para geração individual de descrições
     */
    function init() {
        // Eventos de geração individual via ícone na coluna
        $(document).on('click', '.llms-txt-generate-single', function(e) {
            e.preventDefault();
            
            const $link = $(this);
            const postId = $link.data('post-id');
            const nonce = $link.data('nonce');
            
            if (!postId || !nonce) {
                return;
            }
            
            // Indicador visual de processamento
            const $status = $link.closest('.llms-txt-status');
            const originalContent = $status.html();
            
            $status.html('<span class="spinner is-active" style="float: none; margin: 0;"></span> ' + llmsTxtAdmin.generateText);
            
            // Requisição AJAX
            $.ajax({
                url: llmsTxtAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'llms_txt_generate_single_description',
                    post_id: postId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Atualizar status para sucesso
                        $status.html('<span class="dashicons dashicons-yes-alt llms-txt-icon-success" title="' + llmsTxtAdmin.successText + '"></span>');
                        
                        // Mostrar notificação temporária de sucesso
                        showNotification(llmsTxtAdmin.successText, 'success');
                    } else {
                        // Restaurar conteúdo original e mostrar erro
                        $status.html(originalContent);
                        
                        // Mostrar notificação de erro
                        showNotification(response.data?.message || llmsTxtAdmin.errorText, 'error');
                    }
                },
                error: function() {
                    // Restaurar conteúdo original em caso de erro
                    $status.html(originalContent);
                    
                    // Mostrar notificação de erro
                    showNotification(llmsTxtAdmin.errorText, 'error');
                }
            });
        });
    }

    /**
     * Exibe uma notificação temporária na interface administrativa
     * 
     * @param {string} message Mensagem a ser exibida
     * @param {string} type Tipo da notificação (success, error, warning, info)
     */
    function showNotification(message, type = 'info') {
        // Remover notificações anteriores
        $('.llms-txt-notification').remove();
        
        // Criar elemento de notificação
        const $notification = $('<div class="llms-txt-notification notice is-dismissible notice-' + type + '"><p>' + message + '</p></div>');
        
        // Adicionar botão de fechamento
        const $closeButton = $('<button type="button" class="notice-dismiss"><span class="screen-reader-text">Fechar</span></button>');
        $closeButton.on('click', function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        $notification.append($closeButton);
        
        // Adicionar à interface e posicionar
        $notification.css({
            position: 'fixed',
            top: '32px',
            right: '15px',
            zIndex: 9999,
            maxWidth: '300px'
        });
        
        $('body').append($notification);
        
        // Esconder automaticamente após 3 segundos
        setTimeout(function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Inicializar quando o documento estiver pronto
    $(document).ready(init);
    
})(jQuery);
