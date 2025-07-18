<?php
require_once 'conexao.php'; // Inclui o arquivo de conexão
require_once 'classes/Produto.php'; // Inclui a classe Produto
require_once 'pessoa.php'; // Inclui a classe Pessoa

$mensagem = "";
$erro = "";
$abaAtiva = isset($_GET['aba']) ? $_GET['aba'] : 'cadastrar-produtos';

// Inicializar objetos usando a conexão do arquivo conexao.php
$banco = new BancoDeDados();
$conexao = $banco->obterConexao();

if ($conexao === null) {
    $erro = "Erro: Não foi possível conectar ao banco de dados.";
} else {
    $produto = new Produto($conexao);
    $db = $conexao;
}

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CADASTRAR PRODUTO
    if (isset($_POST['cadastrar_produto'])) {
        $produto->setNome($_POST['nome']);
        $produto->setPreco($_POST['preco']);
        $produto->setQuantidade($_POST['quantidade']);

        $erros = $produto->validar();

        if (empty($erros)) {
            if ($produto->inserir()) {
                $mensagem = "Produto cadastrado com sucesso!";
            } else {
                $erro = "Erro ao cadastrar produto!";
            }
        } else {
            $erro = implode(", ", $erros);
        }
    }

    // CADASTRAR PESSOA
    elseif (isset($_POST['cadastrar_pessoa'])) {
        if ($db !== null) {
            $pessoa = new Pessoa($db);
            $pessoa->nome = $_POST['nome'];
            $pessoa->idade = $_POST['idade'];

            if ($pessoa->criar()) {
                $mensagem = "Pessoa cadastrada com sucesso!";
            } else {
                $erro = "Erro ao cadastrar pessoa!";
            }
        }
    }

    // ATUALIZAR PRODUTO
    elseif (isset($_POST['atualizar_produto'])) {
        $produto->setId($_POST['id']);
        $produto->setNome($_POST['nome']);
        $produto->setPreco($_POST['preco']);
        $produto->setQuantidade($_POST['quantidade']);

        $erros = $produto->validar();

        if (empty($erros)) {
            if ($produto->atualizar()) {
                $mensagem = "Produto atualizado com sucesso!";
            } else {
                $erro = "Erro ao atualizar produto!";
            }
        } else {
            $erro = implode(", ", $erros);
        }
    }

    // DELETAR PRODUTO
    elseif (isset($_POST['deletar_produto'])) {
        if ($produto->deletar($_POST['id'])) {
            $mensagem = "Produto deletado com sucesso!";
        } else {
            $erro = "Erro ao deletar produto!";
        }
    }

    // ALTERAR PESSOA (nome e idade)
    elseif (isset($_POST['alterar_pessoa'])) {
        if ($db !== null) {
            $pessoa = new Pessoa($db);
            $pessoa->id = $_POST['id_pessoa'];
            $pessoa->nome = $_POST['novo_nome'];
            $pessoa->idade = $_POST['nova_idade'];

            // Atualiza nome e idade diretamente
            $query = "UPDATE pessoas SET nome = :nome, idade = :idade WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nome', $pessoa->nome);
            $stmt->bindParam(':idade', $pessoa->idade);
            $stmt->bindParam(':id', $pessoa->id);

            if ($stmt->execute()) {
                $mensagem = "Pessoa atualizada com sucesso!";
            } else {
                $erro = "Erro ao atualizar pessoa!";
            }
        }
    }
}

// Buscar produto para edição
$produtoEdicao = null;
if (isset($_GET['editar']) && $abaAtiva === 'listar-produtos') {
    $produtoTemp = new Produto($conexao);
    if ($produtoTemp->buscarPorId($_GET['editar'])) {
        $produtoEdicao = $produtoTemp;
    } else {
        $erro = "Erro: Produto não encontrado para edição.";
    }
}

