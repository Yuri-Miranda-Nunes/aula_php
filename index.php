<?php
// Arquivo principal do sistema unificado de CRUD para produtos e pessoas
require_once 'conexao.php';
require_once 'Produto.php';
require_once 'pessoa.php';

$mensagem = "";
$erro = "";

$abaAtiva = $_GET['aba'] ?? 'cadastrar-produtos';

$banco = new BancoDeDados();
$conexao = $banco->obterConexao();
$produto = new Produto($conexao);
$pessoa = new Pessoa($conexao);

if ($conexao === null) {
    $erro = "Erro: Não foi possível conectar ao banco de dados.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    } elseif (isset($_POST['atualizar_produto'])) {
        $produto->setId($_POST['id']);
        $produto->setNome($_POST['nome']);
        $produto->setPreco($_POST['preco']);
        $produto->setQuantidade($_POST['quantidade']);

        $erros = $produto->validar();

        if (empty($erros)) {
            if ($produto->atualizar()) {
                $mensagem = "Produto atualizado com sucesso!";
                // Redireciona para remover parâmetro de edição e fechar form de edição
                header("Location: ?aba=listar-produtos");
                exit();
            } else {
                $erro = "Erro ao atualizar produto!";
            }
        } else {
            $erro = implode(", ", $erros);
        }
    } elseif (isset($_POST['deletar_produto'])) {
        if ($produto->deletar($_POST['id'])) {
            $mensagem = "Produto deletado com sucesso!";
        } else {
            $erro = "Erro ao deletar produto!";
        }
    } elseif (isset($_POST['cadastrar_pessoa'])) {
        $pessoa->nome = $_POST['nome'];
        $pessoa->idade = $_POST['idade'];

        if ($pessoa->criar()) {
            $mensagem = "Pessoa cadastrada com sucesso!";
        } else {
            $erro = "Erro ao cadastrar pessoa!";
        }
    } elseif (isset($_POST['alterar_pessoa'])) {
        $pessoa->id = $_POST['id_pessoa'];
        $pessoa->nome = $_POST['novo_nome'];
        $pessoa->idade = $_POST['nova_idade'];

        $query = "UPDATE pessoas SET nome = :nome, idade = :idade WHERE id = :id";
        $stmt = $conexao->prepare($query);
        $stmt->bindParam(':nome', $pessoa->nome);
        $stmt->bindParam(':idade', $pessoa->idade);
        $stmt->bindParam(':id', $pessoa->id);

        if ($stmt->execute()) {
            $mensagem = "Pessoa atualizada com sucesso!";
            // Redireciona para remover parâmetro de edição e fechar form de edição
            header("Location: ?aba=listar-pessoas");
            exit();
        } else {
            $erro = "Erro ao atualizar pessoa!";
        }
    } elseif (isset($_POST['deletar_pessoa'])) {
        if ($pessoa->deletar($_POST['id_pessoa'])) {
            $mensagem = "Pessoa excluída com sucesso!";
        } else {
            $erro = "Erro ao excluir pessoa!";
        }
    }
}

$produtoEdicao = null;
if (isset($_GET['editar']) && $abaAtiva === 'listar-produtos') {
    $produtoTemp = new Produto($conexao);
    if ($produtoTemp->buscarPorId($_GET['editar'])) {
        $produtoEdicao = $produtoTemp;
    } else {
        $erro = "Erro: Produto não encontrado para edição.";
    }
}

