# PRODUCT.md — AgendaSalão

> Documento de definição do MVP. Contém objetivo, escopo, funcionalidades essenciais (com histórias de usuário e critérios de aceitação), funcionalidades futuras, dependências e critério de pronto.

---

## 1. Visão geral

**Produto:** AgendaSalão — sistema web de organização de horários de atendimentos de corte para atendentes/recepcionistas de um salão de beleza.

**Problema atual:** o controle é feito por WhatsApp, o que gera falta de visão ampla dos horários, comunicação pouco prática com cada cliente e dificuldade de consultar detalhes do serviço e preço.

**Objetivo do MVP:** substituir o controle por WhatsApp por uma agenda única, visual e exportável, com login, bloqueio de conflito de horário por profissional e histórico de serviços realizados.

**Contexto de uso:** um único salão / uma única unidade. Uso restrito a 2 recepcionistas. Os clientes continuam solicitando horários por WhatsApp; a recepcionista consulta a agenda e lança o atendimento no sistema.

---

## 2. Stack e restrições técnicas

- **Backend:** PHP 8 puro (sem frameworks).
- **Banco de dados:** MySQL (persistência obrigatória; não usar localStorage).
- **Frontend:** HTML + CSS (+ JS mínimo).
- **Acesso a dados:** PDO com prepared statements.
- **Autenticação:** sessão PHP + `password_hash()` / `password_verify()`.
- **Exportação em PDF:** `window.print()` + CSS `@media print` (sem dependências).
- **Ambiente local:** XAMPP / Laragon.
- **Versionamento:** Git/GitHub.

**Restrições firmes:**
- Sem frameworks (PHP puro).
- Persistência em MySQL.
- Login obrigatório, mesmo sendo uso individual.
- Sem notificações automáticas na 1ª versão.
- Conflito de horário checado apenas por profissional (não por sala/equipamento).
- Um único salão / uma única unidade (sem multi-tenant).

---

## 3. Funcionalidades essenciais do MVP (5)

### F1 — Autenticação (login/logout)

**História de usuário**
> Como recepcionista, quero entrar no sistema com usuário e senha para que só pessoas autorizadas acessem a agenda do salão.

**Critérios de aceitação**
- CA1: Acessar qualquer página interna sem estar logado redireciona para a tela de login.
- CA2: Login com credenciais corretas cria sessão PHP e leva à agenda do dia.
- CA3: Login com credenciais erradas exibe mensagem de erro e não cria sessão.
- CA4: Logout encerra a sessão e volta ao login; "voltar" no navegador não reabre páginas internas.
- CA5: Senhas ficam armazenadas em MySQL com hash (`password_hash`), nunca em texto puro.

**Dependências:** nenhuma (base para todas as demais).

---

### F2 — Cadastro de serviços (com duração e valor padrão)

**História de usuário**
> Como recepcionista, quero cadastrar os serviços com duração e valor padrão para que, ao agendar, eu só escolha o serviço e o sistema preencha duração e valor.

**Critérios de aceitação**
- CA1: É possível cadastrar serviço com nome, duração (min) e valor padrão, gravando no MySQL.
- CA2: É possível listar, editar e inativar serviços.
- CA3: Nome é obrigatório; duração > 0; valor ≥ 0 (validação no servidor em PHP).
- CA4: No formulário de agendamento, ao selecionar um serviço, duração e valor aparecem preenchidos automaticamente (e permanecem editáveis).

**Dependências:** F1 (acesso restrito).

---

### F3 — Gestão de agendamentos com bloqueio de conflito por profissional

**História de usuário**
> Como recepcionista, quero criar, editar, cancelar e marcar como realizado um agendamento, para que a agenda reflita a realidade e nunca haja dois clientes no mesmo horário do mesmo profissional.

