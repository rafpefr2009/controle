# DESIGN.md — AgendaSalão (MVP)

> Desenho visual e responsivo do MVP descrito no `PRODUCT.md`, coerente com as
> telas do `NAVEGACAO.md`. Restrições: CSS3 puro (sem framework, sem CDN),
> classes em português e minúsculas, JS mínimo, `@media print` para o PDF.
>
> Objetivo: **simples, legível e rápido** para 2 recepcionistas usando celular
> ou computador, o dia inteiro.

---

## 1. Princípios

1. **Legibilidade primeiro:** a agenda é a tela principal; hierarquia clara
   entre navegação, data e grade.
2. **Uma coluna de conteúdo por profissional, tempo = posição:** bloco que
   começa às 10:00 e dura 60 min fica no lugar certo, sem decorar regra.
3. **Feedback na página:** mensagens de sucesso/erro em caixas coloridas no
   topo do conteúdo; sem `alert()`.
4. **Mobile-first sem esconder função:** tudo que existe no desktop funciona
   no celular (a grade de horários rola horizontalmente).
5. **Impressão limpa:** o PDF contém só o conteúdo (`.no-print` some).

---

## 2. Estrutura de layout

Todas as páginas internas compartilham `includes/header.php` e
`includes/footer.php`:

```text
┌──────────────────────────────────────────────────┐
│ AgendaSalão     Agenda · Serviços · Histórico  Sair│  ← .topo (no-print)
├──────────────────────────────────────────────────┤
│  [caixa de mensagem: sucesso / erro / aviso]      │  ← .mensagem (se houver)
│  ── conteúdo da tela ──                          │
│                                                  │
└──────────────────────────────────────────────────┘
```

- `.container`: `max-width: 1100px; margin: 0 auto; padding: 0 16px;`.
- Título da tela (`h1`) + subtítulo com data/contexto (`h2` ou `p`).
- Rodapé simples com `no-print`.

---

## 3. Paleta e tipografia

### Cores (CSS custom properties em `:root`)

| Papel | Variável | Valor |
|---|---|---|
| Primária (botões, links, menu ativo) | `--cor-primaria` | `#5b21b6` (violeta) |
| Primária clara (fundo suave) | `--cor-primaria-suave` | `#ede9fe` |
| Texto | `--cor-texto` | `#1f2937` |
| Texto secundário | `--cor-texto-suave` | `#6b7280` |
| Fundo da página | `--cor-fundo` | `#f9fafb` |
| Fundo de cartões/tabelas | `--cor-superficie` | `#ffffff` |
| Borda | `--cor-borda` | `#e5e7eb` |
| Sucesso | `--cor-sucesso` | `#15803d` / fundo `#dcfce7` |
| Erro | `--cor-erro` | `#b91c1c` / fundo `#fee2e2` |
| Aviso | `--cor-aviso` | `#b45309` / fundo `#fef3c7` |

### Status dos agendamentos (F4/F5)

| Status | Classe | Fundo | Detalhe |
|---|---|---|---|
| Agendado | `.status-agendado` | `#dbeafe` | borda esquerda 3px `#1d4ed8` |
| Realizado | `.status-realizado` | `#dcfce7` | borda esquerda 3px `#15803d` |
| Cancelado | `.status-cancelado` | `#e5e7eb` | texto `#6b7280`, `text-decoration: line-through`, opacidade 0,7 |

Cada bloco também mostra o **rótulo textual** do status (ex.: "cancelado"),
garantindo distinção no PDF e para daltônicos.

### Tipografia

- Fonte: pilha de sistema (sem CDN): `system-ui, -apple-system, "Segoe UI",
  Roboto, Arial, sans-serif`.
- Base `16px`, linha 1,5; `h1` 1,5rem, `h2` 1,25rem, texto pequeno 0,875rem.
- Datas/horas em números tabulares quando possível
  (`font-variant-numeric: tabular-nums`).

### Escala de espaçamento

Múltiplos de 4px: `4, 8, 12, 16, 24, 32`. Raio de borda: `8px` (cartões),
`6px` (botões/campos).

---

## 4. Componentes

### 4.1 Menu (`.topo`, `.menu`)

