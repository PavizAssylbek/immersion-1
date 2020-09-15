<?php

/**
 * Проверяет по email, зарегистрирован ли пользователь.
 * @param string email
 * @return string email, или false если не зарегистрирован
 */
function is_user_registered($email)
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

    $sql = 'SELECT email FROM users WHERE email = :email';
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
function add_user()
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

// заменяем в пришедшем массиве из формы пароль на хеш пароля
    $form_data = $_POST;
    $replacements = array("password" => password_hash($form_data["password"], PASSWORD_DEFAULT));
    $form_data = array_replace($form_data, $replacements);

    // автоматическая подготовка всех полей из формы для sql запроса
    $_sql = array();
    $sql = "INSERT INTO `users` SET ";
    foreach ($form_data as $name => $value) {
        $_sql[] = "`" . $name . "` = :" . $name;
    }

    $sql = $sql . implode(', ', $_sql);
    $stmt = $pdo->prepare($sql);

    foreach ($form_data as $name => $value) {
        $stmt->bindValue(':' . $name, $value);
    }
    $stmt->execute();

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
            "role" => $result["role"],
            "name" => $result["name"]
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
    return $statement->fetchAll();
    //$result = $statement->fetchAll(); //
    //return array_replace($result, array("id" => (int) $result["id"], "role" => (int) $result["role"]));
    //return array_walk_recursive($result, 'change_to_int');
}

function change_to_int($value, $key)
{
    //$key["id"] = (int)
}

/**
 *
 */
function set_flash_message($status, $message)
{
    $_SESSION['status'] = $status;
    $_SESSION['message'] = $message;
}

/**
 *
 */
function display_flash_message()
{
    if (isset($_SESSION["status"]))
    {
        switch($_SESSION["status"])
        {
            case  "yellow": $color = "warning";
                break;
            case  "blue": $color = "info";
                break;
            case  "red": $color = "danger";
                break;
            case  "green": $color = "success";
                break;
        }

        echo '<div class="alert alert-'.$color.' text-dark" role="alert">
            '.$_SESSION["message"].'
          </div>';
        unset($_SESSION["status"]);
        unset($_SESSION["message"]);
    }
}


function set_logged($data) // хранит массив  со всеми данными о  пользователе, кроме пароля
{
    $_SESSION["logged_in"] = $data;
}
function is_logged()
{
    if(isset($_SESSION["logged_in"]["id"]))
        return true;
    else
        return false;
}

function is_admin()
{
    if(isset($_SESSION["logged_in"]))
    {
        if($_SESSION["logged_in"]["role"] === '0') // 0 - админ, 1 и выше - пользователи разных уровней доступа
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
    set_flash_message("yellow","Вы вышли");
    unset($_SESSION["logged_in"]);
    unset($_SESSION["editid"]);
    redirect_to("login");
}

/**
 *
 */
function redirect_to($path)
{
    header('Location: '.$path.'.php');
    exit;
}

function is_author($edit_user_id)
{
    if($_SESSION["logged_in"]["id"] === $edit_user_id)
        return $_SESSION["logged_in"]["id"];
    else
        return false;
}


function get_user_by_id($id)
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
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $sql = 'SELECT * FROM users WHERE id = :id';
    $params = [
        ':id'  => $id,
    ];

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement->fetch();
    //$result= $statement->fetch(); // возвращает false, если в базе нет совпадений или массив
    //return array_replace($result, array("id" => (int) $result["id"], "role" => (int) $result["role"]));
}

function edit_info($id, $name, $job, $tel, $adress)
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

    $sql = 'UPDATE users SET name = :name, job = :job, tel = :tel, adress = :adress WHERE id = :id';
    $params = [
        ':id'  => $id,
        ':name'  => $name,
        ':job'  => $job,
        ':tel'  => $tel,
        ':adress'  => $adress,
    ];

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    //return $statement->fetchColumn(); // возвращает false, если в базе нет совпадений или значение из таблицы
}

function set_status($id)
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

    $sql = 'UPDATE users SET status = :status WHERE id = :id';
    $params = [
        ':id'  => $id,
        ':status'  => $_POST["status"]
    ];

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
}

function delete_user($id)
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

    $sql = 'DELETE FROM users WHERE id = :id';
    $params = [
        ':id'  => $id,
    ];

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
}

function edit_credentials($user_id, $email, $password)
{
    $pass = password_hash($password, PASSWORD_DEFAULT);

    $driver = 'mysql';
    $host = 'localhost';
    $db_name = 'immersion';
    $db_user = 'immersion';
    $db_password = 'immersion';
    $charset = 'utf8';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

    $dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
    $pdo = new PDO($dsn, $db_user, $db_password, $options);

    $sql = 'UPDATE users SET email = :email, password = :password WHERE id = :id';
    $params = [
        ':id'  => $user_id,
        ':email'  => $email,
        ':password' => $pass
    ];

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
}
//var_dump(edit_info(2, "aaa", "sdf", "234324", "bbb"));
//var_dump(edit_credentials(3, "sdfd@dsff.aaa", "bbb"));
?>

