<?php
include "database.php";

?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CRUD em Dupla</title>
    <link rel="stylesheet" href="./css/style.css" />
  </head>
  <body>
    <main>
      <div class="form">
        <form action=""></form>
        <?php





$sql = "CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT,
    name VARCHAR(255),
    PRIMARY KEY (id)
)";
$conn->query($sql);


$sql = "SELECT * FROM items";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Actions</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td><button onclick='deleteItem(" . $row["id"] . ")'>Delete</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No data found.";
}

if (isset($_POST["add"])) {
    $name = $_POST["name"];
    $sql = "INSERT INTO items (name) VALUES ('$name')";
    $conn->query($sql);
    header("Location: index.php");
    exit;
}


if (isset($_POST["delete"])) {
    $id = $_POST["id"];
    $sql = "DELETE FROM items WHERE id = $id";
    $conn->query($sql);
    header("Location: index.php");
    exit;
}

$conn->close();
?>


<form action="index.php" method="post">
    <input type="text" name="name" placeholder="Enter item name">
    <button type="submit" name="add">Add</button>
</form>


<script>
    function deleteItem(id) {
        if (confirm("Are you sure you want to delete this item?")) {
            var form = document.createElement("form");
            form.method = "post";
            form.action = "index.php";
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "id";
            input.value = id;
            form.appendChild(input);
            var input2 = document.createElement("input");
            input2.type = "hidden";
            input2.name = "delete";
            input2.value = "true";
            form.appendChild(input2);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
      </div>
    </main>
  </body>
</html>
