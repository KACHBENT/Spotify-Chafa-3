<?php

namespace App\Models;

use CodeIgniter\Model;

class SpotifyUserModel extends Model
{
    protected $table = 'tbl_ope_users';
    protected $primaryKey = 'user_Id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'user_Nombre',
        'user_ApllP',
        'user_ApllM',
        'user_UserName',
        'user_Correo',
        'user_PasswordHash',
        'user_FechaNacimiento',
        'user_Pais',
        'user_Telefono',
        'user_ImageId',
        'user_Activo'
    ];

    public function buscarPorCorreoOUsuario(string $dato): ?array
    {
        return $this->select('tbl_ope_users.*, tbl_cat_rol.rol_Nombre')
            ->join('tbl_rel_user_rol', 'tbl_rel_user_rol.user_Id = tbl_ope_users.user_Id', 'left')
            ->join('tbl_cat_rol', 'tbl_cat_rol.rol_Id = tbl_rel_user_rol.rol_Id', 'left')
            ->groupStart()
                ->where('tbl_ope_users.user_Correo', $dato)
                ->orWhere('tbl_ope_users.user_UserName', $dato)
            ->groupEnd()
            ->where('tbl_ope_users.user_Activo', 1)
            ->first();
    }
}