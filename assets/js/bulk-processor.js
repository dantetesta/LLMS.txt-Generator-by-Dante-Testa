/**
 * Script para processamento em lote de geração de descrições LLMS
 * 
 * @package LLMS_Txt_Generator
 * @since 1.0.0
 * @author Dante Testa (https://dantetesta.com.br)
 */
(function($) {
    'use strict';

    // Variáveis de controle
    let queue = [];
    let processingQueue = false;
    let activeRequests = 0;
    let maxParallelRequests = 3;
    let totalItems = 0;
    let processedItems = 0;
    let successCount = 0;
    let errorCount = 0;
    let skippedCount = 0;
    let isCancelled = false;
    
    // Referências da UI
    let $notification = null;
    let $progressBar = null;
    let $progressText = null;
    let $successCount = null;
    let $errorCount = null;
    let $skippedCount = null;

    /**
     * Inicializa o processador
     * 
     * @param {Array} items Lista de IDs dos posts a serem processados
     */
    function initBulkProcessor(items) {
        // Resetar estado
        queue = items;
        totalItems = items.length;
        processedItems = 0;
        successCount = 0;
        errorCount = 0;
        isCancelled = false;
        
        // Criar notificação
        createNotification();
        
        // Iniciar processamento
        processQueue();
    }

    /**
     * Cria e exibe a notificação com barra de progresso
     */
    function createNotification() {
        // Remover notificação existente, se houver
        if ($notification) {
            $notification.remove();
        }
        
        // Criar estrutura da notificação
        $notification = $('<div class="llms-txt-bulk-notification"></div>');
        
        const $header = $('<div class="llms-txt-bulk-notification-header"></div>');
        $header.append('<h3 class="llms-txt-bulk-notification-title">' + llmsTxtBulk.processingTitle + '</h3>');
        $header.append('<span class="llms-txt-bulk-close dashicons dashicons-no-alt"></span>');
        
        const $progressContainer = $('<div class="llms-txt-progress-bar-container"></div>');
        $progressBar = $('<div class="llms-txt-progress-bar"></div>');
        $progressContainer.append($progressBar);
        
        const $progressInfo = $('<div class="llms-txt-progress-info"></div>');
        $progressText = $('<span class="llms-txt-progress-text">0/' + totalItems + '</span>');
        const $percentage = $('<span class="llms-txt-progress-percentage">0%</span>');
        $progressInfo.append($progressText);
        $progressInfo.append($percentage);
        
        const $actions = $('<div class="llms-txt-actions"></div>');
        $successCount = $('<span class="llms-txt-success-count">' + llmsTxtBulk.successText + ': 0</span>');
        $errorCount = $('<span class="llms-txt-error-count">' + llmsTxtBulk.errorText + ': 0</span>');
        $skippedCount = $('<span class="llms-txt-skipped-count">Pulados: 0</span>');
        const $stats = $('<div class="llms-txt-stats"></div>').append($successCount).append(' | ').append($errorCount).append(' | ').append($skippedCount);
        
        const $cancelButton = $('<button type="button" class="llms-txt-cancel-button">' + llmsTxtBulk.cancelText + '</button>');
        
        $actions.append($stats);
        $actions.append($cancelButton);
        
        // Montar a notificação
        $notification.append($header);
        $notification.append($progressContainer);
        $notification.append($progressInfo);
        $notification.append($actions);
        
        // Adicionar à página
        $('body').append($notification);
        
        // Eventos
        $cancelButton.on('click', cancelProcessing);
        $('.llms-txt-bulk-close').on('click', closeNotification);
    }

    /**
     * Atualiza a barra de progresso
     */
    function updateProgress() {
        const percent = Math.floor((processedItems / totalItems) * 100);
        $progressBar.css('width', percent + '%');
        $progressText.text(processedItems + '/' + totalItems);
        $('.llms-txt-progress-percentage').text(percent + '%');
        $successCount.text(llmsTxtBulk.successText + ': ' + successCount);
        $errorCount.text(llmsTxtBulk.errorText + ': ' + errorCount);
        $skippedCount.text('Pulados: ' + skippedCount);
    }

    /**
     * Processa a fila de itens
     */
    function processQueue() {
        if (isCancelled) {
            finishProcessing();
            return;
        }
        
        processingQueue = true;
        
        // Verificar se precisamos adicionar delay por ter processado muitos itens
        if (processedItems > 0 && processedItems % 500 === 0) {
            showDelayMessage();
            
            setTimeout(() => {
                processNextBatch();
            }, 30000); // 30 segundos de delay a cada 500 itens
            
            return;
        }
        
        processNextBatch();
    }

    /**
     * Exibe mensagem de delay
     */
    function showDelayMessage() {
        const $delayMessage = $('<div class="notice notice-info inline"><p>' + llmsTxtBulk.delayMessage + '</p></div>');
        $notification.append($delayMessage);
        
        setTimeout(() => {
            $delayMessage.fadeOut(500, function() {
                $(this).remove();
            });
        }, 29500);
    }

    /**
     * Processa o próximo lote de itens
     */
    function processNextBatch() {
        // Verificar se ainda há itens na fila
        if (queue.length === 0) {
            if (activeRequests === 0) {
                finishProcessing();
            }
            return;
        }
        
        // Processar mais itens se pudermos
        while (activeRequests < maxParallelRequests && queue.length > 0) {
            const postId = queue.shift();
            processItem(postId);
        }
    }

    /**
     * Processa um item específico
     * 
     * @param {number} postId ID do post a ser processado
     */
    function processItem(postId) {
        activeRequests++;
        
        $.ajax({
            url: llmsTxtBulk.ajaxUrl,
            type: 'POST',
            data: {
                action: 'llms_txt_generate_single_description',
                post_id: postId,
                nonce: llmsTxtBulk.nonce,
                is_bulk: true,
                force_regenerate: llmsTxtBulk.forceRegenerate
            },
            beforeSend: function() {
                console.log('Iniciando requisição para o post ID: ' + postId);
            },
            success: function(response) {
                processedItems++;
                
                if (response.success) {
                    // Verificar se o item foi pulado (já tinha descrição)
                    if (response.data && response.data.skipped) {
                        skippedCount++;
                        
                        // Atualizar ícone na lista administrativa, se estiver visível
                        const $statusCell = $('.llms-txt-status[data-post-id="' + postId + '"]');
                        if ($statusCell.length) {
                            $statusCell.html('<span class="dashicons dashicons-yes llms-txt-icon-skipped" title="' + llmsTxtBulk.skipExistingText + '"></span>');
                        }
                        
                        // Registrar mensagem de pulado no console
                        console.log('Post ID ' + postId + ': Descrição já existente, pulado');
                    } else {
                        successCount++;
                        
                        // Atualizar ícone na lista administrativa, se estiver visível
                        const $statusCell = $('.llms-txt-status[data-post-id="' + postId + '"]');
                        if ($statusCell.length) {
                            $statusCell.html('<span class="dashicons dashicons-yes-alt llms-txt-icon-success" title="' + llmsTxtBulk.successText + '"></span>');
                        }
                        
                        // Registrar mensagem de sucesso no console
                        console.log('Post ID ' + postId + ': Descrição gerada com sucesso');
                    }
                } else {
                    errorCount++;
                    
                    // Exibir mensagem de erro no console
                    const errorMsg = response.data && response.data.message ? response.data.message : 'Erro desconhecido';
                    console.error('Post ID ' + postId + ': ' + errorMsg);
                    
                    // Atualizar ícone de erro na lista administrativa, se estiver visível
                    const $statusCell = $('.llms-txt-status[data-post-id="' + postId + '"]');
                    if ($statusCell.length) {
                        $statusCell.html('<span class="dashicons dashicons-warning llms-txt-icon-error" title="' + errorMsg + '"></span>');
                    }
                }
                
                updateProgress();
                completeRequest();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                processedItems++;
                errorCount++;
                
                // Registrar erro no console
                console.error('Post ID ' + postId + ': Falha na requisição AJAX', textStatus, errorThrown);
                
                // Atualizar ícone de erro na lista administrativa, se estiver visível
                const $statusCell = $('.llms-txt-status[data-post-id="' + postId + '"]');
                if ($statusCell.length) {
                    $statusCell.html('<span class="dashicons dashicons-warning llms-txt-icon-error" title="Falha na requisição"></span>');
                }
                
                updateProgress();
                completeRequest();
            }
        });
    }

    /**
     * Finaliza uma requisição e verifica se deve continuar processamento
     */
    function completeRequest() {
        activeRequests--;
        
        if (queue.length > 0 || activeRequests > 0) {
            // Continuar processando se ainda houver itens na fila
            if (queue.length > 0 && activeRequests < maxParallelRequests) {
                processNextBatch();
            }
        } else {
            // Finalizar processamento
            finishProcessing();
        }
    }

    /**
     * Finaliza o processamento da fila
     */
    function finishProcessing() {
        processingQueue = false;
        
        // Atualizar texto do botão
        const closeText = llmsTxtBulk.closeText || 'Fechar';
        $('.llms-txt-cancel-button').text(closeText).off('click').on('click', closeNotification);
        
        // Atualizar título e estilo da notificação
        const completedTitle = llmsTxtBulk.completeText || 'Processamento concluído';
        $('.llms-txt-bulk-notification-title').text(completedTitle);
        
        // Adicionar mensagem de conclusão
        const completeMsg = llmsTxtBulk.completeMessage || 'Todas as descrições foram processadas.';
        
        // Remover mensagem anterior se existir
        $('.llms-txt-complete-message').remove();
        
        // Adicionar nova mensagem de conclusão com estilo
        const $completeMsg = $('<div class="llms-txt-complete-message" style="margin-top: 10px; padding: 10px; background-color: #ecf8f0; border-left: 4px solid #46b450; color: #333;">' + 
            '<span class="dashicons dashicons-yes-alt" style="color: #46b450; margin-right: 5px;"></span>' + 
            completeMsg + '</div>');
        
        $notification.append($completeMsg);
        
        // Mudar a cor da barra de progresso para verde
        $progressBar.css('background-color', '#46b450');
        
        // Se estamos na página de lista, fornecer opção para recarregar
        const isListPage = typeof llmsTxtBulk.isListingPage !== 'undefined' ? llmsTxtBulk.isListingPage : true;
        
        if (isListPage) {
            // Adicionar botão para recarregar
            const $reloadBtn = $('<button type="button" class="llms-txt-reload-button button button-primary" style="margin-left: 10px;">Recarregar página</button>');
            $reloadBtn.on('click', function() {
                location.reload();
            });
            
            // Adicionar ao container de botões
            $('.llms-txt-actions').append($reloadBtn);
        }
    }

    /**
     * Cancela o processamento da fila
     */
    function cancelProcessing() {
        isCancelled = true;
        queue = [];
        
        // Atualizar UI
        $('.llms-txt-bulk-notification-title').text(llmsTxtBulk.cancelledTitle);
        $('.llms-txt-cancel-button').text(llmsTxtBulk.closeText).off('click').on('click', closeNotification);
    }

    /**
     * Fecha a notificação
     */
    function closeNotification() {
        if ($notification) {
            $notification.fadeOut(300, function() {
                $(this).remove();
                $notification = null;
            });
        }
        
        // Cancelar processamento se ainda estiver ativo
        if (processingQueue) {
            cancelProcessing();
        }
    }

    /**
     * Evento para iniciar o processamento em lote
     */
    $(document).on('llms_txt_init_bulk_process', function(e, items) {
        if (Array.isArray(items) && items.length > 0) {
            initBulkProcessor(items);
        }
    });

    // Exportar funções para uso global
    window.llmsTxtBulkProcessor = {
        init: initBulkProcessor,
        cancel: cancelProcessing
    };

})(jQuery);
