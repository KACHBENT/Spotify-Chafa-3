<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CancionModel;

class CancionesController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $canciones = $db->query("
            SELECT 
                m.*,
                g.genero_Nombre
            FROM tbl_ope_music m
            LEFT JOIN tbl_rel_music_genero mg ON mg.music_Id = m.music_Id
            LEFT JOIN tbl_cat_genero g ON g.genero_Id = mg.genero_Id
            ORDER BY m.music_Id DESC
        ")->getResult();

        return view('Admin/Canciones/index', [
            'canciones' => $canciones
        ]);
    }

    public function create()
    {
        $db = \Config\Database::connect();

        $generos = $db->table('tbl_cat_genero')
            ->where('genero_Activo', 1)
            ->orderBy('genero_Nombre', 'ASC')
            ->get()
            ->getResult();

        return view('Admin/Canciones/create', [
            'generos' => $generos
        ]);
    }
public function store()
{
    $db = \Config\Database::connect();
    $model = new \App\Models\CancionModel();

    $titulo = trim((string) $this->request->getPost('music_Titulo'));
    $duracion = (int) $this->request->getPost('music_DuracionSegundos');
    $fecha = $this->request->getPost('music_FechaLanzamiento') ?: null;
    $letra = trim((string) $this->request->getPost('music_Letra'));
    $generoId = (int) $this->request->getPost('genero_Id');

    $audioFile = $this->request->getFile('music_File');
    $imageFile = $this->request->getFile('music_ImageFile');

    if ($titulo === '' || $duracion <= 0) {
        return redirect()->back()->withInput()->with('error', 'Completa los campos obligatorios');
    }

    if (!$audioFile || !$audioFile->isValid()) {
        return redirect()->back()->withInput()->with('error', 'Debes seleccionar un archivo de audio');
    }

    if (!$imageFile || !$imageFile->isValid()) {
        return redirect()->back()->withInput()->with('error', 'Debes seleccionar una imagen');
    }

    $audioExt = strtolower($audioFile->getExtension());
    $imageExt = strtolower($imageFile->getExtension());

    $audioPermitidos = ['mp3', 'wav', 'ogg', 'mp4'];
    $imagePermitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($audioExt, $audioPermitidos, true)) {
        return redirect()->back()->withInput()->with('error', 'Formato de audio no permitido');
    }

    if (!in_array($imageExt, $imagePermitidos, true)) {
        return redirect()->back()->withInput()->with('error', 'Formato de imagen no permitido');
    }

    $audioName = $audioFile->getRandomName();
    $imageName = $imageFile->getRandomName();

    $audioFile->move(FCPATH . 'music', $audioName);
    $imageFile->move(FCPATH . 'img/canciones', $imageName);

    $musicUrl = '/music/' . $audioName;
    $imageUrl = 'img/canciones/' . $imageName;

    $model->insert([
        'music_Titulo' => $titulo,
        'music_DuracionSegundos' => $duracion,
        'music_UrlArchivo' => $musicUrl,
        'music_ImageUrl' => $imageUrl,
        'music_FechaLanzamiento' => $fecha,
        'music_NumReproducciones' => 0,
        'music_Letra' => $letra !== '' ? $letra : null,
        'music_UserCreadorId' => session('user_Id'),
        'music_Activo' => 1
    ]);

    $musicId = $model->getInsertID();

    if ($generoId > 0) {
        $db->table('tbl_rel_music_genero')->insert([
            'music_Id' => $musicId,
            'genero_Id' => $generoId
        ]);
    }

    return redirect()->to(base_url('admin/canciones'))
        ->with('success', 'Canción creada correctamente');
}

