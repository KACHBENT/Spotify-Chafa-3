<?php

namespace App\Controllers;

use App\Models\SpotifyUserModel;

class RegisterController extends BaseController
{
    public function index()
    {
        return view('Auth/Register');
    }

    public function save()
    {
        $db = \Config\Database::connect();
        $model = new SpotifyUserModel();

        $nombre     = trim((string) $this->request->getPost('nombre'));
        $apellidop  = trim((string) $this->request->getPost('apellidop'));
        $apellidom  = trim((string) $this->request->getPost('apellidom'));
        $username   = trim((string) $this->request->getPost('username'));
        $correo     = trim((string) $this->request->getPost('correo'));
        $password   = (string) $this->request->getPost('password');
        $fecha      = $this->request->getPost('fecha') ?: null;
        $pais       = trim((string) $this->request->getPost('pais'));
        $telefono   = trim((string) $this->request->getPost('telefono'));

        if ($nombre === '' || $username === '' || $correo === '' || $password === '') {
            return redirect()->back()->withInput()->with('error', 'Completa los campos obligatorios');
        }

        $existeUser = $db->table('tbl_ope_users')
            ->groupStart()
                ->where('user_UserName', $username)
                ->orWhere('user_Correo', $correo)
            ->groupEnd()
            ->countAllResults();

        if ($existeUser > 0) {
            return redirect()->back()->withInput()->with('error', 'El usuario o correo ya están registrados');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'user_Nombre'          => $nombre,
            'user_ApllP'           => $apellidop,
            'user_ApllM'           => $apellidom,
            'user_UserName'        => $username,
            'user_Correo'          => $correo,
            'user_PasswordHash'    => $passwordHash,
            'user_FechaNacimiento' => $fecha,
            'user_Pais'            => $pais,
            'user_Telefono'        => $telefono,
            'user_ImageId'         => 1,
            'user_Activo'          => 1
        ];

        $model->insert($data);
        $userId = $model->getInsertID();

        // Buscar rol Oyente
        $rolOyente = $db->table('tbl_cat_rol')
            ->where('rol_Nombre', 'Oyente')
            ->get()
            ->getRowArray();

        if ($rolOyente) {
            $db->table('tbl_rel_user_rol')->insert([
                'user_Id' => $userId,
                'rol_Id'  => $rolOyente['rol_Id'],
                'userRol_Activo' => 1
            ]);
        }

        return redirect()->to('/acceso/login')
            ->with('success', 'Cuenta creada correctamente');
    }
}