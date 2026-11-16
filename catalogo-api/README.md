# Mini-projeto (06/10): Catálogo de Produtos via API REST

O usuário informa o código de um produto, o PHP consulta a **DummyJSON Products API**, mostra o produto e permite salvá-lo no MySQL. Os produtos salvos aparecem em `produtos.php`.

```text
Usuário → index.php (PHP) → GET https://dummyjson.com/products/{id} → JSON → PHP → tela
       → [Salvar Produto] → salvar.php → MySQL → produtos.php
```

## Funcionalidades

| Requisito | Onde |
|---|---|
| 1. Consulta por código | `index.php` (campo, botão "Consultar Produto", área de resultado) |
| 2. Apresentação: ID, nome, descrição, preço, categoria, avaliação, estoque e imagem | `index.php` |
| 3. Erros: código vazio, produto inexistente e falha de comunicação com a API (além de código não numérico e JSON inválido) | `index.php` + validação no navegador em `js/script.js` |
| 4. Salvar produto no MySQL | `salvar.php`, com os dados validados de novo no servidor |
| 5. Listagem com ID, nome, preço, categoria, estoque e avaliação, e "Voltar para consulta" | `produtos.php` |
| Desafio 1: evitar duplicidade | `salvar.php` confere o `id_api` antes do `INSERT`, e a tabela tem `UNIQUE (id_api)` |
| Desafio 2: pesquisa local por nome | `produtos.php?busca=` com `LIKE` |
| Desafio 3: exclusão, com confirmação | `excluir.php` (somente POST) |
| Desafio 4: painel com total, valor médio e unidades em estoque | topo de `produtos.php` |

## Como rodar no XAMPP

1. Copie a pasta `catalogo-api` para `htdocs`.
2. Ligue Apache e MySQL no painel do XAMPP.
3. Importe `banco.sql` pelo phpMyAdmin. Ele cria o banco `catalogo_api` e a tabela `produtos`.
4. Acesse `http://localhost/catalogo-api/`.

A conexão usa o padrão do XAMPP (`localhost`, `root`, sem senha). Para outro MySQL, defina `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS`.

## Testes realizados (16/11/2026)

| Cenário | Resultado |
|---|---|
| Código vazio | "Informe o código do produto." |
| Código `abc` | "O código deve ser um número inteiro positivo." |
| Código `99999` | "Produto com código 99999 não encontrado." |
| API inacessível (proxy inexistente) | "Não foi possível realizar a consulta. Verifique sua conexão ou tente novamente mais tarde." |
| Consultar e salvar os produtos 1, 5 e 12 | Três registros gravados: Essence Mascara Lash Princess, Red Nail Polish, Annibale Colombo Sofa |
| Salvar o produto 1 de novo | "O produto ... já está cadastrado." |
| Painel | 3 produtos, valor médio R$ 839,66, 238 unidades em estoque |
| Pesquisa por "Mascara" | 1 resultado |
| Excluir o último produto | Removido do banco |

## Respostas para reflexão

**1. Qual é a diferença entre os dados recebidos pela API e os dados armazenados no banco?**
A API devolve o produto completo, com dezenas de campos (dimensões, avaliações, galeria de imagens, política de devolução...). O banco guarda só o que a aplicação usa: nome, descrição, preço, categoria, avaliação, estoque e uma imagem. Ele também acrescenta dados nossos: um `id` próprio, o `id_api` para relacionar com a origem e a `data_cadastro`. O registro do banco é uma foto do produto no momento em que foi salvo. Se o preço mudar na API, o banco continua com o valor antigo.

**2. Por que a API utiliza JSON?**
JSON é texto leve, legível por pessoas e entendido por praticamente qualquer linguagem (`json_decode` no PHP, `JSON.parse` no JavaScript). Ele representa bem objetos, listas e números, que é exatamente a forma de um produto, e virou o padrão das APIs REST.

**3. Qual é a função do PHP nesse projeto?**
O PHP faz a ponte entre o navegador, a API e o banco. Ele recebe o código digitado, faz a requisição HTTP à DummyJSON, interpreta o JSON, trata os erros, monta a página, valida os dados antes de gravar e conversa com o MySQL pelo PDO.

**4. O que aconteceria com o sistema se a API ficasse indisponível?**
A consulta de novos produtos para de funcionar, mas o sistema não quebra: a tela mostra a mensagem de falha de comunicação (testado acima). A listagem, a pesquisa, o painel e a exclusão continuam funcionando, porque usam só o banco local.

**5. Por que não devemos confiar que todos os dados recebidos de uma API estarão sempre corretos?**
A API é de outra pessoa: ela pode mudar o formato, deixar campos vazios, mandar um tipo inesperado ou ficar instável. Além disso, os dados passam pelo navegador antes de chegar ao `salvar.php`, e alguém pode alterá-los no caminho. Por isso o projeto confere se os campos esperados existem, converte os tipos, valida a URL da imagem, corta os textos no tamanho das colunas e usa prepared statements.

**6. Qual seria a vantagem de utilizar uma API em vez de cadastrar todos os produtos manualmente?**
Economiza tempo e evita erro de digitação, porque os dados já vêm prontos e padronizados da fonte. Também dá acesso a informações que seriam trabalhosas de levantar (avaliação, imagens, estoque) e permite atualizar o catálogo consultando de novo, em vez de redigitar.
