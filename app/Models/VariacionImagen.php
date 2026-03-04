<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VariacionImagen extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_variacion_imagenes';
    protected $primaryKey = 'id_variacion_imagen';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_variacion',
        'vRuta',
        'eTipo',
        'iOrden',
        'bActivo',
        'tFecha_registro'
    ];
    
    protected $casts = [
        'bActivo' => 'boolean',
        'tFecha_registro' => 'datetime'
    ];
    
    protected $appends = ['url'];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($imagen) {
            if (empty($imagen->tFecha_registro)) {
                $imagen->tFecha_registro = now();
            }
        });
        
        static::deleting(function ($imagen) {
            if ($imagen->vRuta && Storage::disk('public')->exists($imagen->vRuta)) {
                Storage::disk('public')->delete($imagen->vRuta);
                
                // Si la carpeta queda vacía, eliminarla
                $carpeta = dirname($imagen->vRuta);
                if (Storage::disk('public')->exists($carpeta)) {
                    $archivos = Storage::disk('public')->files($carpeta);
                    if (empty($archivos)) {
                        Storage::disk('public')->deleteDirectory($carpeta);
                    }
                }
            }
        });
    }
    
    public function variacion()
    {
        return $this->belongsTo(ProductoVariacion::class, 'id_variacion');
    }
    
    public function getUrlAttribute()
    {
        return $this->vRuta ? Storage::url($this->vRuta) : null;
    }
}