**Critérios de aceitação**
- CA1: É possível criar agendamento com cliente, telefone, profissional, serviço, data/hora de início, duração, valor e observações (gravado no MySQL).
- CA2: Ao salvar, o sistema **bloqueia** sobreposição de horário para o **mesmo profissional** (intervalos que se sobrepõem ou encostam contam como conflito), com verificação via consulta SQL antes do INSERT/UPDATE.
- CA3: Agendamentos com status `cancelado` **não** contam como ocupados na checagem.
- CA4: Ao editar, a checagem de conflito **ignora o próprio registro**.
- CA5: Ao detectar conflito, o sistema exibe mensagem clara e lista os **horários livres do dia** para aquele profissional.
- CA6: É possível editar um agendamento existente (com revalidação de conflito).
- CA7: É possível cancelar (status → `cancelado`, sem apagar o registro).
- CA8: É possível marcar como realizado (status → `realizado`).
- CA9: Duração e valor vêm preenchidos pelo serviço, mas podem ser ajustados manualmente.

**Dependências:** F1 (login), F2 (serviços), profissionais (seed inicial no MySQL — 3 registros).

---

### F4 — Agenda do dia (visão principal por profissional)

**História de usuário**
> Como recepcionista, quero ver a agenda do dia em colunas por profissional para ter uma visão ampla dos horários e resolver rapidamente os pedidos de agendamento por WhatsApp.

**Critérios de aceitação**
- CA1: A tela principal mostra a agenda de **uma data** em **3 colunas** (uma por profissional ativo).
- CA2: Cada agendamento aparece como bloco posicionado conforme horário de início e duração.
- CA3: Blocos são visualmente distintos por status (`agendado`, `realizado`, `cancelado`).
- CA4: É possível navegar por data (dia anterior / hoje / próximo) e escolher uma data específica.
- CA5: Cancelados aparecem visivelmente diferenciados.
- CA6: Há atalho "Novo agendamento" na tela.

**Dependências:** F1, F2, F3 (precisa de agendamentos para exibir).

---

### F5 — Lista/histórico com filtros e exportação em PDF

**História de usuário**
> Como recepcionista, quero filtrar agendamentos por cliente, profissional, período e status e exportar a agenda em PDF para consultar o histórico de serviços realizados e compartilhar/imprimir a agenda do dia.

**Critérios de aceitação**
- CA1: É possível filtrar por **cliente**, **profissional**, **período (data inicial/final)** e **status** (consultas SQL parametrizadas).
- CA2: O filtro de status `realizado` funciona como **histórico de serviços realizados**.
- CA3: A partir da agenda do dia e da lista filtrada, um botão **"Exportar PDF"** gera PDF via `window.print()` + CSS `@media print`, contendo exatamente os dados exibidos.
- CA4: No PDF, menus, filtros e botões **não aparecem**; apenas o conteúdo.
- CA5: O PDF preserva as colunas por profissional e a distinção por status (ao menos por rótulo textual).

**Dependências:** F1, F3 (dados), F4 (reaproveita a tela da agenda para exportação).

---

## 4. Funcionalidades futuras (fora do MVP)

| # | Funcionalidade | Motivo de adiar |
|---|---|---|
| Fut-1 | Tela de CRUD de profissionais | 3 profissionais fixos via seed no MySQL atendem ao MVP |
| Fut-2 | Agendamento pelo cliente (link público) | Muda o modelo de acesso e autenticação |
| Fut-3 | Notificações/lembretes automáticos (WhatsApp/SMS/email) | Depende de integração externa |
| Fut-4 | Relatórios financeiros e gráficos | Não resolve a dor principal |
| Fut-5 | Perfis/permissões (admin, profissional) | Uso restrito às 2 recepcionistas |
| Fut-6 | Recuperação de senha | Baixa prioridade no MVP |
| Fut-7 | Múltiplas unidades | Fora do escopo (um salão) |
| Fut-8 | App mobile | Site responsivo já atende |
| Fut-9 | Geração de PDF no servidor (Dompdf) | `window.print()` atende ao requisito |

---

## 5. Dependências entre funcionalidades
