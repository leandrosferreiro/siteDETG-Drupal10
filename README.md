# Tema DETG para Drupal 10

Tema oficial do **Departamento de Engenharia de Transportes e Geodésia (DETG)** da Escola Politécnica da UFBA, construído para Drupal 10.

---

## Estrutura de Arquivos

```
detg/
├── detg.info.yml          # Metadados do tema (nome, regiões, bibliotecas)
├── detg.libraries.yml     # Definição de CSS e JS
├── detg.breakpoints.yml   # Breakpoints responsivos
├── config/
│   └── install/
│       └── detg.settings.yml
├── css/
│   └── style.css          # Todos os estilos do tema
├── js/
│   └── detg.js            # Menu mobile, busca, smooth scroll
└── templates/
    ├── html.html.twig      # Wrapper HTML completo
    ├── page.html.twig      # Layout da página (header, nav, grid, footer)
    ├── node.html.twig      # Cards de notícia (teaser) e página completa
    ├── menu.html.twig      # Renderização do menu de navegação
    ├── block.html.twig     # Blocos nas sidebars
    └── field.html.twig     # Campos de conteúdo
```

---

## Instalação

### 1. Copiar o tema

```bash
cp -r detg/ /var/www/html/web/themes/custom/detg/
```

### 2. Ativar pelo Drush

```bash
cd /var/www/html
drush theme:enable detg
drush config-set system.theme default detg
drush cr
```

### 3. Ativar pela interface

Acessar **Aparência** (`/admin/appearance`) → localizar **DETG** → clicar em **Definir como padrão**.

---

## Regiões Disponíveis

| Região           | Descrição                            |
|-----------------|--------------------------------------|
| `header`         | Topo — nome e slogan do departamento |
| `primary_menu`   | Menu de navegação principal          |
| `sidebar_first`  | Coluna esquerda (Sistemas, Secretaria) |
| `content`        | Conteúdo central (7 colunas)         |
| `sidebar_second` | Coluna direita (Calendário, Avisos)  |
| `news`           | Seção de notícias (fora do grid)     |
| `footer_col1`    | Rodapé — Sobre o DETG                |
| `footer_col2`    | Rodapé — Acesso Rápido               |
| `footer_col3`    | Rodapé — Contato                     |
| `footer_col4`    | Rodapé — Endereço                    |

---

## Configuração Recomendada de Blocos

### Menu Principal
- Bloco: **Navegação principal** → Região: `primary_menu`

### Sidebar Esquerda
- Bloco personalizado **Sistemas** → Região: `sidebar_first`
- Bloco personalizado **Secretaria** → Região: `sidebar_first`

### Conteúdo Central
- Bloco **Conteúdo da página** → Região: `content`

### Sidebar Direita
- Bloco do módulo **Calendar** (ou iFrame customizado) → Região: `sidebar_second`
- Bloco personalizado **Avisos Importantes** → Região: `sidebar_second`

### Seção de Notícias
- View **Últimas Notícias** (modo teaser) → Região: `news`

---

## Paleta de Cores

| Uso                     | Valor       |
|------------------------|-------------|
| Azul primário (nav)    | `#1e3a8a`   |
| Azul hover / links     | `#2563eb`   |
| Laranja terra (Geodésia)| `#9a3412`  |
| Texto principal        | `#1f2937`   |
| Fundo da página        | `#f8fafc`   |
| Rodapé                 | `#0f172a`   |

---

## Dependências

- **Drupal**: `^10`
- **PHP**: `^8.1`
- **Fonte**: Inter (carregada via Google Fonts)

---

## Tipos de Conteúdo Recomendados

Para o card de notícia (`node--view-mode-teaser`) funcionar corretamente, configure o tipo de conteúdo **Artigo / Notícia** com os campos:

- `field_image` — Imagem de capa
- `field_categoria` — Categoria (ex: Graduação, Pesquisa, Extensão, Eventos)

---

## Sistema de empréstimo de equipamentos

Módulo **DETG Lab Equipamentos** (`web/modules/custom/detg_lab_equip` no site Drupal):

- URL: `/reserva-equipamentos` (menu **Sistemas**, abre em nova aba)
- Layout próprio; catálogo, pedidos online e gestão pela equipe
- Docs: `detg-drupal10-site/web/modules/custom/detg_lab_equip/README.md`

---

## Licença

Este projeto é distribuído sob a **[GNU Affero General Public License v3.0](https://www.gnu.org/licenses/agpl-3.0.html)** (AGPL-3.0).

O texto completo da licença está no arquivo [`LICENSE`](LICENSE).

Em resumo: você pode usar, estudar, modificar e redistribuir o código, desde que as versões modificadas (inclusive quando oferecidas como serviço em rede) permaneçam sob a mesma licença e o código-fonte correspondente fique disponível.

---

## Créditos

Desenvolvido para o DETG/UFBA. Design baseado no protótipo HTML fornecido pelo departamento.  
Sistema de empréstimos inspirado no [VaiVem](https://github.com/willemarcel/vaivem).