// Contar totais
$totalProdutos = $produto->contarTotal();
$totalPessoas = 0;
if ($db !== null) {
    $pessoa = new Pessoa($db);
    $stmt = $pessoa->ler();
    $totalPessoas = $stmt->rowCount();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Unificado de Cadastros</title>
</head>
<body>
    <h1>Sistema Unificado de Cadastros</h1>
    
    <!-- Navegação por abas -->
    <div>
        <a href="?aba=cadastrar-produtos">Cadastrar Produtos</a> |
        <a href="?aba=cadastrar-pessoas">Cadastrar Pessoas</a> |
        <a href="?aba=listar-produtos">Listar Produtos</a> |
        <a href="?aba=listar-pessoas">Listar Pessoas</a>
    </div>
    
    <hr>
    
    <!-- Mensagens -->
    <?php if (!empty($mensagem)): ?>
        <p style="color: green;"><strong>Sucesso:</strong> <?php echo $mensagem; ?></p>
    <?php endif; ?>
    
    <?php if (!empty($erro)): ?>
        <p style="color: red;"><strong>Erro:</strong> <?php echo $erro; ?></p>
    <?php endif; ?>
    
    <!-- ABA CADASTRAR PRODUTOS -->
    <?php if ($abaAtiva === 'cadastrar-produtos'): ?>
        <h2>Cadastrar Produto</h2>
        
        <form method="POST">
            <table border="1">
                <tr>
                    <td>Nome do Produto:</td>
                    <td><input type="text" name="nome" required></td>
                </tr>
                <tr>
                    <td>Preço:</td>
                    <td><input type="number" name="preco" step="0.01" min="0.01" required></td>
                </tr>
                <tr>
                    <td>Quantidade:</td>
                    <td><input type="number" name="quantidade" min="0" required></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="cadastrar_produto" value="Cadastrar Produto">
                    </td>
                </tr>
            </table>
        </form>
        
    <!-- ABA CADASTRAR PESSOAS -->
    <?php elseif ($abaAtiva === 'cadastrar-pessoas'): ?>
        <h2>Cadastrar Pessoa</h2>
        
        <form method="POST">
            <table border="1">
                <tr>
                    <td>Nome Completo:</td>
                    <td><input type="text" name="nome" required></td>
                </tr>
                <tr>
                    <td>Idade:</td>
                    <td><input type="number" name="idade" min="0" max="150" required></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="cadastrar_pessoa" value="Cadastrar Pessoa">
                    </td>
                </tr>
            </table>
        </form>
        
    <!-- ABA LISTAR PRODUTOS -->
    <?php elseif ($abaAtiva === 'listar-produtos'): ?>
        <h2>Lista de Produtos</h2>
        
        <!-- Formulário de edição se houver produto selecionado -->
        <?php if ($produtoEdicao): ?>
            <h3>Editar Produto</h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $produtoEdicao->getId(); ?>">
                <table border="1">
                    <tr>
                        <td>Nome do Produto:</td>
                        <td><input type="text" name="nome" value="<?php echo $produtoEdicao->getNome(); ?>" required></td>
                    </tr>
                    <tr>
                        <td>Preço:</td>
                        <td><input type="number" name="preco" value="<?php echo $produtoEdicao->getPreco(); ?>" step="0.01" min="0.01" required></td>
                    </tr>
                    <tr>
                        <td>Quantidade:</td>
                        <td><input type="number" name="quantidade" value="<?php echo $produtoEdicao->getQuantidade(); ?>" min="0" required></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="atualizar_produto" value="Atualizar Produto">
                            <a href="?aba=listar-produtos">Cancelar</a>
                        </td>
                    </tr>
                </table>
            </form>
            <hr>
        <?php endif; ?>
        
        <!-- Lista de produtos -->
        <?php
        $produtos = $produto->listar();
        if ($produtos && count($produtos) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Preço</th><th>Quantidade</th><th>Ações</th></tr>";

            foreach ($produtos as $prod) {
                echo "<tr>";
                echo "<td>" . $prod['id'] . "</td>";
                echo "<td>" . $prod['nome'] . "</td>";
                echo "<td>R$ " . number_format($prod['preco'], 2, ',', '.') . "</td>";
                echo "<td>" . $prod['quantidade'] . "</td>";
                echo "<td>";
                echo "<a href='?aba=listar-produtos&editar=" . $prod['id'] . "'>Editar</a> | ";
                echo "<form method='POST' style='display: inline;'>";
                echo "<input type='hidden' name='id' value='" . $prod['id'] . "'>";
                echo "<input type='submit' name='deletar_produto' value='Deletar' onclick='return confirm(\"Tem certeza?\");'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Nenhum produto cadastrado.</p>";
        }
        ?>
        
    <!-- ABA LISTAR PESSOAS -->
    <?php elseif ($abaAtiva === 'listar-pessoas'): ?>
        <h2>Lista de Pessoas</h2>

        <?php
        // Verifica se está editando uma pessoa
        $pessoaEdicao = null;
        if (isset($_GET['editar_pessoa']) && $db !== null) {
            $pessoaObj = new Pessoa($db);
            $stmt = $pessoaObj->ler();
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($linha['id'] == $_GET['editar_pessoa']) {
                    $pessoaEdicao = $linha;
                    break;
                }
            }
        }
        ?>

        <?php if ($db !== null): ?>
            <?php
            $pessoa = new Pessoa($db);
            $stmt = $pessoa->ler();
            $num_linhas = $stmt->rowCount();

            if ($num_linhas > 0) {
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Nome</th><th>Idade</th><th>Ações</th></tr>";

                while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $linha['id'] . "</td>";
                    echo "<td>" . $linha['nome'] . "</td>";
                    echo "<td>" . $linha['idade'] . " anos</td>";
                    echo "<td>";
                    echo "<a href='?aba=listar-pessoas&editar_pessoa=" . $linha['id'] . "'>Editar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Nenhuma pessoa cadastrada.</p>";
            }
            ?>

            <?php if ($pessoaEdicao): ?>
                <hr>
                <h3>Editar Pessoa</h3>
                <form method="POST">
                    <input type="hidden" name="id_pessoa" value="<?php echo $pessoaEdicao['id']; ?>">
                    <table border="1">
                        <tr>
                            <td>Nome:</td>
                            <td><input type="text" name="novo_nome" value="<?php echo $pessoaEdicao['nome']; ?>" required></td>
                        </tr>
                        <tr>
                            <td>Idade:</td>
                            <td><input type="number" name="nova_idade" value="<?php echo $pessoaEdicao['idade']; ?>" min="0" required></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="alterar_pessoa" value="Salvar Alterações">
                                <a href="?aba=listar-pessoas">Cancelar</a>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p>Erro de conexão com o banco de dados.</p>
        <?php endif; ?>
        
    <?php endif; ?>
    
    <!-- Estatísticas -->
    <hr>
    <h3>Estatísticas do Sistema</h3>
    <ul>
        <li>Total de produtos: <?php echo $totalProdutos; ?></li>
        <li>Total de pessoas: <?php echo $totalPessoas; ?></li>
    </ul>
</body>
</html>