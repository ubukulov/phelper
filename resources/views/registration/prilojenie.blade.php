@extends('layouts/app')
@section('content')
    <form  id="form_prilojenie" method="post" class="ui small form" style="margin-top: 50px">
        {{ csrf_field() }}

            <div class="field">
                <label id ="label_profession" for="id_profession">Специальность</label>

                <div id="combo_profession" class='ui search selection dropdown' >
                    <input type='hidden' id="id_profession" name='id_profession'>
                    <i class='dropdown icon'></i>
                    <div class='default text'>Выберите специальность</div>
                    <div class='menu' id="list_professions">
                        @foreach($professions as $value)
                            <div class='item' data-value="{{ $value->professionID }}">{{ $value->name }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="three fields">

                <div class="field">
                    {{--<label for="id_profession">Форма обучения</label>--}}
                    {{--<select name="id_study_form" id="id_study_form" class="ui fluid search dropdown " onchange="list_year();" required>--}}
                        {{--<option value="">Выберите форму обучения</option>--}}
                        {{--@if(isset($study_form))--}}
                            {{--@foreach($study_form as $sf)--}}
                                {{--<option value="{{ $sf->Id }}">{{ $sf->NameRu }}</option>--}}
                            {{--@endforeach--}}
                        {{--@endif--}}
                    {{--</select>--}}
                    <label id ="label_study_form" for="id_study_form">Форма обучения</label>
                    <div id="combo_study_form" class='ui selection dropdown' >
                        <input type='hidden' id="id_study_form" name='id_study_form'>
                        <i class='dropdown icon'></i>
                        <div class='default text'>Выберите форму обучения</div>
                        <div class='menu' id="list_study_form">

                        </div>
                    </div>
                </div>

                <div class="field" id="year">
                    <label for="id_year">Год выпуска</label>
                    <div id="combo_year" class='ui selection dropdown' >
                        <input type='hidden' id="id_year" name='id_year'>
                        <i class='dropdown icon'></i>
                        <div class='default text'>Выберите год</div>
                        <div class='menu' id="list_year">

                        </div>
                    </div>
                </div>

            </div>

            <div class="field">
                    <div  id="temp_button" class="ui small orange button ">Запросить данные по выбранной специальности</div>
            </div>

            <div class="two fields">
                <div class="field" id="student">
                    <label for="id_student">Обучающиеся</label>

                    <div id="combo_student" class='ui search selection dropdown' >
                        <input type='hidden' id="id_student" name='id_student'>
                        <i class='dropdown icon'></i>
                        <div class='default text'>Выберите студента</div>
                        <div class='menu' id="menu_student">

                        </div>
                    </div>
                </div>
                <div class="field"></div>
            </div>



            <div class="field">
                    <div id= "submit" class="ui small positive disabled button">Формировать и загрузить</div>
            </div>

        <div class="ui error message hidden" id="error_message"></div>

        <div class="alert alert-warning">
            Перед печатью проверяйте данные!
        </div>

    </form>

    {{--<table class="ui very compact small celled definition table">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th width="5%"></th>--}}
            {{--<th width="30%">Тип практики</th>--}}
            {{--<th width="65%">Название дисциплины</th>--}}
        {{--</tr>--}}

        {{--</thead>--}}
        {{--<tbody id="table-transcript">--}}
        {{--<tr>--}}
            {{--<td class="collapsing">--}}
                {{--<div class="ui fitted slider checkbox">--}}
                    {{--<input type="checkbox"> <label></label>--}}
                {{--</div>--}}
            {{--</td>--}}
        {{--</tr>--}}
        {{--</tbody>--}}
    {{--</table>--}}
@stop