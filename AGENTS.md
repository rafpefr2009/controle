# AGENTS.md — Regras para agentes e desenvolvedores

> Este arquivo vale para qualquer pessoa ou agente de IA que alterar o projeto.
> Leia `PRODUCT.md` (o que construir) e `PLANO.md` (em que ordem) antes de
> começar. Em caso de conflito, `PRODUCT.md` manda no escopo e este arquivo
> manda nas decisões técnicas.

---

## 1. Escopo atual

- O projeto é o MVP **AgendaSalão**, descrito em `PRODUCT.md`.
- Implemente **somente** o que está nas funcionalidades F1 a F5.
- Funcionalidades da lista "futuras" (CRUD de profissionais, link público,
  notificações, relatórios, perfis, recuperação de senha, multi-unidade, app)
  **não devem ser implementadas**.
- Siga as tarefas do `PLANO.md`, uma por vez. Não avance de fase sem executar a
  verificação da tarefa.
- Não invente requisitos. Se algo estiver ambíguo, registre a dúvida e escolha a
  opção mais simples que atenda ao `PRODUCT.md`.

---

## 2. Tecnologias permitidas

| Camada | Permitido |
|---|---|
| Backend | PHP 8 puro, sem framework |
| Banco | MySQL (InnoDB, utf8mb4) |
| Acesso a dados | PDO com prepared statements |
| Frontend | HTML5, CSS3, JavaScript puro (vanilla) |
| Autenticação | Sessão nativa do PHP + `password_hash`/`password_verify` |
| PDF | `window.print()` + CSS `@media print` |
| Ambiente | XAMPP ou Laragon no Windows |
| Versionamento | Git/GitHub |

### Proibido

- Frameworks PHP (Laravel, Symfony, CodeIgniter etc.) e microframeworks.
- Composer, npm, CDNs de bibliotecas ou qualquer dependência externa.
- ORMs, query builders, sistemas de templates (Blade, Twig etc.).
- `localStorage`/`sessionStorage` para persistir dados do sistema.
- Geração de PDF no servidor (Dompdf e similares).
- Multi-tenant, múltiplas unidades, API pública, notificações automáticas.
- JavaScript para validações críticas: o servidor é sempre a fonte da verdade.

---

## 3. Estrutura de arquivos

Use exatamente a estrutura do `PLANO.md` (seção 1). Regras:

- Telas e ações ficam na raiz (`agenda.php`, `servicos.php` etc.).
- Código compartilhado fica em `includes/`.
- CSS e JS ficam em `assets/`.
- SQL fica em `sql/`.
- **Não** criar `src/`, namespaces, classes de controller, autoload, rotas ou
  migrations.
- Um arquivo deve ter uma responsabilidade clara. Se passar de ~300 linhas,
  extraia funções para `includes/`.
- Use `require_once __DIR__ . '/includes/arquivo.php';` para incluir arquivos.
- Nunca use caminhos absolutos do tipo `C:\xampp\...` no código.

---

## 4. Convenções de código

### PHP

- Arquivos com extensão `.php` e codificação **UTF-8 sem BOM**.
- Um arquivo puro de PHP **não** deve ter `?>` no final.
- Indentação com **4 espaços** (sem tabs).
- Nomes em português, `snake_case`:
  - funções: `exigir_login()`, `buscar_conflito()`, `horarios_livres()`;
  - variáveis: `$profissional_id`, `$hora_inicio`;
  - constantes: `HORARIO_ABERTURA`, `INTERVALO_MINUTOS`.
- Não usar `declare(strict_types=1)` no MVP. Converta explicitamente os dados
  de formulário com `(int)`, `(float)` e valide antes de usar.
- Toda saída de dados do usuário/banco no HTML deve passar por `e()`
  (`htmlspecialchars`).
- Redirecione sempre com `redirecionar()` (que chama `exit`).
- Após um POST bem-sucedido, redirecione (padrão PRG: Post/Redirect/Get) para
  evitar reenvio ao atualizar a página.
- Não deixe `var_dump`, `print_r`, `die` de depuração no código commitado.

### Banco de dados

- Tabelas, colunas e status em português, `snake_case`.
- Tabelas: `usuarios`, `profissionais`, `servicos`, `agendamentos`.
- Charset `utf8mb4`, collation `utf8mb4_unicode_ci`, engine InnoDB.
- Chaves estrangeiras reais para `profissional_id` e `servico_id`.
- Valores monetários em `DECIMAL(10,2)`; nunca `FLOAT`.
- Datas em `DATE` (`Y-m-d`) e horários em `TIME` (`H:i`).
- Status permitidos: `agendado`, `realizado`, `cancelado`.
- Serviços e profissionais são **inativados** (`ativo = 0`), nunca apagados.
- Agendamentos **nunca** são apagados; o cancelamento muda o status.
- `sql/schema.sql` é destrutivo (recria tabelas) e serve apenas para o ambiente
  local. Nunca executá-lo em produção.
