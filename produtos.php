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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>') repeat;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-100px); }
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 40px;
        }

        .alert {
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 10px;
            border-left: 4px solid;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }

        .section {
            margin-bottom: 40px;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .section h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section h2 i {
            color: #667eea;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
            transform: translateY(-2px);
        }

        .search-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-container input[type="text"] {
            flex: 1;
            min-width: 200px;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .table .actions {
            white-space: nowrap;
        }

        .table .actions form {
            display: inline;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5em;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-card h3 {
            font-size: 2em;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #6c757d;
            font-size: 0.9em;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4em;
            margin-bottom: 20px;
            color: #dee2e6;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: #495057;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }

            .content {
                padding: 20px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 2em;
            }

            .section {
                padding: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .search-container {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container input[type="text"] {
                min-width: auto;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 600px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.8em;
            }

            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }

            .table th, .table td {
                padding: 10px;
                font-size: 14px;
            }
        }

        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
        }

        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 2em;
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading" id="loading">
        <div class="loading-spinner">
            <i class="fas fa-spinner spinner"></i>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <h1><i class="fas fa-box"></i> Sistema de Produtos</h1>
            <p>Gerencie seus produtos de forma eficiente e organizada</p>
        </div>

        <div class="content">
            <!-- Mensagens -->
            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Sucesso:</strong> <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($erro)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Erro:</strong> <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <!-- Formulário de Cadastro/Edição -->
            <div class="section">
                <h2>
                    <i class="fas fa-<?php echo $produtoEdicao ? 'edit' : 'plus'; ?>"></i>
                    <?php echo $produtoEdicao ? 'Editar Produto' : 'Cadastrar Novo Produto'; ?>
                </h2>
                
                <form method="POST" action="" id="produtoForm">
                    <input type="hidden" name="acao" value="<?php echo $produtoEdicao ? 'atualizar' : 'inserir'; ?>">
                    
                    <?php if ($produtoEdicao): ?>
                        <input type="hidden" name="id" value="<?php echo $produtoEdicao->getId(); ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">
                                <i class="fas fa-tag"></i> Nome do Produto
                            </label>
                            <input type="text" 
                                   id="nome" 
                                   name="nome" 
                                   class="form-control"
                                   value="<?php echo $produtoEdicao ? htmlspecialchars($produtoEdicao->getNome()) : ''; ?>" 
                                   required
                                   placeholder="Digite o nome do produto">
                        </div>
                        
                        <div class="form-group">
                            <label for="preco">
                                <i class="fas fa-dollar-sign"></i> Preço
                            </label>
                            <input type="number" 
                                   id="preco" 
                                   name="preco" 
                                   class="form-control"
                                   value="<?php echo $produtoEdicao ? $produtoEdicao->getPreco() : ''; ?>" 
                                   step="0.01" 
                                   min="0.01" 
                                   required
                                   placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label for="quantidade">
                                <i class="fas fa-cubes"></i> Quantidade
                            </label>
                            <input type="number" 
                                   id="quantidade" 
                                   name="quantidade" 
                                   class="form-control"
                                   value="<?php echo $produtoEdicao ? $produtoEdicao->getQuantidade() : ''; ?>" 
                                   min="0" 
                                   required
                                   placeholder="0">
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-<?php echo $produtoEdicao ? 'save' : 'plus'; ?>"></i>
                            <?php echo $produtoEdicao ? 'Atualizar Produto' : 'Cadastrar Produto'; ?>
                        </button>
                        
                        <?php if ($produtoEdicao): ?>
                            <a href="?" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancelar Edição
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Busca de Produtos -->
            <div class="section">
                <h2>
                    <i class="fas fa-search"></i>
                    Buscar Produtos
                </h2>
                
                <form method="GET" action="">
                    <div class="search-container">
                        <input type="text" 
                               name="buscar" 
                               class="form-control"
                               placeholder="Digite o nome do produto para buscar" 
                               value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                        
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                        
                        <a href="?" class="btn btn-secondary">
                            <i class="fas fa-list"></i>
                            Mostrar Todos
                        </a>
                    </div>
                </form>
            </div>

            <!-- Lista de Produtos -->
            <div class="section">
                <h2>
                    <i class="fas fa-list"></i>
                    Lista de Produtos
                    <span style="font-size: 0.7em; color: #6c757d;">(Total: <?php echo $totalProdutos; ?>)</span>
                </h2>
                
                <?php if (!empty($produtos)): ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-tag"></i> Nome</th>
                                    <th><i class="fas fa-dollar-sign"></i> Preço</th>
                                    <th><i class="fas fa-cubes"></i> Quantidade</th>
                                    <th><i class="fas fa-cogs"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['nome']); ?></td>
                                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                        <td><?php echo $item['quantidade']; ?></td>
                                        <td class="actions">
                                            <a href="?editar=<?php echo $item['id']; ?>" class="btn btn-success">
                                                <i class="fas fa-edit"></i>
                                                Editar
                                            </a>
                                            
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="acao" value="deletar">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-danger" 
                                                        onclick="return confirm('Tem certeza que deseja deletar este produto?');">
                                                    <i class="fas fa-trash"></i>
                                                    Deletar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>Nenhum produto encontrado</h3>
                        <p>Não há produtos cadastrados no sistema ou nenhum produto corresponde à sua busca.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Estatísticas do Sistema -->
            <div class="section">
                <h2>
                    <i class="fas fa-chart-bar"></i>
                    Estatísticas do Sistema
                </h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-box"></i>
                        <h3><?php echo $totalProdutos; ?></h3>
                        <p>Produtos Cadastrados</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-code"></i>
                        <h3>POO</h3>
                        <p>Programação Orientada a Objetos</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class="fas fa-database"></i>
                        <h3>CRUD</h3>
                        <p>Create, Read, Update, Delete</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Adicionar animação de loading nos formulários
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            const loading = document.getElementById('loading');
            
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    loading.style.display = 'block';
                });
            });
            
            // Focar no primeiro campo do formulário
            const firstInput = document.querySelector('#nome');
            if (firstInput) {
                firstInput.focus();
            }
            
            // Adicionar máscara de moeda para o campo preço
            const precoInput = document.querySelector('#preco');
            if (precoInput) {
                precoInput.addEventListener('input', function() {
                    let value = this.value.replace(/[^\d.,]/g, '');
                    this.value = value;
                });
            }
        });
        
        // Função para confirmar exclusão com estilo
        function confirmarExclusao(nome) {
            return confirm(`Tem certeza que deseja deletar o produto "${nome}"?\n\nEsta ação não pode ser desfeita.`);
        }
    </script>
</body>
</html>