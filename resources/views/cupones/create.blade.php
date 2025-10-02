<!DOCTYPE html>
<html>
<head>
    <title>Crear Cupón</title>
</head>
<body>
    <h1>Nuevo Cupón</h1>
    <form action="{{ route('cupones.store') }}" method="POST">
        @csrf
        Código: <input type="text" name="vCodigo_cupon"><br>
        Descuento: <input type="text" name="dDescuento"><br>
        Tipo: 
        <select name="eTipo">
            <option value="porcentaje">Porcentaje</option>
            <option value="monto">Monto</option>
        </select><br>
        Válido desde: <input type="date" name="dValido_desde"><br>
        Válido hasta: <input type="date" name="dValido_hasta"><br>
        Uso máximo: <input type="number" name="iUso_maximo" value="1"><br>
        Activo: <input type="checkbox" name="bActivo" value="1" checked><br>
        <button type="submit">Guardar</button>
    </form>
</body>
</html>