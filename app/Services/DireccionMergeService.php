<?php

namespace App\Services;

use App\Models\Direccion;
use App\Models\DireccionGuest;
use Illuminate\Support\Facades\DB;

class DireccionMergeService
{
    public function merge(string $guestToken, int $userId): void
    {
        DB::transaction(function () use ($guestToken, $userId) {

            $guestAddresses = DireccionGuest::where('vGuest_token', $guestToken)
                ->get();

            if ($guestAddresses->isEmpty()) {
                return;
            }

            // Verificar si el usuario ya tiene dirección principal
            $userHasPrincipal = Direccion::where('id_usuario', $userId)
                ->where('bDireccion_principal', 1)
                ->exists();

            foreach ($guestAddresses as $guest) {

                // Evitar duplicados exactos (opcional pero recomendado)
                $exists = Direccion::where('id_usuario', $userId)
                    ->where('vCalle', $guest->vCalle)
                    ->where('vNumero_exterior', $guest->vNumero_exterior)
                    ->where('vCodigo_postal', $guest->vCodigo_postal)
                    ->where('vCiudad', $guest->vCiudad)
                    ->where('vEstado', $guest->vEstado)
                    ->exists();

                if ($exists) {
                    continue;
                }

                Direccion::create([
                    'id_usuario'           => $userId,
                    'vTelefono_contacto'   => $guest->vTelefono_contacto,
                    'vRFC'                 => $guest->vRFC,
                    'vCalle'               => $guest->vCalle,
                    'vNumero_exterior'     => $guest->vNumero_exterior,
                    'vNumero_interior'     => $guest->vNumero_interior,
                    'vColonia'             => $guest->vColonia,
                    'vCodigo_postal'       => $guest->vCodigo_postal,
                    'vCiudad'              => $guest->vCiudad,
                    'vEstado'              => $guest->vEstado,
                    'vEntre_calle_1'       => $guest->vEntre_calle_1,
                    'vEntre_calle_2'       => $guest->vEntre_calle_2,
                    'tReferencias'         => $guest->tReferencias,
                    'bDireccion_principal' => $userHasPrincipal
                        ? 0
                        : $guest->bDireccion_principal,
                ]);

                // Si acabamos de crear una principal, evitar que otra lo sea
                if (!$userHasPrincipal && $guest->bDireccion_principal) {
                    $userHasPrincipal = true;
                }
            }

            // Eliminar direcciones guest después del merge
            DireccionGuest::where('vGuest_token', $guestToken)->delete();
        });
    }
}
