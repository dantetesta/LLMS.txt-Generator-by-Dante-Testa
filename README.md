# LLMS.txt Generator

Plugin WordPress para gerar e gerenciar o arquivo llms.txt, controlando o acesso de Inteligências Artificiais ao conteúdo do seu site.

## Descrição

O LLMS.txt Generator é um plugin WordPress que permite criar e gerenciar facilmente um arquivo llms.txt para seu site. Similar ao robots.txt, o arquivo llms.txt é um padrão emergente que permite controlar quais partes do seu site podem ser acessadas por Inteligências Artificiais para treinamento ou geração de conteúdo.

### Funcionalidades

- Geração e gerenciamento automático do arquivo llms.txt
- Proteção de conteúdo individual via meta box em posts e páginas
- Geração automática de descrições técnicas via API OpenAI
- Interface administrativa responsiva e intuitiva
- Visualização e regeneração do arquivo llms.txt
- Validação da chave API OpenAI
- Design responsivo mobile-first com Tailwind CSS
- Totalmente traduzível

## Instalação

1. Faça o upload dos arquivos do plugin para o diretório `/wp-content/plugins/llms-txt-generator`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Acesse as configurações em 'Configurações > LLMS.txt Generator'

## Requisitos

- WordPress 5.0 ou superior
- PHP 7.0 ou superior

## Configuração

1. Após ativar o plugin, acesse 'Configurações > LLMS.txt Generator'
2. Habilite o arquivo llms.txt
3. Selecione os tipos de post que poderão ser protegidos
4. Adicione regras personalizadas, se necessário
5. Configure a integração com a API OpenAI (opcional)
6. Salve as configurações

## Uso

### Proteger conteúdo individual

1. Edite um post ou página
2. Na meta box 'LLMS.txt - Controle de IA', marque a opção 'Proteger este conteúdo contra acesso de IAs'
3. Adicione uma descrição técnica ou gere automaticamente via API OpenAI
4. Salve o post ou página

### Visualizar o arquivo llms.txt

- Acesse seu site em `https://seu-site.com/llms.txt`
- Ou clique no botão 'Ver arquivo llms.txt' na página de configurações do plugin

## Integração com OpenAI

Para utilizar a funcionalidade de geração automática de descrições técnicas:

1. Obtenha uma chave de API da OpenAI em [https://platform.openai.com/api-keys](https://platform.openai.com/api-keys)
2. Insira a chave no campo 'Chave da API OpenAI' nas configurações do plugin
3. Valide a chave clicando no botão 'Validar Chave da API'
4. Habilite a opção 'Gerar descrições técnicas automaticamente', se desejar

## Suporte

Para suporte, entre em contato através do site [https://dantetesta.com.br](https://dantetesta.com.br)

## Créditos

Desenvolvido por [Dante Testa](https://dantetesta.com.br)

## Licença

Este plugin é licenciado sob a GPL v2 ou posterior.
