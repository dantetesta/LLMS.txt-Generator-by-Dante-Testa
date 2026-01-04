<p align="center">
  <img src="https://img.shields.io/badge/WordPress-Plugin-blue.svg" alt="WordPress Plugin">
  <img src="https://img.shields.io/badge/Versão-2.2.0-green.svg" alt="Versão">
  <img src="https://img.shields.io/badge/PHP-8.0+-purple.svg" alt="PHP 8.0+">
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

<div align="center" style="margin: 30px 0;">
  <a href="https://github.com/dantetesta/LLMS.txt-Generator-by-Dante-Testa/archive/refs/heads/main.zip" style="display:inline-block;">
    <img src="https://img.shields.io/badge/DOWNLOAD%20PLUGIN-Vers%C3%A3o%202.2.0-2ea44f?style=for-the-badge&logo=wordpress&logoColor=white" alt="DOWNLOAD PLUGIN" width="300">
  </a>
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

- **Integração com OpenAI**: Gere descrições técnicas automaticamente usando GPT-3.5-turbo e outros modelos
- **Integração com DeepSeek**: Alternativa gratuita à OpenAI via OpenRouter com o modelo DeepSeek Chat v3
- **Integração com Google Gemini**: Novo! Use o modelo Gemini Flash 2.0 para geração rápida e gratuita
- **Geração individual**: Crie descrições para posts específicos
- **Geração em massa**: Ferramenta para processar múltiplos posts simultaneamente
- **Customização manual**: Edite as descrições geradas conforme necessário
- **Suporte a CPTs**: Funciona com qualquer tipo de post personalizado
- **Validação de API Keys**: Verifique suas chaves de API antes de usar

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

### 2.2.0 (Janeiro 2026)
- 🤖 **Novo! Integração com Google Gemini API (Flash 2.0)**
- ✨ **Terceira opção de provedor de IA**: OpenAI, DeepSeek e agora Gemini
- 🎨 **Novo card de seleção do Gemini na interface de configurações**
- 🔑 **Campo dedicado para chave API do Google Gemini**
- ✅ **Validação de chave API do Gemini com feedback visual**
- 🔗 **Link direto para obter chave gratuita no Google AI Studio**
- ⚡ **Geração de descrições via Gemini na meta-box individual**
- 📦 **Suporte ao Gemini no bulk generator para processamento em massa**
- 🛡️ **Compatibilidade com PHP 8.0+ e WordPress 6.9**
- 🔧 **Correções de compatibilidade com propriedades dinâmicas (PHP 8.2+)**

### 2.1.0 (Janeiro 2026)
- 🔧 **Correções de compatibilidade com PHP 8.2+**
- 🛡️ **Declaração explícita de propriedades de classe**
- ⚡ **Melhorias de performance e estabilidade**

### 2.0.4 (Janeiro 2025)
- 🌐 **Tradução completa para inglês americano (en_US)**
- 🔧 **Correção do bulk generator para respeitar configuração de metafields em CPTs**
- 🔧 **Correção do botão individual nas admin columns para usar fonte configurada**
- 🔧 **Meta box agora funcional em todos os CPTs habilitados**
- 🔧 **Geração automática respeita configuração de campos personalizados**
- 🔧 **Arquivo llms.txt sem limite de posts (inclui todos os posts)**
- 🔧 **CPTs respeitam fonte de conteúdo configurada no setup**
- 🛠️ **Função auxiliar extract_post_content() centralizada**
- 📊 **Logs de debug adicionados para troubleshooting**
- 🎯 **Consistência entre todas as formas de geração**
- 🔒 **Verificações de segurança aprimoradas**
- 🌍 **Sistema i18n completamente implementado**
- 📚 **Classe LLMS_Txt_I18n gerenciando traduções**
- 🔄 **Carregamento automático de arquivos de tradução**
- 🌐 **Suporte a múltiplos idiomas expandido**

### 2.0.0 (Julho 2025)
- Integração com DeepSeek como alternativa à API OpenAI
- Nova interface com Tailwind CSS
- Geração em massa via Admin Columns
- Melhorias significativas de performance
- Suporte a todos os tipos de post personalizados
- Opção para excluir posts individuais do arquivo

### 1.0.0 (Julho 2025)
- Lançamento inicial do plugin
- Suporte básico ao arquivo llms.txt
- Integração com a API OpenAI
- Meta box para controle de conteúdo individual
- Interface administrativa básica

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
