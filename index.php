<?php

require_once 'db.php';

// Обработка формы входа
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = md5($_POST["password"]);

    // Подготовленный запрос для проверки данных пользователя
    $stmt = $conn->prepare("SELECT u.login, u.password, g.name 
                           FROM users u
                           JOIN groups g ON u.group_id = g.id
                           WHERE u.login = ? AND g.name = 'admin'");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row["password"] == $password) {
            echo "Вход выполнен успешно!";
            // Перенаправление на страницу управления категориями
            header("Location: function.php");
            exit;
        } else {
            echo "Неправильный логин или пароль.";
        }
    } else {
        echo "Неправильный логин или пароль.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Форма входа администратора</title>
</head>
<body>
    <h1>Форма входа администратора</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SCRIPT"]);?>">
        Логин: <input type="text" name="login"><br>
        Пароль: <input type="password" name="password"><br>
        <input type="submit" name="submit" value="Войти">
    </form>
</body>
</html>