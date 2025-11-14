<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Atributo;
use App\Models\ProductoAtributo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoAtributoController extends Controller
{
    // Método para encontrar el ProductoAtributo por los IDs compuestos
    private function findProductoAtributo($productoId, $atributoId)
    {
        return ProductoAtributo::where('id_producto', $productoId)
                              ->where('id_atributo', $atributoId)
                              ->firstOrFail();
    }

    public function store(Request $request, Producto $producto)
    {
        $request->validate([
            'id_atributo' => 'required|exists:tbl_atributos,id_atributo',
            'vValor' => 'nullable|string|max:255',
            'id_opcion' => 'nullable|exists:tbl_atributo_opciones,id_opcion'
        ]);

        try {
            DB::beginTransaction();

            // Verificar si ya existe este atributo para el producto
            $existe = ProductoAtributo::where('id_producto', $producto->id_producto)
                                     ->where('id_atributo', $request->id_atributo)
                                     ->exists();

            if ($existe) {
                return redirect()->back()
                    ->with('error', 'Este atributo ya está asignado al producto');
            }

            ProductoAtributo::create([
                'id_producto' => $producto->id_producto,
                'id_atributo' => $request->id_atributo,
                'vValor' => $request->vValor,
                'id_opcion' => $request->id_opcion
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Atributo agregado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al agregar el atributo: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $productoId, $atributoId)
    {
        $request->validate([
            'vValor' => 'nullable|string|max:255',
            'id_opcion' => 'nullable|exists:tbl_atributo_opciones,id_opcion'
        ]);

        try {
            DB::beginTransaction();

            $productoAtributo = $this->findProductoAtributo($productoId, $atributoId);

            $productoAtributo->update([
                'vValor' => $request->vValor,
                'id_opcion' => $request->id_opcion
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Atributo actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el atributo: ' . $e->getMessage());
        }
    }

   public function destroy($productoId, $atributoId)
    {
        try {
            DB::beginTransaction();

            // Método más directo para eliminar
            $deleted = DB::table('tbl_producto_atributos')
                        ->where('id_producto', $productoId)
                        ->where('id_atributo', $atributoId)
                        ->delete();

            if ($deleted === 0) {
                throw new \Exception('No se encontró el atributo para eliminar');
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Atributo eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error eliminando atributo: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el atributo: ' . $e->getMessage());
        }
    }
    public function getOpciones($atributoId)
    {
        $atributo = Atributo::with('opciones')->findOrFail($atributoId);
        
        return response()->json([
            'opciones' => $atributo->opciones,
            'tipo' => $atributo->eTipo
        ]);
    }
}