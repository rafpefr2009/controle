# PLANO.md — AgendaSalão (MVP)

> Plano de execução do MVP descrito em `PRODUCT.md`.
> Leia também o `AGENTS.md` antes de escrever qualquer código.
>
> **Regra de ouro:** uma tarefa por vez. Só marque `[x]` depois de executar a
> verificação indicada. Se a verificação falhar, corrija antes de avançar.

---

## 1. Estrutura de arquivos proposta

Estrutura pensada para quem está começando: **sem roteador, sem MVC, sem
autoload, sem Composer**. Cada arquivo `.php` na raiz é uma tela ou uma ação
pequena. Os arquivos compartilhados ficam em `includes/`.

```text
controle/
├── index.php                  # só redireciona: logado → agenda.php; senão → login.php
├── login.php                  # tela de login + processamento do POST
├── logout.php                 # encerra a sessão e volta ao login
├── agenda.php                 # F4: agenda do dia em 3 colunas (protegida)
├── agendamento_form.php       # F3: criar/editar agendamento (protegida)
├── agendamento_acao.php       # F3: cancelar / marcar como realizado (POST)
├── servicos.php               # F2: listar e inativar serviços (protegida)
├── servico_form.php           # F2: criar/editar serviço (protegida)
├── historico.php              # F5: lista com filtros + exportação (protegida)
│
├── includes/
│   ├── config.example.php     # modelo de configuração (versionado)
│   ├── config.php             # configuração real (NÃO versionado)
│   ├── db.php                 # conexão PDO (função db())
│   ├── auth.php               # login/logout e exigir_login()
│   ├── funcoes.php            # e(), redirecionar(), formatar_data(), CSRF...
│   ├── header.php             # topo comum: HTML, menu, CSS
│   └── footer.php             # rodapé comum e fechamento do HTML
│
├── assets/
│   ├── css/estilo.css         # estilos da tela + regras de @media print
│   └── js/app.js              # JS mínimo (preencher duração/valor)
│
├── sql/
│   ├── schema.sql             # criação das tabelas (destrutivo, uso local)
│   └── seed.sql               # dados iniciais (profissionais, usuários, serviços)
│
├── .gitignore                 # ignora config.php, logs, etc.
└── README.md                  # como instalar e rodar
```

### Por que essa estrutura é simples

- **Uma tela = um arquivo.** Para achar o código da agenda, abra `agenda.php`.
- **Sem roteamento.** As URLs são os próprios nomes dos arquivos
  (`/controle/agenda.php`). Nada de `.htaccess` ou front controller.
- **`includes/`** guarda apenas o que é repetido (conexão, login, cabeçalho).
- **`assets/`** guarda CSS e JS puros, sem build.
- **`sql/`** permite recriar o banco do zero a qualquer momento.

### O que NÃO criar nesta etapa

- Pasta `src/`, namespaces, classes, autoload, MVC.
- `composer.json`, `package.json`, frameworks ou bibliotecas.
- Sistema de rotas, controllers, migrations, Docker.
- Testes automatizados com PHPUnit (o MVP usa roteiro manual de testes).

---

## 2. Como usar este plano

1. Siga as fases na ordem. As dependências são:
   `Fase 0 → 1 → 2 (F1) → 3 (F2) → 4 (F3) → 5 (F4) → 6 (F5) → 7`.
2. Antes de cada tarefa, leia os critérios de aceitação da funcionalidade no
   `PRODUCT.md`.
3. Execute a verificação escrita na tarefa.
4. Marque o checkbox e faça um commit pequeno (ex.: `feat: login com sessão`).
5. Ao terminar uma fase, rode o roteiro de testes daquela funcionalidade.

---

## 3. Fase 0 — Preparação do ambiente

- [ ] **T0.1 — Criar `.gitignore`**
  - Conteúdo mínimo: `includes/config.php`, `.env`, `*.log`, `Thumbs.db`,
    `.DS_Store`.
  - **Verificar:** `git status` não lista `includes/config.php`;
    `git check-ignore -v includes/config.php` mostra a regra aplicada.

- [ ] **T0.2 — Criar `README.md` inicial**
  - Explicar requisitos (XAMPP com PHP 8 e MySQL), como clonar, onde colocar a
    pasta e como importar os SQLs.
  - **Verificar:** outra pessoa consegue ler e saber o que instalar.

