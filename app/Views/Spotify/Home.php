<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Spotify Clone</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
    background: #121212;
}
::-webkit-scrollbar {
    width: 8px;
}
::-webkit-scrollbar-thumb {
    background: #1db954;
}
.card:hover {
    transform: scale(1.03);
}
#disco {
    background: radial-gradient(circle, #1db954, #000);
    box-shadow: 0 0 20px #1db954;
}
@keyframes girar {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
</head>

<body class="text-white flex">

<div class="w-64 bg-black p-6 min-h-screen">
    <h1 class="text-2xl font-bold text-green-500 mb-10">Spotify</h1>

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

<div class="flex-1 p-8 pb-32 overflow-y-auto">
    <h2 class="text-3xl font-bold mb-2">
        Bienvenido <?= esc(session('user_Name')) ?>
    </h2>

    <p class="text-gray-400 mb-6">
        Explora canciones y agrégalas a tu biblioteca.
    </p>

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

    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-6">
        <?php foreach ($canciones as $c): ?>
            <?php
                $img = !empty($c->music_ImageUrl)
                    ? $c->music_ImageUrl
                    : (!empty($c->image_Url) ? $c->image_Url : 'img/canciones/default.jpg');
            ?>

            <div class="card bg-zinc-900 p-4 rounded-xl transition">
                <div
                    class="cursor-pointer"
                    onclick="reproducir(
                        '<?= esc($c->music_Titulo, 'js') ?>',
                        '<?= base_url($c->music_UrlArchivo) ?>',
                        '<?= base_url($img) ?>'
                    )"
                >
                    <img src="<?= base_url($img) ?>" class="w-full h-40 object-cover rounded-lg mb-3">
                    <h3 class="font-bold"><?= esc($c->music_Titulo) ?></h3>
                    <p class="text-gray-400 text-sm mb-3"><?= esc($c->music_DuracionSegundos) ?> seg</p>
                </div>

                <?php if ((int)$c->enBiblioteca === 1): ?>
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
    </div>
</div>

<div class="fixed bottom-0 left-0 w-full bg-black border-t border-zinc-800 p-4 flex flex-col gap-2">
    <input type="range" id="progress" value="0" class="w-full accent-green-500">

    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <img id="imgPlayer" src="<?= base_url('img/canciones/default.jpg') ?>"
                class="w-14 h-14 rounded-full object-cover">
            <div>
                <p id="tituloPlayer" class="font-bold">Selecciona una canción</p>
                <p id="time" class="text-sm text-gray-400">0:00 / 0:00</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button onclick="prevSong()">⏮</button>
            <button onclick="togglePlay()" id="btnPlay"
                class="bg-green-500 p-3 rounded-full text-black">▶</button>
            <button onclick="nextSong()">⏭</button>
        </div>

        <div class="flex items-center gap-4">
            <input type="range" min="0" max="1" step="0.1" onchange="setVolume(this.value)">
            <div id="disco" class="w-16 h-16 border-4 border-green-500 rounded-full"></div>
        </div>
    </div>
</div>

<audio id="audio"></audio>

<script>
let audio = document.getElementById("audio");
let progress = document.getElementById("progress");
let canciones = [];
let index = 0;

<?php foreach($canciones as $c): ?>
canciones.push({
    titulo: "<?= esc($c->music_Titulo, 'js') ?>",
    url: "<?= base_url($c->music_UrlArchivo) ?>",
    img: "<?= base_url(!empty($c->music_ImageUrl) ? $c->music_ImageUrl : (!empty($c->image_Url) ? $c->image_Url : 'img/canciones/default.jpg')) ?>"
});
<?php endforeach; ?>
function reproducir(titulo, url, img) {
    audio.pause();
    audio.currentTime = 0;

    audio.src = url;
    audio.load();

    audio.play().catch(e => console.log("Error:", e));

    document.getElementById("tituloPlayer").innerText = titulo;

    if (document.getElementById("imgPlayer")) {
        document.getElementById("imgPlayer").src = img;
    }

    document.getElementById("btnPlay").innerText = "⏸";

    guardarEstado(titulo, url, img); // 🔥 CLAVE
}
function togglePlay() {
    if (audio.paused) {
        audio.play();
        document.getElementById("btnPlay").innerText = "⏸";
        activarDisco(true);
    } else {
        audio.pause();
        document.getElementById("btnPlay").innerText = "▶";
        activarDisco(false);
    }
}

function nextSong() {
    if (canciones.length === 0) return;
    index = (index + 1) % canciones.length;
    cargarCancion();
}

function prevSong() {
    if (canciones.length === 0) return;
    index = (index - 1 + canciones.length) % canciones.length;
    cargarCancion();
}

function cargarCancion() {
    let c = canciones[index];
    audio.pause();
    audio.currentTime = 0;
    audio.src = c.url;
    audio.load();
    audio.play().catch(e => console.log("Error:", e));
    document.getElementById("tituloPlayer").innerText = c.titulo;
    document.getElementById("imgPlayer").src = c.img;
    document.getElementById("btnPlay").innerText = "⏸";
    activarDisco(true);
}

audio.onended = () => {
    nextSong();
};

audio.ontimeupdate = () => {
    progress.value = (audio.currentTime / audio.duration) * 100 || 0;
    document.getElementById("time").innerText =
        formatTime(audio.currentTime) + " / " + formatTime(audio.duration);
};

progress.oninput = () => {
    if (!audio.duration) return;
    audio.currentTime = (progress.value / 100) * audio.duration;
};

function setVolume(val) {
    audio.volume = val;
}

function formatTime(sec) {
    if (!sec) return "0:00";
    let m = Math.floor(sec / 60);
    let s = Math.floor(sec % 60);
    return m + ":" + (s < 10 ? "0" + s : s);
}

function activarDisco(active) {
    let disco = document.getElementById("disco");
    disco.style.animation = active ? "girar 4s linear infinite" : "none";
}

document.body.addEventListener("click", () => {
    audio.muted = false;
}, { once: true });

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