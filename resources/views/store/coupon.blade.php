@extends('layouts/store')
@section('content')
    <hr>
    <div class="rowtsk">
        <div class="blog-heading">
            <h3><?= $offer['name']; ?></h3>
        </div>
        <hr />
        <table class="table-tsk">
            <tr>
                <th rowspan="7">

                    <div style="width: 400px" id="myCarousel" class="carousel slide" data-ride="carousel">
                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <?php if(!empty($offer['image'])) :?>
                            <div class="item active">
                                <img width="400" src="{{ $offer['image']  }}" alt="foto">
                            </div>
                            <div class="item">
                                <img width="400" src="{{ $offer['image']  }}" alt="foto">
                            </div>
                            <div class="item">
                                <img width="400" src="{{ $offer['image']  }}" alt="foto">
                            </div>
                            <?php endif; ?>
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
                    <font style="color:#619F05"><i class="fa fa-credit-card fa-2"></i></font>&nbsp;&nbsp;
                    <span style="margin-right: 15px;">cashback</span>

                    <span style="font-size: 12px; margin-left: 30%;">кэшбэк с каждого заказа</span>
                </td>
            </tr>
            <tr>
                <td>
                    <font style="color:#d7d7d7"> <i class="fa fa-clock-o fa-2"></i></font>
                    &nbsp;&nbsp;<span style="font-size: 12px;">До окончания осталось:</span><br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <font style="font-weight: 600; text-decoration: underline" id="countdown-example">

                    </font>
                </td>
            </tr>
            <tr>
                <td>
                    <a style="padding: 10px; color: white; background:#69C22D; text-decoration: none;" href="{{ $offer['gotolink'] }}" target="_blank">Перейти в магазин <?=$offer['name']?></a>
                </td>
            </tr>
            <tr>

            </tr>
            <tr>

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
        <br>
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Условия</a></li>
                <li><a href="#tabs-2">Описание</a></li>
                <li><a href="#tabs-3">Промокоды ({{ $ad_coupons['_meta']['count'] }})</a></li>
            </ul>
            <div id="tabs-1">
                <p class="ali-text">Перед совершением покупок, ознакомьтесь, пожалуйста&nbsp;<a href="https://likemoney.me/cashback/pravila" target="_blank">с правилами совершения покупок с кэшбэком</a>.</p>
                <p>При заказе в магазине партнере, <strong>номера заказов в личном кабинете Likemoney cashback</strong>&nbsp;и в магазине могут <strong>не соответствовать</strong>. В частности, когда заказ имеет несколько позиций. Это связано с тем, что каждой позиции товара магазин присваивает отдельный номер, который передает на сервис Likemoney cashback, как номер заказа.</p>
                <p>Также при начислении средств возможны незначительные колебания стоимости заказов и соответственно сумм кэшбэка. Это связано с перерасчетом и конвертацией валют, в которых магазин Aliexpress ведет денежные операции.&nbsp;Кэшбэк начисляется от суммы в долларах в пересчете на тенге&nbsp;по текущему курсу НБ РК.</p>
                <p>Для получения кэшбэк необходимо совершать <strong>переход с Likemoney cashback&nbsp;на магазин партнера</strong> перед каждой покупкой.</p>
                <p><strong>Совет:</strong> чтобы Ваш <strong>кэшбэк в магазине партнере быстрее подтвердился</strong>, не забудьте подтвердить получение заказа в магазине партнера. Это может существенно сократить время ожидания кэшбэка.</p>
            </div>
            <div id="tabs-2">
                {!! $offer['raw_description'] !!}
            </div>
            <div id="tabs-3">
                @foreach($ad_coupons['results'] as $promo)
                    <div style="width: 75%; float: left; padding: 15px; background: #FFFAF4; border-radius: 10px; margin-bottom: 10px; border-bottom: 1px solid #e8e4de;">
                        <header><?=$promo['name']?></header>
                        <p><span style="font-size: 12px; font-weight: bold;"><?=$promo['description']?></span><br>
                            <span style="float: right;"><img style="margin-right: 5px;" src="{{ asset('img/promo_link.png') }}" alt=""><?=$offer['name']?></span></p>
                    </div>
                    <div style="width: 25%; float: left; padding: 15px 5px 5px 5px; margin-bottom: 10px;">
                        <span style="color: #cc0000; margin: 10px 10px 10px 66px;">{{ promo_count_day($promo['date_end']) }}</span>
                        <p style="font-size: 10px;margin: 0px 10px 10px 48px;">Осталось времени</p>
                        <a href="<?=$promo['goto_link']?>" rel="nofollow" target="_blank"><img src="{{ asset('img/link_btn.png') }}" width="180" alt=""></a>
                    </div>
                @endforeach
                    <div class="clearfix"></div>
            </div>
        </div>
    </div>
@stop