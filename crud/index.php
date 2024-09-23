<?php
include "database.php";
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
            <!-- Selecionar professor -->
            <select name="professor_id" required>
                <option value="">Selecione o Professor</option>
                <?php
                $sql_professores = "SELECT * FROM professores";
                $result_professores = $conn->query($sql_professores);
                while ($professor = $result_professores->fetch_assoc()):
                ?>
                    <option value="<?php echo $professor['id']; ?>"><?php echo htmlspecialchars($professor['nome']); ?></option>
                <?php endwhile; ?>
            </select>
            
            <!-- Selecionar sala -->
            <select name="aula_id" required>
                <option value="">Selecione a Sala</option>
                <?php
                $sql_aulas = "SELECT * FROM aulas";
                $result_aulas = $conn->query($sql_aulas);
                while ($aula = $result_aulas->fetch_assoc()):
                ?>
                    <option value="<?php echo $aula['id']; ?>"><?php echo htmlspecialchars($aula['sala']); ?></option>
                <?php endwhile; ?>
            </select>
            
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
                $sql = "SELECT dh.id, p.nome AS nome_professor, a.sala AS nome_sala, dh.hora_aula 
                        FROM dia_hora dh 
                        JOIN professores p ON dh.professor_id = p.id 
                        JOIN aulas a ON dh.aula_id = a.id";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td class="td-data"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['nome_professor']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['nome_sala']); ?></td>
                    <td class="td-data"><?php echo htmlspecialchars($row['hora_aula']); ?></td>
                    <td>
                        <!-- formulário para atualizar pedido -->
                        <form action="index.php" method="POST" style="display:inline; margin:auto;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            
                            <!-- Selecionar novo professor -->
                            <select name="professor_id" required>
                                <?php
                                $sql_professores = "SELECT * FROM professores";
                                $result_professores = $conn->query($sql_professores);
                                while ($professor = $result_professores->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $professor['id']; ?>" <?php echo $professor['id'] == $row['professor_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($professor['nome']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            
                            <!-- Selecionar nova sala -->
                            <select name="aula_id" required>
                                <?php
                                $sql_aulas = "SELECT * FROM aulas";
                                $result_aulas = $conn->query($sql_aulas);
                                while ($aula = $result_aulas->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $aula['id']; ?>" <?php echo $aula['id'] == $row['aula_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($aula['sala']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            
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

<?php
// Lógica para criação
if (isset($_POST['create'])) {
    $professor_id = $_POST['professor_id'];
    $aula_id = $_POST['aula_id'];
    $hora_aula = $_POST['hora_aula'];

    $sql_insert = "INSERT INTO dia_hora (professor_id, aula_id, hora_aula) VALUES ('$professor_id', '$aula_id', '$hora_aula')";
    $conn->query($sql_insert);
    header("Location: index.php");
}

// Lógica para atualização
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $professor_id = $_POST['professor_id'];
    $aula_id = $_POST['aula_id'];
    $hora_aula = $_POST['hora_aula'];

    $sql_update = "UPDATE dia_hora SET professor_id='$professor_id', aula_id='$aula_id', hora_aula='$hora_aula' WHERE id='$id'";
    $conn->query($sql_update);
    header("Location: index.php");
}

// Lógica para exclusão
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql_delete = "DELETE FROM dia_hora WHERE id='$id'";
    $conn->query($sql_delete);
    header("Location: index.php");
}
?>
