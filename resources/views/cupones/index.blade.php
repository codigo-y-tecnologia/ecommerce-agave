<!DOCTYPE html>
<html>
<head>
    <title>Listado de Cupones</title>
</head>
<body>
    <h1>Cupones</h1>
    <a href="{{ route('cupones.create') }}">Crear Cupón</a>
    <ul>
        @foreach($cupones as $cupon)
            <li>{{ $cupon->vCodigo_cupon }} - {{ $cupon->dDescuento }} ({{ $cupon->eTipo }})</li>
        @endforeach
    </ul>
</body>
</html>