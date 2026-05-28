<p align="center">
  <img src="https://img.shields.io/badge/WordPress-Plugin-blue.svg" alt="WordPress Plugin">
  <img src="https://img.shields.io/badge/Versão-2.3.1-green.svg" alt="Versão">
  <img src="https://img.shields.io/badge/PHP-8.2+-purple.svg" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/WordPress-5.6+-21759b.svg" alt="WordPress 5.6+">
  <img src="https://img.shields.io/badge/Licença-GPL%20v2%2B-orange.svg" alt="Licença">
  <img src="https://img.shields.io/badge/i18n-pt__BR%20%7C%20en__US-yellow.svg" alt="i18n">
</p>

# LLMS.txt Generator

<p align="center">
  <b>Plugin WordPress para gerar, gerenciar e otimizar o arquivo <code>llms.txt</code> do seu site</b><br>
  Controle, com precisão, como ChatGPT, Claude, Gemini e demais sistemas de IA acessam, leem e representam o conteúdo do seu WordPress.
</p>

<p align="center">
  <a href="https://youtu.be/fsVKjmBlwDM">
    <img src="https://img.shields.io/badge/Assista%20o%20vídeo-YouTube-red.svg" alt="YouTube Video">
  </a>
</p>

<div align="center" style="margin: 30px 0;">
  <a href="https://github.com/dantetesta/LLMS.txt-Generator-by-Dante-Testa/archive/refs/heads/main.zip" style="display:inline-block;">
    <img src="https://img.shields.io/badge/DOWNLOAD%20PLUGIN-Vers%C3%A3o%202.3.1-2ea44f?style=for-the-badge&logo=wordpress&logoColor=white" alt="DOWNLOAD PLUGIN" width="300">
  </a>
</div>

<div align="center" style="margin: 20px 0; padding: 15px; background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); border-radius: 12px;">
  <h3>💬 Participe da Comunidade!</h3>
  <p><b>Entre no nosso grupo de WhatsApp para networking, dúvidas e novidades sobre WordPress e IA</b></p>
  <a href="https://chat.whatsapp.com/HPvy2fzidRM2jhNj7ii22u">
    <img src="https://img.shields.io/badge/ENTRAR%20NO%20GRUPO-WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white" alt="Grupo WhatsApp" width="280">
  </a>
</div>

---

## 🚨 Destaques da versão 2.3.1 (Release de segurança)

Esta versão é focada em **hardening de segurança** e atualização do modelo de IA padrão. Recomendada para todos os usuários.

| Mudança | O que muda na prática |
|---|---|
| 🤖 Modelo OpenAI atualizado | `gpt-3.5-turbo` (descontinuado) substituído por **`gpt-4o-mini`**. Mesma chave, mesmo endpoint, mais barato e mais rápido. |
| 🔐 Chaves de API criptografadas | Suas chaves OpenAI / DeepSeek / Gemini agora ficam cifradas em `wp_options` com AES-256-CBC. Quem atualiza não precisa fazer nada — as chaves antigas são re-criptografadas automaticamente no próximo salvamento. |
| 🙈 HTML não vaza chave salva | O campo de chave de API não imprime mais o valor armazenado no HTML. Placeholder indica se já existe chave, e deixar o campo vazio preserva o que está salvo. |
| 🗑️ Arquivo de backup removido | `templates/admin-page.php.bak` (47 KB) que vazava o código completo da interface admin foi removido do pacote. |
| 🚦 Bulk action sinaliza erro | Ações em massa com nonce inválido agora retornam erro explícito em vez de falhar silenciosamente. |
| 📂 Pré-checagem de escrita | `is_writable()` validado antes de gravar `llms.txt`, com log claro se o diretório não for gravável. |

