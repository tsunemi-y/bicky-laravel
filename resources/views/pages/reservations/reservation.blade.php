@extends('layout.common')
@include('layout.header')
@section('description')
    <meta name="description" content="平日・休日の訓練を受け付けます。言葉の遅れ、発達障害、構音障害などが気になられましたらご予約ください。">
@endsection

@section('title')
    <title>予約 | ビッキーことば塾 | 島本町・大山崎町・高槻市 | 発達障害や言葉の遅れが気になる子供の訓練</title>
@endsection

@section('pageCss')
    <link rel="stylesheet" href="{{ asset('css/reservation.css') }}">
@endsection

@section('pageJs')
    <script src="{{ asset('js/reservation.js') }}"></script>  
@endsection

@section('content')

    <div class="row">
        <div class="col">
            <x-breadcrumb item="TOP" item2="予約" url="https://bicky.herokuapp.com/reservation" />
            <h1 class="main-title">予約</h1>
            <div class="mb-4">
              <h3>予約方法</h3>
              <p>
                1. 予約カレンダーから日付を指定<br>
                　※○になっている日付のみ選択可能<br>
                2. 時間を選択<br>
              </p>
            </div>

            <div class="mb-4">
              <h3>お知らせ</h3>
              <p class="text-danger">
                当面の間は、平日のご予約を承っておりません。<br>
                ご迷惑をおかけしますが、ご理解の程、よろしくお願い致します。
              </p>
            </div>

            {{-- 予約カレンダー ここから--}}
            <table class="reserv-table">
                <caption><a href="?ym={{ $calenderInfo['prevMonth'] }}" class="month-select" style="padding-right: 1.5rem">&lt;</a>{{ $calenderInfo['calenderTitle'] }}<a href="?ym={{ $calenderInfo['nextMonth'] }}" class="month-select" style="padding-left: 1.5rem">&gt;</a></caption>
                <thead>
                    <tr>
                        <th>日</th>
                        <th>月</th>
                        <th>火</th>
                        <th>水</th>
                        <th>木</th>
                        <th>金</th>
                        <th>土</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calenderInfo['calenders'] as $calender)
                        {!! $calender !!}
                    @endforeach
                </tbody>
            </table>
            {{-- 予約カレンダー ここまで--}}
        </div>
    </div>

  {{-- 予約時間選択モーダル --}}
  <div class="modal fade" id="avaTimeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <p class="modal-title" id="exampleModalLabel">下記時間からご選択ください。</p>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="jsAvaTime"></div>
          <form action="{{ route('store') }}" method="post" id="jsAvaTimeForm">
            @csrf
            <input type="hidden" value="" id="jsRequestedAvaDate" name="avaDate">
            <input type="hidden" value="" id="jsRequestedAvaTime" name="avaTime">
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- 予約成功モーダル --}}
  @if (session('successReservation'))
    <div class="modal fade" id="successedReservation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
            {{-- <div class="alert alert-success top-alert"> --}}
                <p>{!! session('successReservation') !!}</p>
            {{-- </div>    --}}
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- 予約失敗モーダル --}}
  @if (session('failedReservation'))
    <div class="modal fade" id="failedReservation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
            {{-- <div class="alert alert-success top-alert"> --}}
                <p>{!! session('failedReservation') !!}</p>
            {{-- </div>    --}}
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- 予約キャンセルモーダル --}}
  @if (session('reservationCancel'))
    <div class="modal fade" id="reservationCancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
            {{-- <div class="alert alert-success top-alert"> --}}
                <p>{!! session('reservationCancel') !!}</p>
            {{-- </div>    --}}
          </div>
        </div>
      </div>
    </div>
  @endif

    <script>
      const avaTimes = @json($calenderInfo['avaTimes']);
      const isSuccessedReservation = @json(!empty(session('successReservation')));
      const isFailedReservation = @json(!empty(session('failedReservation')));
      const isCanceledReservation = @json(!empty(session('reservationCancel')));
    </script>  
    
@endsection
