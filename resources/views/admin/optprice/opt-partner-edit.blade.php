@extends('admin/layout/default')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Форма редактирование партнера
        </h1>
    </section>
    <section class="content">
        <form action="{{ url('admin/opt_partner/'.$partner->id) }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="form-group">
                                <label for="title">Наименование</label>
                                <input type="text" class="form-control" id="title" required="required" name="title" placeholder="Введите название" value="{{ $partner->title }}">
                            </div>

                            <div class="form-group">
                                <label for="date_start">Дата начала</label>
                                <input type="text" class="form-control" id="date_start" name="date_start" required="required" value="{{ date('d.m.Y', $partner->date_start) }}">
                            </div>

                            <div class="form-group">
                                <label for="date_end">Дата конца</label>
                                <input type="text" class="form-control" id="date_end" name="date_end" required="required" value="{{ date('d.m.Y', $partner->date_end) }}">
                            </div>

                            <div class="form-group">
                                <label for="assortment">Ассортимент</label>
                                <input type="text" class="form-control" id="assortment" name="assortment" value="{{ $partner->assortment }}">
                            </div>

                            <div class="form-group">
                                <label for="address">Адрес</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ $partner->address }}"/>
                            </div>

                            <div class="form-group">
                                <label for="phone">Телефон</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $partner->phone }}"/>
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
                                <input type="text" class="form-control" id="site" name="site" value="{{ $partner->site }}"/>
                            </div>

                            <div class="form-group">
                                <label for="work_time">Рабочее время</label>
                                <input type="text" class="form-control" id="work_time" name="work_time" value="{{ $partner->work_time }}"/>
                            </div>

                            <div class="form-group">
                                <label for="username">Логин</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ $partner->username }}"/>
                            </div>

                            <div class="form-group">
                                <label for="sort">Сортировка</label>
                                <input type="text" class="form-control" id="sort" name="sort" placeholder="сортировка"  value="{{ $partner->sort }}"/>
                            </div>

                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="text" class="form-control" id="password" name="password" value="{{ $partner->password }}"/>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $partner->email }}"/>
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
                                        @if(!empty($partner->logo))
                                        <img src="{{ asset('uploads/opt_price/partners/small/'.$partner->logo) }}" height="100">
                                        @else
                                        <img src="{{ asset('img/no_thumb.png') }}" height="100">
                                        @endif
                                    </div>
                                    <br>
                                    <button id="upload4" class="blue">Выбрать логотип</button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div id="image1">
                                        @if(!empty($partner->image1))
                                            <img src="{{ asset('uploads/opt_price/partners/small/'.$partner->image1) }}" height="100">
                                        @else
                                            <img src="{{ asset('img/no_thumb.png') }}" height="100">
                                        @endif
                                    </div>
                                    <br>
                                    <button id="upload1" class="blue">Выбрать картинку 1</button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div id="image2">
                                        @if(!empty($partner->image2))
                                            <img src="{{ asset('uploads/opt_price/partners/small/'.$partner->image2) }}" height="100">
                                        @else
                                            <img src="{{ asset('img/no_thumb.png') }}" height="100">
                                        @endif
                                    </div>
                                    <br>
                                    <button id="upload2" class="blue">Выбрать картинку 2</button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div id="image3">
                                        @if(!empty($partner->image3))
                                            <img src="{{ asset('uploads/opt_price/partners/small/'.$partner->image3) }}" height="100">
                                        @else
                                            <img src="{{ asset('img/no_thumb.png') }}" height="100">
                                        @endif
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
                                <textarea id="conditions" class="form-control wysiwyg" name="conditions">{!! $partner->conditions !!}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="features">Описание</label>
                                <textarea id="features" class="form-control wysiwyg" name="features">{!! $partner->features !!}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="description">Полные описание</label>
                                <textarea id="description" class="form-control wysiwyg" name="description">{!! $partner->description !!}</textarea>
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