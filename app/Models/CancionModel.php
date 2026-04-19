<?php

namespace App\Models;

use CodeIgniter\Model;

class CancionModel extends Model
{
    protected $table            = 'tbl_ope_music';
    protected $primaryKey       = 'music_Id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'music_Titulo',
        'music_DuracionSegundos',
        'music_UrlArchivo',
        'music_ImageUrl',
        'music_FechaLanzamiento',
        'music_NumReproducciones',
        'music_Letra',
        'music_UserCreadorId',
        'music_ImageId',
        'music_Activo'
    ];
}