<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Tu Biblioteca</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white flex">

<div class="w-64 bg-black p-6 min-h-screen">
    <h1 class="text-2xl text-green-500 mb-10">Spotify</h1>

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
    <h2 class="text-3xl font-bold mb-6">Tu Biblioteca</h2>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-600 text-white px-4 py-3 rounded-lg mb-6">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-6">
        <?php if (!empty($canciones)): ?>
            <?php foreach ($canciones as $c): ?>
                <?php
                    $img = !empty($c->music_ImageUrl)
                        ? $c->music_ImageUrl
                        : (!empty($c->image_Url) ? $c->image_Url : 'img/canciones/default.jpg');
                ?>

                <div class="bg-zinc-900 p-4 rounded-xl hover:scale-105 transition">
                    <img src="<?= base_url($img) ?>" class="h-40 w-full object-cover rounded-lg mb-3">

                    <p class="font-bold"><?= esc($c->music_Titulo) ?></p>
                    <p class="text-gray-400 text-sm mb-3"><?= esc($c->music_DuracionSegundos) ?> seg</p>

                    <a href="<?= base_url('spotify/biblioteca/quitar/' . $c->music_Id) ?>"
                       class="block text-center bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm">
                        Quitar de biblioteca
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-5 text-center py-16">
                <p class="text-2xl font-semibold mb-2">Tu biblioteca está vacía</p>
                <p class="text-gray-400 mb-6">Agrega canciones desde Inicio o Buscar.</p>
                <a href="<?= base_url('spotify/home') ?>"
                   class="inline-block bg-green-500 hover:bg-green-600 text-black font-semibold px-5 py-3 rounded-lg">
                    Ir al inicio
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>