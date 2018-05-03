@extends('admin/layout/default')
@section('content')
    <section class="content-header">
        <h1>
            Список пользователей
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
                                <th>Реферал</th>
                                <th>Имя</th>
                                <th>Статус</th>
                                <th>Телефон</th>
                                <th>Пароль</th>
                                <th>Авторизация</th>
                                <th>Номер карты</th>
                                <th>Баланс</th>
                                <th>Сколько зарег.</th>
                                <th>Дата регистрации</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($users as $user) :?>
                            <tr>
                                <td>
                                    {{ $user->id }}
                                </td>
                                <td>
                                    {{ $user->referral }}
                                </td>
                                <td>
                                    <a href="#">
                                        @if($user->avatar)
                                        <img src="{{ asset('uploads/users/small/'.$user->avatar) }}" alt="" height="40" width="40" />
                                        @else
                                        <img src="{{ asset('img/blank_avatar_220.png') }}" alt="" height="40" width="40" />
                                        @endif
                                        {{ $user->firstname . " " .$user->lastname }}
                                    </a>
                                </td>
                                <td>

                                </td>
                                <td>
                                    {{ $user->mphone }}
                                </td>
                                <td>
                                    {{ $user->password }}
                                </td>
                                <td>
                                    <button class="blue" >войти</button>
                                </td>
                                <td>
                                    {{ getCardNumberByUserID($user->id) }}
                                </td>
                                <td>
                                    {{ $user->fm }}
                                </td>
                                <td>
                                    {{ date("d-m-Y H:i:s", $user->reg_date) }}
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    {{ $users->links() }}
                </div>
                <!-- /.box -->
            </div>
    </section>
@stop