# NAVEGACAO.md — AgendaSalão (MVP)

> Documento de navegação e telas do MVP descrito no `PRODUCT.md`.
> Lista as telas necessárias, o fluxo principal de uso e, para cada tela,
> campos, botões, mensagens e estados (carregamento, lista vazia, sucesso e erro).
>
> **Público-alvo:** 2 recepcionistas, que recebem pedidos de horário por
> WhatsApp e registram no sistema.
>
> **Representação visual:** os mockups estáticos destas telas estão na pasta
> `prototipo/` (comece por `prototipo/index.html`). São descartáveis e não
> fazem parte do sistema. Os wireframes vetoriais (SVG), um por tela/estado,
> estão na pasta `wireframe/` (comece por `wireframe/index.html`).

---

## 1. Modelo de navegação

- Aplicação de **páginas múltiplas** (sem SPA, sem AJAX): cada ação navega o
  navegador para um arquivo `.php`.
- Padrão **PRG** (Post/Redirect/Get): formulários enviados por POST gravam e
  redirecionam, evitando reenvio ao atualizar a página.
- **Não há "spinner" nem estado de carregamento próprio**: o MVP usa PHP puro
  renderizado no servidor, então o carregamento é o indicador nativo do
  navegador (barra/aba girando). Isso é aceitável no MVP — as operações são
  locais e rápidas.
- Toda página interna exige login (`exigir_login()`). Sem sessão → redireciona
  para `login.php`.
- Menus e botões recebem a classe `no-print` para não saírem na exportação PDF.

---

## 2. Mapa de telas

| Arquivo | Função | Acesso |
|---|---|---|
| `index.php` | Roteador: redireciona para agenda ou login | Público |
| `login.php` | Autenticação (F1) | Público |
| `agenda.php` | **Tela principal** — agenda do dia em colunas (F4) | Protegida |
| `agendamento_form.php` | Criar/editar agendamento (F3) | Protegida |
| `agendamento_acao.php` | Ação POST: cancelar / marcar realizado (F3) | Protegida |
| `servicos.php` | Lista de serviços + inativar (F2) | Protegida |
| `servico_form.php` | Criar/editar serviço (F2) | Protegida |
| `historico.php` | Lista com filtros + exportação PDF (F5) | Protegida |
| `logout.php` | Encerra sessão (ação, sem tela própria) | Protegida |

---

## 3. Fluxo principal de navegação

O caminho mais comum da recepcionista (pedido chega pelo WhatsApp):

```text
login.php
   │  usuário + senha corretos
   ▼
agenda.php (dia atual) ──nova data──► agenda.php?data=YYYY-MM-DD
   │
   │  cliente pede horário
   ▼
agendamento_form.php (novo)
   │  salva ──conflito?──► mensagem + horários livres (permanece no form)
   │  │ ok
   │  ▼  redireciona
agenda.php (bloco novo aparece na coluna do profissional)
   │
   │  cliente compareceu / desmarcou
   ▼
agendamento_acao.php (POST: realizar ou cancelar) ──► agenda.php
   │
   │  fim do dia / consulta
   ▼
historico.php (filtros) ──Exportar PDF──► window.print()
```

Fluxos secundários:

- **Serviços:** `agenda.php` → menu "Serviços" → `servicos.php` →
  (`servico_form.php` novo/edição → volta à lista).
- **Logout:** menu "Sair" → `logout.php` → `login.php`.
- **Erro de login / sessão expirada:** qualquer página interna → `login.php`.

---

## 4. Telas detalhadas

### 4.1 `index.php` — Roteador (sem tela)

- Sem conteúdo: apenas redireciona com `redirecionar()`.
- Logado → `agenda.php`; não logado → `login.php`.

---

### 4.2 `login.php` — Login (F1)

Primeira tela do sistema. Título "AgendaSalão", formulário centralizado.

**Campos**

| Campo | Tipo | Obrigatório | Observação |
|---|---|---|---|
| Usuário | `text` | Sim | `autofocus`, `autocomplete="username"` |
| Senha | `password` | Sim | `autocomplete="current-password"` |

**Botões**

- **Entrar** (`type="submit"`) — envia o POST para a própria página.

**Estados**

- **Carregamento:** nativo do navegador ao enviar o POST.
- **Erro (credenciais inválidas ou campos vazios):** mensagem acima do
  formulário — "Usuário ou senha inválidos." — em caixa de erro vermelha.
  Os campos **não** são preenchidos com a senha digitada (o campo senha nunca
  reaparece; o usuário pode voltar preenchido).
