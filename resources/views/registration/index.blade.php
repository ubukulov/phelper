@extends('layouts/app')
@section('content')
    <form action="{{ url('/statement') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="ui form small" style="margin-top: 50px;">
            <div class="three fields">

                <div class="field">
                    <label for="id_report">Выберите ведомость</label>
                    <select name="id_report" id="id_report" class="ui search dropdown" onchange="list_students();">
                        <option value="1">1) Ведомость РК1-РК2</option>
                        <option value="2">2) Явочный лист</option>
                        <option value="3">3) Явочный лист-тест</option>
                        <option value="4">4) Ведомость ПА</option>
                        <option value="5">5) Ведомость Итоговая</option>
                        <option value="6">6) Индивидуальная ведомость рейтинга</option>
                        <option value="7">7) Индивидуальная ведомость ПА</option>
                        <option value="8">8) Сводная ведомость</option>
                        <!--<option value="9">9) Сводная ведомость стипендиятов</option>-->
                        <option value="10">9) Ведомость защиты курсовых работ, всех видов практик</option>
                        <option value="11">10) Направление в летный семестр</option>
                        <option value="12">11) Рейтинг обучающихся</option>
                    </select>
                </div>

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
                    <select name="id_specialization" onchange="list_groups();" id="id_specialization" class="ui fluid dropdown " required="required">
                        <option value="0">--- Выберите специализация ---</option>
                    </select>
                </div>

            </div>

            <div class="five fields">
				
                <div class="field">
                    <label for="id_group">Группа</label>
                    <select name="id_group" id="id_group" class="ui fluid dropdown " required="required">
                        <option value="0">--- Выберите группу ---</option>
                    </select>
                </div>
				
                <div class="field">
                    <label for="id_profession">Форма обучения</label>
                    <select name="id_study_form" id="id_study_form" class="ui fluid dropdown " onchange="list_course();" required="required">
                        <option value="0">--- Выберите форму обучения ---</option>
                        @if(isset($study_form))
                            @foreach($study_form as $sf)
                                <option value="{{ $sf->Id }}">{{ $sf->NameRu }}</option>
                            @endforeach
                        @endif
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

                <div class="field" id="term">
                    <label for="id_term">Семестр</label>
                    <select name="id_term" id="id_term" class="ui dropdown" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
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
            </div>

            <div class="two fields" id="type_summary" style="display: none;">
                <div class="field">
                    <label for="id_summary">Тип</label>
                    <select name="id_summary" id="id_summary" class="ui dropdown" required>
                        <option value="0">Обычный</option>
                        <option value="1">Конкурс</option>
                    </select>
                </div>

                <div class="field">
                    <label for="start_date">Год поступление</label>
                    <input type="text" id="start_date" name="start_date" placeholder="2016">
                </div>
            </div>

            <div class="five fields">
                <div class="field" id="data1">
                    <label for="time">Рейтинг1</label>
                    <input type="text" id="time1" name="date_time1">
                </div>

                <div class="field" id="data2">
                    <label for="time">Экзамен</label>
                    <input type="text" id="time2" name="date_time2">
                </div>
            </div>
			
			<div class="field">
                <div class="inline field">
                    <div class="ui checkbox">
                        <input type="checkbox" id="with_group" name="with_group" tabindex="0" class="hidden">
                        <label>Без группа</label>
                    </div>
                </div>
            </div>
			
            <br>
            <div class="form-group" id="btn_state">
                <div class="col-sm-10" id="statement">
                    <button type="button" class="btn btn-warning" onclick="statement()">Запросить данные по выбранной специальности</button>
                </div>
                <div class="col-sm-10" style="display: none;" id="individual">
                    <button type="button" class="btn btn-warning" onclick="individual()">Запросить данные по выбранной специальности</button>
                </div>
            </div>

            <div class="two fields">
                <div class="field" id="discipline">
                    <label for="id_subject">Дисциплина</label>
                    <select name="id_subject" id="id_subject" class="ui search dropdown" onchange="lists_of_teacher_and_stream()">

                    </select>
                </div>
                <div class="field" id="str">
                    <label for="id_stream">Поток</label>
                    <select name="id_stream" id="id_stream" class="ui search dropdown">

                    </select>
                </div>
                <div class="field" id="branch" style="display: none;">
                    <label for="stream">Отдел</label>
                    <select name="branch" id="branch" class="ui search dropdown">
                        <option value="1" selected="selected">Управление регистрации</option>
                        <option value="2">Отдел контроля и оценки</option>
                    </select>
                </div>
                <div class="field" id="student" style="display: none;">
                    <label for="id_student">Обучающихся</label>
                    <select name="id_student" id="id_student" class="ui search dropdown">

                    </select>
                </div>
                <div class="field" id="data" style="display: none;">
                    <label for="time">Дата</label>
                    <input type="text" id="time" name="date_time">
                </div>
            </div>

            <div class="two fields">
                <div class="field" id="type">
                    <label for="stream">Тип ведомость</label>
                    <select name="type" id="type" class="ui search dropdown">
                        <option value="0" selected="selected">Обычный</option>
                        <option value="1">с учетом апелляции (Управление регистрации)</option>
                        <option value="2">с учетом апелляции (Центр тестирование)</option>
                        <option value="3">дополнительный семестр</option>
                    </select>
                </div>
				
				<div class="field" id="data">
                    <label for="fio_teacher">ФИО преподавателя</label>
                    <input type="text" id="fio_teacher" name="fio_teacher" placeholder="">
                </div>
            </div>


        </div>

        <br>
        <div class="form-group">
            <div class="col-sm-10">
                <button type="submit" name="submit" id="submit" class="btn btn-success" onclick="check_form_data()" disabled="disabled">Формировать и загрузить</button>
                <button style="display: none;" type="submit" name="summary" id="summary" class="btn btn-success">Формировать и загрузить</button>
            </div>
        </div>
    </form>
@stop