- [ ] **T0.3 — Criar `includes/config.example.php` e `includes/config.php`**
  - `config.example.php` (versionado) com placeholders:
    host `localhost`, banco `agenda_salao`, usuário `root`, senha vazia.
  - `config.php` (ignorado) com os valores locais reais.
  - **Verificar:** `git status` mostra o example, mas não o `config.php`.

- [ ] **T0.4 — Criar o banco de dados**
  - No phpMyAdmin ou MySQL: `agenda_salao`, charset `utf8mb4`,
    collation `utf8mb4_unicode_ci`.
  - **Verificar:** `SHOW DATABASES LIKE 'agenda_salao';` retorna 1 linha.

- [ ] **T0.5 — Fixar o fuso horário**
  - Definir `date_default_timezone_set('America/Sao_Paulo');` em um único
    lugar (sugestão: `includes/config.php` ou `includes/db.php`).
  - **Verificar:** `php -r "require 'includes/config.php'; echo date('Y-m-d H:i');"`
    mostra a hora local correta.

---

## 4. Fase 1 — Base técnica e banco

- [ ] **T1.1 — Escrever `sql/schema.sql`**
  - Tabelas (InnoDB, utf8mb4):
    - `usuarios`: `id`, `nome`, `usuario` (único), `senha_hash`, `ativo`,
      `criado_em`.
    - `profissionais`: `id`, `nome`, `ativo`.
    - `servicos`: `id`, `nome`, `duracao_min`, `valor_padrao`, `ativo`,
      `criado_em`.
    - `agendamentos`: `id`, `cliente`, `telefone`, `profissional_id`,
      `servico_id`, `data` (DATE), `hora_inicio` (TIME), `duracao_min` (INT),
      `valor` (DECIMAL 10,2), `observacoes` (TEXT), `status`
      (`agendado`/`realizado`/`cancelado`), `criado_em`, `atualizado_em`.
  - Chaves estrangeiras para profissional e serviço; índices em
    `(data, profissional_id)` e `(status)`.
  - O arquivo pode começar com `DROP TABLE IF EXISTS` (uso local).
  - **Verificar:** importar em banco vazio sem erro; `SHOW TABLES;` lista as
    4 tabelas; `DESCRIBE agendamentos;` mostra as colunas.

- [ ] **T1.2 — Escrever `sql/seed.sql`**
  - 3 profissionais ativos (ex.: Ana, Carla, Juliana).
  - 2 usuárias de recepção com senha **hasheada**. Gerar o hash com:
    `php -r "echo password_hash('senha123', PASSWORD_DEFAULT), PHP_EOL;"`
  - 3 serviços de exemplo (corte feminino 60 min, corte masculino 30 min,
    escova 45 min).
  - **Verificar:** `SELECT COUNT(*) FROM profissionais;` = 3;
    `SELECT COUNT(*) FROM usuarios;` = 2; `SELECT COUNT(*) FROM servicos;` = 3.

- [ ] **T1.3 — Importar schema e seed**
  - Importar `schema.sql` e depois `seed.sql` pelo phpMyAdmin (ou linha de
    comando).
  - **Verificar:** as consultas da T1.2 retornam os valores esperados e não há
    erro de chave estrangeira.

- [ ] **T1.4 — Criar `includes/db.php`**
  - Função `db(): PDO` que abre a conexão uma única vez (variável estática),
    lendo `config.php`.
  - Configurar `ERRMODE_EXCEPTION`, `FETCH_ASSOC`,
    `ATTR_EMULATE_PREPARES => false`.
  - **Verificar:**
    `php -r "require 'includes/db.php'; var_dump(db()->query('SELECT 1')->fetchColumn());"`
    imprime `string(1) "1"` (ou `int(1) 1`).

- [ ] **T1.5 — Criar `includes/funcoes.php` (parte 1)**
  - `e($texto)`: `htmlspecialchars` com `ENT_QUOTES` e UTF-8.
  - `redirecionar($url)`: `header('Location: ...')` + `exit`.
  - `formatar_data($data)`: `Y-m-d` → `d/m/Y`.
  - `formatar_moeda($valor)`: `number_format` com `R$`.
  - **Verificar:** `php -l includes/funcoes.php` sem erros;
    `e('<b>oi</b>')` retorna o texto escapado.

