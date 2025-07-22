<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte con QR</title>
</head>

<body>
    @foreach ($data as $ticket)
        <div>
            {!! DNS2D::getBarcodeHTML((string) $ticket['uuid'], 'QRCODE') !!}
            <p>{{ $ticket['product_name'] }}</p>
        </div>
    @endforeach
</body>

</html>
