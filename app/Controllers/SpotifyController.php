<?php

namespace App\Controllers;

class SpotifyController extends BaseController
{
    public function home()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/acceso/login');
        }

        $db = \Config\Database::connect();
        $userId = session('user_Id');

        $canciones = $db->query("
            SELECT 
                m.*, 
                i.image_Url,
                CASE 
                    WHEN ul.userLibrary_Id IS NULL THEN 0
                    ELSE 1
                END AS enBiblioteca
            FROM tbl_ope_music m
            LEFT JOIN tbl_ope_image i 
                ON m.music_ImageId = i.image_Id
            LEFT JOIN tbl_rel_user_library ul
                ON ul.music_Id = m.music_Id
                AND ul.user_Id = ?
                AND ul.userLibrary_Activo = 1
            WHERE m.music_Activo = 1
            ORDER BY m.music_Id DESC
        ", [$userId])->getResult();

        return view('Spotify/Home', [
            'canciones' => $canciones
        ]);
    }
public function buscar()
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/acceso/login');
    }

    $db = \Config\Database::connect();
    $userId = (int) session('user_Id');
    $q = trim((string) $this->request->getGet('q'));

    $builder = $db->table('tbl_ope_music m');
    $builder->select("
        m.*,
        i.image_Url,
        CASE 
            WHEN ul.userLibrary_Id IS NULL THEN 0
            ELSE 1
        END AS enBiblioteca
    ", false);

    $builder->join('tbl_ope_image i', 'm.music_ImageId = i.image_Id', 'left');
    $builder->join(
        'tbl_rel_user_library ul',
        'ul.music_Id = m.music_Id AND ul.user_Id = ' . $userId . ' AND ul.userLibrary_Activo = 1',
        'left'
    );
    $builder->where('m.music_Activo', 1);

    if ($q !== '') {
        $builder->like('m.music_Titulo', $q);
    }

    $canciones = $builder->get()->getResult();

    return view('Spotify/Buscar', [
        'canciones' => $canciones,
        'q' => $q
    ]);
}

public function biblioteca()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/acceso/login');
        }

        $db = \Config\Database::connect();
        $userId = session('user_Id');

        $canciones = $db->query("
            SELECT 
                m.*, 
                i.image_Url,
                ul.userLibrary_FechaAgregada
            FROM tbl_rel_user_library ul
            INNER JOIN tbl_ope_music m 
                ON ul.music_Id = m.music_Id
            LEFT JOIN tbl_ope_image i 
                ON m.music_ImageId = i.image_Id
            WHERE ul.user_Id = ?
              AND ul.userLibrary_Activo = 1
              AND m.music_Activo = 1
            ORDER BY ul.userLibrary_FechaAgregada DESC
        ", [$userId])->getResult();

        return view('Spotify/Biblioteca', [
            'canciones' => $canciones
        ]);
    }

    public function agregarBiblioteca($musicId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/acceso/login');
        }

        $db = \Config\Database::connect();
        $userId = session('user_Id');
        $musicId = (int)$musicId;

        $existente = $db->table('tbl_rel_user_library')
            ->where('user_Id', $userId)
            ->where('music_Id', $musicId)
            ->get()
            ->getRowArray();

        if ($existente) {
            $db->table('tbl_rel_user_library')
                ->where('user_Id', $userId)
                ->where('music_Id', $musicId)
                ->update([
                    'userLibrary_Activo' => 1,
                    'userLibrary_FechaAgregada' => date('Y-m-d H:i:s')
                ]);
        } else {
            $db->table('tbl_rel_user_library')->insert([
                'user_Id' => $userId,
                'music_Id' => $musicId,
                'userLibrary_Activo' => 1,
                'userLibrary_FechaAgregada' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->back()->with('success', 'Canción agregada a tu biblioteca');
    }

    public function quitarBiblioteca($musicId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/acceso/login');
        }

        $db = \Config\Database::connect();
        $userId = session('user_Id');
        $musicId = (int)$musicId;

        $db->table('tbl_rel_user_library')
            ->where('user_Id', $userId)
            ->where('music_Id', $musicId)
            ->update([
                'userLibrary_Activo' => 0
            ]);

        return redirect()->back()->with('success', 'Canción eliminada de tu biblioteca');
    }

    public function playlist()
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/acceso/login');
    }

    $db = \Config\Database::connect();
    $userId = (int) session('user_Id');

    $playlists = $db->query("
        SELECT p.*,
               COUNT(pm.playlistMusic_Id) AS totalCanciones
        FROM tbl_ope_playlist p
        LEFT JOIN tbl_rel_playlist_music pm 
            ON pm.playlist_Id = p.playlist_Id
        WHERE p.playlist_UserId = ?
          AND p.playlist_Activo = 1
        GROUP BY p.playlist_Id
        ORDER BY p.playlist_Id DESC
    ", [$userId])->getResult();

    return view('Spotify/Playlist', [
        'playlists' => $playlists
    ]);
}

