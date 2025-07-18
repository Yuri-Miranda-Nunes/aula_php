<?php
// Define a classe Pessoa para manipular registros da tabela 'pessoas'
class Pessoa {
    // Propriedade privada para armazenar a conexão com o banco de dados
    private $conexao;
    // Nome da tabela no banco de dados
    private $nome_tabela = "pessoas";

    // Propriedades públicas correspondentes às colunas da tabela
    public $id;
    public $nome;
    public $idade;

    // Construtor recebe a conexão com o banco de dados e armazena na propriedade $conexao
    public function __construct($db) {
        $this->conexao = $db;
    }

    // Método para criar um novo registro na tabela 'pessoas'
    public function criar() {
        // Monta a query SQL para inserir um novo registro
        $query = "INSERT INTO " . $this->nome_tabela . " SET nome=:nome, idade=:idade";
        // Prepara a query para execução
        $stmt = $this->conexao->prepare($query);

        // Limpa os dados recebidos para evitar XSS e SQL Injection
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->idade = htmlspecialchars(strip_tags($this->idade));

        // Faz o bind dos parâmetros nome e idade na query preparada
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":idade", $this->idade);

        // Executa a query e retorna true se for bem-sucedida, senão retorna false
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para ler todos os registros da tabela 'pessoas'
    public function ler() {
        // Monta a query SQL para selecionar todos os registros ordenados por id
        $query = "SELECT id, nome, idade FROM " . $this->nome_tabela . " ORDER BY id ASC";
        // Prepara a query para execução
        $stmt = $this->conexao->prepare($query);
        // Executa a query
        $stmt->execute();
        // Retorna o statement para ser utilizado posteriormente (ex: fetch dos dados)
        return $stmt;
    }

    // Método para alterar a idade de uma pessoa pelo ID (POO: método da classe)
    public function alterarIdade($novoIdade) {
        // Monta a query SQL para atualizar a idade pelo id
        $query = "UPDATE " . $this->nome_tabela . " SET idade = :idade WHERE id = :id";
        // Prepara a query para execução
        $stmt = $this->conexao->prepare($query);

        // Limpa os dados recebidos para evitar XSS e SQL Injection
        $this->id = htmlspecialchars(strip_tags($this->id));
        $novoIdade = htmlspecialchars(strip_tags($novoIdade));

        // Faz o bind dos parâmetros id e idade na query preparada
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":idade", $novoIdade);

        // Executa a query e retorna true se for bem-sucedida, senão retorna false
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para deletar um registro da tabela 'pessoas' pelo ID
    public function deletar($id) {
        $query = "DELETE FROM " . $this->nome_tabela . " WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>