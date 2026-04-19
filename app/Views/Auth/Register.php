<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Spotify</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .bg-spotify {
            background: radial-gradient(circle at top, #1db954, #000000 60%);
        }

        .fade-in {
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {opacity:0; transform:translateY(20px);}
            to {opacity:1; transform:translateY(0);}
        }

        .input {
            background: #181818;
            border: 1px solid #333;
            transition: 0.3s;
        }

        .input:focus {
            border-color: #1db954;
            outline: none;
        }

        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(29,185,84,0.6);
        }
    </style>
</head>

<body class="bg-spotify flex justify-center items-center min-h-screen">

<div class="fade-in bg-black/80 backdrop-blur-xl p-8 rounded-3xl shadow-2xl w-full max-w-2xl">

    <h1 class="text-green-500 text-4xl font-bold text-center mb-6">
        Crear cuenta
    </h1>

    <form action="<?= base_url('/register/save') ?>" method="POST">

        <div class="grid grid-cols-2 gap-4">

            <input type="text" name="nombre" placeholder="Nombre" required class="input p-3 rounded-xl text-white">
            <input type="text" name="apellidop" placeholder="Apellido paterno" class="input p-3 rounded-xl text-white">

            <input type="text" name="apellidom" placeholder="Apellido materno" class="input p-3 rounded-xl text-white">
            <input type="text" name="username" placeholder="Username" required class="input p-3 rounded-xl text-white">

            <input type="email" name="correo" placeholder="Correo" required class="input p-3 rounded-xl text-white">
            <input type="tel" name="telefono" placeholder="Teléfono" class="input p-3 rounded-xl text-white">

            <input type="date" name="fecha" class="input p-3 rounded-xl text-white">
            <input type="text" name="pais" placeholder="País" class="input p-3 rounded-xl text-white">

            <input type="password" name="password" placeholder="Contraseña" required class="input p-3 rounded-xl text-white col-span-2">

        </div>

        <button class="btn-glow mt-6 w-full bg-green-500 hover:bg-green-600 text-black font-bold py-3 rounded-full transition">
            Registrarme
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="<?= base_url('/acceso/login') ?>" class="text-green-400">
            ¿Ya tienes cuenta? Inicia sesión
        </a>
    </div>

</div>

</body>
</html>