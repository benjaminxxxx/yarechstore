<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'site_name',            // Nombre del sitio
        'site_description',     // Descripción del sitio
        'site_favicon',         // Ruta del favicon
        'site_logo',            // Logo principal
        'site_logo_contrast',   // Logo contraste
        'site_logo_horizontal', // Logo horizontal
        'site_logo_vertical',   // Logo vertical
        'site_language',        // Idioma del sitio
    ];
}
