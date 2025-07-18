/**
 * JavaScript para a meta box do LLMS.txt Generator
 *
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @updated 1.1.0 Adicionado suporte para DeepSeek R1
 * @updated 1.2.0 Adicionada compatibilidade com editor clássico
 * @author Dante Testa (https://dantetesta.com.br)
 */

(function($) {
    'use strict';

    /**
     * Inicialização quando o DOM estiver pronto
     */
    $(document).ready(function() {
        // Detectar se estamos no editor clássico
        const isClassicEditor = $('.llms-txt-meta-box.classic-editor').length > 0;
        
        // Aplicar ajustes específicos para o editor clássico
        if (isClassicEditor) {
            console.log('LLMS.txt: Editor clássico detectado, aplicando ajustes de compatibilidade...');
            
            // Ajustar larguras e espaçamentos para compatibilidade
            $('.llms-txt-meta-box.classic-editor textarea').css({
                'width': '100%',
                'min-height': '120px',
                'margin-bottom': '10px',
                'box-sizing': 'border-box'
            });
            
            // Corrigir comportamento dos toggle switches
            $('.llms-txt-meta-box.classic-editor .switch').each(function() {
                const $switch = $(this);
                const $checkbox = $switch.find('input[type="checkbox"]');
                const $slider = $switch.find('.slider');
                
                // Inicializar corretamente o visual com base no estado atual
                if ($checkbox.is(':checked')) {
                    $slider.css('background-color', '#3b82f6');
                } else {
                    $slider.css('background-color', '#ccc');
                }
                
                // Evento de clique no switch
                $switch.on('click', function(e) {
                    const isChecked = $checkbox.is(':checked');
                    $checkbox.prop('checked', !isChecked).trigger('change');
                    
                    // Atualizar visual
                    if (!isChecked) {
                        $slider.css('background-color', '#3b82f6');
                    } else {
                        $slider.css('background-color', '#ccc');
                    }
                    
                    e.preventDefault();
                });
            });
        }
        // Elementos da meta box
        const $descriptionTextarea = $('#llms_txt_description');
        const $charCount = $('#llms_txt_char_count');
        const $generateBtn = $('#llms_txt_generate_description');
        const $postId = $('#llms_txt_post_id');
        const $providerBadge = $('#llms_txt_provider_badge');
        const $feedback = $('#llms_txt_feedback');
        const $loading = $('#llms_txt_loading');
        const $success = $('#llms_txt_success');
        const $error = $('#llms_txt_error');
        const $errorMessage = $('#llms_txt_error_message');
        
        // Máximo de caracteres permitidos
        const maxChars = 350;

        /**
         * Nota: A geração automática ao carregar a página foi removida.
         * Agora a descrição só é gerada quando o usuário clica no botão.
         */
        // Funcionalidade de geração automática removida conforme solicitação

        /**
         * Atualiza o contador de caracteres quando o texto é alterado
         */
        $descriptionTextarea.on('input', function() {
            updateCharCount();
        });

        /**
         * Gera a descrição técnica via API quando o botão é clicado
         */
        $generateBtn.on('click', function() {
            generateDescription();
        });

        /**
         * Atualiza o contador de caracteres
         */
        function updateCharCount() {
            const currentLength = $descriptionTextarea.val().length;
            const remaining = maxChars - currentLength;
            
            $charCount.text(llms_txt_meta_box.characters_remaining.replace('%d', remaining));
            
            // Adicionar classe de aviso se estiver próximo do limite
            if (remaining < 20) {
                $charCount.removeClass('text-gray-500').addClass('text-orange-500');
            } else {
                $charCount.removeClass('text-orange-500').addClass('text-gray-500');
            }
            
            // Adicionar classe de erro se ultrapassar o limite
            if (remaining < 0) {
                $charCount.removeClass('text-orange-500').addClass('text-red-500');
            } else {
                $charCount.removeClass('text-red-500');
            }
        }

        /**
         * Gera a descrição técnica via API selecionada (OpenAI ou DeepSeek)
         */
        function generateDescription() {
            const postId = $postId.val();
            
            if (!postId) {
                showFeedback('error', llms_txt_meta_box.post_id_missing);
                return;
            }
            
            // Desabilitar botão e mostrar feedback de carregamento
            $generateBtn.prop('disabled', true);
            showFeedback('loading');
            
            // Enviar requisição AJAX
            $.ajax({
                url: llms_txt_meta_box.ajax_url,
                type: 'POST',
                data: {
                    action: 'llms_txt_generate_description',
                    nonce: llms_txt_meta_box.ajax_nonce, // Usando o nonce correto
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        // Atualizar textarea com a descrição gerada
                        $descriptionTextarea.val(response.data.description);
                        updateCharCount();
                        showFeedback('success');
                        
                        // Esconder feedback após 3 segundos
                        setTimeout(function() {
                            hideFeedback();
                        }, 3000);
                    } else {
                        showFeedback('error', response.data.message);
                    }
                },
                error: function() {
                    showFeedback('error', llms_txt_meta_box.generate_error);
                },
                complete: function() {
                    $generateBtn.prop('disabled', false);
                }
            });
        }

        /**
         * Exibe feedback para o usuário
         * 
         * @param {string} type Tipo de feedback (success, error, loading)
         * @param {string} message Mensagem de erro (opcional, apenas para tipo 'error')
         */
        function showFeedback(type, message) {
            // Esconder todos os feedbacks
            $loading.addClass('hidden');
            $success.addClass('hidden');
            $error.addClass('hidden');
            
            // Mostrar feedback específico
            if (type === 'loading') {
                $loading.removeClass('hidden');
            } else if (type === 'success') {
                $success.removeClass('hidden');
            } else if (type === 'error') {
                $errorMessage.text(message);
                $error.removeClass('hidden');
            }
            
            // Mostrar container de feedback
            $feedback.removeClass('hidden');
        }

        /**
         * Esconde todos os feedbacks
         */
        function hideFeedback() {
            $feedback.addClass('hidden');
        }

        // Inicializar contador de caracteres
        updateCharCount();
    });
})(jQuery);
