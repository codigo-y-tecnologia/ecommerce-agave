<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Atributo;
use App\Models\AtributoOpcion;

class AtributosTableSeeder extends Seeder
{
    public function run(): void
    {
        $atributos = [
            [
                'vNombre' => 'Color',
                'tDescripcion' => 'Color principal del producto',
                'eTipo' => 'select',
                'vLabel' => 'Selecciona el color',
                'vPlaceholder' => null,
                'bRequerido' => true,
                'iOrden' => 1,
                'bActivo' => true,
                'opciones' => [
                    ['vValor' => 'rojo', 'vEtiqueta' => 'Rojo', 'bPredeterminado' => false],
                    ['vValor' => 'azul', 'vEtiqueta' => 'Azul', 'bPredeterminado' => false],
                    ['vValor' => 'verde', 'vEtiqueta' => 'Verde', 'bPredeterminado' => false],
                    ['vValor' => 'negro', 'vEtiqueta' => 'Negro', 'bPredeterminado' => true],
                    ['vValor' => 'blanco', 'vEtiqueta' => 'Blanco', 'bPredeterminado' => false],
                ]
            ],
            [
                'vNombre' => 'Tamaño',
                'tDescripcion' => 'Tamaño del producto',
                'eTipo' => 'radio',
                'vLabel' => 'Elige el tamaño',
                'vPlaceholder' => null,
                'bRequerido' => true,
                'iOrden' => 2,
                'bActivo' => true,
                'opciones' => [
                    ['vValor' => 's', 'vEtiqueta' => 'Small (S)', 'bPredeterminado' => false],
                    ['vValor' => 'm', 'vEtiqueta' => 'Medium (M)', 'bPredeterminado' => true],
                    ['vValor' => 'l', 'vEtiqueta' => 'Large (L)', 'bPredeterminado' => false],
                    ['vValor' => 'xl', 'vEtiqueta' => 'Extra Large (XL)', 'bPredeterminado' => false],
                ]
            ],
            [
                'vNombre' => 'Material',
                'tDescripcion' => 'Material de fabricación',
                'eTipo' => 'select',
                'vLabel' => 'Selecciona el material',
                'vPlaceholder' => null,
                'bRequerido' => false,
                'iOrden' => 3,
                'bActivo' => true,
                'opciones' => [
                    ['vValor' => 'algodon', 'vEtiqueta' => 'Algodón 100%', 'bPredeterminado' => true],
                    ['vValor' => 'poliestor', 'vEtiqueta' => 'Poliéster', 'bPredeterminado' => false],
                    ['vValor' => 'lino', 'vEtiqueta' => 'Lino', 'bPredeterminado' => false],
                    ['vValor' => 'seda', 'vEtiqueta' => 'Seda', 'bPredeterminado' => false],
                ]
            ],
            [
                'vNombre' => 'Descripción Personalizada',
                'tDescripcion' => 'Descripción adicional del producto',
                'eTipo' => 'textarea',
                'vLabel' => 'Descripción adicional',
                'vPlaceholder' => 'Escribe una descripción personalizada...',
                'bRequerido' => false,
                'iOrden' => 4,
                'bActivo' => true,
                'opciones' => []
            ],
            [
                'vNombre' => 'Garantía',
                'tDescripcion' => 'Tiempo de garantía del producto',
                'eTipo' => 'texto',
                'vLabel' => 'Tiempo de garantía',
                'vPlaceholder' => 'Ej: 1 año, 6 meses',
                'bRequerido' => false,
                'iOrden' => 5,
                'bActivo' => true,
                'opciones' => []
            ]
        ];

        foreach ($atributos as $atributoData) {
            $opciones = $atributoData['opciones'];
            unset($atributoData['opciones']);

            $atributo = Atributo::create($atributoData);

            foreach ($opciones as $index => $opcionData) {
                AtributoOpcion::create([
                    'id_atributo' => $atributo->id_atributo,
                    'vValor' => $opcionData['vValor'],
                    'vEtiqueta' => $opcionData['vEtiqueta'],
                    'bPredeterminado' => $opcionData['bPredeterminado'],
                    'iOrden' => $index
                ]);
            }
        }
    }
}