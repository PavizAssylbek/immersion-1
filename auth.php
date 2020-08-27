<?php
require_once "functions.php";


// если просто пришли напрямую на login.php, то не выодить никаких сообщений и вывести форму логина
// если пришли напрямую на login.php, то покажет сообщение о залогиненом пользователе без формы

// если введены почта и пароль, то проверка данных и процедура входа, переброска на основной файл main.php
// если почта или пароль неверные, то вывести  сообщение и снова форму
// если логин  или пароль пустые, то вывести сообщение и снова форму логина
if(isset($_POST["email"]) && isset($_POST["password"]))
{
    if (!empty($_POST["email"]) && !empty($_POST["password"])) {
        $result = check_credentials($_POST["email"], $_POST["password"]);

        if ($result) {
        set_logged();
        set_flash_message("logged_in", "Залогинен как ".$result["email"]);
        redirect_to("main");
        }
        else
        {
        set_flash_message("wrong_login_or_pass", "Логин или пароль неверны");
            redirect_to("login");
        }
    }
    else
    {
    set_flash_message("empy_login_or_pass", "Пустой логин или пароль");
        redirect_to("login");
    }
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
?>