- [ ] **T1.6 — Criar `includes/funcoes.php` (parte 2 — CSRF)**
  - `csrf_token()`: gera e guarda o token na sessão.
  - `csrf_campo()`: devolve o `<input type="hidden">`.
  - `validar_csrf()`: compara com `hash_equals` e encerra com erro se inválido.
  - **Verificar:** chamar `csrf_token()` duas vezes no mesmo request devolve o
    mesmo valor; token errado é rejeitado.

- [ ] **T1.7 — Criar `includes/header.php` e `includes/footer.php`**
  - Header: `<!DOCTYPE html>`, `<meta charset="utf-8">`, link do CSS, menu com
    Agenda / Serviços / Histórico / Sair (menu com classe `no-print`).
  - Footer: fecha `main`, inclui `app.js`, fecha `body` e `html`.
  - **Verificar:** criar uma página temporária `teste_layout.php` que inclui os
    dois arquivos, abrir no navegador e depois **apagar** a página.

- [ ] **T1.8 — Criar `assets/css/estilo.css` (base) e `assets/js/app.js` vazio**
  - Reset simples, layout centralizado, menu, formulários, botões, tabelas e
    classes de status (`status-agendado`, `status-realizado`,
    `status-cancelado`).
  - Criar `assets/js/app.js` vazio (será preenchido na T4.4) para o `<script>`
    do footer não apontar para um arquivo inexistente.
  - **Verificar:** a página temporária da T1.7 fica legível, sem CSS quebrado,
    e o console do navegador não mostra erro 404 para o `app.js`.

---

## 5. Fase 2 — F1: Autenticação

- [ ] **T2.1 — Criar `includes/auth.php`**
  - `iniciar_sessao()`: `session_start()` com cookie `httponly`.
  - `usuario_logado()`: retorna o usuário da sessão ou `null`.
  - `exigir_login()`: se não houver usuário, redireciona para `login.php`;
    envia `Cache-Control: no-store` para o botão "voltar" não reabrir a página.
  - `entrar($usuario, $senha)`: busca por `usuario`, usa `password_verify`,
    chama `session_regenerate_id(true)` e grava `usuario_id`/`usuario_nome`.
  - `sair()`: limpa `$_SESSION`, expira o cookie e chama `session_destroy()`.
  - **Verificar:** `php -l includes/auth.php` sem erros.

- [ ] **T2.2 — Criar `login.php` (formulário)**
  - Campos `usuario` e `senha`, botão Entrar, token CSRF, link do CSS.
  - **Verificar:** a página abre em `/controle/login.php` sem erro PHP.

- [ ] **T2.3 — Processar o login (POST)**
  - Validar campos vazios; chamar `entrar()`; em sucesso redirecionar para
    `agenda.php`; em falha voltar ao formulário com mensagem genérica
    ("Usuário ou senha inválidos").
  - **Verificar:** login correto cria sessão e redireciona; login errado não
    cria sessão.

- [ ] **T2.4 — Exibir mensagem de erro**
  - Mostrar a mensagem acima do formulário, escapada com `e()`.
  - **Verificar:** senha errada mostra a mensagem; a senha digitada não
    reaparece no HTML.

- [ ] **T2.5 — Criar `logout.php`**
  - Chamar `sair()` e redirecionar para `login.php`.
  - **Verificar:** após sair, acessar `agenda.php` direto volta ao login.

- [ ] **T2.6 — Criar `index.php` e as páginas protegidas (placeholders)**
  - `index.php`: se houver sessão, redireciona para `agenda.php`; senão, para
    `login.php`.
  - `agenda.php`: incluir `auth.php`, chamar `exigir_login()`, exibir
    header/footer e o texto "Agenda em construção". A Fase 5 substitui o
    conteúdo.
  - Proteger também `servicos.php`, `servico_form.php`, `agendamento_form.php`,
    `agendamento_acao.php` e `historico.php` (podem ser criados vazios/protegidos
    nesta tarefa).
  - **Verificar:** sem login, qualquer uma dessas URLs redireciona para
    `login.php`; com login, abre a página; abrir `/controle/` sem sessão leva
    ao login.

