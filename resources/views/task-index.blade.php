@extends('layouts/task')
@section('content')
    <div class="rowtsk">
        <div class="blog-heading">
            <h3><?= $cert->title; ?></h3>
        </div>
        <hr />
        <table class="table-tsk">
            <tr>
                <th rowspan="6">
                    <div style="width: 400px" id="myCarousel" class="carousel slide" data-ride="carousel">
                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <div class="item active">
                                <img width="400" src="{{ asset('uploads/certs/'.$cert->image) }}" alt="foto">
                            </div>

                            <div class="item">
                                <img width="400" src="{{ asset('uploads/certs/'.$cert->image2) }}" alt="foto">
                            </div>

                            <div class="item">
                                <img width="400" src="{{ asset('uploads/certs/'.$cert->image3) }}" alt="foto">
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
                <td>
                    <?php if($cert->cert_type == 3) :?>
                    <font style="color:#619F05"></font>Цена скидки:<br />
                    <?php else :?>
                    <font style="color:#619F05"></font>Цена:<br />
                    <?php endif;?>
                    <font style="font-family: ubuntu; font-size: 20px; font-weight: 600; color:#619F05">&nbsp;&nbsp;&nbsp;&nbsp;<?= $cert->special1 ?></font>
                </td>
            </tr>
            <tr>
                <td><font style="color:#d7d7d7"> <i class="fa fa-clock-o fa-2"></i></font>&nbsp;&nbsp;До окончания осталось:<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="font-weight: 600; text-decoration: underline" id="countdown-example">

                    </font> </td>
            </tr>
            <tr>
                <td style="border-bottom:1px solid #d7d7d7">
                    <?php if($cert->cert_type == 1) :?>
                    <button type="button" class="playbutton" data-toggle="modal" data-target="#taskSubs">
                        &nbsp;&nbsp;&nbsp;Купить
                    </button>
                    <?php endif; ?>

                    <?php if($cert->cert_type == 2) :?>
                    <button type="button" class="playbutton" data-toggle="modal" data-target="#taskSubs">
                        &nbsp;&nbsp;&nbsp;Купить
                    </button>
                    <?php endif; ?>

                    <?php if($cert->cert_type == 3) :?>
                    <button type="button" class="playbutton" data-toggle="modal" data-target="#taskSubs">
                        &nbsp;&nbsp;&nbsp;Купить
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="line-height:2em; border-bottom:1px solid #d7d7d7"> <font style="color:#d7d7d7"> <i class="fa fa-thumbs-up fa-2"></i></font>&nbsp;&nbsp;Уже купили:  чел.<br /><font style="color:#d7d7d7"><i class="fa fa-eye fa-2"></i></font>&nbsp;&nbsp;Посмотрели задание: <?= $cert->views ?> чел.</td>
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
        <ul class="nav nav-tabs">
            <li lass="active">
                <a data-toggle="tab" href="#conditions">Задание</a>
            </li>
            <li >
                <a data-toggle="tab" href="#description">Описание</a>
            </li>
            <li >
                <a data-toggle="tab" href="#recalls" href="#recalls">Отзывы ()</a>
            </li>

        </ul>

        <div class="tab-content" style="    border: none;">
            <div id="conditions" class="tab-pane fade in active">
                <p> <?= htmlspecialchars_decode($cert->conditions); ?><p>

            </div>

            <div id="description" class="tab-pane fade">

                <p> <?= htmlspecialchars_decode($cert->description); ?></p>
            </div>

            <div id="recalls" class="tab-pane fade">




                <div id="tab-review" class="product-reviews product-section">
                    <h3 class="product-section_title">Отзывы</h3>



                    <form class="form-horizontal">
                        <!-- Reviews list -->
                        <div id="review">

                            <div class="text-right"></div>
                        </div>

                    </form>







                    <div class="well" style="margin-top: 20px;">
                        <h4>Добавление отзыва:</h4>


                    </div>



                </div>




            </div>

        </div>
    </div>
    <div class="row">

        <div  class="task_map">
            <div id="map" style="height: 300px" class="blog-heading">
                <h3>Задание на карте</h3>

            </div>

        </div>

    </div>
    <div class="rowtsk">
        <div class="blog-heading">
            <h3>Особенности</h3>
        </div>
        <img src="{{ asset('img/attention.png') }}" alt="attention" align="left">&nbsp;&nbsp;&nbsp;&nbsp;
        <font color="#cc0033"><em>Для получения вознаграждения предъявите карту Likemoney.me <font color="#d7d7d7"><i class="fa fa-question-circle fa-2"></i></font><br />
                &nbsp;&nbsp;&nbsp;&nbsp;К Вам на телефон придет уведомление о начислении вознаграждения</em></font>
    </div>
@stop