- Barra superior fixa na largura total, fundo branco, borda inferior.
- Logo/marca "AgendaSalão" à esquerda; links Agenda / Serviços / Histórico /
  Sair à direita.
- Página atual: link com `aria-current="page"` e fundo
  `--cor-primaria-suave`.
- **No celular:** links continuam na barra (4 itens curtos cabem); se
  apertarem, o Sair vira ícone/texto "Sair" e os demais viram uma segunda
  linha do menu (`flex-wrap: wrap`).

### 4.2 Botões

| Classe | Uso | Estilo |
|---|---|---|
| `.btn` | base comum | padding `8px 16px`, raio 6px, borda 1px, cursor pointer |
| `.btn-primario` | Salvar, Entrar, Novo agendamento | fundo violeta, texto branco |
| `.btn-secundario` | Cancelar (link), Limpar filtros | fundo branco, borda cinza |
| `.btn-perigo` | Inativar, Cancelar agendamento | fundo vermelho, texto branco |
| `.btn-pequeno` | ações de tabela/linha | padding `4px 10px`, fonte 0,875rem |

Área de toque mínima de **44px** de altura no celular. Todo botão tem texto
visível (sem ícone só).

### 4.3 Formulários

- `.form-campo`: `label` **acima** do campo (funciona igual no desktop e no
  celular), margem inferior 16px.
- `.form-linha`: no desktop, campos curtos lado a lado (data + hora,
  duração + valor) via `display: flex; gap: 16px`; no celular, empilham.
- Inputs com `padding: 10px`, borda 1px, foco com contorno 2px violeta.
- Erros de campo: `.campo-erro` (borda vermelha) + `<p class="erro-campo">`
  logo abaixo do campo.
- Toda tela de formulário tem **Salvar** (primário) e **Cancelar** (link
  secundário) no rodapé do form.

### 4.4 Mensagens de feedback

| Classe | Uso |
|---|---|
| `.mensagem-sucesso` | gravação bem-sucedida (via redirect com `?msg=`) |
| `.mensagem-erro` | validação, conflito, credenciais inválidas |
| `.mensagem-aviso` | avisos (registro não encontrado, etc.) |

Caixa com fundo suave, borda esquerda 4px na cor, ícone textual opcional
("✕", "✓"), texto direto (ver textos no `NAVEGACAO.md` §5). Para o conflito,
a caixa inclui a lista `.lista-horarios` (chips clicáveis com os horários
livres, que apenas preenchem a hora do formulário).

### 4.5 Tabelas (serviços, histórico)

- `.tabela`: largura 100%, cabeçalho com fundo cinza claro, linhas zebradas
  suaves, bordas horizontais apenas.
- Coluna de ações à direita com `.btn-pequeno`.
- **No celular:** `.tabela-container { overflow-x: auto; }` — a tabela rola
  horizontalmente sem quebrar o layout (mantém os dados tabulares intactos).

### 4.6 Grade da agenda (F4) — o componente central

```text
Desktop (≥900px)
┌──────┬──────────┬──────────┬──────────┐
│ 09:00│ (Ana)    │ (Carla)  │ (Juliana)│   ← .eixo-horas + .coluna-profissional
│      │ ┌──────┐ │          │ ┌──────┐ │
│ 10:00│ │Maria │ │          │ │ Rosa │ │   ← .bloco (top/height em px)
│      │ │10:00 │ │          │ │ corte│ │
│ 11:00│ └──────┘ │          │ └──────┘ │
│      │          │ ┌──────┐ │          │
│ 12:00│          │ │Joana │ │          │
└──────┴──────────┴──────────┴──────────┘
   ↑ 600px de altura = 09:00–19:00 (1px por minuto)
```

- `.agenda`: `display: flex` com `.eixo-horas` (coluna fixa de ~56px com os
  rótulos 09:00…19:00) e uma `.coluna-profissional` por profissional ativo
  (`flex: 1; min-width: 180px`).
- Cada coluna tem o nome no topo (sticky no topo da coluna) e um
  `.trilha` de 600px (`position: relative`) com linha de fundo a cada 30 min
  (repeating-linear-gradient sutil).
