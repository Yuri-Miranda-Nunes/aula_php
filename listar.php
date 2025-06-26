<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Define o conjunto de caracteres da página -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade -->
    <title>Document</title> <!-- Título da página -->
</head>
<body>
    <header>
        <h1>Lista de Pessoas Cadastradas</h1> <!-- Título principal da página -->
    </header>
    <section>
        <?php
        // Inclui o arquivo de conexão com o banco de dados
        require_once 'conexao.php';
        // Inclui a classe Pessoa (POO: definição da classe)
        require_once 'pessoa.php';

        // Cria uma instância do banco de dados (POO: instanciando objeto da classe BancoDeDados)
        $database = new BancoDeDados();
        // Obtém a conexão com o banco de dados (POO: usando método do objeto)
        $db = $database->obterConexao();

        // Verifica se a conexão foi bem-sucedida
        if ($db === null) {
            // Encerra o script e exibe mensagem de erro se não conectar
            die("<p class='error'>Erro: Não foi possível conectar ao banco de dados.</p>");
        }

        // Cria uma instância da classe Pessoa passando a conexão (POO: instanciando objeto da classe Pessoa)
        $pessoa = new Pessoa($db);
        // Executa o método para ler todos os registros (POO: usando método do objeto Pessoa)
        $stmt = $pessoa->ler();
        // Conta o número de linhas retornadas
        $num_linhas = $stmt->rowCount();

        // Se houver registros
        if ($num_linhas > 0) {
            // Percorre cada linha retornada
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Exibe os dados da pessoa em um bloco
                echo "<div class='person'>";
                echo "<p>ID: " . $linha['id'] . "</p>";
                echo "<p>Nome: " . $linha['nome'] . "</p>";
                echo "<p>Idade: " . $linha['idade'] . "</p>";
                echo "</div>";
            }
        }   else {
            // Se não houver registros, exibe mensagem
            echo "<p class='error'>Nenhum registro encontrado.</p>";
        }
        ?>
    </section>
</body>
</html>