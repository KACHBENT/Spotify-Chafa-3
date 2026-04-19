<?php

namespace App\Controllers;

use App\Models\SpotifyUserModel;

class AccesoController extends BaseController
{
    public function loginShowForm()
    {
        if (session()->get('isLoggedIn')) {
            if (session()->get('user_Role') === 'Administrador') {
                return redirect()->to('/admin/canciones');
            }

            return redirect()->to('/spotify/home');
        }

        return view('AccesosAdministrativo/Login');
    }

    public function login()
    {
        $usuario  = trim((string) $this->request->getPost('usuario'));
        $password = (string) $this->request->getPost('password');

        if ($usuario === '' || $password === '') {
            return redirect()->back()->withInput()->with('error', 'Debes ingresar usuario y contraseña');
        }

        $userModel = new SpotifyUserModel();
        $user = $userModel->buscarPorCorreoOUsuario($usuario);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Usuario no encontrado');
        }

        if ((int)$user['user_Activo'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Usuario inactivo');
        }

        if (!password_verify($password, $user['user_PasswordHash'])) {
            return redirect()->back()->withInput()->with('error', 'Contraseña incorrecta');
        }

        $rol = $user['rol_Nombre'] ?? 'Oyente';

        session()->set([
            'isLoggedIn'    => true,
            'user_Id'       => $user['user_Id'],
            'user_Name'     => $user['user_Nombre'],
            'user_UserName' => $user['user_UserName'],
            'user_Email'    => $user['user_Correo'],
            'user_Role'     => $rol
        ]);

        if ($rol === 'Administrador') {
            return redirect()->to('/admin/canciones');
        }

        return redirect()->to('/spotify/home');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/acceso/login');
    }
}