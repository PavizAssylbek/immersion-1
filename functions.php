<?php

/**
 * Проверяет по email, зарегистрирован ли пользователь.@deprecated
 * @param string email
 * @return string email, или false если не зарегистрирован
 */
function is_registered($email)
{
    $driver = 'mysql';
    $host = 'localhost';
    $db_name = 'immersion';
    $db_user = 'immersion';
    $db_password = 'immersion';
    $charset = 'utf8';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

    $dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
    $pdo = new PDO($dsn, $db_user, $db_password, $options);

    $sql = 'SELECT email FROM register WHERE email = :email';
    $params = [
        ':email'  => $email,
    ];

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement->fetchColumn(); // возвращает false, если в базе нет совпадений или значение из таблицы
}

/**
 * Добавляет пользователя и пароль в базу, если такого еще нет.
 * @param string email
 * @param string password
 * @return null
 */
function add_user($email, $password)
{
    $driver = 'mysql';
    $host = 'localhost';
    $db_name = 'immersion';
    $db_user = 'immersion';
    $db_password = 'immersion';
    $charset = 'utf8';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

    $dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
    $pdo = new PDO($dsn, $db_user, $db_password, $options);

    $password = password_hash($password, PASSWORD_DEFAULT);
    $sql = 'INSERT INTO register (email, password) VALUE (:email, :password)';
    $params = [
        ':email'  => $email,
        ':password'  => $password,
    ];
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
}

/**
 * Добавляет пользователя и пароль в базу, если такого еще нет.
 * @param string email
 * @param string password
 * @return null
 */
function check_credentials($email, $password)
{
    $driver = 'mysql';
    $host = 'localhost';
    $db_name = 'immersion';
    $db_user = 'immersion';
    $db_password = 'immersion';
    $charset = 'utf8';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

    $dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
    $pdo = new PDO($dsn, $db_user, $db_password, $options);

    $sql = 'SELECT * FROM users WHERE email = :email';
    $params = [
        ':email'  => $email,
        //':password'  => $password,
    ];

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $result = $statement->fetch(); // возвращает false, если в базе нет совпадений или значение из таблицы

    if ($result["email"] && password_verify($password, $result["password"]))
    {
        return [
            "email" => $result["email"],
            "id" => $result["id"],
            "role" => $result["role"]
        ];
    }
    else
    {
        return false;
    }
}

/**
 * Добавляет пользователя и пароль в базу, если такого еще нет.
 * @param string email
 * @param string password
 * @return null
 */
function get_all_users()
{
    $driver = 'mysql';
    $host = 'localhost';
    $db_name = 'immersion';
    $db_user = 'immersion';
    $db_password = 'immersion';
    $charset = 'utf8';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

    $dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
    $pdo = new PDO($dsn, $db_user, $db_password, $options);

    $sql = 'SELECT * FROM users';

    $statement = $pdo->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(); //

}


/*
register.php
status  "already_registered" warning желтый Уведомление! Этот эл. адрес уже занят другим пользователем

login.php
status  "register_success" info  голубой Регистрация успешна
status "empy_login_or_pass" danger красный Пустой логин или пароль
status "wrong_login_or_pass" danger  красный Логин или пароль неверны
status "logged_in" success зеленый залогинен
*/
/**
 *
 */
function set_flash_message($status, $message)
{
    $_SESSION['status'] = $status;
    $_SESSION['message'] = $message;
}

function display_flash_message()
{
    if(isset($_SESSION["status"]))
    {
            switch($_SESSION["status"])
            {
                case  "already_registered": $color = "warning";
                    break;
                case  "register_success": $color = "info";
                    break;
                case  "empy_login_or_pass": $color = "danger";
                    break;
                case  "wrong_login_or_pass": $color = "danger";
                    break;
                case  "logged_in": $color = "success";
                    break;
            }

        echo '<div class="alert alert-'.$color.' text-dark" role="alert">
            '.$_SESSION["message"].'
          </div>';
        unset($_SESSION["status"]);
        unset($_SESSION["message"]);
    }
}

function clear_display_message()
{
    unset($_SESSION["status"]);
    unset($_SESSION["message"]);
}
function set_logged($data) // хранит массив  со всеми данными о  пользователе, кроме пароля
{
    $_SESSION["logged_in"] = $data;
}
function is_logged()
{
    if(isset($_SESSION["logged_in"]))
        return true;
    else
        return false;
}

function is_admin()
{
    if(isset($_SESSION["logged_in"]))
    {
        if($_SESSION["logged_in"]["role"] == 0) // 0 - админ, 1 и выше - пользователи разных уровней доступа
            return true;
        else
            return false;
    }
    return false;
}

function is_me()
{
    if(isset($_SESSION["logged_in"]))
        return $_SESSION["logged_in"]["id"];
    else
        return false;
}

function logout()
{
  unset($_SESSION["logged_in"]);
  unset($_SESSION["status"]);
  redirect_to("login");
}

function redirect_to($path)
{
    header('Location: '.$path.'.php');
}

?>