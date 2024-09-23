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

    // inserir nova sala na tabela "aulas"
    $stmt = $conn->prepare("INSERT INTO aulas (sala) VALUES (?)");
    $stmt->bind_param("s", $sala);
    $stmt->execute();
    $aula_id = $conn->insert_id;

    // inserir na tabela "dia_hora"
    $stmt = $conn->prepare("INSERT INTO dia_hora (professor_id, aula_id, hora_aula) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $professor_id, $aula_id, $hora_aula);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

// UPDATE
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
                // puxar os pedidos READ
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
                                <a href="index.php?delete=<?php echo htmlspecialchars($row['id']); ?>">
                                <button class="button">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 69 14"
                                    class="svgIcon bin-top"
                                >
                                    <g clip-path="url(#clip0_35_24)">
                                    <path
                                        fill="black"
                                        d="M20.8232 2.62734L19.9948 4.21304C19.8224 4.54309 19.4808 4.75 19.1085 4.75H4.92857C2.20246 4.75 0 6.87266 0 9.5C0 12.1273 2.20246 14.25 4.92857 14.25H64.0714C66.7975 14.25 69 12.1273 69 9.5C69 6.87266 66.7975 4.75 64.0714 4.75H49.8915C49.5192 4.75 49.1776 4.54309 49.0052 4.21305L48.1768 2.62734C47.3451 1.00938 45.6355 0 43.7719 0H25.2281C23.3645 0 21.6549 1.00938 20.8232 2.62734ZM64.0023 20.0648C64.0397 19.4882 63.5822 19 63.0044 19H5.99556C5.4178 19 4.96025 19.4882 4.99766 20.0648L8.19375 69.3203C8.44018 73.0758 11.6746 76 15.5712 76H53.4288C57.3254 76 60.5598 73.0758 60.8062 69.3203L64.0023 20.0648Z"
                                    ></path>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_35_24">
                                        <rect fill="white" height="14" width="69"></rect>
                                    </clipPath>
                                    </defs>
                                </svg>

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 69 57"
                                    class="svgIcon bin-bottom"
                                >
                                    <g clip-path="url(#clip0_35_22)">
                                    <path
                                        fill="black"
                                        d="M20.8232 -16.3727L19.9948 -14.787C19.8224 -14.4569 19.4808 -14.25 19.1085 -14.25H4.92857C2.20246 -14.25 0 -12.1273 0 -9.5C0 -6.8727 2.20246 -4.75 4.92857 -4.75H64.0714C66.7975 -4.75 69 -6.8727 69 -9.5C69 -12.1273 66.7975 -14.25 64.0714 -14.25H49.8915C49.5192 -14.25 49.1776 -14.4569 49.0052 -14.787L48.1768 -16.3727C47.3451 -17.9906 45.6355 -19 43.7719 -19H25.2281C23.3645 -19 21.6549 -17.9906 20.8232 -16.3727ZM64.0023 1.0648C64.0397 0.4882 63.5822 0 63.0044 0H5.99556C5.4178 0 4.96025 0.4882 4.99766 1.0648L8.19375 50.3203C8.44018 54.0758 11.6746 57 15.5712 57H53.4288C57.3254 57 60.5598 54.0758 60.8062 50.3203L64.0023 1.0648Z"
                                    ></path>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_35_22">
                                        <rect fill="white" height="57" width="69"></rect>
                                    </clipPath>
                                    </defs>
                                </svg>
                                </button></a>
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
