@extends('admin/layout/default')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Форма добавление партнера
        </h1>
    </section>
    <section class="content">
        <form action="{{ url('admin/opt_partner/store') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="form-group">
                                <label for="title">Наименование</label>
                                <input type="text" class="form-control" id="title" required="required" name="title" placeholder="Введите название">
                            </div>

                            <div class="form-group">
                                <label for="date_start">Дата начала</label>
                                <input type="text" class="form-control" id="date_start" name="date_start" required="required">
                            </div>

                            <div class="form-group">
                                <label for="date_end">Дата конца</label>
                                <input type="text" class="form-control" id="date_end" name="date_end" required="required">
                            </div>

                            <div class="form-group">
                                <label for="assortment">Ассортимент</label>
                                <input type="text" class="form-control" id="assortment" name="assortment">
                            </div>

                            <div class="form-group">
                                <label for="address">Адрес</label>
                                <input type="text" class="form-control" id="address" name="address"/>
                            </div>

                            <div class="form-group">
                                <label for="phone">Телефон</label>
                                <input type="text" class="form-control" id="phone" name="phone"/>
                            </div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="form-group">
                                <label for="site">Сайт</label>
                                <input type="text" class="form-control" id="site" name="site"/>
                            </div>

                            <div class="form-group">
                                <label for="work_time">Рабочее время</label>
                                <input type="text" class="form-control" id="work_time" name="work_time"/>
                            </div>

                            <div class="form-group">
                                <label for="username">Логин</label>
                                <input type="text" class="form-control" id="username" name="username"/>
                            </div>

                            <div class="form-group">
                                <label for="sort">Сортировка</label>
                                <input type="text" class="form-control" id="sort" name="sort" placeholder="сортировка" />
                            </div>

                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="text" class="form-control" id="password" name="password"/>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"/>
                            </div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div id="image4">
                                        <img src="{{ asset('img/no_thumb.png') }}" height="100">
                                    </div>
                                    <br>
                                    <button id="upload4" class="blue">Выбрать логотип</button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div id="image1">
                                        <img src="{{ asset('img/no_thumb.png') }}" height="100">
                                    </div>
                                    <br>
                                    <button id="upload1" class="blue">Выбрать картинку 1</button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div id="image2">
                                        <img src="{{ asset('img/no_thumb.png') }}" height="100">
                                    </div>
                                    <br>
                                    <button id="upload2" class="blue">Выбрать картинку 2</button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div id="image3">
                                        <img src="{{ asset('img/no_thumb.png') }}" height="100">
                                    </div>
                                    <br>
                                    <button id="upload3" class="blue">Выбрать картинку 3</button>
                                </div>
                            </div>
                            <br><br>
                            <hr>

                            <hr>
                            <div class="form-group">
                                <label for="conditions">Условия</label>
                                <textarea id="conditions" class="form-control wysiwyg" name="conditions"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="features">Описание</label>
                                <textarea id="features" class="form-control wysiwyg" name="features"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="description">Полные описание</label>
                                <textarea id="description" class="form-control wysiwyg" name="description"></textarea>
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary" name="submit">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </form>
        </div>
    </section>
@stop