- **Sucesso:** sessão criada (com `session_regenerate_id`), redireciona para
  `agenda.php`. Nenhuma mensagem nesta tela.
- **Acesso já logado:** abrir `login.php` logado redireciona para `agenda.php`.

---

### 4.3 `agenda.php` — Agenda do dia (F4, tela principal)

A **home** do sistema: agenda de uma data em colunas (uma por profissional
ativo; o seed traz 3).

**Campos / controles**

| Controle | Tipo | Observação |
|---|---|---|
| Data | `input type="date"` | envia por GET (`?data=YYYY-MM-DD`) |
| Dia anterior / Hoje / Próximo dia | botões (link GET) | navegam ±1 dia e voltam a hoje |

**Botões**

- **Novo agendamento** — link para `agendamento_form.php` (classe `no-print`).
- **Exportar PDF** — chama `window.print()` (classe `no-print`).
- **Menu:** Agenda / Serviços / Histórico / Sair (`no-print`).

**Conteúdo**

- Cabeçalho com a data por extenso (ex.: "Terça, 10/11/2026").
- Grade com **3 colunas** (Ana, Carla, Juliana), eixo 09:00–19:00
  (600 px ≈ 1 px/minuto). Cada agendamento é um bloco posicionado por
  `top` (minutos desde 09:00) e `height` (duração), exibindo hora, cliente,
  serviço e um rótulo de status.
- Clicar no bloco abre `agendamento_form.php?id=...` (edição).
- Status visualmente distintos: `status-agendado` (azul), `status-realizado`
  (verde), `status-cancelado` (cinza, riscado/opaco).

**Estados**

- **Carregamento:** nativo do navegador ao trocar de data.
- **Lista vazia (dia sem agendamentos):** colunas continuam visíveis (vazias)
  e aparece a mensagem "Nenhum agendamento neste dia." acima da grade, com o
  botão "Novo agendamento" em destaque.
- **Sucesso:** ao voltar de um cadastro/cancelamento/realização, o estado já
  reflete a alteração (feedback visual na própria agenda; mensagens de
  confirmação podem vir por parâmetro simples, ex.: `?msg=criado`).
- **Erro (data inválida na URL):** trata como "hoje" ou mensagem amigável;
  nunca erro de banco.

---

### 4.4 `agendamento_form.php` — Criar/editar agendamento (F3)

Mesma tela para novo (`sem id`) e edição (`?id=...`). Na edição, o título muda
para "Editar agendamento" e o registro vem preenchido.

**Campos**

| Campo | Tipo | Obrigatório | Observação |
|---|---|---|---|
| Cliente | `text` | Sim | nome digitado pela recepcionista |
| Telefone | `text` | Não | formato livre (ex.: WhatsApp) |
| Profissional | `select` | Sim | apenas profissionais **ativos** |
| Serviço | `select` | Sim | apenas serviços **ativos**; o JS preenche duração/valor ao trocar (`data-duracao`, `data-valor`), mantendo os campos editáveis |
| Data | `date` | Sim | `Y-m-d`; padrão hoje (novo) |
| Hora de início | `time` | Sim | `H:i`, passo de 30 min |
| Duração (min) | `number` | Sim | inteiro > 0 |
| Valor (R$) | `number` | Sim | ≥ 0, passo 0,01 |
| Observações | `textarea` | Não | |

**Botões**

- **Salvar** (`submit`) — valida no servidor e grava.
- **Cancelar** — link de volta para `agenda.php`.

**Estados**

- **Carregamento:** nativo do navegador ao salvar.
- **Erro de validação (campo vazio, duração ≤ 0, valor < 0, fora do
  expediente 09:00–19:00):** mensagens junto aos campos ou em caixa acima do
  formulário; **os valores digitados são preservados** (o form re-renderiza
  com os dados enviados, exceto campos internos de segurança).
- **Erro de conflito (CA2/CA5 de F3):** mensagem clara — "Conflito de horário:
  Ana já tem atendimento nesse horário." — seguida da **lista de horários
  livres do dia** para o profissional (grade 09:00–19:00 de 30 em 30 min,
  descontando ocupados e o tempo do serviço em edição). Os dados digitados são
  preservados para ajuste.
- **Sucesso:** grava (INSERT/UPDATE em transação) e redireciona para
  `agenda.php` (PRG).
- **Erro (id inexistente na edição):** mensagem amigável "Agendamento não
  encontrado." com link de volta — nunca mensagem do PDO.

---

### 4.5 `agendamento_acao.php` — Cancelar / realizar (F3, sem tela)

