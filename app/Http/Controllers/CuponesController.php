<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use Illuminate\Http\Request;

class CuponesController extends Controller
{
        public function index()
        {
                $cupones = Cupon::orderBy('id_cupon', 'desc')->get();
                return view('cupones.index', compact('cupones'));
        }

        public function create()
        {
                return view('cupones.create');
        }

        public function store(Request $request)
        {
                $request->merge([
                        'bActivo' => $request->has('bActivo')
                ]);

                $data = $request->validate([
                        'vCodigo_cupon'     => 'required|string|max:50|unique:tbl_cupones',
                        'dDescuento'        => 'required|numeric|min:0|max:999.99', // ✅ FIX: evita "out of range"
                        'dMonto_minimo'     => 'nullable|numeric|min:0',
                        'eTipo'             => 'required|in:porcentaje,monto,envio_gratis',
                        'dValido_desde'     => 'required|date',
                        'dValido_hasta'     => 'required|date|after:dValido_desde',
                        'iUso_maximo'       => 'nullable|integer|min:1',
                        'iUsos_por_usuario' => 'nullable|integer|min:1', // ✅ FIX: campo faltante
                        'bActivo'           => 'boolean',
                ], [
                        'vCodigo_cupon.required'     => 'El código del cupón es obligatorio.',
                        'vCodigo_cupon.unique'       => 'Ya existe un cupón con ese código.',
                        'vCodigo_cupon.max'          => 'El código no puede tener más de 50 caracteres.',
                        'dDescuento.required'        => 'El descuento es obligatorio.',
                        'dDescuento.numeric'         => 'El descuento debe ser un número.',
                        'dDescuento.min'             => 'El descuento no puede ser negativo.',
                        'dDescuento.max'             => 'El descuento no puede ser mayor a 999.99.',
                        'dMonto_minimo.numeric'      => 'El monto mínimo debe ser un número.',
                        'dMonto_minimo.min'          => 'El monto mínimo no puede ser negativo.',
                        'eTipo.required'             => 'El tipo de descuento es obligatorio.',
                        'eTipo.in'                   => 'El tipo seleccionado no es válido.',
                        'dValido_desde.required'     => 'La fecha de inicio es obligatoria.',
                        'dValido_desde.date'         => 'La fecha de inicio no es válida.',
                        'dValido_hasta.required'     => 'La fecha de fin es obligatoria.',
                        'dValido_hasta.date'         => 'La fecha de fin no es válida.',
                        'dValido_hasta.after'        => 'La fecha de fin debe ser posterior a la fecha de inicio.',
                        'iUso_maximo.integer'        => 'El uso máximo debe ser un número entero.',
                        'iUso_maximo.min'            => 'El uso máximo debe ser al menos 1.',
                        'iUsos_por_usuario.integer'  => 'Los usos por usuario deben ser un número entero.',
                        'iUsos_por_usuario.min'      => 'Los usos por usuario deben ser al menos 1.',
                ]);

                Cupon::create($data);  // Cambiado a Cupones

                return redirect()
                        ->route('cupones.index')
                        ->with('success', 'Cupón creado correctamente.');
        }

        public function show($id)
        {
                $cupon = Cupon::findOrFail($id);  // Cambiado a Cupones
                return view('cupones.show', compact('cupon'));
        }

        public function edit($id)
        {
                $cupon = Cupon::findOrFail($id);  // Cambiado a Cupones
                return view('cupones.edit', compact('cupon'));
        }

        public function update(Request $request, $id)
        {
                $cupon = Cupon::findOrFail($id);  // Cambiado a Cupones

                $request->merge([
                        'bActivo' => $request->has('bActivo')
                ]);

                $data = $request->validate([
                        'vCodigo_cupon'     => 'required|string|max:50|unique:tbl_cupones,vCodigo_cupon,' . $id . ',id_cupon',
                        'dDescuento'        => 'required|numeric|min:0|max:999.99', // ✅ FIX
                        'dMonto_minimo'     => 'nullable|numeric|min:0',
                        'eTipo'             => 'required|in:porcentaje,monto,envio_gratis',
                        'dValido_desde'     => 'required|date',
                        'dValido_hasta'     => 'required|date|after:dValido_desde',
                        'iUso_maximo'       => 'nullable|integer|min:1',
                        'iUsos_por_usuario' => 'nullable|integer|min:1', // ✅ FIX
                        'bActivo'           => 'boolean',
                ], [
                        'vCodigo_cupon.required'     => 'El código del cupón es obligatorio.',
                        'vCodigo_cupon.unique'       => 'Ya existe un cupón con ese código.',
                        'vCodigo_cupon.max'          => 'El código no puede tener más de 50 caracteres.',
                        'dDescuento.required'        => 'El descuento es obligatorio.',
                        'dDescuento.numeric'         => 'El descuento debe ser un número.',
                        'dDescuento.min'             => 'El descuento no puede ser negativo.',
                        'dDescuento.max'             => 'El descuento no puede ser mayor a 999.99.',
                        'dMonto_minimo.numeric'      => 'El monto mínimo debe ser un número.',
                        'dMonto_minimo.min'          => 'El monto mínimo no puede ser negativo.',
                        'eTipo.required'             => 'El tipo de descuento es obligatorio.',
                        'eTipo.in'                   => 'El tipo seleccionado no es válido.',
                        'dValido_desde.required'     => 'La fecha de inicio es obligatoria.',
                        'dValido_desde.date'         => 'La fecha de inicio no es válida.',
                        'dValido_hasta.required'     => 'La fecha de fin es obligatoria.',
                        'dValido_hasta.date'         => 'La fecha de fin no es válida.',
                        'dValido_hasta.after'        => 'La fecha de fin debe ser posterior a la fecha de inicio.',
                        'iUso_maximo.integer'        => 'El uso máximo debe ser un número entero.',
                        'iUso_maximo.min'            => 'El uso máximo debe ser al menos 1.',
                        'iUsos_por_usuario.integer'  => 'Los usos por usuario deben ser un número entero.',
                        'iUsos_por_usuario.min'      => 'Los usos por usuario deben ser al menos 1.',
                ]);

                $cupon->update($data);

                return redirect()
                        ->route('cupones.index')
                        ->with('success', 'Cupón actualizado correctamente.');
        }

        public function toggleActivo($id)
        {
                $cupon = Cupon::findOrFail($id);
                $cupon->bActivo = !$cupon->bActivo;
                $cupon->save();

                $estado = $cupon->bActivo ? 'activado' : 'desactivado';
                return redirect()
                        ->route('cupones.index')
                        ->with('success', "Cupón {$estado} correctamente.");
        }

        public function destroy($id)
        {
                $cupon = Cupon::findOrFail($id);  // Cambiado a Cupones
                $cupon->delete();

                return redirect()
                        ->route('cupones.index')
                        ->with('success', 'Cupón eliminado correctamente.');
        }
}
