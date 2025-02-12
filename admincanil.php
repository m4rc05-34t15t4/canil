<?php
session_start();

// Verifica se a senha já foi enviada e está correta
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['senha'])) {
        if ($_POST['senha'] === 'canil123') {
            $_SESSION['logged_in'] = true;
            header("Location: " . $_SERVER['PHP_SELF']); // Redireciona para evitar reenvio do formulário
            exit;
        } else {
            $erro = "Senha incorreta!";
        }
    }

    // Exibe o formulário de login caso a senha ainda não tenha sido inserida corretamente
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Login Administração</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 300px;">
            <h3 class="text-center">Login</h3>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Senha:</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>';
    if (isset($erro)) {
        echo '<div class="alert alert-danger mt-2 text-center">' . $erro . '</div>';
    }
    echo '</div></body></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Diretório de armazenamento
    $uploadDir = 'images/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Função para processar upload de imagem
    function uploadImage($file, $name) {
        global $uploadDir;
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileType, ['jpg', 'jpeg', 'png'])) {
            echo "<div class='alert alert-danger'>Erro: Apenas arquivos .jpg e .png são permitidos para $name.</div>";
            return;
        }
        $targetFile = $uploadDir . $name . '.jpg'; // . $fileType;
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            echo "<div class='alert alert-success'>Imagem $name carregada com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao carregar imagem $name.</div>";
        }
    }
    
    // Upload da criadora
    if (!empty($_FILES['criadora']['name'])) {
        uploadImage($_FILES['criadora'], 'criadora');
    }
    
    // Upload do hotelzinho (3 imagens)
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($_FILES["hotelzinho$i"]["name"])) {
            uploadImage($_FILES["hotelzinho$i"], "hotelzinho$i");
        }
    }
    
    // Upload da galeria (9 imagens)
    for ($i = 1; $i <= 9; $i++) {
        if (!empty($_FILES["galeria$i"]["name"])) {
            uploadImage($_FILES["galeria$i"], "galeria$i");
        }
    }
    
    // Upload dos produtos (4 produtos)
    $produtos = [];
    for ($i = 1; $i <= 4; $i++) {
        if (!empty($_FILES["produto$i"]["name"])) {
            uploadImage($_FILES["produto$i"], "produto$i");
        }
        $produtos[] = [
            'nome' => $_POST["nome$i"] ?? '',
            'preco' => $_POST["preco$i"] ?? '',
            'descricao' => $_POST["descricao$i"] ?? ''
        ];
    }
    file_put_contents('produtos.json', json_encode($produtos));
    echo "<div class='alert alert-success'>Informações dos produtos salvas!</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Administração do Canil</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("produtos.json")
                .then(response => response.json())
                .then(produtos => {
                    produtos.forEach((produto, index) => {
                        $i = index + 1;
                        $(`#produto_${$i} .input-nome`).val(produto.nome);
                        $(`#produto_${$i} .input-preco`).val(produto.preco);
                        $(`#produto_${$i} .input-descricao`).val(produto.descricao);
                    });
                })
                .catch(error => console.error("Erro ao carregar os produtos:", error));
        });
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Paleta de Cores */
        :root {
            --marron-claro: #fcebe3;
            --cinza-gelo: #f8f9fa;
            --gradiente-sutil: linear-gradient(to top right,#ffffff, #e2c4af, #ffffff);
        }

        body {
            background: var(--gradiente-sutil);
            background-color: var(--gradiente-sutil);
            font-family: Arial, sans-serif;
        }
        .container {
            margin: auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="d-flex flex-rown align-items-center justify-content-center">
            <img src="images/logo_canil_t.png" alt="Logo do Canil" style="height: 150px;">
            <h2 class="text-center">Painel de Administração<br><i class="text-secondary">Canil Little Sun’s Rays</i></h2>
        </div>
        <form method="post" enctype="multipart/form-data">
            <div class="d-flex flex-column">
                <div class="d-flex flex-rown flex-wrap align-items-start justify-content-center">
                    <div class="m-2 me-4">
                        <div class="form-group mb-3">
                            <h4 class="text-center">Imagem da Criadora</h4>
                            <input type="file" class="form-control" name="criadora">
                        </div>

                        <hr>
                        
                        <h4 class="text-center">Galeria de Clientes (Máx: 9)</h4>
                        <?php for ($i = 1; $i <= 9; $i++): ?>
                            <div class="form-group mb-3">
                                <input type="file" class="form-control" name="galeria<?= $i ?>">
                            </div>
                        <?php endfor; ?>

                        <hr>
                        
                        <h4 class="text-center">Imagens do Hotelzinho (Máx: 3)</h4>
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <div class="form-group mb-3">
                                <input type="file" class="form-control" name="hotelzinho<?= $i ?>">
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="m-2" style="max-width: 800px">
                        <h4 class="text-center">Produtos (Máx: 4)</h4>
                        <div class="d-flex flex-rown flex-wrap">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div id="produto_<?= $i ?>" class="border p-3 mb-3">
                                    <h4>Produto <?= $i ?></h4>
                                    <div class="form-group">
                                        <input type="file" class="form-control" name="produto<?= $i ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Nome:</label>
                                        <input type="text" class="form-control input-nome" name="nome<?= $i ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Preço:</label>
                                        <input type="text" class="form-control input-preco" name="preco<?= $i ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Descrição:</label>
                                        <textarea class="form-control input-descricao" name="descricao<?= $i ?>"></textarea>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary fs-5">Salvar</button>
                </div>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
