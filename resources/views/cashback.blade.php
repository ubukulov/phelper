@extends('layouts/app')
@section('content')
    <div class="rowtsk">
        <div class="menu-filter" id="menu-filter">
            <ul class="nav navbar-nav navbar-left">
                <li class="active-task">
                    <a href="index">Все (346)</a>
                </li>

                <li>
                    <a href="tasks_main_cat?id=53">Новые (0)</a>
                </li>
                <li>
                    <a href="tasks_main_cat?id=11">Еда (16)</a>
                </li>
                <li>
                    <a href="tasks_main_cat?id=55">Здоровье и спорт (9)</a>
                </li>
                <li>
                    <a href="tasks_main_cat?id=50">Товары (21)</a>
                </li>
                <li>
                    <a href="tasks_main_cat?id=44">Красота (15)</a>
                </li>
                <li>
                    <a href="tasks_main_cat?id=56">Отдых (4)</a>
                </li>
                <li>
                    <a href="tasks_main_cat?id=58">Услуги (19)</a>
                </li>
                <li>
                    <a href="tasks_main_cat?id=57">Развлечения (6)</a>
                </li>
            </ul>
            <!-- /.nav -->
        </div>
        <hr>
        <div class="row">
            @foreach($cash as $partner)
                <div class="col-sm-4 col-xs-6">
                    <div class="brd">
                        <div class="portfolio-item">
                            <!-- /.portfolio-img -->
                            <a href="#">

                                <div class="portfolio-img" style="height: 140px; cursor: pointer;">
                                    <img src="uploads/cashback/partners/small/<?= $partner->logo; ?>" alt="port-1" class="port-item">
                                    <div class="portfolio-img-hover">

                                    </div>
                                    <!-- /.portfolio-img-hover -->
                                </div>
                            </a>
                            <div class="portfolio-item-details">
                                <div class="portfolio-item-name"><?= $partner->title; ?></div>
                                <!-- /.portfolio-item-name -->
                                <div style="float: left;">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td align="center"><font color="#62A005" size="4"><i class="fa fa-credit-card-alt"></i></font></td>
                                            <td style="padding-left:7px; line-height: 15px;"><small>cashback:<br><font color="#62A005">
                                                        <b><?= $partner->older; ?><?php if($partner->older_type == 1) :?> % <?php else :?> тг <?php endif;?></b></font></small>
                                            </td>
                                            <td style="padding-left:15px; line-height: 15px; width: 130px;" align="center">
                                                <a href="#" class="hidden-xs taskbutton">Подробнее</a>
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
@stop