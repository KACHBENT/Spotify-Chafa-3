<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buscar</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
    background: #121212;
}
</style>
</head>

<body class="bg-black text-white flex">

<!-- SIDEBAR -->
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

<!-- CONTENIDO -->
<div class="flex-1 p-8 pb-28">

    <h2 class="text-3xl mb-6 font-bold">Buscar canciones</h2>

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

    <!-- BUSCADOR -->
    <form method="GET" action="<?= base_url('spotify/buscar') ?>" class="mb-6 flex gap-2">
        <input 
            type="text" 
            name="q"
            value="<?= esc($q ?? '') ?>"
            placeholder="Buscar música..."
            class="flex-1 p-3 rounded-lg text-black"
        >

        <button type="submit" class="bg-green-500 px-6 rounded-lg text-black font-bold">
            Buscar
        </button>
    </form>

    <!-- RESULTADOS -->
    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-6">

        <?php if (!empty($canciones)): ?>
            <?php foreach ($canciones as $c): ?>

                <?php
                    $img = !empty($c->music_ImageUrl)
                        ? $c->music_ImageUrl
                        : (!empty($c->image_Url) ? $c->image_Url : 'img/canciones/default.jpg');
                ?>

                <div class="bg-zinc-900 p-4 rounded-xl hover:scale-105 transition">
                    
                    <div class="cursor-pointer"
                        onclick="reproducir(
                            '<?= esc($c->music_Titulo, 'js') ?>',
                            '<?= base_url($c->music_UrlArchivo) ?>',
                            '<?= base_url($img) ?>'
                        )">

                        <img src="<?= base_url($img) ?>"
                             class="w-full h-40 object-cover rounded-lg mb-3">

                        <h3 class="font-bold"><?= esc($c->music_Titulo) ?></h3>

                        <p class="text-gray-400 text-sm mb-3">
                            <?= esc($c->music_DuracionSegundos) ?> seg
                        </p>
                    </div>

                    <?php if (isset($c->enBiblioteca) && (int)$c->enBiblioteca === 1): ?>
                        <a href="<?= base_url('spotify/biblioteca') ?>"
                           class="block text-center bg-zinc-700 hover:bg-zinc-600 px-3 py-2 rounded-lg text-sm">
                            Ya está en tu biblioteca
                        </a>
                    <?php else: ?>
                        <form action="<?= base_url('spotify/biblioteca/agregar/' . $c->music_Id) ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit"
                                    class="w-full bg-green-500 hover:bg-green-600 text-black font-semibold px-3 py-2 rounded-lg text-sm">
                                Agregar a biblioteca
                            </button>
                        </form>
                    <?php endif; ?>

                </div>

            <?php endforeach; ?>
        <?php else: ?>

            <div class="col-span-5 text-center py-16">
                <p class="text-2xl font-semibold mb-2">No hay resultados</p>
                <p class="text-gray-400">Prueba buscando por el nombre de una canción.</p>
            </div>

        <?php endif; ?>

    </div>

</div>

<!-- REPRODUCTOR SIMPLE -->
<div class="fixed bottom-0 left-0 w-full bg-black border-t border-zinc-800 p-4 flex justify-between items-center">
    <div>
        <p id="tituloPlayer" class="font-semibold">Selecciona una canción</p>
    </div>

    <div>
        <button onclick="togglePlay()" id="btnPlay" class="bg-green-500 px-4 py-2 rounded text-black font-bold">
            ▶
        </button>
    </div>
</div>

<audio id="audio"></audio>

<script>
let audio = document.getElementById("audio");
function reproducir(titulo, url, img) {
    audio.pause();
    audio.currentTime = 0;

    audio.src = url;
    audio.load();

    audio.play().catch(e => console.log("Error:", e));

    document.getElementById("tituloPlayer").innerText = titulo;

    document.getElementById("btnPlay").innerText = "⏸";
}

function togglePlay() {
    if (!audio.src) return;

    if (audio.paused) {
        audio.play().catch(e => console.log("Error:", e));
        document.getElementById("btnPlay").innerText = "⏸";
    } else {
        audio.pause();
        document.getElementById("btnPlay").innerText = "▶";
    }
}
// GUARDAR CANCIÓN
function guardarEstado(titulo, url, img) {
    localStorage.setItem("player", JSON.stringify({
        titulo: titulo,
        url: url,
        img: img
    }));
}

// CARGAR CANCIÓN AL ENTRAR
window.addEventListener("load", () => {
    let data = localStorage.getItem("player");

    if (!data) return;

    let c = JSON.parse(data);

    audio.src = c.url;
    document.getElementById("tituloPlayer").innerText = c.titulo;

    if (document.getElementById("imgPlayer")) {
        document.getElementById("imgPlayer").src = c.img;
    }
});
</script>

</body>
</html>