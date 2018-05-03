<ul class="nav navbar-nav navbar-left">
    <li @if($_SERVER['REQUEST_URI'] == '/') class="active-task btn_1" @endif>
        <a href="{{ route('home') }}">Offline cashback</a>
    </li>
    <li @if($_SERVER['REQUEST_URI'] == '/optprice') class="active-task btn_1" @endif>
        <a href="{{ url('/optprice') }}">OptPrice</a>
    </li>
    <li @if($_SERVER['REQUEST_URI'] == '/store') class="active-task btn_1" @endif>
        @if(Auth::check())
            <a href="{{ route('store') }}">Магазин</a>
        @else
            <a style="cursor: pointer" data-toggle="modal" data-target="#loginModal">Магазин</a>
        @endif
    </li>
    {{--<li>--}}
    {{--<a style="cursor: pointer; color: green; font-weight: bold;" href="{{ url('cashback') }}">--}}
    {{--Online cashback--}}
    {{--</a>--}}
    {{--</li>--}}
    <li>
        <a href="https://admotionz.com/drawing" target="_blank">Зарабатывай онлайн</a>
    </li>

</ul>
<!-- /.nav -->