<p align="center">
  <img src="https://img.shields.io/badge/WordPress-Plugin-blue.svg" alt="WordPress Plugin">
  <img src="https://img.shields.io/badge/Versão-2.0.6-green.svg" alt="Versão">
  <img src="https://img.shields.io/badge/Licença-GPL%20v2%2B-orange.svg" alt="Licença">
</p>

# LLMS.txt Generator

<p align="center">
  <b>Plugin WordPress para gerar e gerenciar o arquivo llms.txt</b><br>
  Controle como as Inteligências Artificiais acessam e utilizam o conteúdo do seu site WordPress
</p>

<p align="center">
  <a href="https://youtu.be/fsVKjmBlwDM">
    <img src="https://img.shields.io/badge/Assista%20o%20vídeo-YouTube-red.svg" alt="YouTube Video">
  </a>
</p>

<div align="center" style="margin: 40px 0;">
  <a href="https://github.com/dantetesta/LLMS.txt-Generator-by-Dante-Testa/releases/download/v2.0.6/LLMS.txt-Generator-v2.0.6.zip" style="display:inline-block; text-decoration: none;">
    <img src="https://img.shields.io/badge/📥%20DOWNLOAD%20PLUGIN-Vers%C3%A3o%202.0.6-2ea44f?style=for-the-badge&logo=wordpress&logoColor=white&labelColor=1e40af" alt="DOWNLOAD PLUGIN" width="400" height="60">
  </a>
  <br>
  <small style="color: #666; margin-top: 10px; display: block;">✅ Versão mais recente | 🐛 Correções de bugs | 🚀 Pronto para produção</small>
</div>

## 🔎 O que é o arquivo llms.txt?

O arquivo llms.txt é um padrão emergente similar ao robots.txt, mas focado em Inteligências Artificiais como ChatGPT, Claude e Gemini. Ele permite que proprietários de sites controlem quais partes do seu conteúdo podem ser acessadas, lidas e utilizadas por modelos de IA para treinamento ou geração de conteúdo.

Este plugin simplifica a criação e gerenciamento deste arquivo em sites WordPress, oferecendo uma interface amigável e recursos avançados para controle granular do acesso de IAs ao seu conteúdo.

## ✨ Funcionalidades Detalhadas

### Gerenciamento do Arquivo llms.txt

- **Geração automática**: Criação do arquivo llms.txt com base nas suas configurações
- **Controle de tipos de post**: Escolha quais tipos de conteúdo (posts, páginas, produtos, etc.) incluir
- **Controle de taxonomias**: Inclua ou exclua categorias, tags e taxonomias personalizadas
- **Atualização automática**: O arquivo é regenerado automaticamente quando o conteúdo é publicado ou atualizado
- **Regras personalizadas**: Adicione suas próprias regras ao arquivo

### Descrições Técnicas com IA

- **Integração com OpenAI**: Gere descrições técnicas automaticamente usando GPT-4 e outros modelos
- **Integração com DeepSeek**: Alternativa à OpenAI com o modelo DeepSeek-Coder
- **Geração individual**: Crie descrições para posts específicos
- **Geração em massa**: Ferramenta para processar múltiplos posts simultaneamente
- **Customização manual**: Edite as descrições geradas conforme necessário
- **Suporte a CPTs**: Funciona com qualquer tipo de post personalizado

### Interface Administrativa

- **Design moderno**: Interface limpa e intuitiva com Tailwind CSS
- **Responsivo**: Adapta-se perfeitamente a dispositivos móveis e desktops
- **Meta box dedicada**: Controle fácil do conteúdo individual diretamente na tela de edição
- **Painel administrativo**: Gerenciamento central de todas as configurações
- **Feedback visual**: Notificações toast e indicadores de status
- **Pré-visualização**: Visualize o arquivo llms.txt antes de publicá-lo

### Recursos Avançados

- **Exclusão individual**: Marque posts específicos para não aparecerem no arquivo llms.txt
- **Processamento em lote**: Geração paralela de descrições para múltiplos conteúdos
- **Admin Columns**: Visualização e gerenciamento de descrições diretamente na lista de posts
- **Contador de caracteres**: Monitoramento do tamanho das descrições técnicas
- **Compatibilidade com Gutenberg e Editor Clássico**
- **Configuração de CPTs**: Controle granular sobre fonte de conteúdo para tipos personalizados
- **Campos personalizados**: Suporte completo a metafields como fonte de conteúdo
- **Logs de debug**: Sistema de logging para troubleshooting e monitoramento
- **Validação de APIs**: Verificação automática de chaves de API
- **Fallbacks inteligentes**: Sistema de fallback quando conteúdo configurado não está disponível

