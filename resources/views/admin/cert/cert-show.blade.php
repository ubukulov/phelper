@extends('admin/layout/default')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Форма редактирование задании
    </h1>
</section>
<section class="content">
    <form action="{{ url('admin/cert/'.$cert->id) }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label>Выберите тип</label>
                            <select name="cert_type" id="cert_type" class="form-control">
                                <option value="1" @if($cert->cert_type == 1) selected="selected" @endif>Задания</option>
                                <option value="2" @if($cert->cert_type == 2) selected="selected" @endif>Бизнес</option>
                                <option value="3" @if($cert->cert_type == 3) selected="selected" @endif>Купон</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title">Наименование</label>
                            <input type="text" class="form-control" id="title" value="{{ $cert->title }}" required="required" name="title" placeholder="Введите название">
                        </div>

                        <div class="form-group">
                            <label>Список категории</label>
                            <select class="form-control select2" style="width: 100%;" id="id_main_cat" name="id_main_cat">
                                @foreach($cat as $c)
                                    @if($cert->id_main_cat == $c->id)
                                    <option value="{{ $c->id }}" selected="selected">{{ $c->title }}</option>
                                    @else
                                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Список под категории</label>
                            <select class="form-control select2" style="width: 100%;" id="id_pod_cat" name="id_pod_cat">
                                @foreach($pod_cat as $pc)
                                    @if($cert->id_pod_cat == $pc->id)
                                    <option value="{{ $pc->id }}">{{ $pc->title }}</option>
                                    @else
                                    <option value="{{ $pc->id }}">{{ $pc->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date_start">Дата начала</label>
                            <input type="text" class="form-control" id="date_start" value="{{ date("d.m.Y", $cert->date_start) }}" name="date_start" required="required">
                        </div>

                        <div class="form-group">
                            <label for="date_end">Дата конца</label>
                            <input type="text" class="form-control" id="date_end" value="{{ date("d.m.Y", $cert->date_end) }}" name="date_end" required="required">
                        </div>

                        <div class="form-group">
                            <label for="special1">special1</label>
                            <input type="text" class="form-control" id="special1" name="special1" value="{{ $cert->special1 }}">
                        </div>

                        <div class="form-group">
                            <label for="special2">Цена без скидки</label>
                            <input type="text" class="form-control" id="special2" name="special2" value="{{ $cert->special2 }}" placeholder="Цена без скидки"/>
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
                            <input type="text" class="form-control" id="special4" name="special4" value="{{ $cert->special4 }}" placeholder="special4" />
                        </div>

                        <div class="form-group">
                            <label for="old_price">Старая цена</label>
                            <input type="text" class="form-control" id="old_price" name="old_price" value="{{ $cert->old_price }}" placeholder="old_price" />
                        </div>

                        <div class="form-group">
                            <label for="economy">economy</label>
                            <input type="text" class="form-control" id="economy" name="economy" value="{{ $cert->economy }}" placeholder="economy" />
                        </div>

                        <div class="form-group">
                            <label for="sort">Сортировка</label>
                            <input type="text" class="form-control" id="sort" name="sort" value="{{ $cert->sort }}" placeholder="сортировка" />
                        </div>

                        <div class="form-group">
                            <label for="meta_description">Короткое описание</label>
                            <input type="text" class="form-control" id="meta_description" value="{{ $cert->meta_description }}" name="meta_description" placeholder="meta_description" />
                        </div>

                        <div class="form-group">
                            <label for="meta_keywords">Ключевые слова</label>
                            <input type="text" class="form-control" id="meta_keywords" value="{{ $cert->meta_keywords }}" name="meta_keywords" placeholder="meta_keywords" />
                        </div>

                        <div class="form-group">
                            <label for="special3">Цена со скидкой</label>
                            <input type="text" class="form-control" id="special3" value="{{ $cert->special3 }}" name="special3" placeholder="Цена со скидкой" />
                        </div>

                        <div class="form-group">
                            <label>Список партнеров</label>
                            <select class="form-control select2" style="width: 100%;" name="id_partner">
                                @foreach($partner as $pt)
                                    @if($cert->partner_id == $pt->id)
                                    <option value="{{ $pt->id }}" selected="selected">{{ $pt->name }}</option>
                                    @else
                                    <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                    @endif
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
                                    @if(!empty($cert->image))
                                    <img  src="{{ asset('uploads/certs/small/'.$cert->image) }}" height="100">
                                    <input type="hidden" name="photo1" value="{{ $cert->image }}" />
                                    @else
                                    <img src="{{ asset('img/no_thumb.png') }}"  height="100">
                                    @endif
                                </div>
                                <br>
                                <button id="upload1" class="blue">Выбрать картинку 1</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div id="image2">
                                    @if(!empty($cert->image2))
                                        <img  src="{{ asset('uploads/certs/small/'.$cert->image2) }}" height="100">
                                        <input type="hidden" name="photo2" value="{{ $cert->image2 }}" />
                                    @else
                                        <img src="{{ asset('img/no_thumb.png') }}"  height="100">
                                    @endif
                                </div>
                                <br>
                                <button id="upload2" class="blue">Выбрать картинку 2</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div id="image3">
                                    @if(!empty($cert->image3))
                                        <img  src="{{ asset('uploads/certs/small/'.$cert->image3) }}" height="100">
                                        <input type="hidden" name="photo3" value="{{ $cert->image3 }}" />
                                    @else
                                        <img src="{{ asset('img/no_thumb.png') }}"  height="100">
                                    @endif
                                </div>
                                <br>
                                <button id="upload3" class="blue">Выбрать картинку 3</button>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label for="conditions">Условия</label>
                            <textarea id="conditions" class="form-control wysiwyg" name="conditions">{!! $cert->conditions !!}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="features">Описание</label>
                            <textarea id="features" class="form-control wysiwyg" name="features">{!! $cert->features !!}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="description">Полные описание</label>
                            <textarea id="description" class="form-control wysiwyg" name="description">{!! $cert->description !!}</textarea>
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