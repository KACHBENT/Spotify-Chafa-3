<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Clone</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fuente -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Fondo tipo Spotify */
        .bg-spotify {
            background: radial-gradient(circle at top, #1db954, #000000 60%);
        }

        /* Animación entrada */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Glow botón */
        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(29,185,84,0.6);
        }
    </style>
</head>

<body class="bg-spotify flex items-center justify-center min-h-screen">

<div class="fade-in w-full max-w-md bg-black/80 backdrop-blur-xl p-10 rounded-3xl shadow-2xl">

    <!-- LOGO -->
    <h1 class="text-5xl font-bold text-center text-green-500 mb-8 tracking-wide">
        Spotify
    </h1>

    <!-- MENSAJES -->
    <?php if(session()->getFlashdata('error')): ?>
        <div class="bg-red-500/80 text-white p-3 rounded-lg mb-4 text-center">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="bg-green-500/80 text-white p-3 rounded-lg mb-4 text-center">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form action="<?= base_url('/acceso/login') ?>" method="POST">

        <!-- USER -->
        <div class="mb-5">
            <label class="text-gray-300 text-sm">Usuario o correo</label>
            <input type="text" name="usuario" required
                class="w-full mt-2 p-3 rounded-xl bg-zinc-900 text-white border border-zinc-700 focus:outline-none focus:border-green-500 transition">
        </div>

        <!-- PASSWORD -->
        <div class="mb-6">
            <label class="text-gray-300 text-sm">Contraseña</label>
            <input type="password" name="password" required
                class="w-full mt-2 p-3 rounded-xl bg-zinc-900 text-white border border-zinc-700 focus:outline-none focus:border-green-500 transition">
        </div>

        <!-- BOTÓN -->
        <button type="submit"
            class="btn-glow w-full bg-green-500 hover:bg-green-600 text-black font-bold py-3 rounded-full transition duration-300">
            Iniciar sesión
        </button>
    </form>

    <!-- DIVISOR -->
    <div class="flex items-center my-6">
        <div class="flex-1 h-px bg-zinc-700"></div>
        <span class="px-3 text-gray-400 text-sm">o</span>
        <div class="flex-1 h-px bg-zinc-700"></div>
    </div>

    <!-- REGISTER -->
    <a href="<?= base_url('/register') ?>"
       class="block text-center border border-green-500 text-green-500 py-3 rounded-full hover:bg-green-500 hover:text-black transition">
        Crear una cuenta
    </a>

</div>

</body>
</html>