public function update($id)
{
    $db = \Config\Database::connect();
    $model = new \App\Models\CancionModel();
    $id = (int) $id;

    $cancion = $model->find($id);
    if (!$cancion) {
        return redirect()->to(base_url('admin/canciones'))
            ->with('error', 'La canción no existe');
    }

    $titulo = trim((string) $this->request->getPost('music_Titulo'));
    $duracion = (int) $this->request->getPost('music_DuracionSegundos');
    $fecha = $this->request->getPost('music_FechaLanzamiento') ?: null;
    $letra = trim((string) $this->request->getPost('music_Letra'));
    $generoId = (int) $this->request->getPost('genero_Id');
    $activo = (int) $this->request->getPost('music_Activo');

    if ($titulo === '' || $duracion <= 0) {
        return redirect()->back()->withInput()->with('error', 'Completa los campos obligatorios');
    }

    $musicUrl = $cancion['music_UrlArchivo'];
    $imageUrl = $cancion['music_ImageUrl'];

    $audioFile = $this->request->getFile('music_File');
    if ($audioFile && $audioFile->isValid() && !$audioFile->hasMoved()) {
        $audioExt = strtolower($audioFile->getExtension());
        $audioPermitidos = ['mp3', 'wav', 'ogg', 'mp4'];

        if (!in_array($audioExt, $audioPermitidos, true)) {
            return redirect()->back()->withInput()->with('error', 'Formato de audio no permitido');
        }

        $audioName = $audioFile->getRandomName();
        $audioFile->move(FCPATH . 'music', $audioName);
        $musicUrl = '/music/' . $audioName;
    }

    $imageFile = $this->request->getFile('music_ImageFile');
    if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
        $imageExt = strtolower($imageFile->getExtension());
        $imagePermitidos = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($imageExt, $imagePermitidos, true)) {
            return redirect()->back()->withInput()->with('error', 'Formato de imagen no permitido');
        }

        $imageName = $imageFile->getRandomName();
        $imageFile->move(FCPATH . 'img/canciones', $imageName);
        $imageUrl = 'img/canciones/' . $imageName;
    }

    $model->update($id, [
        'music_Titulo' => $titulo,
        'music_DuracionSegundos' => $duracion,
        'music_UrlArchivo' => $musicUrl,
        'music_ImageUrl' => $imageUrl,
        'music_FechaLanzamiento' => $fecha,
        'music_Letra' => $letra !== '' ? $letra : null,
        'music_Activo' => $activo === 1 ? 1 : 0
    ]);

    $db->table('tbl_rel_music_genero')
        ->where('music_Id', $id)
        ->delete();

    if ($generoId > 0) {
        $db->table('tbl_rel_music_genero')->insert([
            'music_Id' => $id,
            'genero_Id' => $generoId
        ]);
    }

    return redirect()->to(base_url('admin/canciones'))
        ->with('success', 'Canción actualizada correctamente');
}

public function delete($id)
    {
        $model = new CancionModel();
        $id = (int) $id;

        $cancion = $model->find($id);
        if (!$cancion) {
            return redirect()->to(base_url('admin/canciones'))
                ->with('error', 'La canción no existe');
        }

        $model->update($id, [
            'music_Activo' => 0
        ]);

        return redirect()->to(base_url('admin/canciones'))
            ->with('success', 'Canción eliminada correctamente');
    }

    public function edit($id)
{
    $db = \Config\Database::connect();
    $id = (int) $id;

    $cancion = $db->query("
        SELECT 
            m.*,
            mg.genero_Id,
            i.image_Url
        FROM tbl_ope_music m
        LEFT JOIN tbl_rel_music_genero mg 
            ON mg.music_Id = m.music_Id
        LEFT JOIN tbl_ope_image i
            ON i.image_Id = m.music_ImageId
        WHERE m.music_Id = ?
        LIMIT 1
    ", [$id])->getRowArray();

    if (!$cancion) {
        return redirect()->to(base_url('admin/canciones'))
            ->with('error', 'La canción no existe');
    }

    // Normalizar datos viejos
    if (empty($cancion['music_ImageUrl']) && !empty($cancion['image_Url'])) {
        $cancion['music_ImageUrl'] = $cancion['image_Url'];
    }

    if (empty($cancion['music_ImageUrl'])) {
        $cancion['music_ImageUrl'] = 'img/canciones/default.jpg';
    }

    if (empty($cancion['music_UrlArchivo'])) {
        $cancion['music_UrlArchivo'] = '';
    }

    if (empty($cancion['music_FechaLanzamiento'])) {
        $cancion['music_FechaLanzamiento'] = '';
    } else {
        $cancion['music_FechaLanzamiento'] = date('Y-m-d', strtotime($cancion['music_FechaLanzamiento']));
    }

    if (!isset($cancion['genero_Id'])) {
        $cancion['genero_Id'] = '';
    }

    $generos = $db->table('tbl_cat_genero')
        ->where('genero_Activo', 1)
        ->orderBy('genero_Nombre', 'ASC')
        ->get()
        ->getResultArray();

    return view('Admin/Canciones/edit', [
        'cancion' => $cancion,
        'generos' => $generos
    ]);
}
}