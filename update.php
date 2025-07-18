<?php
require_once 'conexao.php';
$banco = new BancoDeDados();
$conexao = $banco->obterConexao();

if (!isset($_GET['id'])) {
    echo "ID não informado!";
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];

    $query = "UPDATE produtos SET nome = :nome, preco = :preco, quantidade = :quantidade WHERE id = :id";
    $stmt = $conexao->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':quantidade', $quantidade);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header("Location: listar.php");
    exit;
}

$query = "SELECT * FROM produtos WHERE id = :id";
$stmt = $conexao->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo "Produto não encontrado!";
    exit;
}
?>

<h2>Editar Produto</h2>
<form method="POST">
    Nome: <input type="text" name="nome" value="<?= $produto['nome'] ?>" required><br><br>
    Preço: <input type="number" name="preco" value="<?= $produto['preco'] ?>" step="0.01" required><br><br>
    Quantidade: <input type="number" name="quantidade" value="<?= $produto['quantidade'] ?>" required><br><br>
    <input type="submit" value="Atualizar">
</form>
<a href="listar.php">Voltar</a>
