<!-- loginModal -->
<div id="loginModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 400px;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="border-color: #fff;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <section class="login-form">
                    <form action="{{ url('user/login') }}" method="post">
                        {{ csrf_field() }}
                        <div class="panel-body">
                            <h2>Авторизация</h2>
                            <div class="input-group form-group">
                                <span class="input-group-addon"><i class="icon_l fa fa-mobile"></i></span>
                                <input type="text" id="login_mphone" name="username" placeholder="телефон" required class="form-control mphone" />
                            </div>
                            <div class="input-group form-group">
                                <span class="input-group-addon"><i class="icon_l fa fa-unlock-alt"></i></span>
                                <input type="password" id="login_password" name="password" placeholder="Пароль" required class="form-control" />
                            </div>
                        </div>
                        <div class="panel-footer" align="center">
                            <button type="submit" name="go" class="actbutton">Вход</button>

                            <input type="hidden" id="location_link" value="" />

                            <br /><font style="line-height:50px">
                                <a href="#" onclick="$('#loginModal').modal('hide');
                                    $('#passwordModal').modal('show');">Забыли пароль?</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="#" onclick="$('#loginModal').modal('hide');
                                    $('#regModal').modal('show');">Регистрация</a>
                            </font>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</div>

<div id="passwordModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 400px;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="border-color: #fff;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="panel-body">
                    <h2>Восстановление пароля</h2>
                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="icon_l fa fa-mobile"></i></span>
                        <input id="r_mphone" type="text" name="email" placeholder="телефон" required class="form-control mphone" />
                    </div>
                </div>
                <div class="panel-footer" align="center">
                    <button onclick="get_sms_password_to_user_phone()" type="submit" name="go" class="actbutton">Получить новый пароль</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="regModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 400px;">

        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-header" style="border-color: #fff;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form method="post" action="" role="login" onsubmit="return false;">
                    <div class="panel-body">
                        <h2>Регистрация</h2>
                        <div class="input-group form-group">
                            <span class="input-group-addon"><i class="icon_l fa fa-user"></i></span>
                            <input id="reg_firstname" type="text" placeholder="Введите ваше имя"  class="form-control" />
                        </div>
                        <div class="input-group form-group">
                            <span class="input-group-addon"><i class="icon_l fa fa-user"></i></span>
                            <input id="reg_firstname" type="text"  placeholder="Введите вашу Фамилию:"  class="form-control" />
                        </div>
                        <div class="input-group form-group">
                            <span class="input-group-addon"><i class="icon_l fa fa-envelope"></i></span>
                            <input type="text" id="reg_email" placeholder="Введите e-mail"  class="form-control" />
                        </div>
                        <div class="input-group form-group">
                            <span class="input-group-addon"><i class="icon_l fa fa-mobile"></i></span>
                            <input type="text" id="reg_mphone" placeholder="Введите номер мобильного телефона"  class="mphone form-control" />
                        </div>
                        <div class="input-group form-group">
                            <span class="input-group-addon"><i class="icon_l fa fa-user-plus"></i></span>
                            <input type="text" id="reg_referral" placeholder="ID пригласившего"  class="int form-control" />
                        </div>
                        <input type="checkbox" id="term_of_use" checked="checked" name="remember" value="1" /> Принимаю условия <a target="_blank" href="{{ asset('files/Terms_of_use_Likemoney.me.pdf') }}">соглашения</a>
                    </div>
                    <div class="panel-footer" align="center">
                        <button onclick="register();" type="submit" name="go" class="actbutton">Регистрация</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>