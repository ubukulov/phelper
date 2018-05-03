@extends('admin/layout/default')
@section('content')
    <section class="content-header">
        <h1>
            Список партнеров
        </h1>
    </section>
    <section class="content">
        <a href="{{ url('/admin/opt_partner/new') }}" class="btn btn-adn">Добавить партнер</a>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        @if(Session::has('message'))
                            <div class="alert alert-success">
                                {!!Session::get('message')!!}
                            </div>
                        @endif
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Дата регистрации</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($partner as $opt) :?>
                            <tr>
                                <td>
                                    {{ $opt->id }}
                                </td>
                                <td class="avatar">
                                    <img src="{{ asset('uploads/opt_price/partners/small/'.$opt->logo) }}" alt="" height="40" width="60" />
                                    {{ $opt->title }}
                                </td>
                                <td>
                                    {{ $opt->created_at }}
                                </td>
                                <td>
                                    <a href="{{ url('/admin/opt_partner/'.$opt->id) }}" class="btn btn-warning">Редактировать</a>
                                    {{--<a href="{{ url('/admin/opt_partner/delete/'.$opt->id) }}" class="btn btn-danger">Удалить</a>--}}
                                    <button onclick="delete_opt_partner({{ $opt->id }});" class="btn btn-danger">Удалить</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    {{--                    {{ $opt_main->links() }}--}}
                </div>
                <!-- /.box -->
            </div>
    </section>
@stop