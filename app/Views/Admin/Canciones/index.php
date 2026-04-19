<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Canciones</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-white min-h-screen">

<div class="flex flex-col lg:flex-row">
    <aside class="w-full lg:w-72 bg-black p-6">
        <h1 class="text-2xl font-bold text-green-500 mb-8">Admin Spotify</h1>

        <nav class="space-y-4">
            <a href="<?= base_url('admin/canciones') ?>" class="block hover:text-green-400">Canciones</a>
            <a href="<?= base_url('spotify/home') ?>" class="block hover:text-green-400">Ver app</a>
            <a href="<?= base_url('acceso/logout') ?>" class="block text-red-400 hover:text-red-500">Cerrar sesión</a>
        </nav>
    </aside>

    <main class="flex-1 p-4 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-3xl font-bold">Gestión de canciones</h2>
                <p class="text-gray-400">Administra el catálogo musical</p>
            </div>

            <a href="<?= base_url('admin/canciones/create') ?>"
               class="bg-green-500 hover:bg-green-600 text-black font-bold px-5 py-3 rounded-lg text-center">
                + Nueva canción
            </a>
        </div>

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

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($canciones as $c): ?>
                <?php $img = !empty($c->music_ImageUrl) ? $c->music_ImageUrl : 'img/canciones/default.jpg'; ?>

                <div class="bg-zinc-900 rounded-xl p-4 shadow-lg">
                    <img src="<?= base_url($img) ?>" class="w-full h-48 object-cover rounded-lg mb-4">

                    <h3 class="text-xl font-bold"><?= esc($c->music_Titulo) ?></h3>
                    <p class="text-sm text-gray-400 mb-1">Duración: <?= esc($c->music_DuracionSegundos) ?> seg</p>
                    <p class="text-sm text-gray-400 mb-1">Género: <?= esc($c->genero_Nombre ?? 'Sin género') ?></p>
                    <p class="text-sm mb-4">
                        Estado:
                        <span class="<?= (int)$c->music_Activo === 1 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= (int)$c->music_Activo === 1 ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </p>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="<?= base_url('admin/canciones/edit/' . $c->music_Id) ?>"
                           class="bg-blue-500 hover:bg-blue-600 text-white text-center py-2 rounded-lg">
                            Editar
                        </a>

                        <a href="<?= base_url('admin/canciones/delete/' . $c->music_Id) ?>"
                           onclick="return confirm('¿Seguro que deseas eliminar esta canción?')"
                           class="bg-red-500 hover:bg-red-600 text-white text-center py-2 rounded-lg">
                            Eliminar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

</body>
</html>