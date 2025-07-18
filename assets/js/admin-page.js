/**
 * JavaScript para a página de administração do LLMS.txt Generator
 * 
 * @author Dante Testa (https://dantetesta.com.br)
 * @version 1.1.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // Container para notificações toast
    const toastContainer = document.createElement('div');
    toastContainer.id = 'llms-txt-toast-container';
    toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col items-end space-y-2 transform transition-transform duration-300';
    toastContainer.style.transform = 'translateX(110%)';
    document.body.appendChild(toastContainer);
    
    // Função para mostrar notificações toast
    window.showToast = function(message, type = 'info') {
        const container = document.getElementById('llms-txt-toast-container');
        
        // Garantir que o container esteja visível
        container.style.transform = 'translateX(0)';
        
        // Criar o toast
        const toast = document.createElement('div');
        toast.className = `flex items-center p-3 mb-2 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-full max-w-xs ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        
        // Ícone
        const icon = document.createElement('div');
        icon.className = 'mr-2 flex-shrink-0';
        if (type === 'success') {
            icon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        } else if (type === 'error') {
            icon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        } else {
            icon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }
        toast.appendChild(icon);
        
        // Mensagem
        const text = document.createElement('div');
        text.className = 'text-sm font-medium';
        text.textContent = message;
        toast.appendChild(text);
        
        // Botão fechar
        const closeBtn = document.createElement('button');
        closeBtn.className = 'ml-auto text-white hover:text-gray-200';
        closeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        closeBtn.addEventListener('click', function() {
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode === container) {
                    container.removeChild(toast);
                }
                
                // Esconder o container se não houver mais toasts
                if (container.children.length === 0) {
                    container.style.transform = 'translateX(110%)';
                }
            }, 300);
        });
        toast.appendChild(closeBtn);
        
        // Adicionar ao container
        container.appendChild(toast);
        
        // Animação de entrada para o toast individual
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        // Remover após 5 segundos
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode === container) {
                    container.removeChild(toast);
                }
                
                // Esconder o container se não houver mais toasts
                if (container.children.length === 0) {
                    container.style.transform = 'translateX(110%)';
                }
            }, 300);
        }, 5000);
    };
    
    // Função para alternar visibilidade dos campos de API com base no provedor selecionado
    function toggleApiFields() {
        const selectedProvider = document.querySelector('input[name="llms_txt_settings[ai_provider]"]:checked').value;
        const apiFields = document.querySelectorAll('.openai-api-fields, .deepseek-api-fields');
        
        // Mostrar/ocultar campos de API apropriados
        apiFields.forEach(field => {
            const provider = field.getAttribute('data-provider');
            if (provider === selectedProvider) {
                field.style.display = 'block';
            } else {
                field.style.display = 'none';
            }
        });
        
        // Atualizar estilo dos cards de seleção de provedor
        const cards = document.querySelectorAll('.provider-card');
        const indicators = document.querySelectorAll('.check-indicator');
        
        cards.forEach((card, index) => {
            const radio = card.querySelector('.provider-radio');
            const indicator = indicators[index];
            
            if (radio.value === selectedProvider) {
                card.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
                card.classList.remove('border-gray-200');
                indicator.classList.add('opacity-100');
                indicator.classList.remove('opacity-0');
            } else {
                card.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
                card.classList.add('border-gray-200');
                indicator.classList.remove('opacity-100');
                indicator.classList.add('opacity-0');
            }
        });
    }
    
    // Adicionar eventos para os botões de rádio de seleção de provedor
    const providerCards = document.querySelectorAll('.provider-card');
    providerCards.forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('.provider-radio');
            radio.checked = true;
            toggleApiFields();
        });
    });
    
    /**
     * Função para validar chave da API
     * Exibe feedback visual com ícones e mensagens claras
     * 
     * @param {string} provider - O provedor da API ('openai' ou 'deepseek')
     */
    function validateApiKey(provider) {
        let apiKey, statusSpan, inputField;
        
        if (provider === 'openai') {
            apiKey = document.getElementById('llms_txt_openai_api_key').value;
            statusSpan = document.getElementById('llms_txt_openai_api_key_status');
            inputField = document.getElementById('llms_txt_openai_api_key');
        } else if (provider === 'deepseek') {
            apiKey = document.getElementById('llms_txt_deepseek_api_key').value;
            statusSpan = document.getElementById('llms_txt_deepseek_api_key_status');
            inputField = document.getElementById('llms_txt_deepseek_api_key');
        } else {
            return;
        }
        
        // Remover classes de estado anterior
        inputField.classList.remove('border-red-500', 'border-green-500', 'border-yellow-500');
        
        if (!apiKey) {
            // Estado: chave não informada
            inputField.classList.add('border-red-500');
            statusSpan.innerHTML = `
                <span class="inline-flex items-center text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Chave não informada</span>
                </span>
            `;
            showToast('Por favor, informe uma chave de API válida.', 'error');
            return;
        }
        
        // Estado: validando
        inputField.classList.add('border-yellow-500');
        statusSpan.innerHTML = `
            <span class="inline-flex items-center text-yellow-500">
                <svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Validando...</span>
            </span>
        `;
        
        // Fazer chamada AJAX para validar a chave
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'llms_txt_validate_api_key',
                nonce: llms_txt_admin.nonce,
                api_key: apiKey,
                api_provider: provider
            },
            success: function(response) {
                if (response.success) {
                    // Estado: chave válida
                    inputField.classList.remove('border-yellow-500');
                    inputField.classList.add('border-green-500');
                    statusSpan.innerHTML = `
                        <span class="inline-flex items-center text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Chave válida!</span>
                        </span>
                    `;
                    showToast(response.data.message, 'success');
                } else {
                    // Estado: chave inválida
                    inputField.classList.remove('border-yellow-500');
                    inputField.classList.add('border-red-500');
                    statusSpan.innerHTML = `
                        <span class="inline-flex items-center text-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Chave inválida</span>
                        </span>
                    `;
                    showToast(response.data.message, 'error');
                }
            },
            error: function() {
                // Estado: erro na validação
                inputField.classList.remove('border-yellow-500');
                inputField.classList.add('border-red-500');
                statusSpan.innerHTML = `
                    <span class="inline-flex items-center text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Erro na validação</span>
                    </span>
                `;
                showToast('Ocorreu um erro ao validar a chave. Por favor, tente novamente.', 'error');
            }
        });
    }
    
    // Adicionar eventos para os botões de validar API
    const validateOpenAIButton = document.getElementById('llms_txt_validate_openai_api_key');
    if (validateOpenAIButton) {
        validateOpenAIButton.addEventListener('click', function() {
            validateApiKey('openai');
        });
    }
    
    const validateDeepSeekButton = document.getElementById('llms_txt_validate_deepseek_api_key');
    if (validateDeepSeekButton) {
        validateDeepSeekButton.addEventListener('click', function() {
            validateApiKey('deepseek');
        });
    }
    
    // Configurar os switchers - implementação simplificada
    const setupToggleSwitchers = function() {
        // Remover completamente todos os eventos existentes
        document.querySelectorAll('.toggle-label').forEach(label => {
            const newLabel = label.cloneNode(true);
            if (label.parentNode) {
                label.parentNode.replaceChild(newLabel, label);
            }
        });
        
        // Abordagem direta: adicionar eventos de clique nos próprios labels
        document.querySelectorAll('.toggle-label').forEach(label => {
            // Encontrar os elementos dentro do label
            const checkbox = label.querySelector('.toggle-checkbox');
            const toggleDot = label.querySelector('.toggle-dot');
            const toggleBg = label.querySelector('.toggle-bg');
            
            if (!checkbox) return;
            
            console.log('Configurando switcher:', checkbox.id, 'Estado:', checkbox.checked ? 'ATIVO' : 'INATIVO');
            
            // Função que atualiza o visual do toggle com base no estado do checkbox
            const atualizarVisual = function() {
                if (!toggleDot || !toggleBg) return;
                
                if (checkbox.checked) {
                    toggleDot.classList.add('transform', 'translate-x-6');
                    toggleBg.classList.add('bg-blue-500');
                    toggleBg.classList.remove('bg-gray-300');
                } else {
                    toggleDot.classList.remove('transform', 'translate-x-6');
                    toggleBg.classList.remove('bg-blue-500');
                    toggleBg.classList.add('bg-gray-300');
                }
                
                // Atualizar estados de acessibilidade
                label.setAttribute('aria-checked', checkbox.checked ? 'true' : 'false');
                
                // Atualizar interfaces dependentes
                if (checkbox.id === 'llms_txt_include_pages') {
                    updateIncludePagesUI(checkbox.checked);
                } else if (checkbox.id === 'llms_txt_include_posts') {
                    updateIncludePostsUI(checkbox.checked);
                }
                
                // Log para debug
                console.log(`Switcher ${checkbox.id} alterado para: ${checkbox.checked ? 'ATIVO' : 'INATIVO'}`);
            };
            
            // Inicializar visual baseado no estado atual
            atualizarVisual();
            
            // Adicionar eventos
            label.addEventListener('click', function(e) {
                // Não fazer nada se clicou diretamente no checkbox (deixar comportamento padrão)
                if (e.target === checkbox) return;
                
                // Impedir propagação e comportamento padrão
                e.preventDefault();
                e.stopPropagation();
                
                // Alternar estado
                checkbox.checked = !checkbox.checked;
                
                // Atualizar visual
                atualizarVisual();
            });
            
            // Garantir que o próprio checkbox também atualize o visual
            checkbox.addEventListener('change', function() {
                atualizarVisual();
            });
            
            // Adicionar suporte para teclado
            label.setAttribute('tabindex', '0');
            label.setAttribute('role', 'switch');
            label.addEventListener('keydown', function(e) {
                if (e.key === ' ' || e.key === 'Enter') {
                    e.preventDefault();
                    checkbox.checked = !checkbox.checked;
                    atualizarVisual();
                }
            });
        });
    };
    
    // Função para debug
    function logSwitcherState() {
        const checkboxes = document.querySelectorAll('.toggle-checkbox');
        checkboxes.forEach(checkbox => {
            console.log(`Estado do switcher ${checkbox.id}: ${checkbox.checked ? 'ATIVO' : 'INATIVO'}`);
        });
    }
    
    // Log inicial
    console.log('Inicializando estado dos switchers...');
    setTimeout(logSwitcherState, 500);
    
    // Função para atualizar a interface com base no estado dos switchers
    function updateIncludePagesUI(isChecked) {
        const pagesSection = document.querySelector('.pages-settings-section');
        if (pagesSection) {
            if (isChecked) {
                pagesSection.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                pagesSection.classList.add('opacity-50', 'pointer-events-none');
            }
        }
    }
    
    // Função para atualizar a interface com base no estado do switcher 'Incluir posts'
    function updateIncludePostsUI(isChecked) {
        const postsSection = document.querySelector('.posts-settings-section');
        if (postsSection) {
            if (isChecked) {
                postsSection.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                postsSection.classList.add('opacity-50', 'pointer-events-none');
            }
        }
    }
    /**
     * Configura os botões de mostrar/ocultar senha
     * Implementa funcionalidade responsiva e acessível
     */
    function setupPasswordToggles() {
        const toggleButtons = document.querySelectorAll('.password-toggle');
        
        toggleButtons.forEach(button => {
            const targetId = button.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const iconElement = button.querySelector('svg');
            const textElement = button.querySelector('.toggle-text');
            
            if (!targetInput) return;
            
            /**
             * Atualiza o texto e ícone do botão com base na largura da tela
             * e no estado atual do campo (visível/oculto)
             */
            function updateButtonAppearance() {
                const isVisible = targetInput.getAttribute('type') === 'text';
                const isMobile = window.innerWidth < 640; // Breakpoint sm do Tailwind
                
                // Atualizar ícone baseado no estado atual
                if (isVisible) {
                    iconElement.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                    button.setAttribute('aria-label', 'Ocultar chave API');
                    if (textElement) textElement.textContent = 'Ocultar';
                } else {
                    iconElement.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
                    button.setAttribute('aria-label', 'Mostrar chave API');
                    if (textElement) textElement.textContent = 'Mostrar';
                }
                
                // Ajustar visibilidade do texto com base no tamanho da tela
                if (isMobile) {
                    if (textElement) textElement.classList.add('hidden');
                } else {
                    if (textElement) textElement.classList.remove('hidden');
                }
            }
            
            // Atualizar inicialmente
            updateButtonAppearance();
            
            // Atualizar quando a janela for redimensionada
            window.addEventListener('resize', updateButtonAppearance);
            
            // Manipulador de clique para alternar visibilidade da senha
            button.addEventListener('click', function() {
                const type = targetInput.getAttribute('type');
                
                if (type === 'password') {
                    targetInput.setAttribute('type', 'text');
                    // Foco no campo para melhor experiência do usuário
                    targetInput.focus();
                } else {
                    targetInput.setAttribute('type', 'password');
                }
                
                // Atualizar aparência do botão após o clique
                updateButtonAppearance();
            });
        });
    }
    
    
    // Inicializar todos os componentes
    setupToggleSwitchers();
    setupPasswordToggles();
    toggleApiFields(); // Garantir que o estado inicial esteja correto
    
    // Adicionar evento para o botão de visualizar arquivo
    const previewButton = document.getElementById('llms_txt_preview_file');
    if (previewButton) {
        // Remover eventos antigos para evitar duplicação
        const newPreviewButton = previewButton.cloneNode(true);
        previewButton.parentNode.replaceChild(newPreviewButton, previewButton);
        
        newPreviewButton.addEventListener('click', function(e) {
            // Prevenir comportamento padrão para evitar reload
            e.preventDefault();
            
            const previewContainer = document.getElementById('llms_txt_preview_container');
            const previewContent = document.getElementById('llms_txt_preview_content');
            
            if (!previewContainer || !previewContent) {
                console.error('Elementos de visualização não encontrados');
                return;
            }
            
            if (previewContainer.classList.contains('hidden')) {
                // Mostrar visualização
                previewContainer.classList.remove('hidden');
                newPreviewButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    Ocultar visualização
                `;
                
                // Fazer chamada AJAX para obter o conteúdo do arquivo
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'llms_txt_get_preview',
                        nonce: llms_txt_admin.nonce
                    },
                    beforeSend: function() {
                        previewContent.textContent = 'Carregando...';
                    },
                    success: function(response) {
                        if (response.success) {
                            previewContent.textContent = response.data.content;
                        } else {
                            previewContent.textContent = response.data.message || 'Erro ao carregar o conteúdo do arquivo.';
                        }
                    },
                    error: function() {
                        previewContent.textContent = 'Erro ao carregar o conteúdo do arquivo.';
                    }
                });
            } else {
                // Ocultar visualização
                previewContainer.classList.add('hidden');
                newPreviewButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Visualizar arquivo
                `;
            }
        });
    }
});
