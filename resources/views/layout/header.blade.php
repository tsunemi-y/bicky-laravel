@section('header')
    {{-- spメニュー中身 ここから --}}
    <div class="menu-inner position-fixed sp-header" id="js-menu-inner">
        @if (!Auth::check())
            <div><a href="{{ route('register') }}" class="menu-inner__link">新規登録</a></div>
            <div><a href="{{ route('login') }}" class="menu-inner__link">ログイン</a></div>
        @endif
        <div><a href="{{ route('greeting') }}" class="menu-inner__link">ご挨拶</a></div>
        <div><a href="{{ route('overview') }}" class="menu-inner__link">訓練内容</a></div>
        <div><a href="{{ route('fee') }}" class="menu-inner__link">料金・プラン</a></div>
        <div><a href="{{ route('reservationTop') }}" class="menu-inner__link">予約</a></div>
        <div><a href="{{ route('remote') }}" class="menu-inner__link">遠方の方へ</a></div>
        <div><a href="{{ route('introduction') }}" class="menu-inner__link">指導員紹介</a></div>
        <div><a href="{{ route('access') }}" class="menu-inner__link">アクセス</a></div>
    </div>
    {{-- spメニュー中身 ここまで --}}

    {{-- ハンバーガーメニュー　ここから --}}
    <div class="menu sp-header" id="js-menu">
        <span class="menu__top" id="js-menu-top"></span>
        <span class="menu__middle" id="js-menu-middle"></span>
        <span class="menu__bottom" id="js-menu-bottom"></span>
    </div>
    {{-- ハンバーガーメニュー　ここまで --}}

    <header class="header">
        <div class="align-items-center container d-flex header__item">
            <div class="header-title mr-2"><a href="{{ route('top') }}"><img src="{{ asset('img/logo.png') }}"
                        alt=""></a></div>
            {{-- pcメニュー　ここから --}}
            <div class="pc-header">
                @if (!Auth::check())
                    <div><a href="{{ route('register') }}" class="pc-header__item">新規登録</a></div>
                    <div><a href="{{ route('login') }}" class="pc-header__item">ログイン</a></div>
                @endif
                <div><a href="{{ route('greeting') }}" class="pc-header__item">ご挨拶</a></div>
                <div><a href="{{ route('overview') }}" class="pc-header__item">訓練内容</a></div>
                <div><a href="{{ route('fee') }}" class="pc-header__item">料金・プラン</a></div>
                <div><a href="{{ route('reservationTop') }}" class="pc-header__item">予約</a></div>
                <div><a href="{{ route('remote') }}" class="pc-header__item">遠方の方へ</a></div>
                <div><a href="{{ route('introduction') }}" class="pc-header__item">指導員紹介</a></div>
                <div><a href="{{ route('access') }}" class="pc-header__item">アクセス</a></div>
            </div>
            {{-- pcメニュー　ここまで --}}
        </div>
    </header>
@endsection

@section('footer')
    <footer class="align-middle footer">
        <p class="footer__content">Copyright (c) bicky All Rights Reserved.</p>
    </footer>
@endsection

@section('heroImg')
    <div class="hero-img mb-4">
        <p class="hero-img__text">
            可能性は、<br>
            無限大
        </p>
    </div>
@endsection
