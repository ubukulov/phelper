@extends('layouts/app')
@section('content')
    <hr>
    <div class="row" style="margin: 0px;">
        @foreach($ad_offers['results'] as $item)
            <div class="col-sm-4 col-xs-6 ad_offers">
                <div class="shop-image">
                    <a rel="nofollow" href="{{ url('/admitad/offer/'.$item['id']) }}" target="_blank">
                        <img width="143" height="59" src="{{ $item['image'] }}">
                    </a>
                </div>
                <div class="shop-title">
                    <a rel="nofollow" class="news_title" href="{{ url('/admitad/offer/'.$item['id']) }}" target="_blank">
                        {{ $item['name'] }}
                    </a>
                </div>
                <div class="cashback-rate-info">
                    <span class="current-rate special">5%</span>
                    <span class="">кэшбэк</span>
                </div>
            </div>
        @endforeach
    </div>
@stop