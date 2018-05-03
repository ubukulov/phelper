@extends('admin/layout/default')
@section('content')
    <section class="content-header">
        <h1>
            Список категории
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Позиция</th>
                                <th>Родитель</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($cat as $opt) :?>
                            <tr>
                                <td>
                                    {{ $opt->id }}
                                </td>
                                <td>
                                    {{ $opt->title }}
                                </td>
                                <td>
                                    {{ $opt->position }}
                                </td>
                                <td>

                                </td>
                                <td>
                                    <a href="#" class="btn btn-warning">Редактировать</a>
                                    <a href="#" class="btn btn-danger">Удалить</a>
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
        </div>
    </section>
@stop