<?php
// Incluir as classes necessárias
require_once 'classes/Produto.php';
require_once 'conexao.php';
require_once 'pessoa.php';

// Variáveis para mensagens e dados
$mensagem = "";
$erro = "";

// Determinar a aba ativa
$abaAtiva = isset($_GET['aba']) ? $_GET['aba'] : 'produtos';

// Instanciar classes
$produto = new Produto();

// Conectar ao banco para pessoas
$database = new BancoDeDados();
$db = $database->obterConexao();

if ($db === null) {
    $erro = "Erro: Não foi possível conectar ao banco de dados.";
}

// Processar formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        
        // OPERAÇÕES COM PRODUTOS
        if ($_POST['acao'] === 'inserir_produto') {
            $produto->setNome($_POST['nome']);
            $produto->setPreco($_POST['preco']);
            $produto->setQuantidade($_POST['quantidade']);
            
            $erros = $produto->validar();
            
            if (empty($erros)) {
                if ($produto->inserir()) {
                    $mensagem = "Produto inserido com sucesso!";
                } else {
                    $erro = "Erro ao inserir produto!";
                }
            } else {
                $erro = implode(", ", $erros);
            }
        }
        
        elseif ($_POST['acao'] === 'atualizar_produto') {
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
        
        elseif ($_POST['acao'] === 'deletar_produto') {
            if ($produto->deletar($_POST['id'])) {
                $mensagem = "Produto deletado com sucesso!";
            } else {
                $erro = "Erro ao deletar produto!";
            }
        }
    }
    
    // OPERAÇÕES COM PESSOAS
    elseif (isset($_POST['alterar_id']) && isset($_POST['nova_idade'])) {
        $pessoa = new Pessoa($db);
        $pessoa->id = $_POST['alterar_id'];
        $novaIdade = $_POST['nova_idade'];
        
        if ($pessoa->alterarIdade($novaIdade)) {
            $mensagem = "Idade alterada com sucesso para o ID {$_POST['alterar_id']}!";
        } else {
            $erro = "Erro ao alterar a idade.";
        }
    }
}

