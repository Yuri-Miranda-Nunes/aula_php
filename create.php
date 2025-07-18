<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];

    $banco = new BancoDeDados();
    $conexao = $banco->obterConexao();

    $query = "INSERT INTO produtos (nome, preco, quantidade) VALUES (:nome, :preco, :quantidade)";
    $stmt = $conexao->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':quantidade', $quantidade);
    $stmt->execute();

    header("Location: listar.php");
    exit;
}
?>

<h2>Cadastrar Produto</h2>
<form method="POST">
    Nome: <input type="text" name="nome" required><br><br>
    Preço: <input type="number" name="preco" step="0.01" required><br><br>
    Quantidade: <input type="number" name="quantidade" required><br><br>
    <input type="submit" value="Salvar">
</form>
<a href="listar.php">Voltar</a>
