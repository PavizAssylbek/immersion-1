<?php
/*
register.php
status  "already_registered" warning желтый Уведомление! Этот эл. адрес уже занят другим пользователем

login.php
status  "register_success" info  голубой Регистрация успешна
status "empy_login_or_pass" danger красный Пустой логин или пароль
status "wrong_login_or_pass" danger  красный Логин или пароль неверны
status "logged_in" success зеленый залогинен
*/

if (isset($_POST['email']) && $_POST['password'])
{

    if(is_registered($_POST['email']))
    {
        set_flash_message("already_registered", "Уведомление! Этот эл. адрес уже занят другим пользователем.");
        redirect_to("register");
    }
    else
    {
        add_user($_POST['email'], $_POST['password']);
        set_flash_message("register_success", "Регистрация успешна");
        redirect_to("login");
    }

}


?>