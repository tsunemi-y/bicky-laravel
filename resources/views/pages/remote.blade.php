@extends('layout.common')
@include('layout.header')

@section('description')
    <meta name="description" content="言語聴覚士・保育士・高校教諭など複数の免許を持った指導員が、言葉の遅れ、発達障害、構音障害などのお子様の訓練を行います。">
@endsection

@section('title')
    <title>遠方の方へ | ビッキーことば塾 | 島本町・大山崎町・高槻市 | 発達障害や言葉の遅れが気になる子供の訓練</title>
@endsection

@section('pageCss')
    <link rel="stylesheet" href="{{ asset('css/remote.css') }}">
    <link rel="stylesheet" href="{{ asset('css/overview.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <x-breadcrumb item="TOP" item2="遠方の方へ" url="https://bicky.herokuapp.com/remote" />
            <h1 class="main-title">遠方の方へ</h1>

            <section class="mb-4">
                <h2>概要</h2>
                <p>
                <p class="mb-2">
                    北海道や沖縄など遠方のために、月1回の来所ができない方や、ご家庭の都合で定期的に来所することが困難な方は、LINEやテレビ電話、Zoomなどのオンラインでのご相談や評価等をさせて頂きます。
                <p>
                <table class="sp-table" border="1">
                    <tr>
                        <th>メリット</th>
                    </tr>
                    <tr>
                        <td>
                            ・すぐに相談できる。<br>
                            ※役場等で専門家支援を受けるまでに時間がかかる場合にオススメ<br>
                            ・具体的な関わりがすぐに知れる。<br>
                            ・継続的に支援を受けられる。<br>
                            ・直訓訓練に比べると、料金が安い。
                        </td>
                    </tr>

                    <tr>
                        <th>デメリット</th>
                    </tr>
                    <tr>
                        <td>
                            ・直訓訓練に比べると、効果が出るのに時間がかかる場合がある。<br>
                            ・動画視聴による分析のため、評価の正確性に欠ける場合がある。
                        </td>
                    </tr>
                </table>
                </p>
            </section>

            <section class="mb-4">
                <h2>お支払い方法</h2>
                <p>
                    ご利用になる前月の末までにお振込みをお願い致します。<br>
                    銀行口座はご登録いただいたメールアドレス宛に送信させていただきます。
                </p>
            </section>

            <section class="mb-4">
                <h2>LINEでの訓練内容</h2>
                <table class="sp-table" border="1">
                    <tr>
                        <th>ご利用方法</th>
                    </tr>
                    <tr>
                        <td>
                            1. <a href="{{ route('register') }}">会員登録画面</a>にて、「ご利用プラン」の「LINE相談」にチェックして新規登録<br>
                            2. ご登録頂いたメールアドレス宛に銀行口座を送信<br>
                            3. 振込み確認後、ご登録頂いた電話番号からLINEを送信<br>
                            ※ご登録いただく電話番号はLINEを使用できるものをお願い致します
                        </td>
                    </tr>

                    <tr>
                        <th>ご利用料金</th>
                    </tr>
                    <tr>
                        <td>
                            4400円(税込み)<br>
                            ※紹介して頂いた方には、1000円割引させていただきます。
                        </td>
                    </tr>

                    <tr>
                        <th>ご利用回数</th>
                    </tr>
                    <tr>
                        <td>週1回(やりとりが終わるまでを1回とします。)</td>
                    </tr>

                    <tr>
                        <th>ご利用を終了したい場合</th>
                    </tr>
                    <tr>
                        <td>
                            いつでも終了いただけます。<br>
                            ※お月謝の返金はございませんので、予めご了承ください
                        </td>
                    </tr>
                </table>
            </section>

            <section class="mb-4">
                <h2>テレビ電話・Zoomでの訓練内容</h2>
                <table class="sp-table" border="1">
                    <tr>
                        <th>ご利用方法</th>
                    </tr>
                    <tr>
                        <td>
                            ご希望の方のみ受け付けます。<br>
                            LINEにて、ご利用希望の旨をお伝えください。
                        </td>
                    </tr>

                    <tr>
                        <th>ご利用料金</th>
                    </tr>
                    <tr>
                        <td>
                            別途1100円(税込み)<br>
                            例）4400円（LINE相談）+ 1100円（テレビ電話・Zoom）の計5500円
                        </td>
                    </tr>

                    <tr>
                        <th>ご利用回数</th>
                    </tr>
                    <tr>
                        <td>月一回のみ</td>
                    </tr>

                    <tr>
                        <th>ご利用を終了したい場合</th>
                    </tr>
                    <tr>
                        <td>
                            いつでも終了いただけます。<br>
                            ※お月謝の返金はございませんので、予めご了承ください
                        </td>
                    </tr>

                    <tr>
                        <th>注意点</th>
                    </tr>
                    <tr>
                        <td>
                            週4回のLINE相談一回分がテレビ電話・Zoom相談になる形になります。<br>
                            1100円のお支払い毎に、利用できるわけではございません。
                        </td>
                    </tr>
                </table>
            </section>
        </div>
    </div>
@endsection
