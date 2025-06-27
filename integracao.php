<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pessoas - Sistema de Cadastro</title>
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

        .nav-link.active {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
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
            max-width: 800px;
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

        /* Form Container */
        .form-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #00d4ff, #0099cc);
        }

        .form-container:hover {
            border-color: rgba(0, 212, 255, 0.3);
            box-shadow: 0 25px 50px rgba(0, 212, 255, 0.1);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #cccccc;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .form-input:focus {
            outline: none;
            border-color: #00d4ff;
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .submit-btn {
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            color: white;
            border: none;
            padding: 1.25rem 2.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 212, 255, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        /* Messages */
        .success-message, .error-message {
            padding: 1.25rem 2rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
            backdrop-filter: blur(10px);
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

        /* Floating Elements */
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }

        .floating-shapes::before,
        .floating-shapes::after {
            content: '';
            position: absolute;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 153, 204, 0.05));
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .floating-shapes::before {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
            animation-delay: -10s;
        }

        .floating-shapes::after {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
            animation-delay: -5s;
        }

        /* Animations */
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-20px) rotate(5deg);
            }
            50% {
                transform: translateY(-40px) rotate(10deg);
            }
            75% {
                transform: translateY(-20px) rotate(5deg);
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
                font-size: 2.5rem;
            }

            .form-container {
                padding: 2rem;
            }

            .nav-container {
                padding: 0 1rem;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .form-input {
                padding: 0.875rem 1rem;
                font-size: 1rem;
            }

            .submit-btn {
                padding: 1rem 2rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Elements -->
    <div class="floating-shapes"></div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="nav-brand">
                <i class="fas fa-users"></i>
                Sistema de Cadastro
            </a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="listar.php" class="nav-link"><i class="fas fa-list"></i> Lista</a></li>
                <li><a href="#" class="nav-link active"><i class="fas fa-user-plus"></i> Cadastrar</a></li>
            </ul>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1 class="page-title">Cadastro de Pessoas</h1>
            <p class="page-subtitle">Adicione novas pessoas ao sistema</p>
        </header>

        <section class="form-container">
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
                echo "<div class='error-message'>";
                echo "<i class='fas fa-exclamation-triangle'></i>";
                echo "Erro: Não foi possível conectar ao banco de dados.";
                echo "</div>";
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
                        echo "<div class='success-message'>";
                        echo "<i class='fas fa-check-circle'></i>";
                        echo "Pessoa '{$pessoa->nome}' cadastrada com sucesso!";
                        echo "</div>";
                        $cadastroSucesso = true;
                    } else {
                        echo "<div class='error-message'>";
                        echo "<i class='fas fa-exclamation-triangle'></i>";
                        echo "Erro ao cadastrar a pessoa.";
                        echo "</div>";
                    }
                }
            }
            ?>

            <form action="" method="post" id="formCadastroPessoa">
                <div class="form-group">
                    <label for="nome" class="form-label">
                        <i class="fas fa-user"></i>
                        Nome Completo
                    </label>
                    <input type="text" id="nome" name="nome" class="form-input" placeholder="Digite o nome completo" required>
                </div>

                <div class="form-group">
                    <label for="idade" class="form-label">
                        <i class="fas fa-birthday-cake"></i>
                        Idade
                    </label>
                    <input type="number" id="idade" name="idade" class="form-input" placeholder="Digite a idade" min="0" max="150" required>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus-circle"></i>
                    Cadastrar Pessoa
                </button>
            </form>
        </section>
    </main>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        });

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

            // Se o cadastro foi bem-sucedido, limpa os campos do formulário e foca no campo nome
            <?php if ($cadastroSucesso): ?>
                document.getElementById('nome').value = '';
                document.getElementById('idade').value = '';
                document.getElementById('nome').focus();
            <?php endif; ?>
        });

        // Form validation and enhancement
        const form = document.getElementById('formCadastroPessoa');
        const nomeInput = document.getElementById('nome');
        const idadeInput = document.getElementById('idade');

        // Add real-time validation feedback
        nomeInput.addEventListener('input', function() {
            if (this.value.length < 2) {
                this.style.borderColor = 'rgba(255, 0, 68, 0.5)';
            } else {
                this.style.borderColor = 'rgba(0, 255, 136, 0.5)';
            }
        });

        idadeInput.addEventListener('input', function() {
            const idade = parseInt(this.value);
            if (idade < 0 || idade > 150 || isNaN(idade)) {
                this.style.borderColor = 'rgba(255, 0, 68, 0.5)';
            } else {
                this.style.borderColor = 'rgba(0, 255, 136, 0.5)';
            }
        });

        // Reset border colors on focus
        [nomeInput, idadeInput].forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#00d4ff';
            });

            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                }
            });
        });

        // Add form submission animation
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.submit-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cadastrando...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>