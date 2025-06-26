<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Define o conjunto de caracteres da página -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade -->
    <title>Document</title> <!-- Título da página -->
</head>
<body>
    <div class="container"> <!-- Container principal -->
        <header>
            <h1>Cadastro de Pessoas</h1> <!-- Título principal -->
        </header>
        <section>
            <?php
            // Inclui o arquivo de conexão com o banco de dados
            include_once 'conexao.php';
            // Inclui a classe Pessoa
            require_once 'pessoa.php';

            // Inicializa a variável de mensagem
            $mensagem = '';
            // Inicializa a variável de controle de sucesso do cadastro
            $cadastroSucesso = false;

            // Cria uma instância do banco de dados
            $database = new BancoDeDados();
            // Obtém a conexão com o banco de dados
            $db = $database->obterConexao();

            // Verifica se a conexão foi bem-sucedida
            if ($db === null) {
                $mensagem = "Erro: Não foi possivel conectar ao banco de dados."; 
            } else {
                // Verifica se o formulário foi enviado via POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Cria uma nova pessoa
                    $pessoa = new Pessoa($db);
                    // Atribui os valores do formulário
                    $pessoa->nome = $_POST['nome'];
                    $pessoa->idade = $_POST['idade'];

                    // Tenta cadastrar a pessoa
                    if ($pessoa->criar()) {
                        $mensagem = "Pessoa '{$pessoa->nome}' cadastrada com sucesso!";
                        $cadastroSucesso = true;
                    } else {
                        $mensagem = "Erro ao cadastrar a pessoa.";
                    }
                }
            }
            ?>

            <form action="" method="post" id="formCadastroPessoa"> <!-- Formulário de cadastro -->
                <div>
                    <label for="nome">Nome:</label> <!-- Rótulo do campo nome -->
                    <input type="text" id="nome" name="nome" required> <!-- Campo de texto para nome -->
                </div>
                <div>
                    <label for="idade">Idade:</label> <!-- Rótulo do campo idade -->
                    <input type="number" id="idade" name="idade" required> <!-- Campo numérico para idade -->
                </div>
                <button type="submit">Cadastrar</button> <!-- Botão de envio -->
            </form>
        </section>
    </div>
    <script>
        // Recebe a mensagem do PHP para o JavaScript
        const mensagemDoPHP = "<?php echo $mensagem; ?>";
        // Recebe o status de sucesso do cadastro do PHP para o JavaScript
        const cadastroFoiSucesso = <?php echo json_encode($cadastroSucesso); ?>;

        // Se houver mensagem, exibe um alerta
        if (mensagemDoPHP) {
            alert(mensagemDoPHP);

            // Se o cadastro foi bem-sucedido, limpa os campos do formulário e foca no campo nome
            if (cadastroFoiSucesso) {
                document.getElementById('nome').value = '';
                document.getElementById('idade').value = '';

                document.getElementById('nome').focus();
            }
        }
    </script>
</body>
</html>