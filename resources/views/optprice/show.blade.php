<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->
<head>
    <title>Likemoney.me</title>
    <!-- meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name='B-verify' content='14e5fe9055dc191ce5cb7c8dfe70ace82b9383bd' />
    <meta name="verify-admitad" content="bcfc76fe10" />

    <!-- stylesheets -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/animate.css">
    <link rel="stylesheet" href="/css/owl.carousel.css">
    <link rel="stylesheet" href="/css/owl.theme.css">
    <link rel="stylesheet" href="/css/style.css">
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">-->
    <link href="/css/ionicons.min.css" rel="stylesheet" type="text/css">

    <!-- end of /#multiple-blog-page -->
    <script type="text/javascript" src="/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap.min.js"></script>
    <!-- script for FAQ using jquery -->
    <script src="/js/ajaxupload.js"></script>

    <script src="/js/countdown.js?1" type="text/javascript"></script>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>


    <script src="/js/jquery.maskedinput-1.3.1.js"></script>


    <link rel="stylesheet" href="/signin.css">
    <link rel="stylesheet" href="/css/mystyles.css">



</head>
<body class="">
<div id="single-blog-left-sidebar" class="">
    <!-- site-navigation start -->
    <nav id="mainNavigation" class="navbar navbar-dafault main-navigation" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="navbar-brand">
                    <span class="sr-only">Likemoney.me</span>
                    <a href="index">
                        <img src="{{ asset('img/opt_price_logo_red.png') }}" class="img-responsive center-block" alt="logo">
                    </a>
                </div>
                <!-- navbar logo -->
                <!-- navbar logo -->
            </div>
            <!-- /.navbar-header -->
            <!-- nav links -->
            <div class="collapse navbar-collapse" id="main-nav-collapse">
                <ul class="nav navbar-nav navbar-right text-uppercase">
                    <li>
                        <a href="index"><span>О НАС</span></a>
                    </li>
                    <li class="dropdown">
                        <a href="leading_2" class="dropdown-toggle" ><span>компаниям</span></a>
                    </li>
                    <li class="dropdown">
                        <a href="leading_1" class="dropdown-toggle"><span>Пользователям</span></a>
                    </li>
                    <li>
                    </li>
                </ul>
                <div class="loginbutton loginbutton768">

                    @if(Auth::check())
                    <a style="color: #fff; cursor: pointer;" href="{{ url('user/account') }}">
                        @if (!empty(Auth::user()->avatar))
                            <img class="my_avatar_small" src="{{ asset('uploads/users/small/'.Auth::user()->avatar) }}" alt="user-photo">
                        @else
                            <img class="my_avatar_small" src="{{ asset('img/blank_avatar_220.png') }}" alt="user-photo">
                        @endif
                        &nbsp;
                        {{ Auth::user()->firstname }}
                        &nbsp;
                        <font color="#619F05">{{ __decode(Auth::user()->fm,env('KEY')) }} тг</font>
                    </a>
                    <br>
                    @else
                        <a style="color: #fff; cursor: pointer;" data-toggle="modal" data-target="#loginModal">
                            <i class="fa fa-male"></i>&nbsp;&nbsp;ЛИЧНЫЙ КАБИНЕТ
                        </a>
                        <br>
                    @endif
                    <!-- /.nav -->
                </div>
                <!-- /.navbar-collapse -->
                <!-- nav links -->
            </div>
            <!-- /.container -->
    </nav>
    <!-- site-navigation end -->

    @include('pattern/__modal_auth')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script>
        $(function () {
            $("#countdown-example").countdown(new Date(2017,10,03,00,00,00), 1);
        })

    </script>


    <script type="text/javascript" src="{{ asset('lib/reating/bootstrap-rating.js') }}"></script>
    <link href="{{ asset('lib/reating/bootstrap-rating.css') }}" rel="stylesheet" />

    <script>

        $('.set_reating').rating();
    </script>

    <script>
        $( function() {
            $( "#tabs" ).tabs();
        } );
    </script>

    <!-- header begin -->
    <header class="page-head">
        <div class="header-wrapper">
            <div class="container">
                <div class="row">

                    <div class="col-md-3 col-xs-4">
                        @if(!empty($partner->logo))
                        <img class="my_avatar" src="{{ asset('uploads/opt_price/partners/small/'.$partner->logo) }}" width="150" height="150" alt="">
                        @else
                        <img class="my_avatar" src="admin/images/blank_avatar_220.png" width="150" height="150" alt="">
                        @endif
                    </div>
                    <div class="col-md-9">
                        <font style="color:#FFF;"><h2>{{ $partner->title }}</h2><font style="font-size:20px;">Статус: <b>Компания - партнер</b></font>&nbsp;<font color="#C63B3C"><i class="fa fa-question-circle"></i></font></font>
                        <br />&nbsp;
                        <div class="p_header">
                            {!! htmlspecialchars_decode($partner->features) !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container -->
        </div>
        <!-- /.header-wrapper -->
    </header>
    <style>
        .p_header > p{
            color: #fff;
        }
    </style>
    <!-- /.page-head (header end) -->

    <!-- Modal -->
    <div class="modal fade" id="taskSubs" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Основные прайсы</h4>
                </div>
                <div class="modal-body">
                    <table class="table-tsk" width="100%">
                        <tbody>
                        <tr  style="border-bottom:1px solid #d7d7d7">
                            <td><b style="font-size: 14px">Наименование:</b></td>
                            <td><b style="font-size: 14px">Кол-во видов:</b></td>
                            <td><b style="font-size: 14px">Кол-во покупок:</b></td>
                            <td><b style="font-size: 14px">Файл:</b></td>
                        </tr>

                        @foreach ($opt_main as $opt_price_main)
                        <tr  style="border-bottom:1px solid #d7d7d7">
                            <td><?= $opt_price_main->title ?></td>
                            <td style="text-align: center;">
                                <?= $opt_price_main->count_type;?>
                            </td>
                            <td>

                            </td>
                            <td>
                                <a href="{{ asset('/uploads/opt_price/files/'.$opt_price_main->file) }}" target="_blank">
                                    <button type="button" class="playbutton" style="padding: 2px 10px; border: solid 1px #0099CC;">
                                        Посмотреть
                                    </button>
                                </a>
                            </td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="taskbutton" data-dismiss="modal">Закрыть</button>
                </div>
            </div>

        </div>
    </div>


    <section class="blog-content">
        <div class="container">
            <div class="row">
                <main class="col-md-9 col-md-push-3" style="display: block;">
                    <article class="blog-item">

                        <div class="rowtsk">
                            <div class="blog-heading">
                                <h3>{{ $partner->title }}</h3>
                            </div>
                            <hr />
                            <table class="table-tsk">
                                <tr>
                                    <th rowspan="6">
                                        <div style="width: 400px" id="myCarousel" class="carousel slide" data-ride="carousel">

                                            <!-- Wrapper for slides -->
                                            <div class="carousel-inner" role="listbox">
                                                <div class="item active">
                                                    <img width="400" src="{{ asset('uploads/opt_price/partners/'.$partner->image1) }}" alt="foto">
                                                </div>

                                                <div class="item">
                                                    <img width="400" src="{{ asset('uploads/opt_price/partners/'.$partner->image2) }}" alt="foto">
                                                </div>

                                                <div class="item">
                                                    <img width="400" src="{{ asset('uploads/opt_price/partners/'.$partner->image3) }}" alt="foto">
                                                </div>

                                            </div>

                                            <!-- Left and right controls -->
                                            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>

                                    </th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td><font style="color:#619F05"><i class="fa fa-credit-card fa-2"></i></font>&nbsp;&nbsp;Ассортимент:<br /><font style="font-family: ubuntu; font-size: 20px; font-weight: 600; color:#619F05">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $partner->assortment }}</font></td>
                                </tr>
                                <tr>
                                    <td><font style="color:#d7d7d7"> <i class="fa fa-clock-o fa-2"></i></font>&nbsp;&nbsp;До окончания осталось:<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="font-weight: 600; text-decoration: underline" id="countdown-example">

                                        </font> </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom:1px solid #d7d7d7">
                                        <button type="button" class="playbutton" data-toggle="modal" data-target="#taskSubs">
                                            <i class="fa fa-play fa-2"></i>&nbsp;&nbsp;&nbsp;Посмотреть прайсы
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="line-height:2em; border-bottom:1px solid #d7d7d7"> <font style="color:#d7d7d7"> <i class="fa fa-thumbs-up fa-2"></i></font>&nbsp;&nbsp;Кол-во покупов: 0 чел.<br /><font style="color:#d7d7d7"><i class="fa fa-eye fa-2"></i></font>&nbsp;&nbsp;Кол-во просмотров: {{ $partner->views }} чел.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <script type="text/javascript">(function () {
                                                if (window.pluso)
                                                    if (typeof window.pluso.start == "function")
                                                        return;
                                                if (window.ifpluso == undefined) {
                                                    window.ifpluso = 1;
                                                    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
                                                    s.type = 'text/javascript';
                                                    s.charset = 'UTF-8';
                                                    s.async = true;
                                                    s.src = ('https:' == window.location.protocol ? 'https' : 'http') + '://share.pluso.ru/pluso-like.js';
                                                    var h = d[g]('body')[0];
                                                    h.appendChild(s);
                                                }
                                            })();</script>
                                        <div class="pluso" data-background="#ebebeb" data-options="medium,square,line,horizontal,counter,theme=04" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir"></div>

                                    </td>
                                </tr>
                            </table>
                            <div id="tabs">
                                <ul>
                                    <li><a href="#tabs-1">Условия</a></li>
                                    <li><a href="#tabs-2">Ассортимент</a></li>
                                    <li><a href="#tabs-3">Описание</a></li>
                                </ul>
                                <div id="tabs-1">
                                    {!! htmlspecialchars_decode($partner->conditions) !!}
                                </div>
                                <div id="tabs-2">
                                    <div class="row">
                                    @foreach ($opt_range as $range)
                                        <div class="col-sm-4 col-xs-6">
                                            <div class="brd">
                                                <div class="portfolio-item">
                                                    <!-- /.portfolio-img -->
                                                    <div class="portfolio-img" style="height: 140px; cursor: pointer;">
                                                        <img src="{{ asset('uploads/opt_price/range/small/'.$range->photo)  }}" alt="port-1" class="port-item">
                                                        <div class="portfolio-img-hover">

                                                        </div>
                                                        <!-- /.portfolio-img-hover -->
                                                    </div>
                                                    <div class="portfolio-item-details">
                                                        <div class="portfolio-item-name">{{ $range->title }}</div>
                                                        <!-- /.portfolio-item-name -->
                                                        <div style="float: left;">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <td align="center"><font color="#62A005" size="4"><i class="fa fa-credit-card-alt"></i></font></td>
                                                                    <td style="padding-left:7px; line-height: 15px;">
                                                                        @if($range->price_range == '1')
                                                                        <small>Цена: от <br><font color="#62A005"><b>{{ $range->price_range_val }}</b></font></small>
                                                                        @else
                                                                        <small>Ассортимент: <br><font color="#62A005"><b>{{ $range->price_range_val }}</b></font></small>
                                                                        @endif
                                                                    </td>
                                                                    <td style="padding-left:15px;" align="center">
                                                                        <a href="task?id=<?= $range->id ?>" class="hidden-xs taskbutton">Подробнее</a>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- /.portfolio-item-details -->
                                                </div>
                                            </div>
                                            <!-- /.portfolio-item -->
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                                <div id="tabs-3">
                                    {!! htmlspecialchars_decode($partner->description) !!}
                                </div>
                            </div>
                        </div>
                    </article>
                </main>
                <!-- begin sidebar -->
                <aside class="col-md-3 col-md-pull-9">

                    <!-- begin twitter widget -->
                    <div style="line-height:2em;">

                        <h3>Информация</h3>
                        <i class="fa fa-map-marker fa-2"></i>&nbsp;&nbsp; <?= $partner->address ?>
                        <br />

                        <? if ($partner->email): ?>
                        <i class="fa fa-envelope fa-2"></i>&nbsp;&nbsp; <?= $partner->email ?>
                        <br />
                        <? endif; ?>

                        <i class="fa fa-phone fa-2"></i>&nbsp;&nbsp; <a href="tel:<?= $partner->phone ?>"><?= $partner->phone ?></a>
                        <br />
                        <i class="fa fa-clock-o fa-2"></i>&nbsp;&nbsp; <?= $partner->work_time ?>
                        <br />
                        <i class="fa fa-link fa-2"></i>&nbsp;&nbsp; <?= $partner->site ?>
                        <hr />
                    </div>
                </aside>
                <!-- end sidebar -->
            </div>
        </div>
    </section>
    <section class="note purple">
        <div class="container section-wrapper text-center">
            <button class="footerbutton">Начать выполнять задания</button>
            <div class="quoter">... и заработать уже сегодня</div>
        </div>
    </section>



    @include('pattern.footer')
    <!-- footer-navigation start -->
    <!-- footer-navigation end -->
</div>


<script>



    $(".faq-q").click(function () {
        var container = $(this).parents(".faq-c");
        var answer = container.find(".faq-a");
        var trigger = container.find(".faq-t");

        answer.slideToggle(200);

        if (trigger.hasClass("faq-o")) {
            trigger.removeClass("faq-o");
        }
        else {
            trigger.addClass("faq-o");
        }
    });
</script>
</body>
</html>