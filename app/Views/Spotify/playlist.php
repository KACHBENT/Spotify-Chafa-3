<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Playlist</title>
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
    <h2 class="text-3xl font-bold mb-6">Mis playlists</h2>

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

    <div class="bg-zinc-900 p-6 rounded-xl mb-8">
        <h3 class="text-xl font-semibold mb-4">Crear nueva playlist</h3>

        <form action="<?= base_url('spotify/playlist/crear') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <input type="text"
                   name="playlist_Nombre"
                   placeholder="Nombre de la playlist"
                   class="w-full p-3 rounded-lg text-black">

            <textarea name="playlist_Descripcion"
                      placeholder="Descripción (opcional)"
                      class="w-full p-3 rounded-lg text-black"></textarea>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="playlist_Publica" value="1">
                <span>Playlist pública</span>
            </label>

            <button type="submit"
                    class="bg-green-500 hover:bg-green-600 text-black font-bold px-5 py-3 rounded-lg">
                Crear playlist
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php if (!empty($playlists)): ?>
            <?php foreach ($playlists as $p): ?>
                <div class="bg-zinc-900 p-5 rounded-xl">
                    <h3 class="text-xl font-bold mb-2"><?= esc($p->playlist_Nombre) ?></h3>

                    <p class="text-gray-400 mb-2">
                        <?= esc($p->playlist_Descripcion ?: 'Sin descripción') ?>
                    </p>

                    <p class="text-sm text-gray-500 mb-4">
                        <?= (int)$p->totalCanciones ?> canciones
                    </p>

                    <a href="<?= base_url('spotify/playlist/ver/' . $p->playlist_Id) ?>"
                       class="inline-block bg-green-500 hover:bg-green-600 text-black font-semibold px-4 py-2 rounded-lg">
                        Ver playlist
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-3 text-center py-16">
                <p class="text-2xl font-semibold mb-2">Aún no tienes playlists</p>
                <p class="text-gray-400">Crea una para empezar.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>