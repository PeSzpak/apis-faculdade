# APIs Faculdade

Exercícios da disciplina **Padrões de Projeto, Frameworks e APIs** (ADS, Senac São José dos Pinhais).

| Data | Atividade | Onde |
|---|---|---|
| Aula 02 | Primeiro JSON e primeira integração (ViaCEP) | [`aula02-primeira-integracao/`](aula02-primeira-integracao) |
| 06/10 | Mini-projeto: Catálogo de Produtos via API REST (DummyJSON + MySQL), com os 4 desafios extras e as respostas de reflexão | [`catalogo-api/`](catalogo-api) |
| Trabalho em equipe | Sistema Acadêmico: API REST de alunos em PHP + MySQL (GET, POST, PUT, DELETE, filtro por curso) | Repositório [sistema-academico-api](https://github.com/PeSzpak/sistema-academico-api) |
| 20/10 | Atividade 01: CEP com número, cidade e UF em destaque, cenários de teste e consulta de CNPJ | [`cadastro-cep/index.php`](cadastro-cep) |
| 20/10 | Atividade 02: formulário próprio de cadastro de clientes | [`cadastro-cep/clientes.php`](cadastro-cep) |
| Projeto | Grão Raro: loja de cafés com checkout real pelo Asaas, API documentada em Swagger | Repositório [grao-raro](https://github.com/PeSzpak/grao-raro) |
| Projeto | Proposta de Solução com APIs de mercado (as 5 questões) | [`proposta-solucao/PROPOSTA.md`](proposta-solucao/PROPOSTA.md) |

## Como rodar os projetos PHP

1. Copie a pasta do projeto para o `htdocs` do XAMPP.
2. Ligue Apache e MySQL no painel do XAMPP.
3. Se o projeto tiver um `.sql`, importe pelo phpMyAdmin.
4. Acesse `http://localhost/<pasta>/`.

As conexões usam o padrão do XAMPP (`localhost`, `root`, sem senha). Para outro MySQL, defina `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS`.
