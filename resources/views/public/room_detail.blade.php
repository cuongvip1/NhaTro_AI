@extends('layouts.app')
@section('title', $item['tieu_de'])
@section('content')
    <article class="card">
        <img src="{{ $item['anh'][0]['url'] ?? '/room.jpg' }}" alt="" style="width:100%;max-height:420px;object-fit:cover">
        <div class="container">
            <h2>{{ $item['tieu_de'] }}</h2>
            <p><strong>{{ number_format($item['gia_niem_yet'], 0, ',', '.') }}₫/tháng</strong></p>
            <p>{{ $item['phong']['day_tro']['dia_chi'] ?? '' }}</p>
            <details>
                <summary>Tiện ích</summary>
                <ul>
                    @foreach(($item['phong']['tien_ich'] ?? []) as $ti)
                        <li>{{ $ti['ten'] }}</li>
                    @endforeach
                </ul>
            </details>
            <hr>
            <p style="white-space:pre-line">{{ $item['mo_ta'] }}</p>
        </div>
    </article>
@endsection