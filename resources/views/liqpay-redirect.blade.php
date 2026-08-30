<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Перенаправлення на оплату…</title>
</head>
<body>
    <p>Зачекайте, відбувається перенаправлення на сторінку оплати…</p>
    <form id="liqpay-form" method="POST" action="{{ $url }}" accept-charset="utf-8">
        <input type="hidden" name="data" value="{{ $data }}" />
        <input type="hidden" name="signature" value="{{ $signature }}" />
    </form>
    <script>
        document.getElementById('liqpay-form').submit();
    </script>
</body>
</html>
