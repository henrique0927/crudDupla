<?php
include "database.php";

// Lógica para criação
if (isset($_POST['create'])) {
    $nome_professor = $_POST['nome_professor'];
    $sala = $_POST['sala'];
    $hora_aula = $_POST['hora_aula'];

    // Inserir novo professor na tabela "professores"
    $stmt = $conn->prepare("INSERT INTO professores (nome_professor) VALUES (?)");
    $stmt->bind_param("s", $nome_professor);
    $stmt->execute();
    $professor_id = $conn->insert_id;

    // Inserir nova sala na tabela "aulas"
    $stmt = $conn->prepare("INSERT INTO aulas (sala) VALUES (?)");
    $stmt->bind_param("s", $sala);
    $stmt->execute();
    $aula_id = $conn->insert_id;

    // Inserir na tabela "dia_hora"
    $stmt = $conn->prepare("INSERT INTO dia_hora (professor_id, aula_id, hora_aula) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $professor_id, $aula_id, $hora_aula);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

// Lógica para atualização
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nome_professor = $_POST['nome_professor'];
    $sala = $_POST['sala'];
    $hora_aula = $_POST['hora_aula'];

    // Atualizar o nome do professor
    $stmt = $conn->prepare("UPDATE professores SET nome_professor=? WHERE professor_id=(SELECT professor_id FROM dia_hora WHERE id=?)");
    $stmt->bind_param("si", $nome_professor, $id);
    $stmt->execute();

    // Atualizar o nome da sala
    $stmt = $conn->prepare("UPDATE aulas SET sala=? WHERE aula_id=(SELECT aula_id FROM dia_hora WHERE id=?)");
    $stmt->bind_param("si", $sala, $id);
    $stmt->execute();

    // Atualizar a hora da aula
    $stmt = $conn->prepare("UPDATE dia_hora SET hora_aula=? WHERE id=?");
    $stmt->bind_param("si", $hora_aula, $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

// Lógica para exclusão
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Remover a entrada da tabela "dia_hora"
    $stmt = $conn->prepare("DELETE FROM dia_hora WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Pedidos</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Sistema de Pedidos</h1>
        
        <!-- formulário para criar novo pedido -->
        <form action="index.php" method="POST">
            <input type="text" name="nome_professor" placeholder="Nome do Professor" required>
            <input type="text" name="sala" placeholder="Sala" required>
            <input type="datetime-local" name="hora_aula" required>
            <button type="submit" name="create">Criar Pedido</button>
        </form>

        <!-- lista de pedidos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Professor</th>
                    <th>Sala</th>
                    <th>Dia Hora</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // puxar os pedidos "READ"
                $sql = "SELECT dh.id, p.nome_professor, a.sala, dh.hora_aula 
                        FROM dia_hora dh 
                        JOIN professores p ON dh.professor_id = p.professor_id 
                        JOIN aulas a ON dh.aula_id = a.aula_id";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td class="td-data"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['nome_professor']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['sala']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['hora_aula']); ?></td>
                    <td>
                        <!-- formulário para atualizar pedido -->
                        <form action="index.php" method="POST" style="display:inline; margin:auto;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="text" name="nome_professor" value="<?php echo htmlspecialchars($row['nome_professor']); ?>" required>
                            <input type="text" name="sala" value="<?php echo htmlspecialchars($row['sala']); ?>" required>
                            <input type="datetime-local" name="hora_aula" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($row['hora_aula']))); ?>" required>
                            
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
</html>
