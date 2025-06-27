<?php
// Definição da classe BancoDeDados (POO: definição de classe)
class BancoDeDados
{
    // Propriedades privadas para armazenar dados de conexão (POO: encapsulamento)
    private $host = "localhost";
    private $nome_banco = "aula_php";
    private $usuario = "root";
    private $senha = "";
    // Propriedade pública para armazenar a conexão (POO: atributo da classe)
    public $conexao;

    // Método público para obter a conexão com o banco de dados (POO: método da classe)
    public function obterConexao() {
        $this->conexao = null;
        try {
            // Criação de um novo objeto PDO para conexão (POO: uso de outro objeto dentro da classe)
            $this->conexao = new PDO("mysql:host={$this->host};port=49170;dbname={$this->nome_banco}", $this->usuario, $this->senha);
            $this->conexao->exec("set names utf8");
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $excecao) {
            // Tratamento de exceção (POO: uso de try/catch e objeto de exceção)
            echo "Erro de conexão: " . $excecao->getMessage();
            return null;
        }
        // Retorna o objeto de conexão (POO: retorno de objeto)
        return $this->conexao;
    }
}