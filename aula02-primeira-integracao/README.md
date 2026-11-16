# Aula 02: primeira integração com API

Primeiro contato com JSON e com uma API pública.

| Arquivo | O que é |
|---|---|
| `szpak.json` | Um objeto JSON escrito à mão com texto, número, booleano, objeto aninhado (`Endereco`) e lista (`diciplinas`). E-mail e endereço foram trocados por dados de exemplo. |
| `primeira-integracao.html` | Formulário de endereço que consulta o **ViaCEP** ao sair do campo CEP e preenche rua, bairro, cidade, UF e código IBGE. Usa JSONP (`callback=meu_callback`), o jeito clássico de chamar uma API de outro domínio só com `<script>`. |

Para testar, abra `primeira-integracao.html` direto no navegador e digite um CEP, por exemplo `80010-000`.
