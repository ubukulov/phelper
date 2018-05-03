@extends('layouts/app')
@section('content')
    <form action="{{ url('/user/settings') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="ui form small" style="margin-top: 50px;">
            <div class="two fields">
                <div class="field">
                    <label id ="username" for="username">Логин</label>
                    <input type="text" disabled="disabled"  value="{{ $user->Login }}">
                </div>
                <div class="field">
                    <label id ="password" for="password">Пароль</label>
                    <input type="text" name="password" required="required"/>
                </div>
            </div>

            <br>
            <div class="form-group">
                <div class="col-sm-10">
                    <button type="submit" name="submit" id="submit" class="btn btn-success">Изменить пароль</button>
                </div>
            </div>
        </div>
    </form>
    <br>
    @if(Session::has('message'))
    <div class="alert alert-info">
        {!! Session::get('message') !!}
    </div>
    @endif
@stop