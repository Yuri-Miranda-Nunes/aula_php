<?php
// Incluir a classe Produto
require_once 'classes/Produto.php';

// Variáveis para mensagens e dados
$mensagem = "";
$erro = "";
$produtos = [];

// Instanciar a classe Produto
$produto = new Produto();

// Processar formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        
        // INSERIR NOVO PRODUTO
        if ($_POST['acao'] === 'inserir') {
            $produto->setNome($_POST['nome']);
            $produto->setPreco($_POST['preco']);
            $produto->setQuantidade($_POST['quantidade']);
            
            // Validar dados
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
        
        // ATUALIZAR PRODUTO
        elseif ($_POST['acao'] === 'atualizar') {
            $produto->setId($_POST['id']);
            $produto->setNome($_POST['nome']);
            $produto->setPreco($_POST['preco']);
            $produto->setQuantidade($_POST['quantidade']);
            
            // Validar dados
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
        elseif ($_POST['acao'] === 'deletar') {
            if ($produto->deletar($_POST['id'])) {
                $mensagem = "Produto deletado com sucesso!";
            } else {
                $erro = "Erro ao deletar produto!";
            }
        }
    }
}

// Buscar produtos (para listagem)
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $produtos = $produto->buscarPorNome($_GET['buscar']);
} else {
    $produtos = $produto->listar();
}

// Buscar produto específico para edição
$produtoEdicao = null;
if (isset($_GET['editar'])) {
    $produtoTemp = new Produto();
    if ($produtoTemp->buscarPorId($_GET['editar'])) {
        $produtoEdicao = $produtoTemp;
    }
}

// Contar total de produtos
$totalProdutos = $produto->contarTotal();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
</head>
<body>
    <h1>Sistema de Cadastro de Produtos</h1>
    
    <!-- Mensagens -->
    <?php if (!empty($mensagem)): ?>
        <p><strong>Sucesso:</strong> <?php echo $mensagem; ?></p>
    <?php endif; ?>
    
    <?php if (!empty($erro)): ?>
        <p><strong>Erro:</strong> <?php echo $erro; ?></p>
    <?php endif; ?>
    
    <!-- Formulário de Cadastro/Edição -->
    <h2><?php echo $produtoEdicao ? 'Editar Produto' : 'Cadastrar Novo Produto'; ?></h2>
    
    <form method="POST" action="">
        <input type="hidden" name="acao" value="<?php echo $produtoEdicao ? 'atualizar' : 'inserir'; ?>">
        
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
                        <a href="?">Cancelar Edição</a>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>
    
    <hr>
    
    <!-- Busca de Produtos -->
    <h2>Buscar Produtos</h2>
    <form method="GET" action="">
        <input type="text" name="buscar" placeholder="Digite o nome do produto" 
               value="<?php echo isset($_GET['buscar']) ? $_GET['buscar'] : ''; ?>">
        <input type="submit" value="Buscar">
        <a href="?">Mostrar Todos</a>
    </form>
    
    <hr>
    
    <!-- Lista de Produtos -->
    <h2>Lista de Produtos (Total: <?php echo $totalProdutos; ?>)</h2>
    
    <?php if (!empty($produtos)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['nome']; ?></td>
                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo $item['quantidade']; ?></td>
                        <td>
                            <a href="?editar=<?php echo $item['id']; ?>">Editar</a>
                            |
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="acao" value="deletar">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="submit" value="Deletar" 
                                       onclick="return confirm('Tem certeza que deseja deletar este produto?');">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum produto encontrado.</p>
    <?php endif; ?>
    
    <hr>
    
    <!-- Informações do Sistema -->
    <h3>Informações do Sistema</h3>
    <ul>
        <li>Total de produtos cadastrados: <?php echo $totalProdutos; ?></li>
        <li>Sistema desenvolvido usando POO (Programação Orientada a Objetos)</li>
        <li>Operações disponíveis: Inserir, Listar, Buscar, Atualizar, Deletar</li>
    </ul>
</body>
</html>