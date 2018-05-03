@extends('layouts/app')
@section('content')
    <table class="table table-striped" style="margin-top: 50px;">
        <thead>
        <th>#</th><th>ФИО</th><th>Логин</th><th>Кол-во контингент</th>
        </thead>
        <tbody>
        @foreach($methodists as $m)
            <tr>
                <td>{{ $m->TutorID }}</td>
                <td>{{ $m->lastname." ".$m->firstname." ".$m->patronymic }}</td>
                <td>{{ $m->Login }}</td>
                <td>{{ $m->sum }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop