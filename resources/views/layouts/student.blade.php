<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Platonus | Отчеты</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ ('/css/bootstrap.min.css') }}">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
{{--<link href="{{ ('/css/ie10-viewport-bug-workaround.css') }}" rel="stylesheet">--}}

<!-- Custom styles for this template -->
    <link href="{{ ('/css/dashboard.css') }}" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ ('/semantic/semantic.min.css') }}">
    {{--<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">--}}
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand">{{ \App\User::getFullName($_SESSION['id_tutor']) }} | Роль в системе: {{ $_SESSION['role'] }}</span>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li @if($_SERVER['REQUEST_URI'] == '/user') class="active" @endif><a href="{{ url('user') }}">Главная</a></li>
                <li><a href="{{ url('user/logout') }}">Выход</a></li>
            </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <div style="margin-top: 50px;">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async src="{{ ('/js/jquery-3.1.1.min.js') }}"></script>
<!--<script type="text/javascript" async src="{{ ('/js/bootstrap.min.js') }}"></script>-->
<script type="text/javascript" async src="{{ ('/semantic/semantic.min.js') }}"></script>
{{--<script async src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>--}}
<!-- Just to make our placeholder images work. Don't actually copy the next line! -->
{{--<script async src="{{ ('/js/holder.min.js') }}"></script>--}}
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
{{--<script async src="{{ ('/js/ie10-viewport-bug-workaround.js') }}"></script>--}}
<script async src="{{ ('/js/app.js') }}"></script>
<!--<script type="text/javascript">
    $.fn.bsModal = $.fn.modal.noConflict();
</script>-->
</body>
</html>