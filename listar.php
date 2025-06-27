<?php
require_once 'conexao.php';
require_once 'pessoa.php';

$database = new BancoDeDados();
$db = $database->obterConexao();

if ($db === null) {
    die("<div class='error-message'>Erro: Não foi possível conectar ao banco de dados.</div>");
}

// Tratamento do formulário do off-canvas para editar nome e idade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id']) && isset($_POST['editar_nome']) && isset($_POST['editar_idade'])) {
    $pessoa = new Pessoa($db);
    $pessoa->id = $_POST['editar_id'];
    $pessoa->nome = $_POST['editar_nome'];
    $pessoa->idade = $_POST['editar_idade'];
    // Atualiza nome e idade
    $query = "UPDATE pessoas SET nome = :nome, idade = :idade WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nome', $pessoa->nome);
    $stmt->bindParam(':idade', $pessoa->idade);
    $stmt->bindParam(':id', $pessoa->id);
    if ($stmt->execute()) {
        echo "";
    } else {
        echo "<div class='error-message'>Erro ao atualizar a pessoa.</div>";
    }
}

// Se o formulário de alteração de idade antigo for enviado (opcional, pode remover se quiser só off-canvas)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alterar_id']) && isset($_POST['nova_idade'])) {
    $pessoa = new Pessoa($db);
    $pessoa->id = $_POST['alterar_id'];
    $novaIdade = $_POST['nova_idade'];
    if ($pessoa->alterarIdade($novaIdade)) {
        echo "<div class='success-message'>Idade alterada com sucesso para o ID {$_POST['alterar_id']}!</div>";
    } else {
        echo "<div class='error-message'>Erro ao alterar a idade.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pessoas - Sistema de Cadastro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 100%);
            color: #ffffff;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #00d4ff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: #ffffff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            background: rgba(0, 212, 255, 0.1);
            color: #00d4ff;
            transform: translateY(-2px);
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Main Content */
        .main-content {
            margin-top: 100px;
            padding: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            font-size: 1.2rem;
            color: #cccccc;
            opacity: 0.8;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .person-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .person-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #00d4ff, #0099cc);
        }

        .person-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 212, 255, 0.2);
            border-color: rgba(0, 212, 255, 0.3);
        }

        .person-info {
            margin-bottom: 1.5rem;
        }

        .person-id {
            font-size: 0.9rem;
            color: #00d4ff;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .person-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .person-age {
            font-size: 1.1rem;
            color: #cccccc;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .edit-btn {
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            justify-content: center;
        }

        .edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.3);
        }

        /* Off-canvas Styles */
        .offcanvas-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(4px);
            z-index: 1002;
        }

        .offcanvas {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 400px;
            height: 100%;
            background: linear-gradient(135deg, #1a1a1a, #2a2a2a);
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
            padding: 2rem;
            z-index: 1003;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .offcanvas.active {
            transform: translateX(0);
        }

        .offcanvas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .offcanvas-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #00d4ff;
        }

        .close-btn {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(90deg);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #cccccc;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #00d4ff;
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }

        .save-btn {
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.3);
        }

        /* Messages */
        .success-message, .error-message {
            padding: 1rem 2rem;
            border-radius: 8px;
            margin: 1rem 2rem;
            font-weight: 600;
            position: fixed;
            top: 100px;
            right: 2rem;
            z-index: 1004;
            animation: slideIn 0.3s ease;
        }

        .success-message {
            background: rgba(0, 255, 136, 0.1);
            color: #00ff88;
            border: 1px solid rgba(0, 255, 136, 0.3);
        }

        .error-message {
            background: rgba(255, 0, 68, 0.1);
            color: #ff0044;
            border: 1px solid rgba(255, 0, 68, 0.3);
        }

        .no-records {
            text-align: center;
            padding: 4rem 2rem;
            color: #cccccc;
            font-size: 1.2rem;
        }

        /* Animations */
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.95);
                flex-direction: column;
                padding: 1rem;
                gap: 1rem;
            }

            .nav-menu.active {
                display: flex;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .main-content {
                padding: 1rem;
                margin-top: 80px;
            }

            .page-title {
                font-size: 2rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .offcanvas {
                width: 100%;
                max-width: 400px;
            }

            .success-message, .error-message {
                right: 1rem;
                left: 1rem;
                margin: 1rem;
            }
        }

        @media (max-width: 480px) {
            .nav-container {
                padding: 0 1rem;
            }

            .person-card {
                padding: 1.5rem;
            }

            .offcanvas {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="nav-brand">
                <i class="fas fa-users"></i>
                Sistema de Cadastro
            </a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="#" class="nav-link"><i class="fas fa-list"></i> Lista</a></li>
                <li><a href="integracao.php" class="nav-link"><i class="fas fa-user-plus"></i> Cadastrar</a></li>
            </ul>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1 class="page-title">Lista de Pessoas</h1>
            <p class="page-subtitle">Gerencie todos os cadastros do sistema</p>
        </header>

        <section class="cards-grid">
            <?php
            $pessoa = new Pessoa($db);
            $stmt = $pessoa->ler();
            $num_linhas = $stmt->rowCount();

            if ($num_linhas > 0) {
                while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='person-card'>";
                    echo "<div class='person-info'>";
                    echo "<div class='person-id'>ID: " . htmlspecialchars($linha['id']) . "</div>";
                    echo "<div class='person-name'>" . htmlspecialchars($linha['nome']) . "</div>";
                    echo "<div class='person-age'>";
                    echo "<i class=''></i>";
                    echo "<span id='idade-{$linha['id']}'>" . htmlspecialchars($linha['idade']) . " anos</span>";
                    echo "</div>";
                    echo "</div>";
                    echo "<button type='button' class='edit-btn' onclick=\"abrirOffcanvas('{$linha['id']}', '" . htmlspecialchars($linha['nome']) . "', '{$linha['idade']}')\">";
                    echo "<i class='fas fa-edit'></i> Editar";
                    echo "</button>";
                    echo "</div>";
                }
            } else {
                echo "<div class='no-records'>";
                echo "<i class='fas fa-users' style='font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;'></i>";
                echo "<p>Nenhum registro encontrado.</p>";
                echo "<p>Que tal adicionar a primeira pessoa?</p>";
                echo "</div>";
            }
            ?>
        </section>
    </main>

    <!-- Offcanvas Overlay -->
    <div class="offcanvas-overlay" id="offcanvasOverlay" onclick="fecharOffcanvas()"></div>

    <!-- Offcanvas para edição -->
    <div class="offcanvas" id="offcanvas">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title">
                <i class="fas fa-user-edit"></i>
                Editar Pessoa
            </h2>
            <button class="close-btn" onclick="fecharOffcanvas()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="formEditar" method="post" action="">
            <input type="hidden" name="editar_id" id="editar_id">
            
            <div class="form-group">
                <label class="form-label" for="editar_nome">
                    <i class="fas fa-user"></i> Nome
                </label>
                <input type="text" name="editar_nome" id="editar_nome" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="editar_idade">
                    <i class="fas fa-birthday-cake"></i> Idade
                </label>
                <input type="number" name="editar_idade" id="editar_idade" class="form-input" min="0" max="150" required>
            </div>
            
            <button type="submit" class="save-btn">
                <i class="fas fa-save"></i>
                Salvar Alterações
            </button>
        </form>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        });

        // Offcanvas functions
        function abrirOffcanvas(id, nome, idade) {
            document.getElementById('editar_id').value = id;
            document.getElementById('editar_nome').value = nome;
            document.getElementById('editar_idade').value = idade;
            
            const overlay = document.getElementById('offcanvasOverlay');
            const offcanvas = document.getElementById('offcanvas');
            
            overlay.style.display = 'block';
            offcanvas.style.display = 'block';
            
            // Trigger animation
            setTimeout(() => {
                offcanvas.classList.add('active');
            }, 10);
        }

        function fecharOffcanvas() {
            const overlay = document.getElementById('offcanvasOverlay');
            const offcanvas = document.getElementById('offcanvas');
            
            offcanvas.classList.remove('active');
            
            setTimeout(() => {
                overlay.style.display = 'none';
                offcanvas.style.display = 'none';
            }, 300);
        }

        // Auto-hide messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const messages = document.querySelectorAll('.success-message, .error-message');
            messages.forEach(message => {
                setTimeout(() => {
                    message.style.opacity = '0';
                    setTimeout(() => {
                        message.remove();
                    }, 300);
                }, 5000);
            });
        });

        // Close offcanvas on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharOffcanvas();
            }
        });
    </script>
</body>
</html>