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

## 3.1 Mudança de escopo registrada — acesso do cliente (17/11/2026)

> Registro explícito de mudança de requisito, autorizada pelo responsável pelo
> produto. O texto original das funcionalidades F1–F5 e da lista de futuras é
> mantido acima/abaixo para histórico; as regras abaixo **prevalecem** sobre a
> lista "funcionalidades futuras" apenas no que conflitar com elas.

**Requisito novo:** o cliente também entra no site (login ou cadastro), consulta
os horários disponíveis e faz seu agendamento manual na **mesma tela da agenda**
usada pelas recepcionistas.

**Regras da tela do cliente:**
- a agenda mantém as 3 colunas por profissional, a navegação por data e os
  blocos posicionados por horário/duração;
- os blocos de outros clientes aparecem apenas como **"Ocupado"**, sem nome,
  telefone ou observações; os atendimentos do próprio cliente aparecem completos;
- **histórico e exportação em PDF não aparecem** no menu nem na tela do cliente;
- **agendar sem cadastro/login não é permitido**: sem esse estado, a tela mostra
  aviso e o botão "Novo agendamento" fica oculto.

**Entrega desta rodada:** protótipo visual (conforme decisão do responsável).
Não há banco de dados, autenticação real nem persistência: o fluxo é simulado
pela navegação entre páginas com dados fictícios. A implementação funcional
continua dependente das fases F1–F7 do `PLANO.md`.

---

## 3.2 Mudança de horário registrada (17/11/2026)

> Outra mudança de requisito autorizada pelo responsável pelo produto. As regras
> abaixo **prevalecem** sobre qualquer menção anterior a "09:00–19:00" neste
> documento e nos demais arquivos do projeto.

**Novo expediente:** das **07:00 às 20:00**, com **2 horas de almoço das 12:00
às 14:00**. O último início permitido pela manhã é **11:30** (desde que o
atendimento termine até as 12:00) e a tarde recomeça às **14:00**.

**Regras decorrentes:**
- nenhum atendimento pode atravessar o almoço: a checagem de disponibilidade trata
  `12:00–14:00` como intervalo ocupado para todos os profissionais;
- agendamentos começam e terminam dentro de 07:00–12:00 ou 14:00–20:00, no
  mesmo dia;
- a regra de conflito (intervalos que se sobrepõem **ou encostam**) continua
  valendo normalmente em cada período.

**Entrega desta rodada:** protótipo visual — grade atualizada na abertura
(`index.php`), na agenda das atendentes (`agenda.php`), na agenda do cliente
(`agenda_cliente.php`) e nos formulários, com faixa de almoço destacada.

---

## 3.3 Navegação para datas futuras e catálogo ampliado (17/11/2026)

> Mudança de requisito autorizada pelo responsável pelo produto. As regras
> abaixo **prevalecem** sobre a limitação de três datas e de três serviços
> descrita nas seções anteriores.

**Requisito novo 1:** a agenda deve avançar para os **próximos dias**, inclusive
datas ainda sem atendimentos, para que o cliente possa agendar com antecedência.
As telas de agenda (recepcionista e cliente) passam a aceitar qualquer data
válida, com navegação dia anterior / próximo dia calculada a partir da data
atualmente exibida. O atalho "Novo agendamento" leva a data escolhida para o
formulário, que devolve o cliente à mesma data ao salvar.

**Requisito novo 2:** o catálogo de serviços é ampliado, mantendo os preços
coordenados com a prática de um salão de bairro: progressiva, selagem, botox
capilar, barba, barba + cabelo, barba + cabelo + sobrancelha e sobrancelha à
parte, além dos serviços já existentes. Combos têm valor menor que a soma dos
serviços avulsos. Serviços inativados continuam na lista, mas não aparecem
nos formulários de agendamento.

**Entrega desta rodada:** protótipo visual (sem banco, sessão ou persistência),
mantendo o mesmo padrão das mudanças §3.1 e §3.2.

---

## 4. Funcionalidades futuras (fora do MVP)

| # | Funcionalidade | Motivo de adiar |
|---|---|---|
| Fut-1 | Tela de CRUD de profissionais | 3 profissionais fixos via seed no MySQL atendem ao MVP |
| Fut-2 | Agendamento pelo cliente (link público) | **Parcialmente atendido no protótipo visual (§3.1)**; a autenticação real permanece futura |
| Fut-3 | Notificações/lembretes automáticos (WhatsApp/SMS/email) | Depende de integração externa |
| Fut-4 | Relatórios financeiros e gráficos | Não resolve a dor principal |
| Fut-5 | Perfis/permissões (admin, profissional) | **Protótipo visual com perfil cliente (§3.1)**; perfis reais no banco permanecem futuros |
| Fut-6 | Recuperação de senha | Baixa prioridade no MVP |
| Fut-7 | Múltiplas unidades | Fora do escopo (um salão) |
| Fut-8 | App mobile | Site responsivo já atende |
| Fut-9 | Geração de PDF no servidor (Dompdf) | `window.print()` atende ao requisito |

---

## 5. Dependências entre funcionalidades