- `.bloco`: `position: absolute; top: minutosDesde09h px; height: duracao px;`
  exibe hora, cliente, serviço e rótulo do status; clicável (vira link para a
  edição); `overflow: hidden` com ellipsis para textos longos.
- Linha "agora" (opcional): borda vermelha fina na posição do horário atual.
- Acima da grade: `.agenda-controles` (no-print) com data, botões
  anterior/hoje/próximo e atalhos **Novo agendamento** e **Exportar PDF**.

### 4.7 Estados vazios

`.vazio`: bloco centralizado com texto ("Nenhum agendamento neste dia."),
ícone textual opcional e o botão de ação principal da tela ao lado.

---

## 5. Responsividade

Dois pontos de quebra, mobile-first:

| Faixa | Comportamento |
|---|---|
| `< 900px` (celular) | menu com wrap; forms em coluna única; tabelas com rolagem horizontal; agenda com rolagem horizontal |
| `≥ 900px` (tablet/desktop) | forms com linhas duplas; agenda com as 3 colunas lado a lado ocupando a largura |

**Agenda no celular (decisão principal):** manter a metáfora de tempo =
posição. A `.agenda` recebe `overflow-x: auto` e o `.eixo-horas` usa
`position: sticky; left: 0;` com fundo opaco, então a recepcionista rola para
ver as colunas de cada profissional com as horas sempre visíveis à esquerda.
Não há troca para "lista de cards" — a sobreposição visual é a informação
mais valiosa da tela e é preservada em qualquer largura.

Todos os layouts usam unidades relativas onde faz sentido (`%`, `rem`,
`max-width`) e `meta viewport` (`width=device-width, initial-scale=1`).

---

## 6. Acessibilidade

- Foco visível: `outline: 2px solid var(--cor-primaria); outline-offset: 2px`
  (nunca `outline: none` sem substituto).
- Contraste mínimo 4,5:1 para texto (paleta da seção 3 já atende; testar
  rótulos sobre os fundos de status).
- Todos os campos com `<label for>` associado; blocos da agenda são `<a>`
  com texto descritivo (cliente + hora + serviço).
- Botões sempre com texto; alvo de toque ≥ 44px no celular.
- Status também por rótulo textual (não apenas cor).

---

## 7. Impressão / "Exportar PDF" (F5)

Regras em `@media print` no `estilo.css`:

1. `.no-print` (menu, controles de data, filtros, botões) → `display: none`.
2. Fundo branco, texto preto, remoção de sombras e bordas decorativas.
3. Título com data e, no histórico, uma linha resumo dos filtros aplicados
   (`.print-resumo`).
4. Blocos da agenda: mantêm posição/altura e colunas por profissional;
   status diferenciado por rótulo textual + borda (impressoras P&B ok).
5. `.bloco` e linhas de tabela recebem `break-inside: avoid` para não
   cortarem no meio.
6. Margem de página: `@page { margin: 12mm; }`, fonte base 12pt.

---

## 8. Convenções e inventário de classes

- Classes em **português, minúsculas**, com hífen; sem utilitários tipo
  `mt-2`. Um arquivo só: `assets/css/estilo.css`.
- IDs em camelCase/português apenas para `label for` e JS
  (`campoServico`, `campoDuracao`).

| Classe | Onde |
|---|---|
| `.topo`, `.menu`, `.menu-link`, `.ativo` | header |
| `.container`, `.conteudo` | layout geral |
| `.mensagem-sucesso`, `.mensagem-erro`, `.mensagem-aviso`, `.erro-campo`, `.campo-erro` | feedback |
| `.btn`, `.btn-primario`, `.btn-secundario`, `.btn-perigo`, `.btn-pequeno`, `.no-print` | botões |
| `.form-campo`, `.form-linha`, `.form-rodape` | formulários |
| `.tabela`, `.tabela-container`, `.col-acoes` | listas |
| `.agenda`, `.agenda-controles`, `.eixo-horas`, `.coluna-profissional`, `.trilha`, `.bloco`, `.linha-agora` | agenda |
| `.status-agendado`, `.status-realizado`, `.status-cancelado` | status |
| `.lista-horarios`, `.horario-livre` | horários livres no conflito |
| `.vazio` | estados vazios |
| `.print-resumo` | resumo de filtros no PDF |
