@extends('layout.common')
@include('layout.header')
@section('pageCss')
    <link rel="stylesheet" href="{{ asset('css/reservation.css') }}">
@endsection
@section('pageJs')
    <script src="{{ asset('js/reservation.js') }}"></script>  
@endsection
@section('content')
    <div class="row">
        <div class="col mt-4">
          <h1 class="main-title">予約キャンセル</h1>
        <form action="{{ route('destroy', ['reservation' => $reservation->id] ) }}" method="post" class="w-100 mx-auto">
            @csrf
            <div>
                <div>
                    <label>【予約日】</label>
                    <p>{{ $reservation->reservation_date }}</p>
                </div>
                <div class="mt-3">
                    <label>【予約時間】</label>
                    <p>{{ $reservation->reservation_time }}</p>
                </div>
            </div>
            <div class="text-center mt-5">
                <input type="submit" value="キャンセル" class="btn btn-secondary w-100">
            </div>
        </form>
        </div>
      </div>
      
@endsection