<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nueva canción</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-white min-h-screen">

<div class="max-w-4xl mx-auto p-4 md:p-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Nueva canción</h1>
        <a href="<?= base_url('admin/canciones') ?>" class="text-green-400 hover:text-green-300">← Volver</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-600 text-white px-4 py-3 rounded-lg mb-6">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/canciones/store') ?>" method="post" enctype="multipart/form-data" class="bg-zinc-900 p-6 rounded-xl space-y-5">
        <?= csrf_field() ?>

        <div>
            <label class="block mb-2">Título</label>
            <input type="text" name="music_Titulo" value="<?= old('music_Titulo') ?>" class="w-full p-3 rounded-lg text-black">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block mb-2">Duración en segundos</label>
                <input type="number" name="music_DuracionSegundos" value="<?= old('music_DuracionSegundos') ?>" class="w-full p-3 rounded-lg text-black">
            </div>

            <div>
                <label class="block mb-2">Fecha de lanzamiento</label>
                <input type="date" name="music_FechaLanzamiento" value="<?= old('music_FechaLanzamiento') ?>" class="w-full p-3 rounded-lg text-black">
            </div>
        </div>

        <div>
            <label class="block mb-2">Archivo de audio</label>
            <input type="file" name="music_File" accept=".mp3,audio/mpeg,.wav,audio/wav,.ogg,audio/ogg,.mp4,audio/mp4" class="w-full p-3 rounded-lg bg-white text-black">
            <p class="text-sm text-gray-400 mt-2">Se guardará automáticamente en <strong>/public/music/</strong></p>
        </div>

        <div>
            <label class="block mb-2">Imagen de canción</label>
            <input type="file" name="music_ImageFile" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full p-3 rounded-lg bg-white text-black">
            <p class="text-sm text-gray-400 mt-2">Se guardará automáticamente en <strong>/public/img/canciones/</strong></p>
        </div>

        <div>
            <label class="block mb-2">Género</label>
            <select name="genero_Id" class="w-full p-3 rounded-lg text-black">
                <option value="">Selecciona un género</option>
                <?php foreach ($generos as $g): ?>
                    <option value="<?= $g->genero_Id ?>"><?= esc($g->genero_Nombre) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block mb-2">Letra</label>
            <textarea name="music_Letra" rows="5" class="w-full p-3 rounded-lg text-black"><?= old('music_Letra') ?></textarea>
        </div>

        <button type="submit" class="w-full md:w-auto bg-green-500 hover:bg-green-600 text-black font-bold px-6 py-3 rounded-lg">
            Guardar canción
        </button>
    </form>
</div>

</body>
</html>