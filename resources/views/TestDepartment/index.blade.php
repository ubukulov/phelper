@extends('layouts/app')
@section('content')
    <form action="{{ url('/test') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group" style="margin-top: 50px;">
            <label for="file" class="col-sm-2 control-label">Выбор файла</label>
            <div class="col-sm-10">
                <input type="file" name="file" id="file" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="submit" class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success">Отправить</button>
            </div>
        </div>
    </form>
	@if (Session::has('message'))
		<div style="margin-top: 15px;" class="alert alert-info">{{ Session::get('message') }}</div>
	@endif
@stop