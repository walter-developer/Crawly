# Teste Crawly

Projeto com finalidade "Teste de avaliação" CRAWLY SOLUCOES E TECNOLOGIA LTDA.

#############################################################################################################################

PASSOA PASSO PARA EXECUTAR A APLICAÇÃO:

1° - Instalar o docker e docker compose na maquina que executará o projeto.

2° - Adicionar um host para o projeto na maquina local editando:
2.1 Para Linux - Editar o aquivo ( /etc/hosts ) e adicinonar [ 127.0.0.1 teste-crawly.com.br ] no aquivo de hosts
2.2 Para Windows - Editar o aquivo ( C:\Windows\System32\drivers\etc\hosts ) e adicinonar [ 127.0.0.1 teste-crawly.com.br ] no aquivo de hosts

3° - Clonar o repositório do teste ( https://github.com/walter-developer/Crawly.git ) para uma pasta local (Exe.. C:/Projeto/Crawly/ )

4° - Via terminal, acessar o diretório que foi clonado o projeto ( Exe.. cd C:/Projeto/Crawly/ )

5° - Iniciar o servidor web executando ( docker-compose up -d ) no terminal.

6° - Aguarde de 5 a 10 segundos para que o composer complete a instalação do autoload e phpunit.

7° - Abra um navegador e acesse teste-crawly.com.br, a página exibirá o resultado como solicitado via email.

#############################################################################################################################

PASSOA PASSO PARA EXECUTAR OS TESTES:

1° - Via terminal, acessar o diretório que foi clonado o projeto ( Exe.. cd C:/Projeto/Crawly/ ).

2° - No termina digitar: docker container exec -it php-8 /bin/sh, para acessar o container do php

3° - No terminal do container, acessar o diretório da aplicação: cd /app/crawly/

4° - No terminal do container, acessar o diretório da aplicação, executar o comando php vendor/bin/phpunit.

#############################################################################################################################

ABISTRAÇÃO DA APLICAÇÃO:

1° -Toda lógica se encontra dentro da pasta app.

2° - Como o teste se resumia em apenas retirar informaççoes da web e não persistir em nenhum banco de dados, ultilizei o PHP VANILLA "PHP PURO",
até para melhor avaliação de código e entendimento do fluxo de desenvolvimento.

3° - Basicamento dentro de app tenho 3 classes:

Crawly.php => Classe responsável por executar o fluxo e fazer o parser do token principal para o secundário.
Html.php => Classe responsável de tratar html e retornar campos e tags que preciso.
Http.php => Classe responsável por fazer as requsições http ultilizando CURL do php.

Observação para tratamento de Html e Requsições http, geralmente uso bibliotecas como DOM - https://www.php.net/manual/pt_BR/book.dom.php e GUZZLEPHP - https://docs.guzzlephp.org/en/stable/#
Porém como citado acima, fiz como alternativa essas classes manualmente para uma melhor avaliação de como resolver sem usar nenhuma bliblioteca.

4° o repositório vendo que está na raiz do projeto está somente para uso do autoload e psr-4

#############################################################################################################################

ABISTRAÇÃO DA SOLUÇÃO:

Crawly.php => A classe Crawly.php é instanciada, no contrutor, já será instanciado as outras duas classes de helpers Html.php, Http.php.

Crawly.php => Método run é executado, e começa o fuxo da busca pela resposta.

Http.php => Primeiro é feito uma requisição http buscando a view acessada via link enviado no e-mail, e retornado o html para classe Crawly.php.

Crawly.php => A classe Crawly.php passo o html da view retornada na requisição para classe helper Html.php .

Html.php => A classe Html.php busca através de regex os campos contido no html da view, e retorna um array associativo contento chave e valor para classe Crawly.php.

Crawly.php => A classe Crawly.php pega o token que foi localizado na primeria requisição, e faz o parse do novo token para ser solicitado a resposta, e faz outra ruequisição http.

Http.php => A classe Http.php busca a resposta enviando o novo token e os cookies da requsição anterior, e passa a resposta novamente para a classe Crawly.php.

Crawly.php => A classe Crawly.php pega o html da resposta, passa para a classe Html.php, que retorna o numero de resposta novamente para a classe Crawly.php .

Crawly.php => Por fim a classe Crawly.php imprime o resultado na tela, como solicitado por e-mail.

vendor/bin/phpunit ./tests/CrawlyTest.php
