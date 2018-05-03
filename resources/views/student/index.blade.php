@extends('layouts/student')
@section('content')
    <form class="ui form small" style="margin-top: 50px;" action="{{ url('/user/entrant') }}" method="post" id="kt_f">
            {{ csrf_field() }}
            <div class="ui error message"></div>
            <div id="preloader">
            </div>
            <div class="field">
                <label id ="label_profession" for="id_student">Абитуриент / Обучающихся</label><br>
                @if(Session::has('message'))
                    <div class="alert alert-success alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {!!Session::get('message')!!}
                    </div>
                @endif
                <div class="ui search selection dropdown" id="st">
                    <input type="hidden" name="id_student" required="required">
                    <i class="dropdown icon"></i>
                    <div class="default text">Выберите абитуриента</div>
                    <div class="menu">
                        @foreach($students as $key => $value)
                            <div class="item" data-value="{{ $value->id }}">{{ $value->fio }} | {!! getStudentKtInfo($value->id) !!}</div>
                        @endforeach
                    </div>
                </div>
            </div>


            <br><br>
            {{-- Результаты ЕНТ --}}
            <div class="ui form">
                <div class="inline field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="is_result_ent" id="ENT" tabindex="0" class="hidden">
                        <label><strong>ҰБТ сынақтарының нәтижелері / Результаты ЕНТ</strong></label>
                    </div>
                </div>
            </div>
            <div class="ui form small" style="margin-top: 30px; background: #FBE7E7; display: none; padding: 5px;" id="ENT_FORM">
                <div class="four fields">
                    <div class="field">
                        <label>Сертификат сериясы/Серия сертификата</label>
                        <input type="text" name="seria_certificate3" id="seria_certificate3">
                    </div>
                    <div class="field">
                        <label>Сертификат нөмері/Номер сертификата</label>
                        <input type="text" name="number_certificate3" id="number_certificate3">
                    </div>
                    <div class="field">
                        <label>ТЖК нөмері/Номер ИКТ</label>
                        <input type="text" name="number_ikt3" id="number_ikt3">
                    </div>
                    <div class="field">
                        <label>Ұпай саны/Кол-во баллов</label>
                        <input type="text" name="count_ball3" id="count_ball3">
                    </div>
                </div>
                <br>
                <div class="three fields">
                    <div class="field">
                        <label>Математикалық сауаттылықтан балы/Баллы по математической грамотности</label>
                        <input type="text" name="ball_math3" id="ball_math3">
                    </div>
                    <div class="field">
                        <label>Қазақстан тарихынан балы &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;   Баллы по истории Казахстана</label>
                        <input type="text" name="ball_history3" id="ball_history3">
                    </div>
                    <div class="field">
                        <label>Оқу сауаттылығынан балы/Баллы по грамматическому чтению</label>
                        <input type="text" name="ball_read3" id="ball_read3">
                    </div>
                </div>
                <br>
                <div class="tow fields">
                    <div class="field" style="width: 550px;">
                        <label>1-таңдау пәні/1-профильный предмет</label>
                        <select name="ent_profile_subject1" id="ent_profile_subject1" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="one_ball3" style="width: 220px;" placeholder="Балы / Балл" id="one_ball3">
                    </div>
                    <div class="field" style="width: 550px;">
                        <label>2-таңдау пәні/2-профильный предмет</label>
                        <select name="ent_profile_subject2" id="ent_profile_subject2" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="two_ball3" style="width: 220px;" placeholder="Балы / Балл" id="two_ball3">
                    </div>
                </div>
                <br>
                <div class="tow fields">
                    <div class="field" style="width: 550px;">
                        <label>1-шығармашылық емтихан/1-творческий экзамен</label>
                        <select name="ent_creative_exam1" id="ent_creative_exam1" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="oneExamBall3" style="width: 220px;" placeholder="Балы / Балл" id="oneExamBall3">
                    </div>
                    <div class="field" style="width: 550px;">
                        <label>2-шығармашылық емтихан/2-творческий экзамен</label>
                        <select name="ent_creative_exam2" id="ent_creative_exam2" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="twoExamBall3" style="width: 220px;" placeholder="Балы / Балл" id="twoExamBall3">
                    </div>
                </div>
                <br>
                <div class="field">
                    <label>Арнайы пән (тест) / Специальный предмет (тест)</label>
                    <input type="text" name="special_subject3" style="width: 500px;" id="special_subject3">
                    <input type="text" name="special_ball3" style="width: 220px;" placeholder="Балы / Балл" id="special_ball3">
                </div>
            </div>
            {{-- Результаты ЕНТ --}}


            {{-- Результаты КТ --}}
            <div class="ui form" style="margin-top: 30px;">
                <div class="inline field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="is_result_kt" id="KT" tabindex="0" class="hidden">
                        <label><strong>КТ сынақтарының нәтижелері / Результаты КТ</strong></label>
                    </div>
                </div>
            </div>
            <div class="ui form small" style="margin-top: 30px; background: #FBE7E7; display: none; padding: 5px;" id="KT_FORM">
                <div class="four fields">
                    <div class="field">
                        <label>Сертификат сериясы/Серия сертификата</label>
                        <input type="text" id="seria_certificate" name="seria_certificate">
                    </div>
                    <div class="field">
                        <label>Сертификат нөмері/Номер сертификата</label>
                        <input type="text" id="number_certificate" name="number_certificate">
                    </div>
                    <div class="field">
                        <label>ТЖК нөмері/Номер ИКТ</label>
                        <input type="text" id="number_ikt" name="number_ikt">
                    </div>
                    <div class="field">
                        <label>Ұпай саны/Кол-во баллов</label>
                        <input type="text" id="count_ball" name="count_ball">
                    </div>
                </div>
                <br>
                <div class="three fields">
                    <div class="field">
                        <label>Математикалық сауаттылықтан балы/Баллы по математической грамотности</label>
                        <input type="text" id="ball_math" name="ball_math">
                    </div>
                    <div class="field">
                        <label>Қазақстан тарихынан балы &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;   Баллы по истории Казахстана</label>
                        <input type="text" id="ball_history" name="ball_history">
                    </div>
                    <div class="field">
                        <label>Оқу сауаттылығынан балы/Баллы по грамматическому чтению</label>
                        <input type="text" id="ball_read" name="ball_read">
                    </div>
                </div>
                <br>
                <div class="tow fields">
                    <div class="field" style="width: 550px;">
                        <label>1-таңдау пәні/1-профильный предмет</label>
                        <select id="kt_profile_subject1" name="kt_profile_subject1" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="one_ball" name="one_ball" style="width: 220px;" placeholder="Балы / Балл">
                    </div>
                    <div class="field" style="width: 550px;">
                        <label>2-таңдау пәні/2-профильный предмет</label>
                        <select id="kt_profile_subject2" name="kt_profile_subject2" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="two_ball" name="two_ball" style="width: 220px;" placeholder="Балы / Балл">
                    </div>
                </div>
                <br>
                <div class="tow fields">
                    <div class="field" style="width: 550px;">
                        <label>1-шығармашылық емтихан/1-творческий экзамен</label>
                        <select id="kt_creative_exam1" name="kt_creative_exam1" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="oneExamBall" name="oneExamBall" style="width: 220px;" placeholder="Балы / Балл">
                    </div>
                    <div class="field" style="width: 550px;">
                        <label>2-шығармашылық емтихан/2-творческий экзамен</label>
                        <select id="kt_creative_exam2" name="kt_creative_exam2" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="twoExamBall" name="twoExamBall" style="width: 220px;" placeholder="Балы / Балл">
                    </div>
                </div>
                <br>
                <div class="field">
                    <label>Арнайы пән (тест) / Специальный предмет (тест)</label>
                    <input type="text" id="special_subject" name="special_subject" style="width: 500px;">
                    <input type="text" id="special_ball" name="special_ball" style="width: 220px;" placeholder="Балы / Балл">
                </div>
            </div>
            {{-- Результаты КТ --}}


            {{-- Результаты 1-повторного КТ --}}
            <div class="ui form" style="margin-top: 30px;">
                <div class="inline field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="is_result_kt1" id="KT1" tabindex="0" class="hidden">
                        <label><strong>1-қайталау КТ сынақтарының нәтижелері / Результаты 1-повторного КТ</strong></label>
                    </div>
                </div>
            </div>
            <div class="ui form small" style="margin-top: 30px; background: #FBE7E7; display: none; padding: 5px;" id="KT_FORM1">
                <div class="four fields">
                    <div class="field">
                        <label>Сертификат сериясы/Серия сертификата</label>
                        <input type="text" name="seria_certificate1" id="seria_certificate1">
                    </div>
                    <div class="field">
                        <label>Сертификат нөмері/Номер сертификата</label>
                        <input type="text" name="number_certificate1" id="number_certificate1">
                    </div>
                    <div class="field">
                        <label>ТЖК нөмері/Номер ИКТ</label>
                        <input type="text" name="number_ikt1" id="number_ikt1">
                    </div>
                    <div class="field">
                        <label>Ұпай саны/Кол-во баллов</label>
                        <input type="text" name="count_ball1" id="count_ball1">
                    </div>
                </div>
                <br>
                <div class="three fields">
                    <div class="field">
                        <label>Математикалық сауаттылықтан балы/Баллы по математической грамотности</label>
                        <input type="text" name="ball_math1" id="ball_math1">
                    </div>
                    <div class="field">
                        <label>Қазақстан тарихынан балы &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;   Баллы по истории Казахстана</label>
                        <input type="text" name="ball_history1" id="ball_history1">
                    </div>
                    <div class="field">
                        <label>Оқу сауаттылығынан балы/Баллы по грамматическому чтению</label>
                        <input type="text" name="ball_read1" id="ball_read1">
                    </div>
                </div>
                <br>
                <div class="tow fields">
                    <div class="field" style="width: 550px;">
                        <label>1-таңдау пәні/1-профильный предмет</label>
                        <select name="kt1_profile_subject1" id="kt1_profile_subject1" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="one_ball1" id="one_ball1" style="width: 220px;" placeholder="Балы / Балл">
                    </div>
                    <div class="field" style="width: 550px;">
                        <label>2-таңдау пәні/2-профильный предмет</label>
                        <select name="kt1_profile_subject2" id="kt1_profile_subject2" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="two_ball1" id="two_ball1" style="width: 220px;" placeholder="Балы / Балл">
                    </div>
                </div>
                <br>
                <div class="tow fields">
                    <div class="field" style="width: 550px;">
                        <label>1-шығармашылық емтихан/1-творческий экзамен</label>
                        <select name="kt1_creative_exam1" id="kt1_creative_exam1"  class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="oneExamBall1" id="oneExamBall1" style="width: 220px;" placeholder="Балы / Балл">
                    </div>
                    <div class="field" style="width: 550px;">
                        <label>2-шығармашылық емтихан/2-творческий экзамен</label>
                        <select name="kt1_creative_exam2" id="kt1_creative_exam2" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="twoExamBall1" id="twoExamBall1" style="width: 220px;" placeholder="Балы / Балл">
                    </div>
                </div>
                <br>
                <div class="field">
                    <label>Арнайы пән (тест) / Специальный предмет (тест)</label>
                    <input type="text" name="special_subject1" id="special_subject1" style="width: 500px;">
                    <input type="text" name="special_ball1" id="special_ball1" style="width: 220px;" placeholder="Балы / Балл">
                </div>
            </div>
            {{-- Результаты 1-повторного КТ --}}

            {{-- Результаты 2-повторного КТ --}}
            <div class="ui form" style="margin-top: 30px;">
                <div class="inline field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="is_result_kt2" id="KT2" tabindex="0" class="hidden">
                        <label><strong>2-қайталау КТ сынақтарының нәтижелері / Результаты 2-повторного КТ</strong></label>
                    </div>
                </div>
            </div>
            <div class="ui form small"style="margin-top: 30px; background: #FBE7E7; display: none; padding: 5px;" id="KT_FORM2">
                <div class="four fields">
                    <div class="field">
                        <label>Сертификат сериясы/Серия сертификата</label>
                        <input type="text" name="seria_certificate2" id="seria_certificate2">
                    </div>
                    <div class="field">
                        <label>Сертификат нөмері/Номер сертификата</label>
                        <input type="text" name="number_certificate2" id="number_certificate2">
                    </div>
                    <div class="field">
                        <label>ТЖК нөмері/Номер ИКТ</label>
                        <input type="text" name="number_ikt2" id="number_ikt2">
                    </div>
                    <div class="field">
                        <label>Ұпай саны/Кол-во баллов</label>
                        <input type="text" name="count_ball2" id="count_ball2">
                    </div>
                </div>
                <br>
                <div class="three fields">
                    <div class="field">
                        <label>Математикалық сауаттылықтан балы/Баллы по математической грамотности</label>
                        <input type="text" name="ball_math2" id="ball_math2">
                    </div>
                    <div class="field">
                        <label>Қазақстан тарихынан балы &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;   Баллы по истории Казахстана</label>
                        <input type="text" name="ball_history2" id="ball_history2">
                    </div>
                    <div class="field">
                        <label>Оқу сауаттылығынан балы/Баллы по грамматическому чтению</label>
                        <input type="text" name="ball_read2" id="ball_read2">
                    </div>
                </div>
                <br>
                <div class="tow fields">
                    <div class="field" style="width: 550px;">
                        <label>1-таңдау пәні/1-профильный предмет</label>
                        <select name="kt2_profile_subject1" id="kt2_profile_subject1" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="one_ball2" style="width: 220px;" placeholder="Балы / Балл" id="one_ball2">
                    </div>
                    <div class="field" style="width: 550px;">
                        <label>2-таңдау пәні/2-профильный предмет</label>
                        <select name="kt2_profile_subject2" id="kt2_profile_subject2" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="two_ball2" style="width: 220px;" placeholder="Балы / Балл" id="two_ball2">
                    </div>
                </div>
                <br>
                <div class="tow fields">
                    <div class="field" style="width: 550px;">
                        <label>1-шығармашылық емтихан/1-творческий экзамен</label>
                        <select name="kt2_creative_exam1" id="kt2_creative_exam1" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="oneExamBall2" style="width: 220px;" placeholder="Балы / Балл" id="oneExamBall2">
                    </div>
                    <div class="field" style="width: 550px;">
                        <label>2-шығармашылық емтихан/2-творческий экзамен</label>
                        <select name="kt2_creative_exam2" id="kt2_creative_exam2" class="ui dropdown KT">
                            <option value="0">-- Выберите --</option>
                            @foreach($subjects as $value)
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="twoExamBall2" style="width: 220px;" placeholder="Балы / Балл" id="twoExamBall2">
                    </div>
                </div>
                <br>
                <div class="field">
                    <label>Арнайы пән (тест) / Специальный предмет (тест)</label>
                    <input type="text" name="special_subject2" style="width: 500px;" id="special_subject2">
                    <input type="text" name="special_ball2" style="width: 220px;" placeholder="Балы / Балл" id="special_ball2">
                </div>
            </div>
            {{-- Результаты 2-повторного КТ --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="ui form" style="margin-top: 30px;">
                        <div class="inline field">
                            <div class="ui checkbox">
                                <input type="checkbox" id="winner_olimp" name="winner_olimp" tabindex="0" class="hidden">
                                <label><strong>Республикалық, халықаралық олимпиадалардың, жарыстардың жеңімпазы/Победитель республиканских, международных олимпиад, конкурсов</strong></label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <input type="text" id="winner_olimp_txt" name="winner_olimp_txt" class="form-control" placeholder="Шараның атауы, орны, мерзімі/Название мероприятии, место, дата">
                </div>
                <div class="col-md-6">
                    <div class="ui form" style="margin-top: 30px;">
                        <div class="inline field">
                            <div class="ui checkbox">
                                <input type="checkbox" id="res_program" name="res_program" tabindex="0" class="hidden">
                                <label><strong>Республикалық мәдени бағдарламаларға қатысу/Участие в республиканских культ.-массовых программах</strong></label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <input style="margin-top: 17px;" type="text" class="form-control" id="res_program_txt" name="res_program_txt" placeholder="Например, Жасыл ел">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="field" id="term">
                        <label for="id_term">Спорт.жетістіктер/спорт.достижения</label><br>
                        <select id="sport_achievement" name="sport_achievement" class="ui dropdown" required>
                            <option value="0">-- Выберите --</option>
                            <option value="1">разрядник</option>
                            <option value="2">КМС</option>
                            <option value="3">МС</option>
                            <option value="4">басқа/другое</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Спорт түрі/Вид спорта</label><br><br>
                    <input type="text" id="type_sport" name="type_sport" class="form-control">
                </div>
            </div>
            <div class="row" style="margin-top: 30px;">
                <h4>Университеттегі спорттық секцияларға қатысу ынтасы / Пожелание по участию в спортивных секциях университета</h4>
                <div class="col-md-3">
                    @foreach($sport_sections as $item1)
                        @if($item1->id < 6)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="sport_section[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($sport_sections as $item1)
                        @if($item1->id > 5 AND $item1->id < 11)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="sport_section[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($sport_sections as $item1)
                        @if($item1->id > 10 AND $item1->id < 16)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="sport_section[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($sport_sections as $item1)
                        @if($item1->id > 15)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="sport_section[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="row" style="margin-top: 30px;">
                <h4>Университет клубтарына қатысу ынтасы / Пожелание по участию в клубах университета</h4>
                <div class="col-md-3">
                    @foreach($univer_clubs as $item1)
                        @if($item1->id < 6)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="univer_clubs[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($univer_clubs as $item1)
                        @if($item1->id > 5 AND $item1->id < 11)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="univer_clubs[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($univer_clubs as $item1)
                        @if($item1->id > 10 AND $item1->id < 17)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="univer_clubs[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($univer_clubs as $item1)
                        @if($item1->id > 15)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="univer_clubs[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="row" style="margin-top: 30px;">
                <h4>Қызығушылық бойынша шығармашылық үйірмелерге қатысу ынтасы / Пожелание по участию в творческих кружках по интересам</h4>
                <div class="col-md-3">
                    @foreach($creative_clubs as $item1)
                        @if($item1->id < 6)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="creative_clubs[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($creative_clubs as $item1)
                        @if($item1->id > 5 AND $item1->id < 11)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="creative_clubs[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($creative_clubs as $item1)
                        @if($item1->id > 10 AND $item1->id < 17)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="creative_clubs[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-3">
                    @foreach($creative_clubs as $item1)
                        @if($item1->id > 15)
                            <div class="ui form">
                                <div class="inline field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" name="creative_clubs[]" value="{{ $item1->id }}" tabindex="0" class="hidden">
                                        <label>{{ $item1->title }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <br><br>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
@stop