- Aceita **somente POST** com token CSRF e `acao` = `cancelar` ou `realizar`.
- Atualiza o `status` e redireciona para `agenda.php`.
- **Erro:** GET ou CSRF inválido → rejeitado (mensagem genérica/redirect);
  id inexistente → mensagem amigável.

---

### 4.6 `servicos.php` — Lista de serviços (F2)

**Conteúdo:** tabela com Nome, Duração, Valor, Status e Ações, ordenada por
nome. O formulário de inativar é POST inline com CSRF.

**Botões**

- **Novo serviço** → `servico_form.php`.
- **Editar** (por linha) → `servico_form.php?id=...`.
- **Inativar** (por linha, POST com confirmação) → remove dos ativos; o
  registro continua no banco.
- **Menu:** Agenda / Serviços / Histórico / Sair.

**Estados**

- **Lista vazia:** "Nenhum serviço cadastrado." + botão "Novo serviço".
- **Serviço inativado:** sai da lista de ativos (ou aparece marcado como
  inativo, sem botão de inativar de novo).
- **Sucesso:** volta à lista refletindo a alteração.
- **Erro:** mensagem genérica de sistema; nunca detalhe do PDO.

---

### 4.7 `servico_form.php` — Criar/editar serviço (F2)

**Campos**

| Campo | Tipo | Obrigatório | Validação (servidor) |
|---|---|---|---|
| Nome | `text` | Sim | não vazio |
| Duração (min) | `number` | Sim | inteiro > 0 |
| Valor padrão (R$) | `number` | Sim | ≥ 0 |

**Botões:** **Salvar** (submit) e **Cancelar** (link para `servicos.php`).

**Estados**

- **Erro de validação:** mensagens junto aos campos, valores preservados.
- **Sucesso:** INSERT/UPDATE e redireciona para `servicos.php` (PRG).
- **Erro (id inexistente):** "Serviço não encontrado." com link de volta.

---

### 4.8 `historico.php` — Histórico, filtros e PDF (F5)

**Campos de filtro (GET)**

| Campo | Tipo | Observação |
|---|---|---|
| Cliente | `text` | busca parcial (LIKE) |
| Profissional | `select` | "Todos" como padrão |
| Data inicial / Data final | `date` | período |
| Status | `select` | Todos / Agendado / Realizado / Cancelado — `realizado` = histórico de serviços |

**Botões**

- **Filtrar** (submit GET) — recarrega a página com `?` de consulta.
- **Limpar filtros** — volta à listagem sem filtros.
- **Exportar PDF** (`no-print`) — `window.print()` com exatamente o
  resultado filtrado exibido.
- **Menu:** Agenda / Serviços / Histórico / Sair.

**Conteúdo:** tabela com Data, Hora, Cliente, Telefone, Profissional,
Serviço, Valor e Status (rótulo textual preservado na impressão).

**Estados**

- **Carregamento:** nativo do navegador ao filtrar.
- **Lista vazia:** "Nenhum agendamento encontrado com esses filtros." +
  sugestão de limpar os filtros.
- **Sucesso:** a tabela reflete a combinação de filtros aplicada.
- **Erro (datas invertidas / inválidas):** mensagem amigável sugerindo
  corrigir o período; nunca erro de banco.

---

### 4.9 `logout.php` — Sair (F1, sem tela)

- Encerra a sessão e redireciona para `login.php`.
- Páginas protegidas enviam `Cache-Control: no-store`, então o botão "voltar"
  não reexibe conteúdo interno.

---

## 5. Mensagens padrão (padrão de texto)

| Situação | Texto |
|---|---|
| Login inválido | "Usuário ou senha inválidos." |
| Agenda vazia | "Nenhum agendamento neste dia." |
| Histórico vazio | "Nenhum agendamento encontrado com esses filtros." |
| Serviços vazio | "Nenhum serviço cadastrado." |
| Conflito | "Conflito de horário: {profissional} já tem atendimento nesse horário." + horários livres |
| Registro inexistente | "{Agendamento/Serviço} não encontrado." |
| Erro inesperado | "Erro inesperado. Tente novamente." (detalhe só no log) |

Mensagens ficam **na própria página** (caixa colorida acima do formulário),
sem `alert()`. Toda saída dinâmica passa por `e()`.

---

## 6. Estados transversais

| Estado | Comportamento |
|---|---|
| Não autenticado | qualquer página interna → `login.php` |
| Sessão encerrada + "voltar" | página não reexibida (`no-store`) |
| POST sem CSRF / CSRF inválido | rejeitado com `hash_equals` falhando |
| Erro de banco | mensagem genérica ao usuário; detalhe apenas no log local |
| Dados de formulário inválidos | re-render com valores preservados e mensagens por campo |
