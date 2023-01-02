ご予約を受け付けました。
【予約日時】 {{ $reservationDate ?? '' }}
【予約時間】 {{ $reservationTime ?? '' }}

予約をキャンセルする場合は、下記URLからお願いいたします。
{{ route('show', $reservationId) }}