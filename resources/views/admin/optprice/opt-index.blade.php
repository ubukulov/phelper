@extends('admin/layout/default')
@section('content')
    <section class="content-header">
        <h1>
            Список прайсов
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
                                <th>Партнер</th>
                                <th>Название</th>
                                <th>Покупок</th>
                                <th>Кол-во видов</th>
                                <th>Дата публикации</th>
                                <th colspan="2">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($opt_main as $opt) :?>
                            <tr>
                                <td>
                                    {{ $opt->id }}
                                </td>
                                <td class="avatar">
                                    <img src="{{ asset('uploads/opt_price/partners/small/'.getOptPartnerLogo($opt->id_partner)) }}" alt="" height="40" width="60" />
                                    {{ getOptPartnerData($opt->id_partner)->title }}
                                </td>
                                <td>
                                    {{ $opt->title }}
                                </td>
                                <td>

                                </td>
                                <td>
                                    {{ $opt->count_type }}
                                </td>
                                <td>
                                    {{ $opt->created_at }}
                                </td>
                                <td>
                                    <a href="#" class="btn btn-warning">Редактировать</a>
                                </td>
                                <td>
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
    </section>
@stop