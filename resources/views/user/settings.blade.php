@extends('user/layout/default')
@section('content')
    <div class="rowtsk">
        <form method="post" action="{{ url('user/setting') }}" role="login">
            {{ csrf_field() }}
            <div class="panel-body">
                <h2>Редактирование профиля  </h2>
                <div class="input-group form-group">
                    <span class="input-group-addon"><i class="icon_l fa fa-user"></i></span>
                    <input id="e_firstname" name="firstname" required="required" type="text" placeholder="Введите ваше имя"  class="form-control" value="{{ Auth::user()->firstname }}" />
                </div>
                <div class="input-group form-group">
                    <span class="input-group-addon"><i class="icon_l fa fa-user"></i></span>
                    <input id="e_lastname" name="lastname" required="required" type="text"  placeholder="Введите вашу Фамилию:"  class="form-control" value="{{ Auth::user()->lastname }}" />
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon"><i class="icon_l fa fa-users"></i></span>
                    <select class="form-control" id="e_sex" name="sex">
                        <option @if(Auth::user()->sex == 0) selected="selected" @endif value="0">Не выбрано</option>
                        <option @if(Auth::user()->sex == 1) selected="selected" @endif value="1">Мужской пол</option>
                        <option @if(Auth::user()->sex == 2) selected="selected" @endif value="2">Женский пол</option>
                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon"><i class="icon_l fa fa-calendar"></i></span>
                    <input type="text" id="e_birthday" name="birthday" placeholder="Введите дату рождения"  class="birthday form-control" value="{{ Auth::user()->birthday }}" />
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon"><i class="icon_l fa fa-map-marker"></i></span>
                    <select class="form-control" id="e_city" name="city">
                        <option value="0">Выберите город</option>

                        <?php foreach (getCities() as $city): ?>
                        <option @if ($city->id == Auth::user()->city) selected="selected" @endif value="{{ $city->id }}">{{ $city->city }}</option>
                        <? endforeach; ?>

                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon"><i class="icon_l fa fa-envelope"></i></span>
                    <input type="text" id="e_email" name="email" required="required" placeholder="Введите e-mail"  class="form-control" value="{{ Auth::user()->email }}" />
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon"><i class="icon_l fa fa-mobile"></i></span>
                    <input type="text" id="e_mphone" name="mphone" required="required" placeholder="Введите номер мобильного телефона"  class="mphone form-control" value="{{ Auth::user()->mphone }}" />
                </div>

            </div>
            <div class="panel-footer" align="center">
                <button type="submit" name="go" class="actbutton">Сохранить</button>
            </div>
        </form>
    </div>
@stop