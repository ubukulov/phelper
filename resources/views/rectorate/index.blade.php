@extends('layouts/rectorate')
@section('content')
<div class="row" style="margin-top: 80px; margin-left: 270px;">
    <h1>Список отчетов</h1>
    <hr>
    <ol>
        <li><a href="{{ url('/rectorate/report/1') }}">Информация по обеспеченности студентов местами в общежитиях по состоянию на {{ date("d.m.Y") }}</a></li>
    </ol>
</div>
@stop