$totalProdutos = $produto->contarTotal();
$totalPessoas = 0;
if ($conexao !== null) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --bg-dark: #0f0f23;
            --bg-card: #1a1a2e;
            --text-light: #ffffff;
            --text-gray: #a0a0a0;
            --success-color: #00d4aa;
            --error-color: #ff6b6b;
            --warning-color: #ffd93d;
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            --shadow-hover: 0 30px 60px rgba(0, 0, 0, 0.4);
            --border-radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, #16213e 100%);
            color: var(--text-light);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--primary-color) 0%, transparent 70%);
            opacity: 0.1;
            border-radius: 50%;
            z-index: -1;
        }

        .header h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            text-shadow: 0 0 30px rgba(102, 126, 234, 0.3);
        }

        .header p {
            color: var(--text-gray);
            font-size: 1.1rem;
            opacity: 0.8;
        }

        .nav-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
            flex-wrap: wrap;
            background: var(--bg-card);
            padding: 10px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .nav-tab {
            padding: 15px 25px;
            background: transparent;
            color: var(--text-gray);
            text-decoration: none;
            border-radius: 15px;
            transition: var(--transition);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .nav-tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .nav-tab:hover::before {
            left: 100%;
        }

        .nav-tab:hover {
            color: var(--text-light);
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-tab.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-5px);
        }

        .card h2 {
            color: var(--text-light);
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: rgba(255, 255, 255, 0.08);
        }

        .form-control::placeholder {
            color: var(--text-gray);
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--error-color), #ff5252);
            color: var(--text-light);
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 107, 107, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #00b894);
            color: var(--text-light);
            box-shadow: 0 10px 30px rgba(0, 212, 170, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table th,
        .table td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table th {
            background: #23234a; /* cor sólida escura, pode ajustar para o tom que preferir */
            color: var(--text-light);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
            border: none;
        }

        .table td {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .alert {
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-color), #00b894);
            color: var(--text-light);
        }

        .alert-error {
            background: linear-gradient(135deg, var(--error-color), #ff5252);
            color: var(--text-light);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card .icon {
            font-size: 3rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--text-light);
            font-weight: 800;
        }

        .stat-card p {
            color: var(--text-gray);
            font-size: 1rem;
            font-weight: 600;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .nav-tabs {
                flex-direction: column;
                gap: 5px;
            }
            
            .nav-tab {
                padding: 12px 20px;
                font-size: 0.9rem;
            }
            
            .card {
                padding: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .table th,
            .table td {
                padding: 15px 10px;
                font-size: 0.85rem;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                padding: 12px 20px;
                font-size: 0.9rem;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .nav-tab {
                padding: 10px 15px;
            }
            
            .card {
                padding: 15px;
            }
            
            .form-control {
                padding: 12px 15px;
            }
            
            .table th,
            .table td {
                padding: 10px 8px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header fade-in">
            <h1><i class="fas fa-database"></i> Sistema Unificado</h1>
            <p>Gerenciamento completo de produtos e pessoas</p>
        </div>
        
        <!-- Navegação por abas -->
        <nav class="nav-tabs fade-in">
            <a href="?aba=cadastrar-produtos" class="nav-tab <?php echo $abaAtiva === 'cadastrar-produtos' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> Cadastrar Produtos
            </a>
            <a href="?aba=cadastrar-pessoas" class="nav-tab <?php echo $abaAtiva === 'cadastrar-pessoas' ? 'active' : ''; ?>">
                <i class="fas fa-user-plus"></i> Cadastrar Pessoas
            </a>
            <a href="?aba=listar-produtos" class="nav-tab <?php echo $abaAtiva === 'listar-produtos' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Listar Produtos
            </a>
            <a href="?aba=listar-pessoas" class="nav-tab <?php echo $abaAtiva === 'listar-pessoas' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Listar Pessoas
            </a>
        </nav>
        
        <!-- Mensagens -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-success fade-in">
                <i class="fas fa-check-circle"></i>
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($erro)): ?>
            <div class="alert alert-error fade-in">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>
        
        <!-- ABA CADASTRAR PRODUTOS -->
        <?php if ($abaAtiva === 'cadastrar-produtos'): ?>
            <div class="card fade-in">
                <h2><i class="fas fa-plus-circle"></i> Cadastrar Produto</h2>
                
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nome">Nome do Produto</label>
                            <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome do produto" required>
                        </div>
                        <div class="form-group">
                            <label for="preco">Preço</label>
                            <input type="number" name="preco" id="preco" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label for="quantidade">Quantidade</label>
                            <input type="number" name="quantidade" id="quantidade" class="form-control" min="0" placeholder="0" required>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" name="cadastrar_produto" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cadastrar Produto
                        </button>
                    </div>
                </form>
            </div>
            
        <!-- ABA CADASTRAR PESSOAS -->
        <?php elseif ($abaAtiva === 'cadastrar-pessoas'): ?>
            <div class="card fade-in">
                <h2><i class="fas fa-user-plus"></i> Cadastrar Pessoa</h2>
                
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nome">Nome Completo</label>
                            <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome completo" required>
                        </div>
                        <div class="form-group">
                            <label for="idade">Idade</label>
                            <input type="number" name="idade" id="idade" class="form-control" min="0" max="150" placeholder="Digite a idade" required>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" name="cadastrar_pessoa" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cadastrar Pessoa
                        </button>
                    </div>
                </form>
            </div>
            
        <!-- ABA LISTAR PRODUTOS -->
        <?php elseif ($abaAtiva === 'listar-produtos'): ?>
            <div class="card fade-in">
                <h2><i class="fas fa-box"></i> Lista de Produtos</h2>
                
                <!-- Formulário de edição se houver produto selecionado -->
                <?php if ($produtoEdicao): ?>
                    <div class="card" style="margin-bottom: 30px; border-left: 4px solid var(--warning-color);">
                        <h3><i class="fas fa-edit"></i> Editar Produto</h3>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $produtoEdicao->getId(); ?>">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nome">Nome do Produto</label>
                                    <input type="text" name="nome" id="nome" class="form-control" value="<?php echo $produtoEdicao->getNome(); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="preco">Preço</label>
                                    <input type="number" name="preco" id="preco" class="form-control" value="<?php echo $produtoEdicao->getPreco(); ?>" step="0.01" min="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantidade">Quantidade</label>
                                    <input type="number" name="quantidade" id="quantidade" class="form-control" value="<?php echo $produtoEdicao->getQuantidade(); ?>" min="0" required>
                                </div>
                            </div>
                            <div class="actions">
                                <button type="submit" name="atualizar_produto" class="btn btn-success">
                                    <i class="fas fa-save"></i> Atualizar Produto
                                </button>
                                <a href="?aba=listar-produtos" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                
                <!-- Lista de produtos -->
                <div class="table-container">
                    <?php
                    $produtos = $produto->listar();
                    if ($produtos && count($produtos) > 0) {
                        echo "<table class='table'>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th><i class='fas fa-hashtag'></i> ID</th>";
                        echo "<th><i class='fas fa-tag'></i> Nome</th>";
                        echo "<th><i class='fas fa-dollar-sign'></i> Preço</th>";
                        echo "<th><i class='fas fa-boxes'></i> Quantidade</th>";
                        echo "<th><i class='fas fa-cogs'></i> Ações</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";

                        foreach ($produtos as $prod) {
                            echo "<tr>";
                            echo "<td>" . $prod['id'] . "</td>";
                            echo "<td>" . $prod['nome'] . "</td>";
                            echo "<td>R$ " . number_format($prod['preco'], 2, ',', '.') . "</td>";
                            echo "<td>" . $prod['quantidade'] . "</td>";
                            echo "<td>";
                            echo "<div class='actions'>";
                            echo "<a href='?aba=listar-produtos&editar=" . $prod['id'] . "' class='btn btn-secondary'>";
                            echo "<i class='fas fa-edit'></i> Editar";
                            echo "</a>";
                            echo "<form method='POST' style='display: inline;'>";
                            echo "<input type='hidden' name='id' value='" . $prod['id'] . "'>";
                            echo "<button type='submit' name='deletar_produto' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja deletar este produto?\");'>";
                            echo "<i class='fas fa-trash'></i> Deletar";
                            echo "</button>";
                            echo "</form>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";
                    } else {
                        echo "<div style='text-align: center; padding: 40px; color: var(--text-gray);'>";
                        echo "<i class='fas fa-inbox' style='font-size: 4rem; margin-bottom: 20px; opacity: 0.3;'></i>";
                        echo "<h3>Nenhum produto cadastrado</h3>";
                        echo "<p>Comece cadastrando seu primeiro produto!</p>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            
        <!-- ABA LISTAR PESSOAS -->
        <?php elseif ($abaAtiva === 'listar-pessoas'): ?>
            <div class="card fade-in">
                <h2><i class="fas fa-users"></i> Lista de Pessoas</h2>

                <?php
                // Verifica se está editando uma pessoa
                $pessoaEdicao = null;
                if (isset($_GET['editar_pessoa']) && $conexao !== null) {
                    $stmt = $pessoa->ler();
                    while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        if ($linha['id'] == $_GET['editar_pessoa']) {
                            $pessoaEdicao = $linha;
                            break;
                        }
                    }
                }
                ?>

                <!-- Formulário de edição se houver pessoa selecionada -->
                <?php if ($pessoaEdicao): ?>
                    <div class="card" style="margin-bottom: 30px; border-left: 4px solid var(--warning-color);">
                        <h3><i class="fas fa-user-edit"></i> Editar Pessoa</h3>
                        <form method="POST">
                            <input type="hidden" name="id_pessoa" value="<?php echo $pessoaEdicao['id']; ?>">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="novo_nome">Nome Completo</label>
                                    <input type="text" name="novo_nome" id="novo_nome" class="form-control" value="<?php echo $pessoaEdicao['nome']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="nova_idade">Idade</label>
                                    <input type="number" name="nova_idade" id="nova_idade" class="form-control" value="<?php echo $pessoaEdicao['idade']; ?>" min="0" max="150" required>
                                </div>
                            </div>
                            <div class="actions">
                                <button type="submit" name="alterar_pessoa" class="btn btn-success">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                                <a href="?aba=listar-pessoas" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Lista de pessoas -->
                <div class="table-container">
                    <?php if ($conexao !== null): ?>
                        <?php
                        $stmt = $pessoa->ler();
                        $num_linhas = $stmt->rowCount();

                        if ($num_linhas > 0) {
                            echo "<table class='table'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th><i class='fas fa-hashtag'></i> ID</th>";
                            echo "<th><i class='fas fa-user'></i> Nome</th>";
                            echo "<th><i class='fas fa-birthday-cake'></i> Idade</th>";
                            echo "<th><i class='fas fa-cogs'></i> Ações</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";

                            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $linha['id'] . "</td>";
                                echo "<td>" . $linha['nome'] . "</td>";
                                echo "<td>" . $linha['idade'] . " anos</td>";
                                echo "<td>";
                                echo "<div class='actions'>";
                                // Botão editar
                                echo "<a href='?aba=listar-pessoas&editar_pessoa=" . $linha['id'] . "' class='btn btn-secondary'>";
                                echo "<i class='fas fa-edit'></i> Editar";
                                echo "</a>";
                                // Botão excluir (form isolado)
                                echo "<form method='POST' style='display: inline; margin-left:5px;'>";
                                echo "<input type='hidden' name='id_pessoa' value='" . $linha['id'] . "'>";
                                echo "<button type='submit' name='deletar_pessoa' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja deletar esta pessoa?\");'>";
                                echo "<i class='fas fa-trash'></i> Excluir";
                                echo "</button>";
                                echo "</form>";
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                        } else {
                            echo "<div style='text-align: center; padding: 40px; color: var(--text-gray);'>";
                            echo "<i class='fas fa-users' style='font-size: 4rem; margin-bottom: 20px; opacity: 0.3;'></i>";
                            echo "<h3>Nenhuma pessoa cadastrada</h3>";
                            echo "<p>Comece cadastrando sua primeira pessoa!</p>";
                            echo "</div>";
                        }
                        ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px; color: var(--error-color);">
                            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; margin-bottom: 20px;"></i>
                            <h3>Erro de Conexão</h3>
                            <p>Não foi possível conectar ao banco de dados.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Estatísticas do Sistema -->
        <div class="stats">
            <div class="stat-card fade-in">
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <h3><?php echo $totalProdutos; ?></h3>
                <p>Produtos Cadastrados</p>
            </div>
            <div class="stat-card fade-in">
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?php echo $totalPessoas; ?></h3>
                <p>Pessoas Cadastradas</p>
            </div>
            <div class="stat-card fade-in">
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3><?php echo $totalProdutos + $totalPessoas; ?></h3>
                <p>Total de Registros</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="text-align: center; margin-top: 50px; padding: 30px; color: var(--text-gray); border-top: 1px solid rgba(255, 255, 255, 0.1);">
            <p><i class="fas fa-code"></i> Sistema Unificado de Cadastros - Desenvolvido com PHP e MySQL</p>
            <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.7;">
                <i class="fas fa-heart" style="color: var(--error-color);"></i> 
                Feito com dedicação para facilitar o gerenciamento de dados
            </p>
        </div>
    </div>

    <!-- Scripts para melhorar a experiência do usuário -->
    <script>
        // Animação de fade-in para elementos quando carregam
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        

        // Validação de formulários em tempo real
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value < 0) {
                    this.style.borderColor = 'var(--error-color)';
                } else {
                    this.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                }
            });
        });

        

        // Auto-hide para mensagens de sucesso e erro
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });

        // Smooth scroll para navegação
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Tooltip para botões
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                if (this.querySelector('.fas')) {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                }
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Formatação automática de preços
        document.querySelectorAll('input[name="preco"]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseFloat(this.value).toFixed(2);
                }
            });
        });

        // Capitalização automática de nomes
        document.querySelectorAll('input[name="nome"], input[name="novo_nome"]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value) {
                    this.value = this.value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
                }
            });
        });
    </script>
</body>
</html>