- [ ] **T2.7 — Rodar o roteiro da F1**
  - CA1: acesso direto sem sessão → login.
  - CA2: credenciais corretas → agenda.
  - CA3: credenciais erradas → mensagem e sem sessão.
  - CA4: logout → login; botão "voltar" não mostra a agenda.
  - CA5: conferir no banco que `senha_hash` não é texto puro.
  - **Verificar:** todos os itens acima passam.

---

## 6. Fase 3 — F2: Cadastro de serviços

- [ ] **T3.1 — Listar serviços em `servicos.php`**
  - Consulta `SELECT` ordenada por nome; tabela com nome, duração, valor,
    status e ações (Editar / Inativar).
  - **Verificar:** os 3 serviços do seed aparecem.

- [ ] **T3.2 — Criar `servico_form.php` (novo serviço)**
  - Formulário com nome, duração (min) e valor padrão; token CSRF; botão
    Salvar e link Cancelar.
  - **Verificar:** a página abre e envia o POST para ela mesma.

- [ ] **T3.3 — Validar e gravar serviço**
  - No servidor: nome obrigatório; duração inteira > 0; valor numérico ≥ 0.
  - Em erro, mostrar mensagens e preservar os valores digitados.
  - Em sucesso, `INSERT` com prepared statement e redirecionar para
    `servicos.php`.
  - **Verificar:** serviço válido aparece na lista e no banco; nome vazio,
    duração 0 e valor negativo são rejeitados.

- [ ] **T3.4 — Editar serviço**
  - `servico_form.php?id=...` carrega o registro e, no POST, faz `UPDATE`.
  - **Verificar:** alterar nome/duração/valor reflete na lista e no banco;
    ID inexistente mostra mensagem amigável.

- [ ] **T3.5 — Inativar serviço**
  - Ação via POST com CSRF em `servicos.php`: `UPDATE servicos SET ativo = 0`.
  - **Verificar:** o serviço some da lista de ativos (ou aparece como inativo),
    continua no banco e não é apagado.

- [ ] **T3.6 — Rodar o roteiro da F2**
  - CA1: cadastrar com nome, duração e valor.
  - CA2: listar, editar e inativar.
  - CA3: validações no servidor (nome, duração > 0, valor ≥ 0).
  - CA4: fica para a Fase 4 (preenchimento automático no agendamento).
  - **Verificar:** todos os itens acima passam.

---

## 7. Fase 4 — F3: Agendamentos e conflito de horário

- [ ] **T4.1 — Criar `buscar_conflito()` em `includes/funcoes.php`**
  - Assinatura sugerida:
    `buscar_conflito(PDO $pdo, int $profissional_id, string $data, string $inicio, string $fim, ?int $ignorar_id = null): ?array`
  - Regra do `PRODUCT.md`: conflito quando os intervalos se sobrepõem **ou
    encostam**. Portanto a comparação é inclusiva:
    `hora_inicio <= :fim AND ADDTIME(hora_inicio, SEC_TO_TIME(duracao_min * 60)) >= :inicio`.
  - Ignorar `status = 'cancelado'` e, na edição, ignorar o próprio `id`
    (acrescentar `AND id <> :ignorar_id` só quando houver ID).
  - **Verificar:** testar com dados fixos no banco (ver matriz na T4.8).

- [ ] **T4.2 — Criar `horarios_livres()` em `includes/funcoes.php`**
  - Expediente fixo: 09:00 às 19:00, intervalos de 30 min (constantes).
  - Gerar candidatos e remover os que conflitam com agendamentos do
    profissional na data, considerando a duração escolhida.
  - **Verificar:** com um agendamento 10:00–11:00 e um serviço de 60 min, os
    horários 09:00, 09:30, 10:00, 10:30 e 11:00 **não** aparecem como livres
    (pois encostam ou sobrepõem); 11:30 aparece como livre.

- [ ] **T4.3 — Criar `agendamento_form.php` (novo)**
  - Campos: cliente, telefone, profissional (select de ativos), serviço (select
    de ativos), data, hora de início, duração, valor e observações.
  - Token CSRF e botão Salvar.
  - **Verificar:** o formulário abre com os profissionais e serviços do seed.

