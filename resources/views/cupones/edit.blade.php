<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cupón</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow-sm border-0">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Cupón — {{ $cupon->vCodigo_cupon }}</h4>
                </div>

                <div class="card-body p-4">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cupones.update', $cupon->id_cupon) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Código --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Código del Cupón</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="text" name="vCodigo_cupon"
                                    class="form-control @error('vCodigo_cupon') is-invalid @enderror"
                                    value="{{ old('vCodigo_cupon', $cupon->vCodigo_cupon) }}" required>
                                @error('vCodigo_cupon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tipo --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo de Descuento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-list"></i></span>
                                <select name="eTipo" class="form-select @error('eTipo') is-invalid @enderror" required>
                                    <option value="porcentaje"   {{ old('eTipo', $cupon->eTipo) == 'porcentaje'   ? 'selected' : '' }}>Porcentaje (%)</option>
                                    <option value="monto"        {{ old('eTipo', $cupon->eTipo) == 'monto'        ? 'selected' : '' }}>Monto fijo ($)</option>
                                    <option value="envio_gratis" {{ old('eTipo', $cupon->eTipo) == 'envio_gratis' ? 'selected' : '' }}>Envío gratis</option>
                                </select>
                                @error('eTipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Descuento y Monto mínimo --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Descuento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                    <input type="number" step="0.01" name="dDescuento"
                                        class="form-control @error('dDescuento') is-invalid @enderror"
                                        value="{{ old('dDescuento', $cupon->dDescuento) }}" required>
                                    @error('dDescuento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Monto mínimo <span class="text-muted">(opcional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" step="0.01" name="dMonto_minimo"
                                        class="form-control @error('dMonto_minimo') is-invalid @enderror"
                                        placeholder="Sin límite" value="{{ old('dMonto_minimo', $cupon->dMonto_minimo) }}">
                                    @error('dMonto_minimo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Fechas --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Válido desde</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" name="dValido_desde"
                                        class="form-control @error('dValido_desde') is-invalid @enderror"
                                        value="{{ old('dValido_desde', $cupon->dValido_desde) }}" required>
                                    @error('dValido_desde')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Válido hasta</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                    <input type="date" name="dValido_hasta"
                                        class="form-control @error('dValido_hasta') is-invalid @enderror"
                                        value="{{ old('dValido_hasta', $cupon->dValido_hasta) }}" required>
                                    @error('dValido_hasta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Uso máximo y Usos por usuario --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Uso máximo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="number" name="iUso_maximo"
                                        class="form-control @error('iUso_maximo') is-invalid @enderror"
                                        value="{{ old('iUso_maximo', $cupon->iUso_maximo) }}" min="1">
                                    @error('iUso_maximo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Usos por usuario <span class="text-muted">(opcional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="number" name="iUsos_por_usuario"
                                        class="form-control @error('iUsos_por_usuario') is-invalid @enderror"
                                        placeholder="Sin límite" value="{{ old('iUsos_por_usuario', $cupon->iUsos_por_usuario ?? '') }}" min="1">
                                    @error('iUsos_por_usuario')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Activo --}}
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="bActivo" value="1"
                                    id="bActivo" {{ old('bActivo', $cupon->bActivo) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="bActivo">Cupón activo</label>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn text-white px-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('cupones.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left me-1"></i> Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>