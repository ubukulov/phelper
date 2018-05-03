@extends('user/layout/default')
@section('content')
    <div class="row">
        <div class="rowtsk-bkg">
            <img src="{{ asset('img/card.png') }}" alt="card_likemone.me" align="right" class="img-responsive">
            <div class="blog-heading">
                <h3>Карта Likemoney.me</h3>

            </div>

            <input value="88888888" id="account_card_num" class="form-control int" maxlength="8" placeholder="Номер карты" disabled="disabled">

            <div class="c1">
                <span class="error_search_account_card"></span>
            </div>
            <br />
            <br />
            <a href="#">Какие возможности дает карта?</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="#">Где можно приобрести карту?</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="#">Как сменить пин-код карты?</a>
        </div>
    </div>

    <div class="rowtsk">
        <div class="blog-heading">
            <h3>Вывод средств</h3>
        </div>
        <table class="table">
            <tr>
                <td>
                    <div class="form-inline">
                        <img src="{{ asset('img/li_1.png') }}">
                        <div class="form-group">
                            <label for="exampleInputName2">&nbsp;&nbsp;Укажите сумму:&nbsp;&nbsp;&nbsp;</label>
                            <input maxlength="5" id="vyvod_amount" name="vyvod_amount" type="text" class="form-control int" placeholder="Введите сумму">
                        </div>
                        <span id="vyvod_commission">-комиссия%</span>
                        <font color="#d7d7d7"><i class="fa fa-question-circle fa-2"></i></font>&nbsp;
                        <button onclick="account.vyvod_send_sms_code()" id="btn_vyvod" type="submit" class="btn btn-danger">вывод</button>

                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="form-inline">
                        <img src="{{ asset('img/li_2.png') }}">
                        <div class="form-group">
                            <label for="exampleInputName2">&nbsp;&nbsp;К выводу:&nbsp;&nbsp;&nbsp;</label>
                            <input style="width: 100px" disabled="" id="vyvod_amount_total" name="amount" type="text" class="form-control int" placeholder="сумма">
                            тг.
                            <input rel="txtTooltip" maxlength="4" style="width: 100px; display: none" id="vyvod_sms" class="form-control int" placeholder="смс код" data-toggle="tooltip" title="Введите 4-х значный смс код который был выслан на ваш телефон" />
                        </div>

                        <button onclick="account.vyvod_get()" id="btn_vyvod_get" style="display: none"   class="btn btn-danger">подтвердить</button>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@stop