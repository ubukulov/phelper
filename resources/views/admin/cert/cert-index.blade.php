@extends('admin/layout/default')
@section('content')
    <section class="content-header">
        <h1>
            Список задании
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <a href="{{ url('admin/cert/new') }}"><i class="fa fa-file-o"></i>&nbsp;&nbsp;Добавить задания</a>
                        <br><br>
                        @if(Session::has('message'))
                        <div class="alert alert-success">
                            {!!Session::get('message')!!}
                        </div>
                        @endif
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Партнер</th>
                                    <th>Название</th>
                                    <th>Покупок</th>
                                    <th>Дата публикации</th>
                                    <th>Дата окончания</th>
                                    <th colspan="2">Действие</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($certs as $cert) :?>
                                <tr>
                                    <td>
                                        {{ $cert->id }}
                                    </td>
                                    <td>
                                        <img src="{{ asset('uploads/certs/small/'.$cert->image) }}" alt="" height="40" width="60" />{{ $cert->partner }}
                                    </td>
                                    <td>
                                        {{ $cert->title }}
                                    </td>
                                    <td>
                                        {{ $cert->purchased }}
                                    </td>
                                    <td>
                                        {{ date("d-m-Y H:i:s", $cert->date_start) }}
                                    </td>
                                    <td>
                                        {{ date("d-m-Y H:i:s", $cert->date_end) }}
                                    </td>
                                    <td>
                                        <a href="{{ url('admin/cert/'.$cert->id) }}" class="btn btn-warning">Редактировать</a>
                                    </td>
                                    <td>
                                        <button onclick="delete_cert({{ $cert->id }});" class="btn btn-danger">Удалить</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    {{ $certs->links() }}
                </div>
                <!-- /.box -->
        </div>
    </section>
@stop