- Toda consulta que usa entrada do usuário deve usar prepared statements. Não
  concatene valores na string SQL; só fragmentos fixos podem ser concatenados.
- Não reutilize o mesmo placeholder nomeado (`:id`) duas vezes quando
  `ATTR_EMULATE_PREPARES` estiver desligado. Monte a cláusula condicionalmente
  ou use parâmetros distintos.

### Datas, horas e dinheiro

- Fuso horário único: `America/Sao_Paulo`.
- No banco e nos formulários: `Y-m-d` e `H:i`.
- Na tela: `d/m/Y` e `H:i`.
- Dinheiro na tela: `R$ 1.234,56` via `number_format`.
- Agendamentos devem começar e terminar no mesmo dia (expediente 09:00–19:00).

### HTML, CSS e JavaScript

- HTML semântico e labels associados aos campos (`<label for="...">`).
- Classes CSS em português e minúsculas: `.agenda`, `.bloco`, `.status-cancelado`.
- Elementos que não devem sair na impressão recebem a classe `no-print`.
- JS apenas como melhoria: preencher duração/valor e chamar `window.print()`.
- Sem `alert()` para regras de negócio; mensagens ficam na própria página.
- Acessibilidade mínima: foco visível, contraste razoável, botões com texto.

---

## 5. Segurança

- Senhas **somente** com `password_hash($senha, PASSWORD_DEFAULT)` e
  `password_verify`. Nunca grave nem compare senha em texto puro.
- No login, chame `session_regenerate_id(true)` após validar as credenciais.
- Configure o cookie de sessão com `httponly` e `samesite=Lax`.
- Páginas internas começam com `exigir_login()`.
- Envie `Cache-Control: no-store` nas páginas protegidas para o botão "voltar"
  não exibir conteúdo após o logout.
- Todo formulário que altera dados (POST) deve ter token CSRF; valide com
  `hash_equals`.
- Ações destrutivas (cancelar, inativar) só por POST, nunca por GET.
- Escape toda saída com `e()`; não confie em dados de formulário, URL ou banco.
- Não exiba mensagens internas de erro do PDO para o usuário. Em
  desenvolvimento, registre no log; na tela, mostre mensagem genérica.
- Não use `extract()`, `eval()`, `include`/`require` com caminho vindo do
  usuário.

---

## 6. Segredos e configuração

- Credenciais ficam **apenas** em `includes/config.php`.
- `includes/config.php` é ignorado pelo Git. O arquivo versionado é
  `includes/config.example.php`, com placeholders.
- O `.gitignore` deve conter, no mínimo:
  ```text
  includes/config.php
  .env
  *.log
  Thumbs.db
  .DS_Store
  ```
- Nunca commitar: senha real, hash de produção, dump do banco com dados reais,
  tokens, chaves ou URLs privadas.
- Antes de cada commit, rode `git status` e `git diff` e confirme que nenhum
  segredo aparece.
- Se um segredo for commitado por engano: remova do histórico (ex.:
  `git filter-repo` ou BFG), troque a credencial e avise o responsável. Apenas
  apagar o arquivo em um novo commit **não** resolve.
- O `seed.sql` pode conter um hash de senha padrão para desenvolvimento local.
  Documente no README que essa senha é provisória e não deve ser usada em
  produção.
- Não coloque dados pessoais reais de clientes no seed nem em exemplos.

---

## 7. Regra de conflito de horário (não alterar sem combinar)

Conforme o `PRODUCT.md`, dois atendimentos do **mesmo profissional** conflitam
quando os intervalos se sobrepõem **ou encostam**. Exemplo: 10:00–11:00
conflita com 11:00–12:00 e também com 09:00–10:00.

Regras obrigatórias:

- A checagem é **por profissional**, não por sala/equipamento.
- `status = 'cancelado'` **não** ocupa horário.
- Na edição, o próprio registro é ignorado.
- A comparação é inclusiva:
  `hora_inicio <= :fim AND ADDTIME(hora_inicio, SEC_TO_TIME(duracao_min * 60)) >= :inicio`.
- A checagem e a gravação devem ocorrer na **mesma transação** para evitar
  corrida entre as duas recepcionistas.
- Em conflito, mostre mensagem clara e a lista de horários livres do dia para o
  profissional.
- Se o salão decidir permitir atendimentos emendados (11:00 após 10:00), isso é
  uma **mudança de requisito**: atualize o `PRODUCT.md` antes de trocar a
  comparação para `<` e `>`.

---

## 8. Procedimento de testes

O projeto **não usa testes automatizados** no MVP. Todo teste é manual e deve
ser reproduzível.

### 8.1 Antes de testar

1. Inicie Apache e MySQL no XAMPP.
2. Confirme que o banco `agenda_salao` existe e foi importado.
3. Confirme que `includes/config.php` existe e aponta para o banco local.
4. Abra `http://localhost/controle/`.

### 8.2 Verificação de sintaxe (obrigatória após editar PHP)

No PowerShell, na raiz do projeto:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Todos os arquivos devem retornar `No syntax errors detected`.

### 8.3 Teste de fumaça por funcionalidade

Execute o roteiro da fase correspondente no `PLANO.md`. Resumo:

| Funcionalidade | O que conferir |
|---|---|
| F1 — Login | Sem sessão → redireciona; login certo/errado; logout; "voltar" bloqueado |
| F2 — Serviços | Criar, listar, editar, inativar; validações no servidor |
| F3 — Agendamentos | Criar, editar, cancelar, realizar; conflito e horários livres |
| F4 — Agenda | 3 colunas, posição/altura dos blocos, status, navegação por data |
| F5 — Histórico | Filtros combinados, histórico de realizados, impressão limpa |

### 8.4 Matriz de conflito (executar sempre que mexer nessa regra)

Com um agendamento de Ana em 10:00–11:00:

| Novo horário | Resultado esperado |
|---|---|
| 10:30–11:30 | Conflito (sobrepõe) |
| 11:00–12:00 | Conflito (encosta depois) |
| 09:00–10:00 | Conflito (encosta antes) |
| 09:00–09:30 | Livre |
| 10:00–11:00 (edição do mesmo) | Permitido (ignora o próprio) |
| 14:30–15:30, com 14:00–15:00 cancelado | Livre (cancelado não ocupa) |

### 8.5 Teste de persistência

1. Crie um agendamento.
2. Pare e inicie o MySQL (ou reinicie o XAMPP).
3. Recarregue a agenda.
4. O agendamento deve continuar lá.

### 8.6 Reset do banco para testes

Pelo phpMyAdmin: apague o banco `agenda_salao`, crie novamente e importe
`sql/schema.sql` e `sql/seed.sql`.

Ou pela linha de comando (ajuste o caminho do MySQL se necessário):

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE IF EXISTS agenda_salao; CREATE DATABASE agenda_salao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
C:\xampp\mysql\bin\mysql.exe -u root agenda_salao < sql/schema.sql
C:\xampp\mysql\bin\mysql.exe -u root agenda_salao < sql/seed.sql
```

> `schema.sql` recria as tabelas. Use apenas no ambiente local.

### 8.7 Testes de segurança manuais

- Acessar `agenda.php` em uma janela anônima → deve ir para o login.
- Fazer logout e clicar em "voltar" → não deve mostrar a agenda.
- Enviar um POST sem `csrf_token` (editar o HTML ou usar DevTools) → deve ser
  rejeitado.
- Tentar `servico_form.php?id=999999` → mensagem amigável, sem erro do PDO.
- Procurar no HTML por valores digitados em campos de senha → não devem
  reaparecer.

---

## 9. Fluxo de trabalho para agentes

1. Leia `PRODUCT.md`, `PLANO.md` e este `AGENTS.md`.
2. Escolha a primeira tarefa não marcada do `PLANO.md`.
3. Implemente **somente** o necessário para essa tarefa.
4. Rode a verificação descrita e o `php -l` nos arquivos alterados.
5. Atualize o checkbox no `PLANO.md`.
6. Faça um commit pequeno, em português, por exemplo:
   - `feat: login com sessão e logout`
   - `feat: cadastro e edição de serviços`
   - `fix: conflito ao editar o próprio agendamento`
   - `docs: atualiza README`
7. Não faça commit de código que não foi testado.
8. Não altere `PRODUCT.md` para "fazer o código passar". Se o requisito mudou,
   registre a mudança de forma explícita.

### Definition of Done de uma tarefa

- [ ] A verificação da tarefa no `PLANO.md` passou.
- [ ] `php -l` sem erros nos arquivos alterados.
- [ ] Nenhum segredo ou dado real no diff.
- [ ] Nenhum `var_dump`/`print_r`/`die` esquecido.
- [ ] Checkbox do `PLANO.md` marcado.
- [ ] Commit pequeno e descritivo.
