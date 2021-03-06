@extends('layouts/app')
@section('content')
    <form action="{{ url('/user/statement') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="ui form small" style="margin-top: 50px;">
            <div class="three fields">

                <div class="field">
                    <label id ="label_profession" for="id_profession">Специальность</label>
                    <select name="id_profession" id="id_profession" class="ui search dropdown" onchange="study_forms();specialization();" required>
                        <option value="">--- Выберите специальность ---</option>
                        @foreach($professions as $value)
                            <option value="{{ $value->professionID }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="id_specialization">Специализации</label>
                    <select name="id_specialization" id="id_specialization" class="ui fluid search dropdown " required="required">
                        <option value="0">--- Выберите специализация ---</option>
                    </select>
                </div>

                <div class="field">
                    <label for="id_profession">Форма обучения</label>
                    <select name="id_study_form" id="id_study_form" class="ui fluid search dropdown " onchange="list_course();" required="required">
                        <option value="0">--- Выберите форму обучения ---</option>
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
                    <select name="id_course" id="id_course" class="ui dropdown" required>
                        @if(isset($course))
                            @foreach($course as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="field" id="term">
                    <label for="id_term">Семестр</label>
                    <select name="id_term" id="id_term" class="ui dropdown" required>
                        <option value="1">1 семестр</option>
                        <option value="2">2 семестр</option>
                        <option value="3">3 семестр</option>
                        <option value="4">4 семестр</option>
                        <option value="5">5 семестр</option>
                        <option value="6">6 семестр</option>
                    </select>
                </div>
                <div class="field">
                    <label for="id_study_lang">Язык обучения</label>
                    <select name="id_study_lang" id="id_study_lang" class="ui dropdown" required>
                        @foreach($study_lang as $sl)
                            <option value="{{ $sl->Id }}">{{ $sl->NameRU }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="start_date">Год поступление</label>
                    <input type="text" id="start_date" name="start_date" placeholder="2016">
                </div>
            </div>
        </div>

        <br>
        <div class="form-group">
            <div class="col-sm-10">
                <button type="submit" name="summary" id="summary" class="btn btn-success">Формировать и загрузить</button>
            </div>
        </div>
    </form>
@stop