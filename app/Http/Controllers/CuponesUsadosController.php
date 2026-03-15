<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuponesUsadosController extends Controller
{
    public function index(Request $request)
    {
        $cuponesUsados = DB::table('tbl_cupon_usos as cu')
            ->leftJoin('tbl_cupones as c',   'cu.id_cupon',  '=', 'c.id_cupon')
            ->leftJoin('tbl_usuarios as u',  'cu.id_usuario','=', 'u.id_usuario')
            ->leftJoin('tbl_ventas as v',    'cu.id_venta',  '=', 'v.id_venta')
            ->leftJoin('tbl_pedidos as p',   'v.id_pedido',  '=', 'p.id_pedido')
            ->select(
                'cu.id_cupon',
                'cu.id_venta',
                'cu.id_usuario',
                'cu.guest_token',
                'cu.tFecha_uso',
                'c.vCodigo_cupon as codigo_cupon',
                'c.eTipo as tipo_cupon',
                'c.dDescuento as descuento_cupon',
                'c.dDescuento as dDescuento_aplicado',
                // Si es usuario registrado usa sus datos, si es invitado usa los del pedido
                DB::raw('COALESCE(u.vNombre,   p.vNombre)   as usuario_nombre'),
                DB::raw('COALESCE(u.vApaterno, p.vApaterno) as usuario_apellido1'),
                DB::raw('COALESCE(u.vEmail,    p.vEmail)    as usuario_email')
            )
            ->orderByDesc('cu.tFecha_uso')
            ->get();

        return view('cupones_usados.index', compact('cuponesUsados'));
    }

    public function create()
    {
        $cupones  = DB::table('tbl_cupones')->select('id_cupon', 'vCodigo_cupon', 'eTipo', 'dDescuento')->get();
        $usuarios = DB::table('tbl_usuarios')->select('id_usuario', 'vNombre', 'vApaterno', 'vEmail')->get();
        $ventas   = DB::table('tbl_ventas')->select('id_venta')->orderByDesc('id_venta')->get();

        return view('cupones_usados.create', compact('cupones', 'usuarios', 'ventas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cupon'    => 'required|integer|exists:tbl_cupones,id_cupon',
            'id_venta'    => 'required|integer|exists:tbl_ventas,id_venta',
            'id_usuario'  => 'nullable|integer|exists:tbl_usuarios,id_usuario',
            'guest_token' => 'nullable|string|max:36',
            'tFecha_uso'  => 'required|date',
        ]);

        DB::table('tbl_cupon_usos')->insert([
            'id_cupon'    => $request->id_cupon,
            'id_venta'    => $request->id_venta,
            'id_usuario'  => $request->id_usuario,
            'guest_token' => $request->guest_token,
            'tFecha_uso'  => $request->tFecha_uso,
        ]);

        return redirect()->route('cupones_usados.index')
                         ->with('success', 'Cupón registrado correctamente.');
    }

    public function show($id)
    {
        [$idCupon, $idVenta] = explode('-', $id);

        $cuponUsado = DB::table('tbl_cupon_usos as cu')
            ->leftJoin('tbl_cupones as c',  'cu.id_cupon',   '=', 'c.id_cupon')
            ->leftJoin('tbl_usuarios as u', 'cu.id_usuario', '=', 'u.id_usuario')
            ->leftJoin('tbl_ventas as v',   'cu.id_venta',   '=', 'v.id_venta')
            ->leftJoin('tbl_pedidos as p',  'v.id_pedido',   '=', 'p.id_pedido')
            ->select(
                'cu.id_cupon', 'cu.id_venta', 'cu.id_usuario',
                'cu.guest_token', 'cu.tFecha_uso',
                'c.vCodigo_cupon as codigo_cupon',
                'c.eTipo as tipo_descuento',
                'c.dDescuento as valor_descuento',
                'c.dValido_hasta as fecha_expiracion',
                DB::raw('COALESCE(u.vNombre,   p.vNombre)   as usuario_nombre'),
                DB::raw('COALESCE(u.vApaterno, p.vApaterno) as usuario_apellido1'),
                DB::raw('COALESCE(u.vEmail,    p.vEmail)    as usuario_email')
            )
            ->where('cu.id_cupon', $idCupon)
            ->where('cu.id_venta',  $idVenta)
            ->firstOrFail();

        return view('cupones_usados.show', compact('cuponUsado'));
    }

    public function edit($id)
    {
        [$idCupon, $idVenta] = explode('-', $id);

        $cuponUsado = DB::table('tbl_cupon_usos')
            ->where('id_cupon', $idCupon)
            ->where('id_venta',  $idVenta)
            ->firstOrFail();

        $cupones  = DB::table('tbl_cupones')->select('id_cupon', 'vCodigo_cupon', 'eTipo', 'dDescuento')->get();
        $usuarios = DB::table('tbl_usuarios')->select('id_usuario', 'vNombre', 'vApaterno', 'vEmail')->get();
        $ventas   = DB::table('tbl_ventas')->select('id_venta')->orderByDesc('id_venta')->get();

        return view('cupones_usados.edit', compact('cuponUsado', 'cupones', 'usuarios', 'ventas'));
    }

    public function update(Request $request, $id)
    {
        [$idCupon, $idVenta] = explode('-', $id);

        $request->validate([
            'id_cupon'    => 'required|integer|exists:tbl_cupones,id_cupon',
            'id_venta'    => 'required|integer|exists:tbl_ventas,id_venta',
            'id_usuario'  => 'nullable|integer|exists:tbl_usuarios,id_usuario',
            'guest_token' => 'nullable|string|max:36',
            'tFecha_uso'  => 'required|date',
        ]);

        DB::table('tbl_cupon_usos')
            ->where('id_cupon', $idCupon)
            ->where('id_venta',  $idVenta)
            ->update([
                'id_cupon'    => $request->id_cupon,
                'id_venta'    => $request->id_venta,
                'id_usuario'  => $request->id_usuario,
                'guest_token' => $request->guest_token,
                'tFecha_uso'  => $request->tFecha_uso,
            ]);

        $newId = $request->id_cupon . '-' . $request->id_venta;

        return redirect()->route('cupones_usados.show', ['id' => $newId])
                         ->with('success', 'Cupón actualizado correctamente.');
    }

    public function destroy($id)
    {
        [$idCupon, $idVenta] = explode('-', $id);

        DB::table('tbl_cupon_usos')
            ->where('id_cupon', $idCupon)
            ->where('id_venta',  $idVenta)
            ->delete();

        return redirect()->route('cupones_usados.index')
                         ->with('success', 'Registro eliminado correctamente.');
    }
}