- [ ] **T4.4 — Preencher duração e valor ao escolher o serviço**
  - No `assets/js/app.js`, ler `data-duracao` e `data-valor` da opção
    selecionada e preencher os campos, que continuam editáveis.
  - **Verificar:** trocar o serviço atualiza os dois campos; editar depois
    mantém o valor digitado.

- [ ] **T4.5 — Salvar agendamento com checagem de conflito**
  - Validar cliente, profissional, serviço, data, hora, duração > 0, valor ≥ 0
    e início/fim dentro do expediente (09:00–19:00, no mesmo dia).
  - Calcular o fim em PHP; abrir transação; chamar `buscar_conflito()`; se
    houver conflito, mostrar mensagem clara **e** a lista de horários livres do
    dia para aquele profissional; senão, `INSERT` e commit.
  - **Verificar:** agendamento válido é gravado; conflito não é gravado e
    mostra os horários livres.

- [ ] **T4.6 — Editar agendamento**
  - `agendamento_form.php?id=...` carrega o registro; no POST faz `UPDATE`
    chamando `buscar_conflito(..., $ignorar_id = id)`.
  - **Verificar:** salvar sem mudar o horário funciona (não conflita consigo
    mesmo); mudar para um horário ocupado é bloqueado.

- [ ] **T4.7 — Criar `agendamento_acao.php` (cancelar/realizar)**
  - Aceitar somente POST com CSRF e parâmetro `acao` (`cancelar` ou
    `realizar`); atualizar o status e redirecionar para `agenda.php`.
  - **Verificar:** cancelar muda para `cancelado` e não apaga o registro;
    realizar muda para `realizado`.

- [ ] **T4.8 — Rodar o roteiro da F3**
  - Preparar: agendamento de Ana em 10:00–11:00.
  - Novo 10:30–11:30 → **conflito**.
  - Novo 11:00–12:00 → **conflito** (encosta).
  - Novo 09:00–10:00 → **conflito** (encosta).
  - Novo 09:00–09:30 → **livre**.
  - Editar o próprio 10:00–11:00 sem mudar → **permitido**.
  - Cancelar um agendamento das 14:00 e tentar 14:30 → **livre** (cancelado
    não ocupa).
  - **Verificar:** todos os casos acima, mais CA1, CA5, CA6, CA7, CA8 e CA9.

---

## 8. Fase 5 — F4: Agenda do dia

- [ ] **T5.1 — Consultar a agenda em `agenda.php`**
  - Ler `?data=YYYY-MM-DD` (padrão: hoje); buscar profissionais ativos e
    agendamentos da data, ordenados por hora.
  - **Verificar:** com dados no banco, a consulta retorna os registros do dia.

- [ ] **T5.2 — Montar as 3 colunas por profissional**
  - Uma coluna por profissional ativo, com o nome no topo.
  - **Verificar:** o seed gera exatamente 3 colunas.

- [ ] **T5.3 — Posicionar os blocos por horário e duração**
  - Coluna com altura fixa (ex.: 600 px para 09:00–19:00 = 1 px por minuto);
    bloco com `top` = minutos desde 09:00 e `height` = duração.
  - **Verificar:** um agendamento 10:00–11:00 começa na posição de 10:00 e tem
    60 px de altura.

- [ ] **T5.4 — Diferenciar visualmente os status**
  - Classes `status-agendado`, `status-realizado`, `status-cancelado`, com cor
    ou rótulo textual; cancelado com destaque (ex.: riscado/opaco).
  - **Verificar:** os três status são distinguíveis na tela.

- [ ] **T5.5 — Navegar por data**
  - Botões "Dia anterior", "Hoje" e "Próximo dia", além de `<input type="date">`
    que envia por GET.
  - **Verificar:** cada botão muda a data e recarrega a agenda correta.

- [ ] **T5.6 — Adicionar atalho "Novo agendamento"**
  - Botão com classe `no-print` apontando para `agendamento_form.php`.
  - **Verificar:** o botão abre o formulário de novo agendamento.

