<?php
include "database.php";

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<body>
    <div class="container">
        <h1>Sistema de Pedidos</h1>
        
        <!-- formulário para criar novo pedido -->
        <form action="index.php" method="POST">
            <input type="text" name="nome_cliente" placeholder="Nome do Cliente" required>
            <input type="text" name="nome_produto" placeholder="Nome do Produto" required>
            <input type="number" name="quantidade" placeholder="Quantidade" required>
            <input type="date" name="data_pedido" placeholder="Data do Pedido" required>
            <button type="submit" name="create">Criar Pedido</button>
        </form>

        <!-- lista de pedidos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Cliente</th>
                    <th>Nome do Produto</th>
                    <th>Quantidade</th>
                    <th>Data do Pedido</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // puxar os pedidos "READ"
                $sql = "SELECT * FROM pedidos";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td class="td-data"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['nome_cliente']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['nome_produto']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['quantidade']); ?></td>
                    <td class="data-pedido"><?php echo htmlspecialchars($row['data_pedido']); ?></td>
                    <td>
                        <!-- formulário para atualizar pedido -->
                        <form action="index.php" method="POST" style="display:inline; margin:auto;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="text" name="nome_cliente" value="<?php echo htmlspecialchars($row['nome_cliente']); ?>" required>
                            <input type="text" name="nome_produto" value="<?php echo htmlspecialchars($row['nome_produto']); ?>" required>
                            <input type="number" name="quantidade" value="<?php echo htmlspecialchars($row['quantidade']); ?>" required>
                            <input type="date" name="data_pedido" value="<?php echo htmlspecialchars($row['data_pedido']); ?>" required>
                            <div id="botao-junto">
                                <button type="submit" name="update">Atualizar</button>
                                <!-- botão para excluir pedido -->
                                <a href="index.php?delete=<?php echo htmlspecialchars($row['id']); ?>" class="delete">Excluir</a>
                            </div>
                            
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</body>
</html>
