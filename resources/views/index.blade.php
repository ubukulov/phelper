@extends('layouts/app')
@section('content')
    <div class="rowtsk">
        <div class="menu-filter" id="menu-filter">
            <ul class="nav navbar-nav navbar-left">
                <li class="active-task">
                    <a href="{{ route('home') }}">Все (346)</a>
                </li>
                @foreach($cats as $cat)
                <li>
                    <a href="{{ url('/cert/cat/'.$cat->id) }}">{{ $cat->title }} ({{ count_certs_main_cat($cat->id) }})</a>
                </li>
                @endforeach
            </ul>
            <!-- /.nav -->
        </div>
        <hr>
        <div class="row" id="row">
            @foreach($certs as $key=>$cert)
            <div class="col-sm-4 col-xs-6">
                <div class="brd">
                    <div class="portfolio-item">
                        <!-- /.portfolio-img -->
                        <a @if(Auth::check()) href="{{ url('/task/'.$cert->id) }}" @else data-toggle="modal" onclick="set_login_location_link('task?id={{ $cert->id }}')" data-target="#loginModal" @endif>

                            <div class="portfolio-img" style="height: 140px; cursor: pointer;">
                                <img src="{{ asset('uploads/certs/small/'.$cert->image) }}" alt="port-1" class="port-item">
                                <div class="portfolio-img-hover">

                                </div>
                                <!-- /.portfolio-img-hover -->
                            </div>
                        </a>
                        <div class="portfolio-item-details">
                            <div class="portfolio-item-name">{{ $cert->title }}</div>
                            <!-- /.portfolio-item-name -->
                            <div style="float: left;">
                                <table>
                                    <tbody>
                                    <tr>
                                        <!-- Бизнес -->
                                        @if($cert->cert_type == 2)
                                        <td width="130">
                                            <span style="text-decoration: line-through; font-size: 12px;">{{ $cert->special2 }} тг</span><br>
                                            <span style="color: green; font-weight: bold;">{{ $cert->special3 }} тг</span>
                                        </td>
                                            @if(is_tariff(Auth::id()))
                                            <td align="center"><font color="#62A005" size="4"><i class="fa fa-credit-card-alt"></i></font></td>
                                            <td style="padding-left:7px; line-height: 15px;"><small>cashback:<br><font color="#62A005"><b>{{ $cert->special1 }}</b></font></small></td>
                                            @else
                                            <td align="right">
                                                <a @if (Auth::check()) href="{{ url('/task/'.$cert->id) }}" @else data-toggle="modal" onclick="set_login_location_link('task?id={{ $cert->id }}')" data-target="#loginModal" @endif class="hidden-xs taskbutton">Подробнее</a>
                                            </td>
                                            @endif
                                        @endif

                                        <!-- Купон -->
                                        <?php if($cert->cert_type == 3) :?>
                                        <td align="center"><font color="#62A005" size="4"><i class="fa fa-credit-card-alt"></i></font></td>
                                        <td style="padding-left:7px; line-height: 15px; width: 130px;"><small>скидка:<br><font color="#62A005"><b><?= $cert->special1 ?></b></font></small></td>
                                        <td align="right">
                                            <a <? if (Auth::check()): ?> href="{{ url('/task/'.$cert->id) }}" <? else: ?> data-toggle="modal" onclick="set_login_location_link('task?id=<?= $cert->id ?>')" data-target="#loginModal" <? endif; ?> class="hidden-xs taskbutton">Подробнее</a>
                                        </td>
                                        <?php endif; ?>

                                        <!-- Задания -->
                                        <?php if($cert->cert_type == 1) :?>
                                        <td align="center"><font color="#62A005" size="4"><i class="fa fa-credit-card-alt"></i></font></td>
                                        <td style="padding-left:7px; line-height: 15px; width: 130px;">
                                            <small>@if($key == 0)Франшиза:@else cashback: @endif<br><font color="#62A005"><b><?= $cert->special1 ?></b></font></small>
                                        </td>
                                        <td align="right">
                                            <a <? if (Auth::check()): ?> href="{{ url('/task/'.$cert->id) }}" <? else: ?> data-toggle="modal" onclick="set_login_location_link('task?id=<?= $cert->id ?>')" data-target="#loginModal" <? endif; ?> class="hidden-xs taskbutton">Подробнее</a>
                                        </td>
                                        <?php endif; ?>

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
            <!-- /.col-md-4 -->
        </div>
    </div>
@stop