### 🌐 Internacionalização

- **Multilíngue**: Suporte completo a múltiplos idiomas
- **Português brasileiro**: Tradução nativa completa (pt_BR)
- **Inglês americano**: Tradução completa (en_US)
- **Sistema i18n**: Arquitetura robusta para adição de novos idiomas
- **Formatos modernos**: Suporte a arquivos .po, .mo e .l10n.php
- **Tradução automática**: Interface administrativa traduzida automaticamente
- **Localização JavaScript**: Scripts localizados para feedback em tempo real

## 💾 Instalação

### Método Padrão

1. Faça o download do plugin [aqui](https://github.com/dantetesta/LLMS.txt-Generator-by-Dante-Testa/archive/refs/heads/main.zip)
2. Acesse seu painel WordPress > Plugins > Adicionar Novo > Enviar Plugin
3. Selecione o arquivo ZIP baixado e clique em "Instalar Agora"
4. Após a instalação, clique em "Ativar Plugin"

### Via FTP

1. Descompacte o arquivo ZIP do plugin
2. Faça upload da pasta `LLMS-Plugin` para o diretório `/wp-content/plugins/` do seu servidor
3. Acesse seu painel WordPress > Plugins
4. Localize "LLMS.txt Generator" na lista e clique em "Ativar"

## ⚙️ Configuração

### Configuração Básica

1. Após ativar o plugin, acesse **Configurações > LLMS.txt Generator**
2. Na seção **Geral**:
   - Habilite o arquivo llms.txt
   - Configure a descrição do seu site
   - Selecione os tipos de conteúdo a incluir

### Configuração de Integração com IA

1. Na seção **Integração com IA**:
   - Escolha o provedor (OpenAI ou DeepSeek)
   - Insira sua chave API
   - Clique em "Validar Chave da API"
   - Selecione o modelo de IA desejado
   - Configure a geração automática de descrições

### Regras Personalizadas

1. Na seção **Regras Personalizadas**:
   - Adicione quaisquer instruções específicas para IAs
   - Use a sintaxe padrão do llms.txt

## 📝 Uso

### Controle de Conteúdo Individual

1. Edite qualquer post, página ou tipo de post personalizado
2. Localize a meta box **LLMS.txt - Controle de IA** abaixo do editor
3. Para **excluir** o conteúdo do arquivo llms.txt, marque a opção correspondente
4. Para adicionar uma **descrição técnica**:
   - Digite manualmente no campo
   - Ou clique em "Gerar Automaticamente" para usar IA
5. Salve o post para aplicar as alterações

### Geração em Massa

1. Acesse Posts > Todos os Posts (ou qualquer outro tipo de conteúdo)
2. Selecione os itens que deseja processar
3. No menu suspenso "Ações em Massa", escolha "Gerar descrições LLMS"
4. Clique em "Aplicar"
5. Aguarde a conclusão do processamento em lote

### Verificar o Arquivo llms.txt

- Acesse seu site em `https://seu-site.com/llms.txt`
- Ou clique no botão "Ver arquivo llms.txt" no painel administrativo

## 🔧 Suporte Técnico

### Recursos de Suporte

- **Vídeo tutorial**: [Assista no YouTube](https://youtu.be/fsVKjmBlwDM)
- **Site oficial**: [dantetesta.com.br](https://dantetesta.com.br)
- **E-mail de suporte**: dante.testa@gmail.com

### Relatar Problemas

Encontrando problemas com o plugin? Por favor, abra uma issue no GitHub com as seguintes informações:
- Versão do WordPress
- Versão do PHP
- Versão do plugin
- Descrição detalhada do problema
- Passos para reproduzir o erro

## 💳 Apoie o Desenvolvimento

Se este plugin está ajudando seu site a ter uma melhor interação com IAs, considere apoiar o desenvolvimento:

### PIX (Brasil)

- **Chave PIX**: dante.testa@gmail.com
- *Pague um café pra mim se você gostou da solução!* 😊

### PayPal (Internacional)

- **PayPal**: dante.testa@gmail.com
- [Link direto para doação](https://www.paypal.com/donate/?hosted_button_id=BAQGVU8MGWDTN)

## 📝 Changelog

### 🚀 Versão 2.0.6 (18 de Janeiro 2025)

#### 🐛 **Correções Críticas**
- **Erro Fatal Corrigido**: Resolvido erro `Call to undefined function get_current_screen()` no bulk generator
- **Verificação de Segurança**: Adicionada verificação `function_exists()` antes de usar `get_current_screen()`
- **Compatibilidade Melhorada**: Sistema mais robusto para diferentes contextos do WordPress
- **Timing de Hooks**: Correção de problemas de timing entre hooks `wp_redirect` e `current_screen`

#### 🔧 **Melhorias Técnicas**
- **Bulk Actions**: Sistema de ações em massa mais estável e confiável
- **Error Handling**: Tratamento de erros aprimorado para prevenir falhas fatais
- **Compatibilidade**: Melhor suporte a diferentes versões do WordPress

---

### 🌟 Versão 2.0.5 (17 de Janeiro 2025)

#### 🎨 **Interface e Usabilidade**
- **Layout Responsivo**: Interface ocupa 100% da largura disponível
- **Switchers Melhorados**: Visual aprimorado para seleção de IA (OpenAI/DeepSeek)
- **Botões Modernos**: Design mais intuitivo e acessível
- **Seção do Desenvolvedor**: Layout profissional com informações de contato

#### 🔧 **Correções de Funcionalidade**
- **Switchers de Posts/Páginas**: Correção no salvamento de estados
- **Campos Ocultos**: Implementação de hidden inputs para garantir envio correto
- **JavaScript Aprimorado**: Melhor gerenciamento de eventos e estados

---

### 🌐 Versão 2.0.4 (16 de Janeiro 2025)

#### 🌍 **Internacionalização**
- **Tradução Completa**: Suporte total ao inglês americano (en_US)
- **Sistema i18n**: Classe `LLMS_Txt_I18n` implementada
- **Arquivos de Tradução**: `.po`, `.mo` e `.l10n.php` para múltiplos idiomas
- **Carregamento Automático**: Detecção automática do idioma do WordPress

#### 🔧 **Correções de CPTs**
- **Bulk Generator**: Respeita configuração de metafields em CPTs
- **Admin Columns**: Botão individual usa fonte configurada corretamente
- **Meta Box**: Funcional em todos os CPTs habilitados
- **Geração Automática**: Respeita configuração de campos personalizados
- **Arquivo llms.txt**: Remove limite de 20 posts (inclui todos)

#### 🛠️ **Melhorias Técnicas**
- **Função Auxiliar**: `extract_post_content()` centralizada
- **Logs de Debug**: Sistema de troubleshooting implementado
- **Consistência**: Unificação entre todas as formas de geração
- **Segurança**: Verificações aprimoradas em todo o sistema

---

### 🎉 Versão 2.0.0 (Julho 2025)

#### 🚀 **Recursos Principais**
- **DeepSeek Integration**: Alternativa à API OpenAI com modelo R1
- **Interface Moderna**: Nova UI com Tailwind CSS
- **Admin Columns**: Geração em massa via interface administrativa
- **Performance**: Melhorias significativas de velocidade
- **CPTs Completos**: Suporte total a tipos de post personalizados
- **Exclusão Individual**: Opção para excluir posts específicos do arquivo

#### 🎨 **Design e UX**
- **Responsividade**: Layout adaptável para todos os dispositivos
- **Acessibilidade**: Melhorias para usuários com necessidades especiais
- **Feedback Visual**: Indicadores de status e progresso

---

### 🌱 Versão 1.0.0 (Julho 2025)

#### 🎯 **Lançamento Inicial**
- **Arquivo llms.txt**: Suporte básico ao padrão LLMS
- **OpenAI Integration**: Integração com API GPT-4
- **Meta Box**: Controle individual de conteúdo
- **Interface Admin**: Painel administrativo básico
- **WordPress Core**: Compatibilidade com versões 5.6+

## 👨‍💻 Sobre o Desenvolvedor

[Dante Testa](https://dantetesta.com.br) é um desenvolvedor web especializado em WordPress e entusiasta da tecnologia. Apaixonado por criar soluções que unem inovação e criatividade, dedica-se a desenvolver plugins e temas que simplificam a vida de seus usuários. Como educador, compartilha conhecimento através de cursos, mentorias e conteúdo gratuito, ajudando profissionais a dominarem o ecossistema WordPress. Sua missão é democratizar o acesso a ferramentas de qualidade e conhecimento técnico, tornando a web mais acessível para todos.

### Outros Recursos

- [Canal no YouTube](https://www.youtube.com/@dantetesta)
- [Website](https://dantetesta.com.br)
- [GitHub](https://github.com/dantetesta)

## 🔐 Licença

Este plugin é licenciado sob a [GPL v2 ou posterior](http://www.gnu.org/licenses/gpl-2.0.html).

---

<p align="center">
  Feito com ❤️ no Brasil
</p>
