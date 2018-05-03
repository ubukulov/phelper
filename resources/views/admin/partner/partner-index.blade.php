@extends('admin/layout/default')
@section('content')
    <section class="content-header">
        <h1>
            Список партнеров
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
                                <th>Телефон</th>
                                <th>Адрес</th>
                                <th>Логин</th>
                                <th>Пароль</th>
                                <th>Удалить</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($partners as $partner) :?>
                            <tr>
                                <td>
                                    {{ $partner->id }}
                                </td>
                                <td>
                                    {{ $partner->name }}
                                </td>
                                <td>
                                    {{ $partner->phone }}
                                </td>
                                <td>
                                    {{ $partner->address }}
                                </td>
                                <td>
                                    {{ $partner->login }}
                                </td>
                                <td>
                                    {{ $partner->pass }}
                                </td>
                                <td>
                                    <button onclick="delite_cert({{ $partner->id }}); event.cancelBubble = true" class="red">удалить</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    {{ $partners->links() }}
                </div>
                <!-- /.box -->
            </div>
    </section>
@stop