public function crearPlaylist()
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/acceso/login');
    }

    $nombre = trim((string) $this->request->getPost('playlist_Nombre'));
    $descripcion = trim((string) $this->request->getPost('playlist_Descripcion'));
    $publica = $this->request->getPost('playlist_Publica') ? 1 : 0;

    if ($nombre === '') {
        return redirect()->back()->with('error', 'El nombre de la playlist es obligatorio');
    }

    $db = \Config\Database::connect();
    $db->table('tbl_ope_playlist')->insert([
        'playlist_Nombre' => $nombre,
        'playlist_Descripcion' => $descripcion,
        'playlist_Publica' => $publica,
        'playlist_UserId' => (int) session('user_Id'),
        'playlist_Activo' => 1
    ]);

    return redirect()->to(base_url('spotify/playlist'))->with('success', 'Playlist creada correctamente');
}

public function verPlaylist($playlistId)
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/acceso/login');
    }

    $db = \Config\Database::connect();
    $userId = (int) session('user_Id');
    $playlistId = (int) $playlistId;

    $playlist = $db->table('tbl_ope_playlist')
        ->where('playlist_Id', $playlistId)
        ->where('playlist_UserId', $userId)
        ->where('playlist_Activo', 1)
        ->get()
        ->getRow();

    if (!$playlist) {
        return redirect()->to(base_url('spotify/playlist'))
            ->with('error', 'La playlist no existe o no te pertenece');
    }

    $cancionesPlaylist = $db->query("
        SELECT m.*, i.image_Url, pm.playlistMusic_Orden
        FROM tbl_rel_playlist_music pm
        INNER JOIN tbl_ope_music m 
            ON m.music_Id = pm.music_Id
        LEFT JOIN tbl_ope_image i 
            ON i.image_Id = m.music_ImageId
        WHERE pm.playlist_Id = ?
          AND m.music_Activo = 1
        ORDER BY pm.playlistMusic_Orden ASC, pm.playlistMusic_Id ASC
    ", [$playlistId])->getResult();

    $todasCanciones = $db->query("
        SELECT 
            m.*, 
            i.image_Url,
            CASE 
                WHEN pm.playlistMusic_Id IS NULL THEN 0
                ELSE 1
            END AS enPlaylist
        FROM tbl_ope_music m
        LEFT JOIN tbl_ope_image i 
            ON i.image_Id = m.music_ImageId
        LEFT JOIN tbl_rel_playlist_music pm
            ON pm.music_Id = m.music_Id
           AND pm.playlist_Id = ?
        WHERE m.music_Activo = 1
        ORDER BY m.music_Id DESC
    ", [$playlistId])->getResult();

    return view('Spotify/PlaylistDetalle', [
        'playlist' => $playlist,
        'cancionesPlaylist' => $cancionesPlaylist,
        'todasCanciones' => $todasCanciones
    ]);
}

public function agregarCancionPlaylist($musicId)
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/acceso/login');
    }

    $playlistId = (int) $this->request->getPost('playlist_Id');
    $musicId = (int) $musicId;
    $userId = (int) session('user_Id');
    $db = \Config\Database::connect();

    $playlist = $db->table('tbl_ope_playlist')
        ->where('playlist_Id', $playlistId)
        ->where('playlist_UserId', $userId)
        ->where('playlist_Activo', 1)
        ->get()
        ->getRowArray();

    if (!$playlist) {
        return redirect()->back()->with('error', 'Playlist inválida');
    }

    $existe = $db->table('tbl_rel_playlist_music')
        ->where('playlist_Id', $playlistId)
        ->where('music_Id', $musicId)
        ->countAllResults();

    if ($existe > 0) {
        return redirect()->back()->with('error', 'La canción ya está en esa playlist');
    }

    $ultimoOrden = $db->table('tbl_rel_playlist_music')
        ->selectMax('playlistMusic_Orden')
        ->where('playlist_Id', $playlistId)
        ->get()
        ->getRowArray();

    $nuevoOrden = ((int)($ultimoOrden['playlistMusic_Orden'] ?? 0)) + 1;

    $db->table('tbl_rel_playlist_music')->insert([
        'playlist_Id' => $playlistId,
        'music_Id' => $musicId,
        'playlistMusic_Orden' => $nuevoOrden
    ]);

    return redirect()->back()->with('success', 'Canción agregada a la playlist');
}

public function quitarCancionPlaylist($playlistId, $musicId)
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/acceso/login');
    }

    $playlistId = (int) $playlistId;
    $musicId = (int) $musicId;
    $userId = (int) session('user_Id');
    $db = \Config\Database::connect();

    $playlist = $db->table('tbl_ope_playlist')
        ->where('playlist_Id', $playlistId)
        ->where('playlist_UserId', $userId)
        ->where('playlist_Activo', 1)
        ->get()
        ->getRowArray();

    if (!$playlist) {
        return redirect()->to(base_url('spotify/playlist'))
            ->with('error', 'Playlist inválida');
    }

    $db->table('tbl_rel_playlist_music')
        ->where('playlist_Id', $playlistId)
        ->where('music_Id', $musicId)
        ->delete();

    return redirect()->back()->with('success', 'Canción eliminada de la playlist');
}
}