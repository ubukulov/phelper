@extends('admin/layout/default')
@section('content')
    <section class="content-header">
        <h1>
            {{ $title }}
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
                                <th>Номер карты</th>
                                <th>Пин</th>
                                <th>Дата выпуска</th>
                                <th>Пользователь</th>
                                <th>Баланс</th>
                                <th>Блокировать</th>
                                <th>Экспортировано</th>
                                <th>Отправить пароль</th>
                                <th>Удалить</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($cards as $card) :?>
                            <tr>
                                <td>
                                    {{ $card->code }}
                                </td>
                                <td>
                                    {{ $card->protection_code }}
                                </td>
                                <td>
                                    {{ $card->date }}
                                </td>
                                <td>

                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    <input type="checkbox" value="{{ $card->id }}" @if($card->blocked) checked="checked" @endif>
                                </td>
                                <td>
                                    <input type="checkbox" value="{{ $card->id }}" @if($card->exported) checked="checked" @endif disabled="disabled">
                                </td>
                                <td>
                                @if($card->user_id)
                                    <button class="card_{{ $card->user_id }}" onclick="get_sms_password_to_user({{ $card->user_id }})" style="width: 150px;">отправить пароль</button>
                                @endif
                                </td>
                                <td>
                                    <button onclick="delite_cert({{ $card->id }}); event.cancelBubble = true" class="red">удалить</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    {{ $cards->links() }}
                </div>
                <!-- /.box -->
            </div>
    </section>
@stop