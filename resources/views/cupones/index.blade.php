<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cupones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        #buscadorCupones::placeholder { color: rgba(0,0,0,0.45); }
        #buscadorCupones { color: #000 !important; }
        #buscadorCupones:focus { outline: none; box-shadow: none; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #2d3748;">
            <i class="fas fa-ticket-alt me-2" style="color: #667eea;"></i>Cupones
        </h2>
        <a href="{{ route('cupones.create') }}" class="btn text-white px-4"
           style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-plus me-1"></i> Crear Cupón
        </a>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Listado de Cupones</h5>
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text border-0" style="background: rgba(255,255,255,0.25);">
                        <i class="fas fa-search text-white"></i>
                    </span>
                    <input type="text" id="buscadorCupones"
                        class="form-control border-0"
                        placeholder="Buscar cupón..."
                        style="background: rgba(255,255,255,0.9); color: #000;"
                        autocomplete="off"
                        onFocus="this.style.background='rgba(255,255,255,1)'"
                        onBlur="this.style.background='rgba(255,255,255,0.9)'">
                    <button class="btn border-0" type="button"
                        onclick="limpiarBusqueda()" title="Limpiar"
                        style="background: rgba(255,255,255,0.25); color: white;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <small id="resultadosBusqueda" class="text-white opacity-75 mt-1 d-block"></small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaCupones">
                    <thead class="table-light">
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Descuento</th>
                            <th>Monto mínimo</th>
                            <th>Válido desde</th>
                            <th>Válido hasta</th>
                            <th>Usos</th>
                            <th>Activo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cupones as $cupon)
                        <tr data-fila="{{ $cupon->id_cupon }} {{ $cupon->vCodigo_cupon }} {{ $cupon->eTipo }} {{ $cupon->dDescuento }} {{ $cupon->dMonto_minimo }} {{ $cupon->dValido_desde }} {{ $cupon->dValido_hasta }} {{ $cupon->iUsos_actuales }}/{{ $cupon->iUso_maximo }} {{ $cupon->bActivo ? 'activo' : 'inactivo' }}">
                            <td class="text-muted">{{ $cupon->id_cupon }}</td>
                            <td>
                                <span class="badge fs-6 px-3 py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    {{ $cupon->vCodigo_cupon }}
                                </span>
                            </td>
                            <td>
                                @if($cupon->eTipo == 'porcentaje')
                                    <span class="badge bg-primary"><i class="fas fa-percent me-1"></i>Porcentaje</span>
                                @elseif($cupon->eTipo == 'monto')
                                    <span class="badge bg-success"><i class="fas fa-dollar-sign me-1"></i>Monto fijo</span>
                                @elseif($cupon->eTipo == 'envio_gratis')
                                    <span class="badge bg-info text-dark"><i class="fas fa-truck me-1"></i>Envío gratis</span>
                                @endif
                            </td>
                            <td class="fw-semibold">
                                @if($cupon->eTipo == 'porcentaje')
                                    {{ $cupon->dDescuento }}%
                                @else
                                    ${{ number_format($cupon->dDescuento, 2) }}
                                @endif
                            </td>
                            <td>
                                {{ $cupon->dMonto_minimo ? '$' . number_format($cupon->dMonto_minimo, 2) : '-' }}
                            </td>
                            <td><small>{{ \Carbon\Carbon::parse($cupon->dValido_desde)->format('d/m/Y') }}</small></td>
                            <td><small>{{ \Carbon\Carbon::parse($cupon->dValido_hasta)->format('d/m/Y') }}</small></td>
                            <td>
                                <small class="text-muted">
                                    {{ $cupon->iUsos_actuales }} / {{ $cupon->iUso_maximo }}
                                </small>
                            </td>
                            <td>
                                @if($cupon->bActivo)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Activo</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('cupones.edit', $cupon->id_cupon) }}"
                                   class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cupones.toggleActivo', $cupon->id_cupon) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @if($cupon->bActivo)
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Desactivar">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-ticket-alt fa-3x mb-3 d-block opacity-25"></i>
                                No hay cupones registrados.
                                <a href="{{ route('cupones.create') }}">Crear el primero</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($cupones->count() > 0)
        <div class="card-footer text-muted text-end">
            <small>Total: {{ $cupones->count() }} cupón(es)</small>
        </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const input = document.getElementById('buscadorCupones');
const resultado = document.getElementById('resultadosBusqueda');

input.addEventListener('keyup', filtrar);

function filtrar() {
    const texto = input.value.toLowerCase().trim();
    const filas = document.querySelectorAll('#tablaCupones tbody tr[data-fila]');
    let visibles = 0;
    filas.forEach(fila => {
        const contenido = fila.getAttribute('data-fila').toLowerCase();
        if (contenido.includes(texto)) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    resultado.textContent = texto === '' ? '' : visibles + ' resultado(s) encontrado(s)';
}

function limpiarBusqueda() {
    input.value = '';
    filtrar();
    input.focus();
}
</script>
</body>
</html>