@extends('admin/layout/default')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Форма добавление задании
    </h1>
</section>
<section class="content">
    <form action="{{ url('admin/cert/store') }}" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label>Выберите тип</label>
                            <select name="cert_type" id="cert_type" class="form-control">
                                <option value="1">Задания</option>
                                <option value="2">Бизнес</option>
                                <option value="3">Купон</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title">Наименование</label>
                            <input type="text" class="form-control" id="title" required="required" name="title" placeholder="Введите название">
                        </div>

                        <div class="form-group">
                            <label>Список категории</label>
                            <select class="form-control select2" style="width: 100%;" id="id_main_cat" name="id_main_cat">
                                @foreach($cat as $c)
                                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Список под категории</label>
                            <select class="form-control select2" style="width: 100%;" id="id_pod_cat" name="id_pod_cat">
                                @foreach($pod_cat as $pc)
                                    <option value="{{ $pc->id }}">{{ $pc->title }}</option>
                                @endforeach
                            </select>
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
                            <label for="special1">special1</label>
                            <input type="text" class="form-control" id="special1" name="special1">
                        </div>

                        <div class="form-group">
                            <label for="special2">Цена без скидки</label>
                            <input type="text" class="form-control" id="special2" name="special2" placeholder="Цена без скидки"/>
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
                        <label for="special4">special4</label>
                        <input type="text" class="form-control" id="special4" name="special4" placeholder="special4" />
                    </div>

                    <div class="form-group">
                        <label for="old_price">Старая цена</label>
                        <input type="text" class="form-control" id="old_price" name="old_price" placeholder="old_price" />
                    </div>

                    <div class="form-group">
                        <label for="economy">economy</label>
                        <input type="text" class="form-control" id="economy" name="economy" placeholder="economy" />
                    </div>

                    <div class="form-group">
                        <label for="sort">Сортировка</label>
                        <input type="text" class="form-control" id="sort" name="sort" placeholder="сортировка" />
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Короткое описание</label>
                        <input type="text" class="form-control" id="meta_description" name="meta_description" placeholder="meta_description" />
                    </div>

                    <div class="form-group">
                        <label for="meta_keywords">Ключевые слова</label>
                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="meta_keywords" />
                    </div>

                    <div class="form-group">
                        <label for="special3">Цена со скидкой</label>
                        <input type="text" class="form-control" id="special3" name="special3" placeholder="Цена со скидкой" />
                    </div>

                    <div class="form-group">
                        <label>Список партнеров</label>
                        <select class="form-control select2" style="width: 100%;" name="id_partner">
                            @foreach($partner as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                            @endforeach
                        </select>
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <div id="image1">
                                <img src="{{ asset('img/no_thumb.png') }}" height="100" name="image1">
                            </div>
                            <br>
                            <button id="upload1" class="blue">Выбрать картинку 1</button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div id="image2">
                                <img src="{{ asset('img/no_thumb.png') }}" height="100" name="image2">
                            </div>
                            <br>
                            <button id="upload2" class="blue">Выбрать картинку 2</button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div id="image3">
                                <img src="{{ asset('img/no_thumb.png') }}" height="100" name="image3">
                            </div>
                            <br>
                            <button id="upload3" class="blue">Выбрать картинку 3</button>
                        </div>
                    </div>
                    <br><br>
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