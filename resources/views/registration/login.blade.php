<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="{{ ('/css/bootstrap.min.css') }}">
    <style>
        .login_form{
            width: 400px; margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="login_form">
        <form class="form-signin" action="{{ url('auth') }}" method="post">
            {{ csrf_field() }}
            <h2 class="form-signin-heading">Авторизация</h2>
            <label for="login" class="sr-only">Логин</label>
            <input type="text" id="login" name="login" class="form-control" placeholder="Введите логин" required autofocus>
            <br>
            <label for="password" class="sr-only">Пароль</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Введите пароль" required>
            <div class="checkbox"> <label> <input type="checkbox" value="remember-me"> Запомнить меня? </label> </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
        </form>
        @if (Session::has('message'))
            <div style="margin-top: 15px;" class="alert alert-info">{{ Session::get('message') }}</div>
        @endif
    </div>
</body>
</html>