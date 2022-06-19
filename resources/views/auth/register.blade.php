@extends('layout.common')
@include('layout.header')
@section('pageJs')
    <script src="{{ asset('js/register.js') }}"></script>
@endsection
@section('content')
    <div class="row">
        <div class="col">
            <x-breadcrumb item="TOP" item2="新規登録" url="https://bicky.herokuapp.com/register" />
            <h1 class="main-title">新規登録</h1>
            {{-- 新規登録フォーム ここから --}}
            <div>
                <form action="{{ route('register') }}" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="parentName" class="form-label">保護者氏名<span class="text-danger">【必須】</span></label>
                        <input type="text" class="form-control" id="parentName" name="parentName" placeholder="田中太郎"
                            value="{{ old('parentName') }}">
                        @if ($errors->has('parentName'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('parentName') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">メールアドレス<span class="text-danger">【必須】</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="name@example.com" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('email') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="tel" class="form-label" style="margin-bottom: 0;">電話番号<span
                                class="text-danger">【必須】</span></label>
                        <p class="small" style="margin-bottom: .5rem;">※ハイフンなしでご入力ください</p>
                        <input type="tel" class="form-control" id="tel" name="tel" placeholder="08012345678"
                            value="{{ old('tel') }}">
                        @if ($errors->has('tel'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('tel') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label" style="margin-bottom: 0;">パスワード<span
                                class="text-danger">【必須】</span></label>
                        <p class="small" style="margin-bottom: .5rem;">※8桁の半角英数字でご入力ください</p>
                        <input type="password" class="form-control" id="password" name="password">
                        @if ($errors->has('password'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('password') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">パスワード確認<span
                                class="text-danger">【必須】</span></label>
                        <input type="password" class="form-control" id="password-confirm" name="password_confirmation">
                    </div>

                    <div class="mb-3">
                        <label for="childName" class="form-label">利用児氏名<span class="text-danger">【必須】</span></label>
                        <input type="text" class="form-control" id="childName" name="childName" placeholder="田中太郎"
                            value="{{ old('childName') }}">
                        @if ($errors->has('childName'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('childName') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="age" class="form-label">利用児年齢<span class="text-danger">【必須】</span></label>
                        <input type="number" class="form-control w-25" id="age" name="age" placeholder="5"
                            value="{{ old('age') }}">
                        @if ($errors->has('age'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('age') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label d-block">性別<span class="text-danger">【必須】</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="male" value="男の子"
                                checked>
                            <label class="form-check-label" for="male">
                                男の子
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="female" value="女の子">
                            <label class="form-check-label" for="female">
                                女の子
                            </label>
                        </div>
                        @if ($errors->has('gender'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('gender') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">診断名</label>
                        <input type="text" class="form-control" id="diagnosis" name="diagnosis" placeholder="自閉症の疑い"
                            value="{{ old('diagnosis') }}">
                        @if ($errors->has('diagnosis'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('diagnosis') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="childName2" class="form-label">利用児氏名2<span
                                class="text-danger">【兄弟児でご利用の場合にのみ入力】</span></label>
                        <input type="text" class="form-control" name="childName2" id="childName2" placeholder="田中太郎"
                            value="{{ old('childName2') }}">
                        @if ($errors->has('childName2'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('childName2') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3 d-none" id="age2">
                        <label for="age2" class="form-label">利用児年齢2<span class="text-danger">【必須】</span></label>
                        <input type="number" class="form-control w-25" name="age2" placeholder="5"
                            value="{{ old('age2') }}">
                        @if ($errors->has('age2'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('age2') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3 d-none" id="gender2">
                        <label for="gender2" class="form-label d-block">性別2<span class="text-danger">【必須】</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender2" id="male2" value="男の子"
                                checked>
                            <label class="form-check-label" for="male2">
                                男の子
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender2" id="female2"
                                value="女の子">
                            <label class="form-check-label" for="female2">
                                女の子
                            </label>
                        </div>
                        @if ($errors->has('gender2'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('gender2') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3 d-none" id="diagnosis2">
                        <label for="diagnosis2" class="form-label">診断名2</label>
                        <input type="text" class="form-control" name="diagnosis2" placeholder="自閉症の疑い"
                            value="{{ old('diagnosis2') }}">
                        @if ($errors->has('diagnosis2'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('diagnosis2') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="postCode" class="form-label" style="margin-bottom: 0;">郵便番号<span
                                class="text-danger">【必須】</span></label>
                        <p class="small" style="margin-bottom: .5rem;">※ハイフンなしでご入力ください</p>
                        <input type="text" class="form-control" id="postCode" name="postCode" placeholder="1234567"
                            size="10" maxlength="8" onKeyUp="AjaxZip3.zip2addr(this,'','address','address');"
                            value="{{ old('postCode') }}">
                        @if ($errors->has('postCode'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('postCode') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">住所<span class="text-danger">【必須】</span></label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="〇〇区"
                            value="{{ old('address') }}">
                        @if ($errors->has('address'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('address') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">ご利用プラン<span class="text-danger">【必須】</span></label>

                        {{-- LINE相談 --}}
                        <div class="mb-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="lineConsultation" id="line" value="1">
                                <label class="form-check-label" for="line">
                                    LINE相談のみ
                                </label>
                            </div>
                        </div>

                        {{-- 利用回数 --}}
                        <div class="mb-1" id="numberOfUse">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="numberOfUse" id="one" value="1" checked>
                                <label class="form-check-label" for="one">
                                    月一回利用
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="numberOfUse" id="two" value="2">
                                <label class="form-check-label" for="two">
                                    月二回利用
                                </label>
                            </div>
                        </div>

                        {{-- コースタイプ選択 --}}
                        <div id="coursePlan">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="coursePlan" id="weekday" value="1" checked>
                                <label class="form-check-label" for="weekday">
                                    平日利用
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="coursePlan" id="holiday" value="2">
                                <label class="form-check-label" for="holiday">
                                    休日利用
                                </label>
                            </div>
                        </div>

                        @if ($errors->has('coursePlan'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('coursePlan') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="introduction" class="form-label">紹介先</label>
                        <input type="text" class="form-control" id="introduction" name="introduction"
                            placeholder="〇〇区役所" value="{{ old('introduction') }}">
                        @if ($errors->has('introduction'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('introduction') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="consaltation" class="form-label">相談内容</label>
                        <textarea type="text" rows="5" class="form-control" id="consaltation" name="consaltation"
                            placeholder="言葉が出てこない。癇癪がすごい。">{{ old('consaltation') }}</textarea>
                        @if ($errors->has('consaltation'))
                            <div class="error text-danger">
                                <p>{{ $errors->first('consaltation') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="text-center mt-5">
                        <input type="submit" value="送信" class="btn btn-secondary w-100" id="jsRegistrationBtn">
                    </div>

                </form>
            </div>
            {{-- 新規登録フォーム ここまで --}}
        </div>
    </div>
@endsection