- [ ] **T5.7 — Rodar o roteiro da F4**
  - CA1: uma data, 3 colunas.
  - CA2: bloco posicionado por início/duração.
  - CA3: status visualmente distintos.
  - CA4: navegação por data funciona.
  - CA5: cancelados visíveis e diferenciados.
  - CA6: atalho de novo agendamento presente.
  - **Verificar:** todos os itens acima passam.

---

## 9. Fase 6 — F5: Histórico, filtros e PDF

- [ ] **T6.1 — Criar filtros em `historico.php`**
  - Campos: cliente (texto), profissional (select), data inicial, data final e
    status (select). Enviar por GET.
  - Montar o `WHERE` dinamicamente **apenas com fragmentos fixos** e valores
    via `bindValue`/`execute`.
  - **Verificar:** cada filtro isolado retorna o subconjunto esperado.

- [ ] **T6.2 — Exibir a lista filtrada**
  - Tabela com data, hora, cliente, telefone, profissional, serviço, valor e
    status; mensagem quando não houver resultados.
  - **Verificar:** os dados exibidos batem com o banco; combinação de filtros
    funciona.

- [ ] **T6.3 — Usar status `realizado` como histórico**
  - Com o filtro `realizado`, a lista mostra apenas atendimentos concluídos.
  - **Verificar:** agendados e cancelados não aparecem nesse filtro.

- [ ] **T6.4 — Criar regras de impressão em `estilo.css`**
  - `@media print`: esconder `.no-print`, remover sombras/cores de fundo
    desnecessárias, ajustar margens e fonte.
  - **Verificar:** na pré-visualização de impressão, menu e botões somem.

- [ ] **T6.5 — Adicionar botão "Exportar PDF"**
  - Botão `no-print` que chama `window.print()` em `agenda.php` e
    `historico.php`.
  - **Verificar:** a pré-visualização mostra apenas o conteúdo; a agenda mantém
    as colunas por profissional e os rótulos de status.

- [ ] **T6.6 — Rodar o roteiro da F5**
  - CA1: filtros por cliente, profissional, período e status.
  - CA2: `realizado` funciona como histórico.
  - CA3: PDF gerado por `window.print()` contém os dados exibidos.
  - CA4: menus, filtros e botões não aparecem na impressão.
  - CA5: colunas e status preservados no PDF.
  - **Verificar:** todos os itens acima passam.

---

## 10. Fase 7 — Fechamento

- [ ] **T7.1 — Revisão de segurança**
  - Todos os POST têm CSRF; todas as saídas usam `e()`; todas as consultas com
    dados de usuário usam prepared statements; nenhuma credencial versionada;
    páginas internas chamam `exigir_login()`.
  - **Verificar:** `git grep -n "config.php"` e `git status` sem segredos;
    testar um POST sem token (deve falhar).

- [ ] **T7.2 — Teste completo de ponta a ponta**
  - Recriar o banco do zero (`schema.sql` + `seed.sql`), refazer o roteiro das
    F1 a F5 em sequência e reiniciar o Apache/MySQL para confirmar a
    persistência.
  - **Verificar:** todos os critérios de aceitação do `PRODUCT.md` passam.

- [ ] **T7.3 — Atualizar o `README.md`**
  - Instalação, importação do banco, usuário inicial, como rodar e como
    redefinir os dados.
  - **Verificar:** seguir o README em uma pasta limpa funciona.

- [ ] **T7.4 — Commit final e tag**
  - Commit com a mensagem `chore: MVP concluído` e tag `v1.0.0`.
  - **Verificar:** `git log --oneline` e `git tag` mostram o commit e a tag.

---

## 11. Critério de pronto (resumo)

- [ ] F1 a F5 com todos os critérios de aceitação verificados.
- [ ] Dados persistem no MySQL após reiniciar o servidor.
- [ ] Nenhuma página interna acessível sem login.
- [ ] Conflito de horário bloqueado por profissional, incluindo intervalos que
      encostam; cancelados não ocupam horário.
- [ ] Agenda em 3 colunas com blocos posicionados por horário/duração.
- [ ] Filtros e exportação em PDF funcionando.
- [ ] Sem erros/warnings PHP nas telas; sem credenciais no Git.
- [ ] README permite outra pessoa instalar e testar.
