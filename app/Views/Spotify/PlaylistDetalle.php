<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle Playlist</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white flex">

<div class="w-64 bg-black p-6 min-h-screen">
    <h1 class="text-2xl text-green-500 mb-10 font-bold">Spotify</h1>

    <ul class="space-y-4">
        <li><a href="<?= base_url('spotify/home') ?>" class="hover:text-green-400">Inicio</a></li>
        <li><a href="<?= base_url('spotify/buscar') ?>" class="hover:text-green-400">Buscar</a></li>
        <li><a href="<?= base_url('spotify/biblioteca') ?>" class="hover:text-green-400">Tu biblioteca</a></li>
        <li><a href="<?= base_url('spotify/playlist') ?>" class="hover:text-green-400">Playlist</a></li>
    </ul>

    <div class="mt-10">
        <a href="<?= base_url('acceso/logout') ?>" class="text-red-400 hover:text-red-500">
            Cerrar sesión
        </a>
    </div>
</div>

<div class="flex-1 p-8">
    <a href="<?= base_url('spotify/playlist') ?>" class="text-green-400 hover:text-green-300">
        ← Volver a playlists
    </a>

    <h2 class="text-3xl font-bold mt-4 mb-2"><?= esc($playlist->playlist_Nombre) ?></h2>
    <p class="text-gray-400 mb-8"><?= esc($playlist->playlist_Descripcion ?: 'Sin descripción') ?></p>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-600 text-white px-4 py-3 rounded-lg mb-6">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-600 text-white px-4 py-3 rounded-lg mb-6">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="mb-10">
        <h3 class="text-2xl font-semibold mb-4">Canciones en esta playlist</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-6">
            <?php if (!empty($cancionesPlaylist)): ?>
                <?php foreach ($cancionesPlaylist as $c): ?>
                    <?php
                        $img = !empty($c->music_ImageUrl)
                            ? $c->music_ImageUrl
                            : (!empty($c->image_Url) ? $c->image_Url : 'img/canciones/default.jpg');
                    ?>

                    <div class="bg-zinc-900 p-4 rounded-xl">
                        <img src="<?= base_url($img) ?>" class="w-full h-40 object-cover rounded-lg mb-3">

                        <h4 class="font-bold"><?= esc($c->music_Titulo) ?></h4>
                        <p class="text-gray-400 text-sm mb-3"><?= esc($c->music_DuracionSegundos) ?> seg</p>

                        <a href="<?= base_url('spotify/playlist/quitar-cancion/' . $playlist->playlist_Id . '/' . $c->music_Id) ?>"
                           class="block text-center bg-red-500 hover:bg-red-600 px-3 py-2 rounded-lg text-sm">
                            Quitar
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-400 col-span-5">Esta playlist todavía no tiene canciones.</p>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <h3 class="text-2xl font-semibold mb-4">Agregar canciones</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-6">
            <?php foreach ($todasCanciones as $c): ?>
                <?php
                    $img = !empty($c->music_ImageUrl)
                        ? $c->music_ImageUrl
                        : (!empty($c->image_Url) ? $c->image_Url : 'img/canciones/default.jpg');
                ?>

                <div class="bg-zinc-900 p-4 rounded-xl">
                    <img src="<?= base_url($img) ?>" class="w-full h-40 object-cover rounded-lg mb-3">

                    <h4 class="font-bold"><?= esc($c->music_Titulo) ?></h4>
                    <p class="text-gray-400 text-sm mb-3"><?= esc($c->music_DuracionSegundos) ?> seg</p>

                    <?php if ((int)$c->enPlaylist === 1): ?>
                        <button class="w-full bg-zinc-700 px-3 py-2 rounded-lg text-sm cursor-not-allowed" disabled>
                            Ya agregada
                        </button>
                    <?php else: ?>
                        <form action="<?= base_url('spotify/playlist/agregar-cancion/' . $c->music_Id) ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="playlist_Id" value="<?= $playlist->playlist_Id ?>">

                            <button type="submit"
                                    class="w-full bg-green-500 hover:bg-green-600 text-black font-semibold px-3 py-2 rounded-lg text-sm">
                                Agregar a playlist
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>