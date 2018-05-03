@extends('layouts/app')
@section('content')
    <form action="{{ url('user/change') }}" method="post" class="form-horizontal" style="margin-top: 50px;">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="id_tutor" class="col-sm-2 control-label">Методист</label>
            <div class="col-sm-10">
                <select name="id_tutor" id="id_tutor" class="form-control" onchange="contingent()">
                    <option value="0">--- Выберите методисть --- </option>
                @foreach($methodists as $m)
                    <option value="{{ $m->TutorID }}">
                        {{ $m->lastname. " ".$m->firstname." ".$m->patronymic }}
                    </option>
                @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="id_profession" class="col-sm-2 control-label">Специальность</label>
            <div class="col-sm-10">
                <select name="id_profession" id="id_profession" class="form-control" required onchange="list_of_study_forms()">
                    <option value="0">--- Выберите специальность ---</option>
                    <optgroup label="Бакалавр">
                        @foreach($profession as $p)
                            @if(substr($p->name,0,1) == 5)
                        <option value="{{ $p->id }}">
                            {{ $p->name }}
                        </option>
                            @endif
                        @endforeach
                    </optgroup>
                    <optgroup label="Магистратура">
                        @foreach($profession as $p)
                            @if(substr($p->name,0,2) == '6M')
                                <option value="{{ $p->id }}">
                                    {{ $p->name }}
                                </option>
                            @endif
                        @endforeach
                    </optgroup>
                    <optgroup label="Докторантура">
                        @foreach($profession as $p)
                            @if(substr($p->name,0,2) == '6D')
                                <option value="{{ $p->id }}">
                                    {{ $p->name }}
                                </option>
                            @endif
                        @endforeach
                    </optgroup>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Форма обучения и курс </label>
            <div class="col-sm-10" id="study_form">

            </div>
        </div>

        <div class="form-group">
            <label for="submit" class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
        <th>Специальность</th><th>Форма обучения</th><th>Курс</th><th>Кол-во</th><th>Удалить</th>
        </thead>
        <tbody id="contingent">

        </tbody>
    </table>
    <p id="p_count">Итого: <span id="count"></span> обучающихся</p>
@stop