// Buscar produto específico para edição
$produtoEdicao = null;
if (isset($_GET['editar']) && $abaAtiva === 'produtos') {
    $produtoTemp = new Produto();
    if ($produtoTemp->buscarPorId($_GET['editar'])) {
        $produtoEdicao = $produtoTemp;
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
    <title>Sistema Unificado - Cadastros</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 24px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        
        .tab.active {
            background-color: white;
            border-bottom: 2px solid white;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .mensagem {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        input[type="submit"], button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        
        input[type="submit"]:hover, button:hover {
            background-color: #0056b3;
        }
        
        .person {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .alterar-form {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .alterar-form input[type="number"] {
            width: 120px;
        }
        
        .stats {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .stats h3 {
            margin-top: 0;
            color: #495057;
        }
        
        .stats ul {
            margin: 10px 0;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .nav-buttons a {
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .nav-buttons a:hover {
            background-color: #5a6268;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema Unificado de Cadastros</h1>
        
        <!-- Abas -->
        <div class="tabs">
            <a href="?aba=produtos" class="tab <?php echo $abaAtiva === 'produtos' ? 'active' : ''; ?>">
                Produtos (<?php echo $totalProdutos; ?>)
            </a>
            <a href="?aba=pessoas" class="tab <?php echo $abaAtiva === 'pessoas' ? 'active' : ''; ?>">
                Pessoas (<?php echo $totalPessoas; ?>)
            </a>
        </div>
        
        <!-- Mensagens -->
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem sucesso">
                <strong>Sucesso:</strong> <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($erro)): ?>
            <div class="mensagem erro">
                <strong>Erro:</strong> <?php echo $erro; ?>
            </div>
        <?php endif; ?>
        
        <!-- Conteúdo da Aba Produtos -->
        <div class="tab-content <?php echo $abaAtiva === 'produtos' ? 'active' : ''; ?>">
            <h2><?php echo $produtoEdicao ? 'Editar Produto' : 'Cadastrar Novo Produto'; ?></h2>
            
            <form method="POST" action="?aba=produtos">
                <input type="hidden" name="acao" value="<?php echo $produtoEdicao ? 'atualizar_produto' : 'inserir_produto'; ?>">
                
                <?php if ($produtoEdicao): ?>
                    <input type="hidden" name="id" value="<?php echo $produtoEdicao->getId(); ?>">
                <?php endif; ?>
                
                <table>
                    <tr>
                        <td><label for="nome">Nome do Produto:</label></td>
                        <td>
                            <input type="text" id="nome" name="nome" 
                                   value="<?php echo $produtoEdicao ? $produtoEdicao->getNome() : ''; ?>" 
                                   required>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="preco">Preço:</label></td>
                        <td>
                            <input type="number" id="preco" name="preco" 
                                   value="<?php echo $produtoEdicao ? $produtoEdicao->getPreco() : ''; ?>" 
                                   step="0.01" min="0.01" required>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="quantidade">Quantidade:</label></td>
                        <td>
                            <input type="number" id="quantidade" name="quantidade" 
                                   value="<?php echo $produtoEdicao ? $produtoEdicao->getQuantidade() : ''; ?>" 
                                   min="0" required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" value="<?php echo $produtoEdicao ? 'Atualizar Produto' : 'Cadastrar Produto'; ?>">
                            <?php if ($produtoEdicao): ?>
                                <a href="?aba=produtos" style="color: #6c757d;">Cancelar Edição</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </form>
            
            <hr>
            
            <!-- Lista de Produtos -->
            <h3>Produtos Cadastrados</h3>
            <?php
            $produtosList = new Produto();
            $produtos = $produtosList->listarTodos();
            
            if ($produtos && count($produtos) > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nome</th><th>Preço</th><th>Quantidade</th><th>Ações</th></tr>";
                
                foreach ($produtos as $prod) {
                    echo "<tr>";
                    echo "<td>" . $prod['id'] . "</td>";
                    echo "<td>" . $prod['nome'] . "</td>";
                    echo "<td>R$ " . number_format($prod['preco'], 2, ',', '.') . "</td>";
                    echo "<td>" . $prod['quantidade'] . "</td>";
                    echo "<td>";
                    echo "<a href='?aba=produtos&editar=" . $prod['id'] . "' style='margin-right: 10px;'>Editar</a>";
                    echo "<form method='POST' action='?aba=produtos' style='display: inline;'>";
                    echo "<input type='hidden' name='acao' value='deletar_produto'>";
                    echo "<input type='hidden' name='id' value='" . $prod['id'] . "'>";
                    echo "<button type='submit' class='delete-btn' onclick='return confirm(\"Tem certeza que deseja deletar este produto?\")'>Deletar</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Nenhum produto cadastrado.</p>";
            }
            ?>
        </div>
        
        <!-- Conteúdo da Aba Pessoas -->
        <div class="tab-content <?php echo $abaAtiva === 'pessoas' ? 'active' : ''; ?>">
            <h2>Lista de Pessoas Cadastradas</h2>
            
            <?php if ($db !== null): ?>
                <?php
                $pessoa = new Pessoa($db);
                $stmt = $pessoa->ler();
                $num_linhas = $stmt->rowCount();
                
                if ($num_linhas > 0) {
                    while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='person'>";
                        echo "<p><strong>ID:</strong> " . $linha['id'] . "</p>";
                        echo "<p><strong>Nome:</strong> " . $linha['nome'] . "</p>";
                        echo "<p><strong>Idade:</strong> " . $linha['idade'] . " anos</p>";
                        
                        // Formulário para alterar idade
                        echo "<form class='alterar-form' method='post' action='?aba=pessoas'>";
                        echo "<input type='hidden' name='alterar_id' value='" . $linha['id'] . "'>";
                        echo "<input type='number' name='nova_idade' min='0' placeholder='Nova idade' required>";
                        echo "<button type='submit'>Alterar Idade</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='erro'>Nenhuma pessoa encontrada.</p>";
                }
                ?>
            <?php else: ?>
                <p class="erro">Erro de conexão com o banco de dados.</p>
            <?php endif; ?>
        </div>
        
        <!-- Estatísticas do Sistema -->
        <div class="stats">
            <h3>Estatísticas do Sistema</h3>
            <ul>
                <li>Total de produtos cadastrados: <?php echo $totalProdutos; ?></li>
                <li>Total de pessoas cadastradas: <?php echo $totalPessoas; ?></li>
                <li>Sistema desenvolvido usando POO (Programação Orientada a Objetos)</li>
                <li>Operações disponíveis: Inserir, Atualizar, Deletar e Listar</li>
            </ul>
        </div>
    </div>
</body>
</html>