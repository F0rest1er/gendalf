<?php
    $servername = "localhost"; //Используется локальный сервер OpenServer
    $username = "root"; //Логин для входа в phpmyadmin 
    $password = ""; //Пароль не стоит
    $dbname = "gendalf_tz";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Проверка соединения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>