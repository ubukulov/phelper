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

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><!--<script src="{{ ('/js/ie8-responsive-file-warning.js') }}"></script>--><![endif]-->
    {{--<script src="{{ ('/js/ie-emulation-modes-warning.js') }}></script>--}}

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <!--<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>-->
    <!--<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>-->
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="{{ ('/semantic/semantic.min.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
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
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                {{--<li><a href="#">Dashboard</a></li>--}}
                {{--<li><a href="#">Settings</a></li>--}}
                {{--<li><a href="#">Profile</a></li>--}}
                {{--<li><a href="#">Help</a></li>--}}
                {{--<li><a href="{{ url('user/logout') }}">Выход</a></li>--}}
            </ul>
            {{--<form class="navbar-form navbar-right">--}}
                {{--<input type="text" class="form-control" placeholder="Search...">--}}
            {{--</form>--}}
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li @if($_SERVER['REQUEST_URI'] == '/rectorate') class="active" @endif><a href="{{ url('rectorate') }}">Отчеты</a></li>

                <li><a href="{{ url('user/logout') }}">Выход</a></li>
            </ul>
        </div>
        <div>
            @yield('content')
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ ('/js/jquery-3.1.1.min.js') }}"></script>
<script type="text/javascript" src="{{ ('/semantic/semantic.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
{{--<script type="text/javascript" src="{{ ('/js/bootstrap.min.js') }}"></script>--}}
<!-- Just to make our placeholder images work. Don't actually copy the next line! -->
<script src="{{ ('/js/holder.min.js') }}"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="{{ ('/js/ie10-viewport-bug-workaround.js') }}"></script>
<script src="{{ ('/js/app.js') }}"></script>
</body>
</html>