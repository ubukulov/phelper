@extends('layouts/student')
@section('content')
<div id="js_loader" class="js">
    <form class="ui form small" style="margin-top: 50px;" action="{{ url('/user/entrant') }}" method="post" id="kt_f">
        {{ csrf_field() }}
        <div class="ui error message"></div>
        <div id="preloader">
        </div>
        <div class="field">
            <label id ="label_profession" for="id_student">Абитуриент</label><br>
            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {!!Session::get('message')!!}
                </div>
            @endif
            <div class="ui search selection dropdown" id="st">
            <input type="hidden" name="id_student" @if(isset($_SESSION['student_results'])) value="{{ $_SESSION['student_results']['id_student'] }}" @elseif(isset($_SESSION['id_student'])) value="{{ $_SESSION['id_student'] }}" @endif required="required">
            <i class="dropdown icon"></i>
            <div class="default text">Выберите абитуриента</div>
            <div class="menu">
                @foreach($students as $key => $value)
                    @if(isset($_SESSION['student_results']))
                    <div @if(array_key_exists($value->id, $_SESSION['student_results'])) class="item active selected"  @else class="item" @endif data-value="{{ $value->id }}">{{ $value->fio }} | {!! getStudentKtInfo($value->id) !!}</div>
                    @elseif(isset($_SESSION['id_student']))
                    <div @if($value->id == $_SESSION['id_student']) class="item active selected"  @else class="item" @endif data-value="{{ $value->id }}">{{ $value->fio }} | {!! getStudentKtInfo($value->id) !!}</div>
                    @else
                        <div class="item" data-value="{{ $value->id }}">{{ $value->fio }} | {!! getStudentKtInfo($value->id) !!}</div>
                    @endif
                @endforeach
            </div>
            </div>
        </div>


    <br><br>
    {{-- Результаты ЕНТ --}}
    <div class="ui form">
        <div class="inline field">
            <div class="ui checkbox">
                @if(isset($_SESSION['student_ent']))
                <input checked="checked" type="checkbox" name="is_result_ent" id="ENT" tabindex="0" class="hidden">
                @else
                <input type="checkbox" name="is_result_ent" id="ENT" tabindex="0" class="hidden">
                @endif
                <label><strong>ҰБТ сынақтарының нәтижелері / Результаты ЕНТ</strong></label>
            </div>
        </div>
    </div>
    <div class="ui form small" @if(isset($_SESSION['student_ent'])) style="margin-top: 30px; background: #FBE7E7; padding: 5px;"  @else style="margin-top: 30px; background: #FBE7E7; display: none; padding: 5px;" @endif id="ENT_FORM">
        <div class="four fields">
            <div class="field">
                <label>Сертификат сериясы/Серия сертификата</label>
                <input type="text" name="seria_certificate3" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['seria_certificate'] }}" @endif>
            </div>
            <div class="field">
                <label>Сертификат нөмері/Номер сертификата</label>
                <input type="text" name="number_certificate3" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['number_certificate'] }}" @endif>
            </div>
            <div class="field">
                <label>ТЖК нөмері/Номер ИКТ</label>
                <input type="text" name="number_ikt3" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['number_ikt'] }}" @endif>
            </div>
            <div class="field">
                <label>Ұпай саны/Кол-во баллов</label>
                <input type="text" name="count_ball3" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['count_ball'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="three fields">
            <div class="field">
                <label>Математикалық сауаттылықтан балы/Баллы по математической грамотности</label>
                <input type="text" name="ball_math3" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['ball_math'] }}" @endif>
            </div>
            <div class="field">
                <label>Қазақстан тарихынан балы &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;   Баллы по истории Казахстана</label>
                <input type="text" name="ball_history3" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['ball_history'] }}" @endif>
            </div>
            <div class="field">
                <label>Оқу сауаттылығынан балы/Баллы по грамматическому чтению</label>
                <input type="text" name="ball_read3" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['ball_read'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="tow fields">
            <div class="field" style="width: 550px;">
                <label>1-таңдау пәні/1-профильный предмет</label>
                <select name="ent_profile_subject1" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_ent']))
                        <option @if($_SESSION['student_ent']['kt_profile_subject1'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                        <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="one_ball3" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['one_ball'] }}" @endif>
            </div>
            <div class="field" style="width: 550px;">
                <label>2-таңдау пәні/2-профильный предмет</label>
                <select name="ent_profile_subject2" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_ent']))
                            <option @if($_SESSION['student_ent']['kt_profile_subject2'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="two_ball3" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['two_ball'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="tow fields">
            <div class="field" style="width: 550px;">
                <label>1-шығармашылық емтихан/1-творческий экзамен</label>
                <select name="ent_creative_exam1" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_ent']))
                            <option @if($_SESSION['student_ent']['kt_creative_exam1'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="oneExamBall3" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['oneExamBall'] }}" @endif>
            </div>
            <div class="field" style="width: 550px;">
                <label>2-шығармашылық емтихан/2-творческий экзамен</label>
                <select name="ent_creative_exam2" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_ent']))
                            <option @if($_SESSION['student_ent']['kt_creative_exam2'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="twoExamBall3" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['twoExamBall'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="field">
            <label>Арнайы пән (тест) / Специальный предмет (тест)</label>
            <input type="text" name="special_subject3" style="width: 500px;" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['special_subject'] }}" @endif>
            <input type="text" name="special_ball3" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_ent'])) value="{{ $_SESSION['student_ent']['special_ball'] }}" @endif>
        </div>
    </div>
    {{-- Результаты ЕНТ --}}


    {{-- Результаты КТ --}}
    <div class="ui form" style="margin-top: 30px;">
        <div class="inline field">
            <div class="ui checkbox">
                @if(isset($_SESSION['student_kt']))
                <input checked="checked" type="checkbox" name="is_result_kt" id="KT" tabindex="0" class="hidden">
                @else
                <input type="checkbox" name="is_result_kt" id="KT" tabindex="0" class="hidden">
                @endif
                <label><strong>КТ сынақтарының нәтижелері / Результаты КТ</strong></label>
            </div>
        </div>
    </div>
    <div class="ui form small" @if(isset($_SESSION['student_kt'])) style="margin-top: 30px; background: #FBE7E7; padding: 5px;"  @else style="margin-top: 30px; background: #FBE7E7; display: none; padding: 5px;" @endif id="KT_FORM">
        <div class="four fields">
            <div class="field">
                <label>Сертификат сериясы/Серия сертификата</label>
                <input type="text" name="seria_certificate" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['seria_certificate'] }}" @endif>
            </div>
            <div class="field">
                <label>Сертификат нөмері/Номер сертификата</label>
                <input type="text" name="number_certificate" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['number_certificate'] }}" @endif>
            </div>
            <div class="field">
                <label>ТЖК нөмері/Номер ИКТ</label>
                <input type="text" name="number_ikt" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['number_ikt'] }}" @endif>
            </div>
            <div class="field">
                <label>Ұпай саны/Кол-во баллов</label>
                <input type="text" name="count_ball" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['count_ball'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="three fields">
            <div class="field">
                <label>Математикалық сауаттылықтан балы/Баллы по математической грамотности</label>
                <input type="text" name="ball_math" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['ball_math'] }}" @endif>
            </div>
            <div class="field">
                <label>Қазақстан тарихынан балы &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;   Баллы по истории Казахстана</label>
                <input type="text" name="ball_history" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['ball_history'] }}" @endif>
            </div>
            <div class="field">
                <label>Оқу сауаттылығынан балы/Баллы по грамматическому чтению</label>
                <input type="text" name="ball_read" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['ball_read'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="tow fields">
            <div class="field" style="width: 550px;">
                <label>1-таңдау пәні/1-профильный предмет</label>
                <select name="kt_profile_subject1" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt']))
                            <option @if($_SESSION['student_kt']['kt_profile_subject1'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="one_ball" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['one_ball'] }}" @endif>
            </div>
            <div class="field" style="width: 550px;">
                <label>2-таңдау пәні/2-профильный предмет</label>
                <select name="kt_profile_subject2" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt']))
                            <option @if($_SESSION['student_kt']['kt_profile_subject2'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="two_ball" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['two_ball'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="tow fields">
            <div class="field" style="width: 550px;">
                <label>1-шығармашылық емтихан/1-творческий экзамен</label>
                <select name="kt_creative_exam1" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt']))
                            <option @if($_SESSION['student_kt']['kt_creative_exam1'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="oneExamBall" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['oneExamBall'] }}" @endif>
            </div>
            <div class="field" style="width: 550px;">
                <label>2-шығармашылық емтихан/2-творческий экзамен</label>
                <select name="kt_creative_exam2" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt']))
                            <option @if($_SESSION['student_kt']['kt_creative_exam2'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="twoExamBall" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['twoExamBall'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="field">
            <label>Арнайы пән (тест) / Специальный предмет (тест)</label>
            <input type="text" name="special_subject" style="width: 500px;" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['special_subject'] }}" @endif>
            <input type="text" name="special_ball" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt'])) value="{{ $_SESSION['student_kt']['special_ball'] }}" @endif>
        </div>
    </div>
    {{-- Результаты КТ --}}


    {{-- Результаты 1-повторного КТ --}}
    <div class="ui form" style="margin-top: 30px;">
        <div class="inline field">
            <div class="ui checkbox">
                @if(isset($_SESSION['student_kt1']))
                <input checked="checked" type="checkbox" name="is_result_kt1" id="KT1" tabindex="0" class="hidden">
                @else
                <input type="checkbox" name="is_result_kt1" id="KT1" tabindex="0" class="hidden">
                @endif
                <label><strong>1-қайталау КТ сынақтарының нәтижелері / Результаты 1-повторного КТ</strong></label>
            </div>
        </div>
    </div>
    <div class="ui form small" @if(isset($_SESSION['student_kt1'])) style="margin-top: 30px; background: #FBE7E7; padding: 5px;"  @else style="margin-top: 30px; background: #FBE7E7; display: none; padding: 5px;" @endif id="KT_FORM1">
        <div class="four fields">
            <div class="field">
                <label>Сертификат сериясы/Серия сертификата</label>
                <input type="text" name="seria_certificate1" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['seria_certificate'] }}" @endif>
            </div>
            <div class="field">
                <label>Сертификат нөмері/Номер сертификата</label>
                <input type="text" name="number_certificate1" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['number_certificate'] }}" @endif>
            </div>
            <div class="field">
                <label>ТЖК нөмері/Номер ИКТ</label>
                <input type="text" name="number_ikt1" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['number_ikt'] }}" @endif>
            </div>
            <div class="field">
                <label>Ұпай саны/Кол-во баллов</label>
                <input type="text" name="count_ball1" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['count_ball'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="three fields">
            <div class="field">
                <label>Математикалық сауаттылықтан балы/Баллы по математической грамотности</label>
                <input type="text" name="ball_math1" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['ball_math'] }}" @endif>
            </div>
            <div class="field">
                <label>Қазақстан тарихынан балы &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;   Баллы по истории Казахстана</label>
                <input type="text" name="ball_history1" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['ball_history'] }}" @endif>
            </div>
            <div class="field">
                <label>Оқу сауаттылығынан балы/Баллы по грамматическому чтению</label>
                <input type="text" name="ball_read1" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['ball_read'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="tow fields">
            <div class="field" style="width: 550px;">
                <label>1-таңдау пәні/1-профильный предмет</label>
                <select name="kt1_profile_subject1" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt1']))
                            <option @if($_SESSION['student_kt1']['kt_profile_subject1'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="one_ball1" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['one_ball'] }}" @endif>
            </div>
            <div class="field" style="width: 550px;">
                <label>2-таңдау пәні/2-профильный предмет</label>
                <select name="kt1_profile_subject2" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt1']))
                            <option @if($_SESSION['student_kt1']['kt_profile_subject2'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="two_ball1" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['two_ball'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="tow fields">
            <div class="field" style="width: 550px;">
                <label>1-шығармашылық емтихан/1-творческий экзамен</label>
                <select name="kt1_creative_exam1"  class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt1']))
                            <option @if($_SESSION['student_kt1']['kt_creative_exam1'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="oneExamBall1" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['oneExamBall'] }}" @endif>
            </div>
            <div class="field" style="width: 550px;">
                <label>2-шығармашылық емтихан/2-творческий экзамен</label>
                <select name="kt1_creative_exam2" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt1']))
                            <option @if($_SESSION['student_kt1']['kt_creative_exam2'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="twoExamBall1" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['twoExamBall'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="field">
            <label>Арнайы пән (тест) / Специальный предмет (тест)</label>
            <input type="text" name="special_subject1" style="width: 500px;" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['special_subject'] }}" @endif>
            <input type="text" name="special_ball1" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt1'])) value="{{ $_SESSION['student_kt1']['special_ball'] }}" @endif>
        </div>
    </div>
    {{-- Результаты 1-повторного КТ --}}

    {{-- Результаты 2-повторного КТ --}}
    <div class="ui form" style="margin-top: 30px;">
        <div class="inline field">
            <div class="ui checkbox">
                @if(isset($_SESSION['student_kt2']))
                <input checked="checked" type="checkbox" name="is_result_kt2" id="KT2" tabindex="0" class="hidden">
                @else
                <input type="checkbox" name="is_result_kt2" id="KT2" tabindex="0" class="hidden">
                @endif
                <label><strong>2-қайталау КТ сынақтарының нәтижелері / Результаты 2-повторного КТ</strong></label>
            </div>
        </div>
    </div>
    <div class="ui form small" @if(isset($_SESSION['student_kt2'])) style="margin-top: 30px; background: #FBE7E7; padding: 5px;"  @else style="margin-top: 30px; background: #FBE7E7; display: none; padding: 5px;" @endif id="KT_FORM2">
        <div class="four fields">
            <div class="field">
                <label>Сертификат сериясы/Серия сертификата</label>
                <input type="text" name="seria_certificate2" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['seria_certificate'] }}" @endif>
            </div>
            <div class="field">
                <label>Сертификат нөмері/Номер сертификата</label>
                <input type="text" name="number_certificate2" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['number_certificate'] }}" @endif>
            </div>
            <div class="field">
                <label>ТЖК нөмері/Номер ИКТ</label>
                <input type="text" name="number_ikt2" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['number_ikt'] }}" @endif>
            </div>
            <div class="field">
                <label>Ұпай саны/Кол-во баллов</label>
                <input type="text" name="count_ball2" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['count_ball'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="three fields">
            <div class="field">
                <label>Математикалық сауаттылықтан балы/Баллы по математической грамотности</label>
                <input type="text" name="ball_math2" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['ball_math'] }}" @endif>
            </div>
            <div class="field">
                <label>Қазақстан тарихынан балы &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;   Баллы по истории Казахстана</label>
                <input type="text" name="ball_history2" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['ball_history'] }}" @endif>
            </div>
            <div class="field">
                <label>Оқу сауаттылығынан балы/Баллы по грамматическому чтению</label>
                <input type="text" name="ball_read2" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['ball_read'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="tow fields">
            <div class="field" style="width: 550px;">
                <label>1-таңдау пәні/1-профильный предмет</label>
                <select name="kt2_profile_subject1" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt2']))
                            <option @if($_SESSION['student_kt2']['kt_profile_subject1'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="one_ball2" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['one_ball'] }}" @endif>
            </div>
            <div class="field" style="width: 550px;">
                <label>2-таңдау пәні/2-профильный предмет</label>
                <select name="kt2_profile_subject2" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt2']))
                            <option @if($_SESSION['student_kt2']['kt_profile_subject2'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="two_ball2" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['two_ball'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="tow fields">
            <div class="field" style="width: 550px;">
                <label>1-шығармашылық емтихан/1-творческий экзамен</label>
                <select name="kt2_creative_exam1" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt2']))
                            <option @if($_SESSION['student_kt2']['kt_creative_exam1'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="oneExamBall2" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['oneExamBall'] }}" @endif>
            </div>
            <div class="field" style="width: 550px;">
                <label>2-шығармашылық емтихан/2-творческий экзамен</label>
                <select name="kt2_creative_exam2" class="ui dropdown KT">
                    @foreach($subjects as $value)
                        @if(isset($_SESSION['student_kt2']))
                            <option @if($_SESSION['student_kt2']['kt_creative_exam2'] == $value->id) selected="selected" @endif value="{{ $value->id }}">{{ $value->title }}</option>
                        @else
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" name="twoExamBall2" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['twoExamBall'] }}" @endif>
            </div>
        </div>
        <br>
        <div class="field">
            <label>Арнайы пән (тест) / Специальный предмет (тест)</label>
            <input type="text" name="special_subject2" style="width: 500px;" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['special_subject'] }}" @endif>
            <input type="text" name="special_ball2" style="width: 220px;" placeholder="Балы / Балл" @if(isset($_SESSION['student_kt2'])) value="{{ $_SESSION['student_kt2']['special_ball'] }}" @endif>
        </div>
    </div>
    {{-- Результаты 2-повторного КТ --}}
    <div class="row">
        <div class="col-md-6">
            <div class="ui form" style="margin-top: 30px;">
                <div class="inline field">
                    <div class="ui checkbox">
                        @if(isset($_SESSION['student_results']))
                        <input type="checkbox" @if($_SESSION['student_results']['winner_olimp'] == 1) checked="checked" @endif name="winner_olimp" tabindex="0" class="hidden">
                        @else
                        <input type="checkbox" name="winner_olimp" tabindex="0" class="hidden">
                        @endif
                        <label><strong>Республикалық, халықаралық олимпиадалардың, жарыстардың жеңімпазы/Победитель республиканских, международных олимпиад, конкурсов</strong></label>
                    </div>
                </div>
            </div>
            <br>
            @if(isset($_SESSION['student_results']))
            <input type="text" value="{{ $_SESSION['student_results']['winner_olimp_txt'] }}" name="winner_olimp_txt" class="form-control" placeholder="Шараның атауы, орны, мерзімі/Название мероприятии, место, дата">
            @else
            <input type="text" name="winner_olimp_txt" class="form-control" placeholder="Шараның атауы, орны, мерзімі/Название мероприятии, место, дата">
            @endif
        </div>
        <div class="col-md-6">
            <div class="ui form" style="margin-top: 30px;">
                <div class="inline field">
                    <div class="ui checkbox">
                        @if(isset($_SESSION['student_results']))
                        <input @if($_SESSION['student_results']['res_program'] == 1) checked="checked" @endif type="checkbox" name="res_program" tabindex="0" class="hidden">
                        @else
                        <input type="checkbox" name="res_program" tabindex="0" class="hidden">
                        @endif
                        <label><strong>Республикалық мәдени бағдарламаларға қатысу/Участие в республиканских культ.-массовых программах</strong></label>
                    </div>
                </div>
            </div>
            <br>
            @if(isset($_SESSION['student_results']))
            <input type="text" value="{{ $_SESSION['student_results']['res_program_txt'] }}" class="form-control" name="res_program_txt" placeholder="Например, Жасыл ел">
            @else
            <input type="text" class="form-control" name="res_program_txt" placeholder="Например, Жасыл ел">
            @endif
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="field" id="term">
                <label for="id_term">Спорт.жетістіктер/спорт.достижения</label><br>
                <select name="sport_achievement" class="ui dropdown" required>
                    @if(isset($_SESSION['student_results']))
                    <option @if($_SESSION['student_results']['sport_achievement'] == 1) selected="selected" @endif value="1">разрядник</option>
                    <option @if($_SESSION['student_results']['sport_achievement'] == 2) selected="selected" @endif value="2">КМС</option>
                    <option @if($_SESSION['student_results']['sport_achievement'] == 3) selected="selected" @endif value="3">МС</option>
                    <option @if($_SESSION['student_results']['sport_achievement'] == 4) selected="selected" @endif value="4">басқа/другое</option>
                    @else
                    <option value="1">разрядник</option>
                    <option value="2">КМС</option>
                    <option value="3">МС</option>
                    <option value="4">басқа/другое</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <label>Спорт түрі/Вид спорта</label>
            @if(isset($_SESSION['student_results']))
            <input type="text" value="{{ $_SESSION['student_results']['type_sport'] }}" name="type_sport" class="form-control">
            @else
            <input type="text" name="type_sport" class="form-control">
            @endif
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
</div>
@stop