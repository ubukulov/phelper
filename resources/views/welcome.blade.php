@extends('layouts/app')
@section('content')
    <form action="{{ url('/statement') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="id_institute" class="col-sm-2 control-label">Институт</label>
            <div class="col-sm-10">
                <select name="id_institute" id="id_institute" class="form-control" onchange="lists_of_institute();" required>
                    <option value="0"> -- Выберите институт --</option>
                    @foreach($ins as $i)
                    <option value="{{ $i->FacultyID }}">{{ $i->facultyNameRU }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="id_chair" class="col-sm-2 control-label">Кафедры</label>
            <div class="col-sm-10">
                <select name="id_chair" id="id_chair" class="form-control" onchange="lists_of_chair();" required>

                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="id_profession" class="col-sm-2 control-label">Специальность</label>
            <div class="col-sm-10">
                <select name="id_profession" id="id_profession" class="form-control" onchange="lists_of_profession();" required>

                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="id_profession" class="col-sm-2 control-label">Группа</label>
            <div class="col-sm-10">
                <select name="id_group" id="id_group" class="form-control" required>

                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="id_study_form" class="col-sm-2 control-label">Форма обучения</label>
            <div class="col-sm-10">
                <select name="id_study_form" id="id_study_form" class="form-control" onchange="lists_of_profession()"  required>
                    @foreach($study_forms as $sf)
                        <option value="{{ $sf->Id }}">{{ $sf->NameRu }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="id_study_lang" class="col-sm-2 control-label">Язык обучения</label>
            <div class="col-sm-10">
                <select name="id_study_lang" id="id_study_lang" class="form-control"  onchange="lists_of_profession()"  required>
                    @foreach($study_lang as $sl)
                        <option value="{{ $sl->Id }}">{{ $sl->NameRU }}</option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="form-group">
            <label for="id_subject" class="col-sm-2 control-label">Семестр</label>
            <div class="col-sm-10">
                <select name="id_term" id="id_term" class="form-control" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="id_subject" class="col-sm-2 control-label">Дисциплина</label>
            <div class="col-sm-10">
                <select name="id_subject" id="id_subject" class="form-control" required>

                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="submit" class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
                <button type="submit" name="submit" id="submit" class="btn btn-success" >Отправить</button>
            </div>
        </div>
    </form>
@stop