<?php
// Incluir o arquivo de conexão
require_once 'conexao.php';

// Definição da classe Produto (POO: definição de classe)
class Produto
{
    private $id;
    private $nome;
    private $preco;
    private $quantidade;
    private $conexao;

    // Construtor agora recebe a conexão como parâmetro
    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // Métodos getter e setter (POO: encapsulamento com métodos de acesso)
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getPreco()
    {
        return $this->preco;
    }

    public function setPreco($preco)
    {
        $this->preco = $preco;
    }

    public function getQuantidade()
    {
        return $this->quantidade;
    }

    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;
    }

    // Método para inserir um novo produto (POO: método da classe)
    public function inserir()
    {
        try {
            $sql = "INSERT INTO produtos (nome, preco, quantidade) VALUES (:nome, :preco, :quantidade)";
            $stmt = $this->conexao->prepare($sql);
            
            // Bind dos parâmetros (POO: uso de prepared statements)
            $stmt->bindParam(':nome', $this->nome);
            $stmt->bindParam(':preco', $this->preco);
            $stmt->bindParam(':quantidade', $this->quantidade);
            
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $excecao) {
            echo "Erro ao inserir produto: " . $excecao->getMessage();
            return false;
        }
    }

    // Método para listar todos os produtos (POO: método da classe)
    public function listar()
    {
        try {
            $sql = "SELECT * FROM produtos ORDER BY id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $excecao) {
            echo "Erro ao listar produtos: " . $excecao->getMessage();
            return false;
        }
    }

    public function listarTodos() {
        $sql = "SELECT * FROM produtos";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para buscar um produto por ID (POO: método da classe)
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM produtos WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($produto) {
                $this->id = $produto['id'];
                $this->nome = $produto['nome'];
                $this->preco = $produto['preco'];
                $this->quantidade = $produto['quantidade'];
                return true;
            }
            return false;
        } catch (PDOException $excecao) {
            echo "Erro ao buscar produto: " . $excecao->getMessage();
            return false;
        }
    }

    // Método para atualizar um produto (POO: método da classe)
    public function atualizar()
    {
        try {
            $sql = "UPDATE produtos SET nome = :nome, preco = :preco, quantidade = :quantidade WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            
            // Bind dos parâmetros
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':nome', $this->nome);
            $stmt->bindParam(':preco', $this->preco);
            $stmt->bindParam(':quantidade', $this->quantidade);
            
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $excecao) {
            echo "Erro ao atualizar produto: " . $excecao->getMessage();
            return false;
        }
    }

    // Método para deletar um produto (POO: método da classe)
    public function deletar($id)
    {
        try {
            $sql = "DELETE FROM produtos WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $excecao) {
            echo "Erro ao deletar produto: " . $excecao->getMessage();
            return false;
        }
    }

    // Método para buscar produtos por nome (POO: método da classe)
    public function buscarPorNome($nome)
    {
        try {
            $sql = "SELECT * FROM produtos WHERE nome LIKE :nome ORDER BY id";
            $stmt = $this->conexao->prepare($sql);
            $nomeBusca = "%" . $nome . "%";
            $stmt->bindParam(':nome', $nomeBusca);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $excecao) {
            echo "Erro ao buscar produtos por nome: " . $excecao->getMessage();
            return false;
        }
    }

    // Método para contar total de produtos (POO: método da classe)
    public function contarTotal()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM produtos";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (PDOException $excecao) {
            echo "Erro ao contar produtos: " . $excecao->getMessage();
            return 0;
        }
    }

    // Método para validar dados do produto (POO: método de validação)
    public function validar()
    {
        $erros = [];
        
        if (empty($this->nome)) {
            $erros[] = "Nome é obrigatório";
        }
        
        if ($this->preco <= 0) {
            $erros[] = "Preço deve ser maior que zero";
        }
        
        if ($this->quantidade < 0) {
            $erros[] = "Quantidade não pode ser negativa";
        }
        
        return $erros;
    }
}

// Exemplo de uso da classe Produto
/*
// Criar uma instância da classe Produto
$produto = new Produto($conexao);

// Inserir um novo produto
$produto->setNome("Notebook");
$produto->setPreco(2500.00);
$produto->setQuantidade(10);

if ($produto->inserir()) {
    echo "Produto inserido com sucesso!";
} else {
    echo "Erro ao inserir produto!";
}

// Listar todos os produtos
$produtos = $produto->listar();
foreach ($produtos as $item) {
    echo "ID: " . $item['id'] . " - Nome: " . $item['nome'] . " - Preço: R$ " . $item['preco'] . " - Quantidade: " . $item['quantidade'] . "\n";
}

// Buscar produto por ID
$produto = new Produto($conexao);
if ($produto->buscarPorId(1)) {
    echo "Produto encontrado: " . $produto->getNome();
} else {
    echo "Produto não encontrado!";
}

// Atualizar produto
$produto->setNome("Mouse Gamer");
$produto->setPreco(150.00);
$produto->setQuantidade(5);

if ($produto->atualizar()) {
    echo "Produto atualizado com sucesso!";
} else {
    echo "Erro ao atualizar produto!";
}

// Deletar produto
if ($produto->deletar(1)) {
    echo "Produto deletado com sucesso!";
} else {
    echo "Erro ao deletar produto!";
}
*/