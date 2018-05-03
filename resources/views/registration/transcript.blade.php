@extends('layouts/app')
@section('content')
    <form action="{{ url('/transcript') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="ui form small" style="margin-top: 50px;">
            <div class="three fields">
                <div class="field">
                    <label id ="label_profession" for="id_profession">Специальность</label>
                    <select name="id_profession" id="id_profession" class="ui search dropdown" onchange="study_forms();" required>
                        <option value="">--- Выберите специальность ---</option>
                        @foreach($professions as $value)
                            <option value="{{ $value->professionID }}">{{ $value->name }} ({{ $value->professionID }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="id_profession">Форма обучения</label>
                    <select name="id_study_form" id="id_study_form" class="ui fluid search dropdown " onchange="list_course();" required="required">
                        <option value="">--- Выберите форму обучения ---</option>
                        {{--@if(isset($study_form))--}}
                            {{--@foreach($study_form as $sf)--}}
                                {{--<option value="{{ $sf->Id }}">{{ $sf->NameRu }}</option>--}}
                            {{--@endforeach--}}
                        {{--@endif--}}
                    </select>
                </div>
                <div class="field">
                    <label for="id_course">Курс</label>
                    <select name="id_course" id="id_course" class="ui dropdown" required>
                        @if(isset($course))
                            @foreach($course as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="four fields">
                <div class="field">
                    <label for="id_term">Семестр</label>
                    <select name="id_term[]" id="id_term" class="ui fluid dropdown selection" multiple="multiple">
                        <option value="00" selected="selected">Все</option>
                        <option value="1">1 семестр</option>
                        <option value="2">2 семестр</option>
                        <option value="3">3 семестр</option>
                        <option value="4">4 семестр</option>
                        <option value="5">5 семестр</option>
                        <option value="6">6 семестр</option>
                        <option value="7">7 семестр</option>
                        <option value="8">8 семестр</option>
                    </select>
                </div>
                <div class="field">
                    <label for="id_study_lang">Язык обучения</label>
                    <select name="id_study_lang" id="id_study_lang" class="ui search dropdown" required>
                        @foreach($study_lang as $sl)
                            <option value="{{ $sl->Id }}">{{ $sl->NameRU }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="id_status">Статус</label>
                    <select name="id_status" id="id_status" class="ui search dropdown">
                        <option value="1">Обучающихся</option>
                        <option value="3">Отчислен</option>
                        <option value="2">Выпускник</option>
                    </select>
                </div>

                <div class="field">
                    <label for="id_lang">Выберите язык</label>
                    <select name="id_lang" id="id_lang" class="ui search dropdown">
                        <option value="1">Казахский</option>
                        <option value="2">Русский</option>
                        <option value="3">Английский</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <div class="inline field">
                    <div class="ui checkbox">
                        <input type="checkbox" id="data_from_tran" name="data_from_tran" tabindex="0" class="hidden">
                        <label>Получить данные напрямую из транскрипта (если введен все предметы в транскрипт напрямую)</label>
                    </div>
                </div>
            </div>

            <div class="form-group" id="btn_state">
                <div class="col-sm-10" id="individual">
                    <button type="button" class="btn btn-warning" onclick="individual()">Запросить данные по выбранной специальности</button>
                </div>
            </div>

            <div class="three fields">
                <div class="field" id="student">
                    <label for="id_student">Обучающихся</label>
                    <select name="id_student" id="id_student" class="ui search dropdown">

                    </select>
                </div>
                <div class="field" id="data">
                    <label for="time">Дата</label>
                    <input type="text" id="time" name="date_time">
                </div>
                <div class="field">
                    <label for="trans_type">Тип транскрипта</label>
                    <select name="trans_type" id="trans_type" class="ui search dropdown">
                        <option value="0">Обычный (без подписи ректора и ECTS)</option>
                        <option value="1">с подписью ректора</option>
                        <option value="2">с ECTS</option>
                        <option value="3">с ECTS и подписью ректора</option>
                    </select>
                </div>
            </div>
            <br>
            <div class="form-group">
                <div class="col-sm-10">
                    <button type="submit" name="submit" id="submit" class="btn btn-success" onclick="check_form_data()" disabled="disabled">Формировать и загрузить</button>
                </div>
            </div>
        </div>
    </form>
@stop