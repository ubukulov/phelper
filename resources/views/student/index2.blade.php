@extends('layouts/student')
@section('content')
    <form action="{{ url('/statement') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="ui form small" style="margin-top: 50px;">
            <div class="three fields">
                <div class="field">
                    <label id ="label_profession" for="id_profession">Специальность</label>
                    <select name="id_profession" id="id_profession" class="ui search dropdown" onchange="specialization();" required>
                        <option value="">--- Выберите специальность ---</option>
                        @foreach($professions as $value)
                            <option value="{{ $value->professionID }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="id_specialization">Специализации</label>
                    <select name="id_specialization" id="id_specialization" class="ui fluid search dropdown " required="required">
                        <option value="">--- Выберите специализация ---</option>
                    </select>
                </div>

                <div class="field">
                    <label for="id_profession">Форма обучения</label>
                    <select name="id_study_form" id="id_study_form" class="ui fluid search dropdown " required="required">
                        <option value="">--- Выберите форму обучения ---</option>
                        @if(isset($study_form))
                            @foreach($study_form as $sf)
                                <option value="{{ $sf->Id }}">{{ $sf->NameRu }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

            </div>

            <div class="four fields">

                <div class="field">
                    <label for="id_course">Курс</label>
                    <select name="id_course" id="id_course" class="ui search dropdown" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
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


            </div>
            <br>
            <div class="form-group" id="btn_state">
                <div class="col-sm-10" id="individual">
                    <button type="button" class="btn btn-warning" onclick="students()">Получить список студентов по специальности</button>
                </div>
            </div>

            <div class="two fields">

                <div class="field" id="student">
                    <label for="id_student">Обучающихся</label>
                    <select name="id_student" id="id_student" class="ui search dropdown">

                    </select>
                </div>
            </div>
            <hr>
            <h3>Данные о проживание</h3>
            <div class="three fields">
                <div class="field">
                    <label for="id_dorm">Укажите общежитие</label>
                    <select name="id_dorm" id="id_dorm" class="ui search dropdown">
                        <option value="0">--- не указано ---</option>
                        @foreach($dorms as $dorm)
                            <option value="{{ $dorm->dormitoryID }}">{{ $dorm->address }}</option>
                        @endforeach    
                    </select>
                </div>
                <div class="field">
                    <label for="room">Комната</label>
                    <input type="text" class="form-control">
                </div>
                <div class="field">
                    <label for="other">Или другой</label>
                    <input type="text" class="form-control">
                </div>
            </div>
            <hr>
            <h3>Спортивные секции</h3>
            <div class="three fields">
                <div class="field">

                </div>
            </div>
        </div>


    </form>
@stop