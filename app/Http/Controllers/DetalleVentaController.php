<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use PDF;

class DetalleVentaController extends Controller
{
    public function index(Request $request)
    {
        // Query principal con LEFT JOINs para enriquecer datos
        // MODIFICADO: Ahora agrupa por id_venta para evitar duplicados
        $query = DetalleVenta::selectRaw('
            MIN(tbl_detalle_ventas.id_detalle_venta) as id_detalle_venta,
            tbl_detalle_ventas.id_venta,
            tbl_ventas.id_usuario,
            GROUP_CONCAT(DISTINCT tbl_productos.vNombre SEPARATOR ", ") as nombre_producto,
            SUM(tbl_detalle_ventas.iCantidad) as iCantidad,
            AVG(tbl_detalle_ventas.dPrecio_unitario) as dPrecio_unitario,
            SUM(tbl_detalle_ventas.dSubtotal) as dSubtotal,
            COALESCE(tbl_usuarios.vNombre, "Sin usuario") as usuario_nombre,
            COALESCE(tbl_usuarios.vApaterno, "") as usuario_apellido1,
            COALESCE(tbl_usuarios.vAmaterno, "") as usuario_apellido2,
            COALESCE(tbl_usuarios.vEmail, "No especificado") as usuario_email,
            COALESCE(MIN(tbl_direcciones.vTelefono_contacto), "No registrado") as usuario_telefono,
            COALESCE(MIN(tbl_direcciones.vCiudad), "No especificada") as vCiudad,
            COALESCE(MIN(tbl_direcciones.vEstado), "No especificado") as vEstado,
            COALESCE(tbl_ventas.dTotal, 0) as total_venta,
            tbl_ventas.tFecha_venta as fecha_venta,
            COALESCE(tbl_ventas.eEstado, "completada") as eEstado,
            YEAR(tbl_ventas.tFecha_venta) as año_venta,
            MONTH(tbl_ventas.tFecha_venta) as mes_venta,
            WEEK(tbl_ventas.tFecha_venta) as semana_venta,
            COUNT(DISTINCT tbl_detalle_ventas.id_producto) as num_productos
        ')
            ->leftJoin('tbl_ventas', 'tbl_detalle_ventas.id_venta', '=', 'tbl_ventas.id_venta')
            ->leftJoin('tbl_usuarios', 'tbl_ventas.id_usuario', '=', 'tbl_usuarios.id_usuario')
            ->leftJoin('tbl_direcciones', function ($join) {
                $join->on('tbl_usuarios.id_usuario', '=', 'tbl_direcciones.id_usuario');
            })
            ->leftJoin('tbl_productos', 'tbl_detalle_ventas.id_producto', '=', 'tbl_productos.id_producto')
            ->groupBy(
                'tbl_detalle_ventas.id_venta',
                'tbl_ventas.id_usuario',
                'tbl_usuarios.vNombre',
                'tbl_usuarios.vApaterno',
                'tbl_usuarios.vAmaterno',
                'tbl_usuarios.vEmail',
                'tbl_ventas.dTotal',
                'tbl_ventas.tFecha_venta',
                'tbl_ventas.eEstado'
            )
            ->orderBy('tbl_detalle_ventas.id_venta', 'desc');

        // Agregar búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('tbl_detalle_ventas.id_venta', $search);
                } else {
                    $q->where('tbl_usuarios.vNombre', 'like', "%{$search}%")
                        ->orWhere('tbl_usuarios.vEmail', 'like', "%{$search}%")
                        ->orWhere('tbl_productos.vNombre', 'like', "%{$search}%");
                }
            });
        }

        // Filtro por nombre de cliente
        if ($request->filled('cliente')) {
            $cliente = $request->cliente;
            $query->where(function ($q) use ($cliente) {
                $q->where('tbl_usuarios.vNombre', 'LIKE', "%{$cliente}%")
                    ->orWhere('tbl_usuarios.vApaterno', 'LIKE', "%{$cliente}%")
                    ->orWhere('tbl_usuarios.vAmaterno', 'LIKE', "%{$cliente}%")
                    ->orWhereRaw("CONCAT(tbl_usuarios.vNombre, ' ', tbl_usuarios.vApaterno, ' ', tbl_usuarios.vAmaterno) LIKE ?", ["%{$cliente}%"]);
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->havingRaw('MIN(tbl_direcciones.vEstado) LIKE ?', ["%{$request->estado}%"]);
        }

        // Filtro por producto
        if ($request->filled('producto')) {
            $query->havingRaw('GROUP_CONCAT(DISTINCT tbl_productos.vNombre SEPARATOR ", ") LIKE ?', ["%{$request->producto}%"]);
        }

        // Filtro por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('tbl_ventas.tFecha_venta', $request->fecha);
        }

        $detallesVenta = $query->paginate(10)->appends($request->all());

        // Datos para gráficas - Productos más vendidos
        $productosData = DetalleVenta::selectRaw('
            tbl_productos.vNombre as producto,
            SUM(tbl_detalle_ventas.iCantidad) as total_vendido,
            SUM(tbl_detalle_ventas.dSubtotal) as ingresos
        ')
            ->leftJoin('tbl_productos', 'tbl_detalle_ventas.id_producto', '=', 'tbl_productos.id_producto')
            ->groupBy('tbl_productos.id_producto', 'tbl_productos.vNombre')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        // Datos para gráficas - Ciudades con más ventas
        $ciudadesData = DetalleVenta::selectRaw('
            COALESCE(tbl_direcciones.vCiudad, "Sin Ciudad") as ciudad,
            COUNT(*) as total_ventas,
            SUM(tbl_detalle_ventas.dSubtotal) as ingresos,
            SUM(tbl_detalle_ventas.iCantidad) as unidades_vendidas
        ')
            ->leftJoin('tbl_ventas', 'tbl_detalle_ventas.id_venta', '=', 'tbl_ventas.id_venta')
            ->leftJoin('tbl_usuarios', 'tbl_ventas.id_usuario', '=', 'tbl_usuarios.id_usuario')
            ->leftJoin('tbl_direcciones', 'tbl_usuarios.id_usuario', '=', 'tbl_direcciones.id_usuario')
            ->groupBy('tbl_direcciones.vCiudad')
            ->orderByDesc('total_ventas')
            ->limit(15)
            ->get();

        // Datos para gráficas - Ventas mensuales (CORREGIDO)
        $ventasMensuales = DetalleVenta::selectRaw('
            YEAR(tbl_ventas.tFecha_venta) as año,
            MONTH(tbl_ventas.tFecha_venta) as mes,
            COUNT(*) as total_ventas,
            SUM(tbl_detalle_ventas.dSubtotal) as ingresos
        ')
            ->leftJoin('tbl_ventas', 'tbl_detalle_ventas.id_venta', '=', 'tbl_ventas.id_venta')
            ->groupByRaw('YEAR(tbl_ventas.tFecha_venta), MONTH(tbl_ventas.tFecha_venta)')
            ->orderByRaw('YEAR(tbl_ventas.tFecha_venta), MONTH(tbl_ventas.tFecha_venta)')
            ->get();

        // Datos para gráficas - Ventas por estado
        $ventasPorEstado = DetalleVenta::selectRaw('
            COALESCE(tbl_direcciones.vEstado, "Sin Estado") as estado,
            COUNT(*) as total_ventas,
            SUM(tbl_detalle_ventas.dSubtotal) as ingresos
        ')
            ->leftJoin('tbl_ventas', 'tbl_detalle_ventas.id_venta', '=', 'tbl_ventas.id_venta')
            ->leftJoin('tbl_usuarios', 'tbl_ventas.id_usuario', '=', 'tbl_usuarios.id_usuario')
            ->leftJoin('tbl_direcciones', 'tbl_usuarios.id_usuario', '=', 'tbl_direcciones.id_usuario')
            ->groupBy('tbl_direcciones.vEstado')
            ->orderByDesc('total_ventas')
            ->get();

        return view('detalle_venta.index', compact(
            'detallesVenta',
            'productosData',
            'ciudadesData',
            'ventasMensuales',
            'ventasPorEstado'
        ));
    }

    public function create()
    {
        $ventas = Venta::orderBy('id_venta', 'desc')->get();
        $productos = Producto::orderBy('id_producto', 'desc')->get();

        return view('detalle_venta.create', compact('ventas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_venta' => 'required|exists:tbl_ventas,id_venta',
            'id_producto' => 'required|exists:tbl_productos,id_producto',
            'iCantidad' => 'required|integer|min:1',
            'dPrecio_unitario' => 'required|numeric|min:0',
        ]);

        $subtotal = $request->iCantidad * $request->dPrecio_unitario;

        DetalleVenta::create([
            'id_venta' => $request->id_venta,
            'id_producto' => $request->id_producto,
            'iCantidad' => $request->iCantidad,
            'dPrecio_unitario' => $request->dPrecio_unitario,
            'dSubtotal' => $subtotal,
        ]);

        return redirect()->route('detalle_venta.index')
            ->with('success', 'Detalle de venta creado correctamente.');
    }

    public function show($id)
    {
        // Obtenemos el detalle de venta con todos los datos enriquecidos
        $detalleVenta = DetalleVenta::selectRaw('
            tbl_detalle_ventas.id_detalle_venta,
            tbl_detalle_ventas.id_venta,
            tbl_detalle_ventas.id_producto,
            tbl_detalle_ventas.iCantidad,
            tbl_detalle_ventas.dPrecio_unitario,
            tbl_detalle_ventas.dSubtotal,
            COALESCE(tbl_usuarios.vNombre, "Sin usuario") as usuario_nombre,
            COALESCE(tbl_usuarios.vApaterno, "") as usuario_apellido1,
            COALESCE(tbl_usuarios.vAmaterno, "") as usuario_apellido2,
            COALESCE(tbl_usuarios.vEmail, "No especificado") as usuario_email,
            COALESCE(tbl_direcciones.vTelefono_contacto, "No registrado") as usuario_telefono,
            COALESCE(tbl_direcciones.vCalle, "") as vCalle,
            COALESCE(tbl_direcciones.vNumero_exterior, "") as vNumero_exterior,
            COALESCE(tbl_direcciones.vCiudad, "No especificada") as vCiudad,
            COALESCE(tbl_direcciones.vEstado, "No especificado") as vEstado,
            COALESCE(tbl_productos.vNombre, "Producto no encontrado") as nombre_producto,
            COALESCE(tbl_productos.vCodigo_barras, "") as codigo_barras,
            COALESCE(tbl_ventas.dTotal, 0) as total_venta,
            COALESCE(tbl_ventas.eMetodo_pago, "") as metodo_pago,
            COALESCE(tbl_ventas.eEstado, "") as estado_venta,
            YEAR(tbl_ventas.tFecha_venta) as año_venta,
            MONTH(tbl_ventas.tFecha_venta) as mes_venta,
            WEEK(tbl_ventas.tFecha_venta) as semana_venta,
            tbl_ventas.tFecha_venta
        ')
            ->leftJoin('tbl_ventas', 'tbl_detalle_ventas.id_venta', '=', 'tbl_ventas.id_venta')
            ->leftJoin('tbl_usuarios', 'tbl_ventas.id_usuario', '=', 'tbl_usuarios.id_usuario')
            ->leftJoin('tbl_direcciones', 'tbl_usuarios.id_usuario', '=', 'tbl_direcciones.id_usuario')
            ->leftJoin('tbl_productos', 'tbl_detalle_ventas.id_producto', '=', 'tbl_productos.id_producto')
            ->where('tbl_detalle_ventas.id_detalle_venta', $id)
            ->firstOrFail();

        return view('detalle_venta.show', compact('detalleVenta'));
    }

    public function edit($id)
    {
        $detalleVenta = DetalleVenta::findOrFail($id);
        $ventas = Venta::orderBy('id_venta', 'desc')->get();
        $productos = Producto::orderBy('id_producto', 'desc')->get();
        return view('detalle_venta.edit', compact('detalleVenta', 'ventas', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_venta' => 'required|exists:tbl_ventas,id_venta',
            'id_producto' => 'required|exists:tbl_productos,id_producto',
            'iCantidad' => 'required|integer|min:1',
            'dPrecio_unitario' => 'required|numeric|min:0',
        ]);

        $detalleVenta = DetalleVenta::findOrFail($id);

        $subtotal = $request->iCantidad * $request->dPrecio_unitario;

        $detalleVenta->update([
            'id_venta' => $request->id_venta,
            'id_producto' => $request->id_producto,
            'iCantidad' => $request->iCantidad,
            'dPrecio_unitario' => $request->dPrecio_unitario,
            'dSubtotal' => $subtotal,
        ]);

        return redirect()->route('detalle_venta.index')
            ->with('success', 'Detalle de venta actualizado correctamente.');
    }

    public function destroy($id)
    {
        $detalleVenta = DetalleVenta::findOrFail($id);
        $detalleVenta->delete();

        return redirect()->route('detalle_venta.index')
            ->with('success', 'Detalle de venta eliminado correctamente.');
    }

    public function generarPDF($id)
    {
        $detalle = DetalleVenta::findOrFail($id);
        $pdf = PDF::loadView('detalles.pdf', compact('detalle'));
        return $pdf->download("detalle-{$id}.pdf");
    }
}
