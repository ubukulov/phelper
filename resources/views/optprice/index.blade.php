@extends('layouts/optprice')
@section('content')
    <div class="menu-filter" id="menu-filter">
        <ul class="nav navbar-nav navbar-left">
            <li class="active-task">
                <a href="opt_price">Все (2)</a>
            </li>
            <li class="active-task">
                <a href="index">Новые (2)</a>
            </li>

            <li>
                <a href="tasks_main_cat?id=1">Продукты (0)</a>
            </li>
            <li>
                <a href="tasks_main_cat?id=2">Техника (0)</a>
            </li>
            <li class="active-task">
                <a href="index">Популярное (2)</a>
            </li>
            <li class="active-task">
                <a href="index">Ассортимент (2)</a>
            </li>
        </ul>
        <!-- /.nav -->
    </div>
    <hr>
    <div class="row">

        @foreach($opt as $partner)
            <div class="col-sm-4 col-xs-6">
                <div class="brd">
                    <div class="portfolio-item">
                        <!-- /.portfolio-img -->
                        <a @if(Auth::check()) href="{{ url('/optprice/'.$partner->id) }}" @else data-toggle="modal" onclick="set_login_location_link('opt_price_partner?id=<?= $partner->id ?>')" data-target="#loginModal" @endif>

                            <div class="portfolio-img" style="height: 140px; cursor: pointer;">
                                <img src="{{ asset('uploads/opt_price/partners/small/'.$partner->logo) }}" alt="port-1" class="port-item">
                                <div class="portfolio-img-hover">

                                </div>
                                <!-- /.portfolio-img-hover -->
                            </div>
                        </a>
                        <div class="portfolio-item-details">
                            <div class="portfolio-item-name">{{ $partner->title }}</div>
                            <!-- /.portfolio-item-name -->
                            <div style="float: left;">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td style="padding-left:7px; line-height: 15px;"><small>Ассортимент:<br><font size="2" color="#62A005">{{ $partner->assortment }}</font></small></td>
                                        <td style="padding-left:15px;" align="center">
                                            <a @if(Auth::check()) href="{{ url('/optprice/'.$partner->id) }}" @else data-toggle="modal" onclick="set_login_location_link('opt_price_partner?id=<?= $partner->id ?>')" data-target="#loginModal" @endif class="hidden-xs taskbutton">Подробнее</a>
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
@stop