<?php
require_once 'conexao.php';

if (!isset($_GET['id'])) {
    echo "ID não informado!";
    exit;
}

$id = $_GET['id'];

$banco = new BancoDeDados();
$conexao = $banco->obterConexao();

$query = "DELETE FROM produtos WHERE id = :id";
$stmt = $conexao->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

header("Location: listar.php");
exit;