Detalhes técnicos completos no [Changelog](#-changelog).

---

## 🔎 O que é o arquivo `llms.txt`?

O **`llms.txt`** é um padrão aberto e emergente, proposto para fazer pelo conteúdo dos sites o que o `robots.txt` faz pelos buscadores: dar aos sistemas de IA (ChatGPT, Claude, Gemini, Perplexity, etc.) instruções claras sobre **o que** está no seu site, **como** ele deve ser interpretado e **quais partes** podem ou não ser usadas para treinamento e geração de respostas.

Diferente do `robots.txt`, o `llms.txt` não é apenas uma lista de permissões — ele é um **mapa estruturado em Markdown** com:

- Título e descrição do site
- Listas categorizadas de páginas e posts mais importantes
- Descrições técnicas curtas para cada item
- Regras e contexto adicional para os modelos

Quando bem escrito, ele melhora dramaticamente como os LLMs entendem e citam o seu conteúdo.

### Por que isso importa?

- 🎯 **Citações mais precisas**: respostas geradas por IA tendem a referenciar seu site com a descrição que **você** escreveu, em vez de uma síntese aleatória.
- 🧭 **Controle de acesso**: você define quais posts/páginas/CPTs entram e quais ficam de fora.
- 🚀 **AEO/GEO ready**: o arquivo serve como base sólida para estratégias de *Answer Engine Optimization* e *Generative Engine Optimization*.
- ⏱️ **Atualização automática**: tudo que você publicar entra no arquivo sem intervenção manual.

---

## ✨ Funcionalidades

### 📄 Gerenciamento do arquivo `llms.txt`

- **Geração automática** servida em `https://seu-site.com/llms.txt`
- **Atualização incremental** sempre que um post for publicado, atualizado ou excluído
- **Pré-visualização** do arquivo direto no painel antes de publicar
- **Inclusão/exclusão por tipo**: posts, páginas, produtos WooCommerce, CPTs personalizados
- **Exclusão individual** por post via meta box
- **Conteúdo personalizado**: adicione blocos em Markdown ao topo/rodapé do arquivo
- **Cabeçalho UTF-8 com BOM** para compatibilidade máxima
- **Pré-checagem de escrita** (`is_writable()`) com log claro de falha

### 🤖 Descrições técnicas com IA

Três provedores integrados, todos plugáveis lado a lado:

| Provedor | Modelo padrão | Custo | Velocidade |
|---|---|---|---|
| **OpenAI** | `gpt-4o-mini` | $$ (pago) | ⚡ Muito rápido |
| **DeepSeek (via OpenRouter)** | `deepseek/deepseek-chat-v3-0324:free` | 🆓 Gratuito | ⚡ Rápido |
| **Google Gemini** | `gemini-2.0-flash` | 🆓 Gratuito | ⚡⚡ Ultra rápido |

- **Geração individual** via meta box no editor de cada post
- **Geração em massa** via *bulk action* no admin de posts
- **Validação prévia** das chaves API antes do uso
- **Edição manual** das descrições geradas a qualquer momento
- **Limite de 350 caracteres** otimizado para AEO
- **Prompt estruturado** focado em informação técnica, sem linguagem promocional

### 🎨 Interface administrativa

- Design moderno com **Tailwind CSS**
- Totalmente **responsivo** (desktop, tablet, mobile)
- **Toasts e indicadores de status** em tempo real
- **Admin Columns** com botões de geração direta na listagem
- **Meta box dedicada** abaixo do editor (Gutenberg e Clássico)
- **Contador de caracteres** em tempo real

### 🔧 Configuração granular para CPTs

Cada *Custom Post Type* pode ter sua própria fonte de conteúdo:

- `post_content` — corpo do post
- `post_excerpt` — resumo manual
- `custom_fields` — concatenação de meta fields específicos (ex.: `_descricao`, `_info`, `_atributos`)

Útil para WooCommerce, *plugins de portfólio*, *learning management systems* e qualquer estrutura que armazene dados em meta.

### 🌐 Internacionalização

- 🇧🇷 **Português Brasileiro** (pt_BR) — tradução nativa completa
- 🇺🇸 **English (en_US)** — tradução completa
- Arquitetura preparada para novos idiomas (`.po`, `.mo`, `.l10n.php`)
- Localização dos scripts JS para feedback em tempo real

### 🔐 Segurança (v2.3.1)

- **Criptografia AES-256-CBC** das chaves de API armazenadas em `wp_options`
- **Backward compatibility**: chaves antigas em texto plano continuam funcionando e são re-criptografadas no primeiro salvamento
- **Inputs `type="password"`** sem `value` renderizado para evitar vazamento via DOM/extensões
- **Nonces e capabilities** em todos os handlers AJAX e bulk actions
- **`sslverify => true`** em todas as chamadas HTTP externas
- **Escape contextual** (`esc_html`, `esc_attr`, `esc_url`) em todas as saídas
- **Sanitização** (`sanitize_text_field` + `wp_unslash`) em todas as entradas
- Sem `eval`, `shell_exec`, `unserialize` de dados externos, `include` dinâmico

---

## 📦 Requisitos

| Item | Mínimo | Recomendado |
|---|---|---|
| **WordPress** | 5.6 | 6.5+ |
| **PHP** | 8.2 | 8.3 ou 8.4 |
| **Extensões PHP** | `openssl`, `mbstring`, `json` | mesmas |
| **Permissão de escrita** | em `ABSPATH` (raiz do WP) | — |
| **Chave de API** (opcional) | OpenAI, OpenRouter ou Gemini | — |

> ⚠️ Sem `openssl`, a criptografia das chaves de API não funciona — o plugin ainda roda, mas perde a camada de hardening introduzida na 2.3.1.

---

## 💾 Instalação

### Método 1 — Upload via admin (recomendado)

1. Baixe o ZIP em [Releases](https://github.com/dantetesta/LLMS.txt-Generator-by-Dante-Testa/archive/refs/heads/main.zip)
2. No WP Admin, vá em **Plugins → Adicionar novo → Enviar plugin**
3. Selecione o ZIP e clique em **Instalar agora**
4. Clique em **Ativar plugin**

### Método 2 — FTP/SSH

```bash
cd wp-content/plugins/
unzip llms-txt-generator-2.3.1.zip
# A pasta deve se chamar exatamente "llms-txt-generator"
```

Em seguida, ative em **Plugins** no WP Admin.

### Método 3 — WP-CLI

```bash
wp plugin install https://github.com/dantetesta/LLMS.txt-Generator-by-Dante-Testa/archive/refs/heads/main.zip --activate
```

---

## ⚙️ Configuração rápida

### 1. Configurações gerais

Acesse **Configurações → LLMS.txt Generator** e:

- ✅ Marque **Habilitar arquivo llms.txt**
- ✏️ Preencha a **descrição do site** (1–3 frases sobre o seu projeto)
- ☑️ Selecione os **tipos de post** que devem ser incluídos
- 📝 (Opcional) adicione **conteúdo personalizado** em Markdown

### 2. Integração com IA

Na aba **Integração com IA**:

1. Escolha o provedor: **OpenAI**, **DeepSeek (via OpenRouter)** ou **Gemini**
2. Cole a chave de API no campo correspondente
3. Clique em **Validar chave** — a chave é testada no provedor antes de salvar
4. Salve as configurações

> 💡 **Onde obter cada chave:**
> - OpenAI: <https://platform.openai.com/api-keys>
> - OpenRouter (DeepSeek grátis): <https://openrouter.ai/keys>
> - Google Gemini: <https://aistudio.google.com/app/apikey>

### 3. Verifique o arquivo gerado

Acesse `https://seu-site.com/llms.txt` no navegador. O conteúdo deve aparecer estruturado, em Markdown, listando o conteúdo conforme suas configurações.

---

## 📝 Uso no dia a dia

### Controle de um post específico

1. Edite o post → role até a meta box **LLMS.txt — Descrição LLMS**
2. Para **excluir** o post do arquivo, marque a caixa correspondente
3. Para escrever **manualmente** a descrição, basta digitar (máx. 350 caracteres)
4. Para **gerar com IA**, clique no botão **Gerar automaticamente**
5. Atualize o post

### Geração em massa

1. Vá em **Posts → Todos os posts** (ou Páginas, ou seu CPT)
2. Selecione os posts desejados
3. No menu **Ações em massa**, escolha:
   - **Gerar descrições LLMS (apenas novos)** — pula posts que já têm descrição
   - **Gerar descrições LLMS (forçar todos)** — sobrescreve descrições existentes
4. Clique em **Aplicar**
5. O JavaScript gerencia a fila, mostrando progresso em tempo real

### Regenerar o arquivo manualmente

Em **Configurações → LLMS.txt Generator**, clique em **Regenerar arquivo**.

---

## 🪝 Hooks para desenvolvedores

O plugin expõe filtros que permitem customização sem editar o core.

### `llms_txt_generator_bulk_post_types`

Modifica os tipos de post elegíveis para *bulk action*.

```php
add_filter('llms_txt_generator_bulk_post_types', function ($post_types) {
    // Adicionar suporte a um CPT customizado
    $post_types[] = 'meu_cpt';
    return $post_types;
});
```

### `llms_txt_pre_generate_technical_description`

Permite injetar uma descrição customizada antes da chamada à IA. Se retornar valor não vazio, a IA não é chamada e os tokens são economizados.

```php
add_filter('llms_txt_pre_generate_technical_description', function ($description, $post) {
    if (get_post_meta($post->ID, 'minha_descricao_pronta', true)) {
        return get_post_meta($post->ID, 'minha_descricao_pronta', true);
    }
    return $description; // string vazia → segue para a IA
}, 10, 2);
```

### `llms_txt_generated_technical_description`

Pós-processamento da descrição gerada pela IA. Útil para padronizar formatação, adicionar prefixos, etc.

```php
add_filter('llms_txt_generated_technical_description', function ($description, $post) {
    return mb_strtoupper(mb_substr($description, 0, 1)) . mb_substr($description, 1);
}, 10, 2);
```

---

## 🧪 Troubleshooting

<details>
<summary><b>O arquivo /llms.txt retorna 404</b></summary>

- Verifique se **Habilitar arquivo llms.txt** está marcado
- Vá em **Configurações → Links permanentes** e clique em **Salvar** (sem alterar nada) para forçar reescrita das regras
- Confirme se o WordPress consegue escrever em `ABSPATH` (raiz do site). Em caso de falha, o log do PHP terá uma linha começando com `LLMS.txt Generator: destino não gravável em...`
</details>

<details>
<summary><b>Erro "Chave da API OpenAI não configurada" após atualizar para 2.3.1</b></summary>

A versão 2.3.1 lê chaves criptografadas. Se você estiver vendo esse erro **após** ter salvado a chave novamente em 2.3.1, é provável que a extensão `openssl` do PHP não esteja disponível.

Execute no servidor: `php -m | grep openssl`. Se nada aparecer, peça à sua hospedagem para habilitar a extensão.

Chaves salvas em versões anteriores continuam funcionando — o erro só aparece se você re-salvar sem `openssl` disponível.
</details>

<details>
<summary><b>A geração em massa para no meio</b></summary>

- Verifique a aba do navegador: o processamento é feito client-side, fechar a aba interrompe a fila
- Em servidores compartilhados, ajuste o intervalo entre chamadas se atingir rate limit da API
- Confirme o saldo da sua conta OpenAI / créditos OpenRouter
</details>

<details>
<summary><b>Como faço para resetar tudo?</b></summary>

```sql
DELETE FROM wp_options WHERE option_name LIKE 'llms_txt_%';
DELETE FROM wp_postmeta WHERE meta_key LIKE '_llms_txt_%';
```

Em seguida, desative e reative o plugin.
</details>

<details>
<summary><b>Posso usar com cache de página (WP Rocket, LiteSpeed, etc.)?</b></summary>

Sim. O arquivo `llms.txt` é estático, servido direto do disco. O cache de plugins não interfere.

Se o cache impedir a regeneração ao publicar, exclua a rota `/llms.txt` da lista de URLs cacheadas.
</details>

---

## 🛠️ Para contribuidores

```bash
git clone https://github.com/dantetesta/LLMS.txt-Generator-by-Dante-Testa.git
cd LLMS.txt-Generator-by-Dante-Testa

# Linter do WordPress (opcional, mas recomendado)
composer require --dev wp-coding-standards/wpcs dealerdirect/phpcodesniffer-composer-installer
vendor/bin/phpcs --standard=WordPress --extensions=php .
```

Pull requests bem-vindos. Por favor, mantenha:

- PHP 8.2+ compatível
- Sanitização e escape consistentes com o resto do código
- Strings traduzíveis com `__()` / `_e()` / `esc_html__()` no text domain `llms-txt-generator`

---

## 🔧 Suporte

- 🎥 **Tutorial em vídeo**: [YouTube](https://youtu.be/fsVKjmBlwDM)
- 🌐 **Site oficial**: [dantetesta.com.br](https://dantetesta.com.br)
- 📧 **E-mail**: <dante.testa@gmail.com>
- 💬 **WhatsApp (grupo)**: [Entrar](https://chat.whatsapp.com/HPvy2fzidRM2jhNj7ii22u)
- 🐛 **Issues**: [GitHub Issues](https://github.com/dantetesta/LLMS.txt-Generator-by-Dante-Testa/issues)

Ao abrir uma issue, por favor inclua:
- Versão do WordPress, PHP e do plugin
- Provedor de IA em uso (se aplicável)
- Passos para reproduzir
- Log de erros (`wp-content/debug.log` se `WP_DEBUG` ativo)

---

## 💳 Apoie o desenvolvimento

Se o plugin economizou seu tempo ou melhorou seu SEO/AEO, considere:

### 🇧🇷 PIX
**Chave:** `dante.testa@gmail.com`

### 🌎 PayPal
**Conta:** `dante.testa@gmail.com` — [doar diretamente](https://www.paypal.com/donate/?hosted_button_id=BAQGVU8MGWDTN)

---

## 📝 Changelog

### 2.3.1 (Maio 2026) — Release de segurança
- 🤖 **Modelo OpenAI atualizado**: `gpt-3.5-turbo` (descontinuado pela OpenAI) substituído por `gpt-4o-mini` — mesmo endpoint `/v1/chat/completions`, mais barato e mais rápido
- 🔐 **Criptografia AES-256-CBC das chaves de API** em `wp_options` via nova classe `LLMS_Txt_Crypto`
  - Backward compat: chaves legadas em texto plano continuam funcionando e são re-criptografadas no próximo salvamento
  - Chave derivada de `AUTH_KEY` + `SECURE_AUTH_SALT` (com fallback para `wp_salt()`)
- 🙈 **Inputs de chave de API não renderizam mais o valor** no atributo `value` do HTML
  - Placeholder indica se já existe chave salva
  - Campo vazio preserva a chave atual no salvamento (evita apagar acidentalmente)
- 🗑️ **Remoção do arquivo `templates/admin-page.php.bak`** (47 KB) que vazava o código completo da interface admin
- 📂 **`is_writable()` pre-check** antes de `file_put_contents()` na gravação do `llms.txt`, com `error_log` claro em caso de falha
- 🚦 **Bulk action com nonce inválido** agora sinaliza erro via querystring (`llms_txt_bulk_error=invalid_nonce`) em vez de falhar silenciosamente
- 🧹 `.gitignore` atualizado para excluir `*.bak` e `*.backup` de futuras distribuições

### 2.3.0 (Abril 2026)
- 🛡️ **Hardening de segurança completo**: sanitização de nonces com `sanitize_text_field(wp_unslash())` em todos os handlers AJAX
- 🛡️ **Verificação de capability** (`current_user_can`) no bulk action handler
- 🛡️ **Verificação de nonce WordPress** (`bulk-posts`) no processamento de ações em massa
- 🛡️ **Escape de mensagens de erro** de APIs externas com `esc_html()` contra XSS refletido
- 🛡️ **`sslverify => true` explícito** em todas as chamadas `wp_remote_post` (OpenAI, DeepSeek, Gemini)
- 🛡️ **Sanitização de `$_REQUEST`** com `wp_unslash()` e `absint()` no bulk action
- 🛡️ **`esc_attr()`/`esc_html()`** em outputs do template meta-box
- 🔧 **PHP 8.2+ obrigatório**: `Requires PHP` atualizado de 8.0 para 8.2
- 🔧 **Null safety**: cast `(string)` em `strip_tags`, `preg_replace`, `mb_strlen`, `mb_substr`
- 🔧 **`strip_tags()` substituído por `wp_strip_all_tags()`** nos handlers AJAX
- 🔧 **`substr()` substituído por `mb_substr()`** para suporte UTF-8 correto
- 🔧 **`date()` substituído por `wp_date()`** no rodapé do llms.txt
- 🔧 **Tipagem nullable** em `LLMS_Txt_I18n::$instance`
- 🔧 **`error_log()` condicionado a `WP_DEBUG`** em produção (removidos ~15 logs soltos)
- ✅ Validado com PHP 8.4.12, PHPCompatibility 8.2-8.4 (0 erros), PHPStan level 6

### 2.2.0 (Janeiro 2026)
- 🤖 **Novo! Integração com Google Gemini API (Flash 2.0)**
- ✨ Terceira opção de provedor de IA: OpenAI, DeepSeek e agora Gemini
- 🎨 Novo card de seleção do Gemini na interface de configurações
- 🔑 Campo dedicado para chave API do Google Gemini com validação visual
- 🔗 Link direto para obter chave gratuita no Google AI Studio
- ⚡ Geração de descrições via Gemini na meta box individual
- 📦 Suporte ao Gemini no bulk generator para processamento em massa
- 🛡️ Compatibilidade com PHP 8.0+ e WordPress 6.9
- 🔧 Correções de compatibilidade com propriedades dinâmicas (PHP 8.2+)

### 2.1.0 (Janeiro 2026)
- 🔧 Correções de compatibilidade com PHP 8.2+
- 🛡️ Declaração explícita de propriedades de classe
- ⚡ Melhorias de performance e estabilidade

### 2.0.4 (Janeiro 2025)
- 🌐 Tradução completa para inglês americano (en_US)
- 🔧 Bulk generator respeita configuração de metafields em CPTs
- 🔧 Botão individual nas admin columns usa fonte configurada
- 🔧 Meta box funcional em todos os CPTs habilitados
- 🔧 Geração automática respeita configuração de campos personalizados
- 🔧 Arquivo `llms.txt` sem limite de posts (inclui todos)
- 🛠️ Função auxiliar `extract_post_content()` centralizada
- 📊 Logs de debug para troubleshooting
- 🔒 Verificações de segurança aprimoradas
- 🌍 Sistema i18n completamente implementado

### 2.0.0 (Julho 2025)
- Integração com DeepSeek como alternativa à API OpenAI
- Nova interface com Tailwind CSS
- Geração em massa via Admin Columns
- Melhorias significativas de performance
- Suporte a todos os tipos de post personalizados
- Opção para excluir posts individuais do arquivo

### 1.0.0 (Julho 2025)
- Lançamento inicial
- Suporte básico ao arquivo `llms.txt`
- Integração com a API OpenAI
- Meta box para controle de conteúdo individual
- Interface administrativa básica

---

## 👨‍💻 Sobre o desenvolvedor

<p align="center">
  <img src="https://dantetesta.com.br/wp-content/uploads/2026/03/foto-dante-1.webp" alt="Dante Testa" width="160" style="border-radius: 50%; border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
</p>

**[Dante Testa](https://dantetesta.com.br)** é desenvolvedor web especializado em **WordPress, Inteligência Artificial aplicada e Vibe Coding**. Cria plugins, temas e ferramentas que unem boas práticas de engenharia com curiosidade técnica acelerada por IA.

Como educador, compartilha conhecimento através de cursos, mentorias e conteúdo gratuito em vídeo e texto, ajudando profissionais a dominarem o ecossistema WordPress moderno e a integrá-lo com modelos de linguagem de última geração.

Sua missão é **democratizar o acesso a ferramentas de qualidade** e conhecimento técnico, tornando a web mais acessível, mais segura e mais inteligente para todos.

### 🔗 Onde encontrar

- 🌐 **Site**: [dantetesta.com.br](https://dantetesta.com.br)
- 🎥 **YouTube**: [@dantetesta](https://www.youtube.com/@dantetesta)
- 💻 **GitHub**: [@dantetesta](https://github.com/dantetesta)
- 💬 **WhatsApp (grupo)**: [entrar na comunidade](https://chat.whatsapp.com/HPvy2fzidRM2jhNj7ii22u)

---

## 🔐 Licença

Este plugin é distribuído sob a [GPL v2 ou posterior](http://www.gnu.org/licenses/gpl-2.0.html), a mesma licença do próprio WordPress.

Você pode usar, modificar e redistribuir livremente — comercial ou pessoalmente — desde que preserve a licença e os créditos.

---

<p align="center">
  Feito com ❤️ no Brasil — Dante Testa Soluções Digitais
</p>
