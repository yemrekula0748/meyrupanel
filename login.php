<?php
session_start();
require 'DB.php'; // Veritabanı bağlantısı

$db = new DB();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['user_name']; // Dropdown'dan seçilen e-posta
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM users WHERE email = '" . $db->escape($email) . "'";
        $result = $db->query($sql);

        if ($db->numRows($result) > 0) {
            $user = $db->fetchAssoc($result);

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: index.php"); // Başarılı giriş yapıldıysa yönlendirme
                exit;
            } else {
                $error = "Geçersiz şifre.";
            }
        } else {
            $error = "Kullanıcı bulunamadı.";
        }
    } catch (Exception $e) {
        $error = "Bir hata oluştu: " . $e->getMessage();
    }
}

?>

<?php
// Kullanıcı adlarını almak için veritabanından çek
$userSql = "SELECT name, email FROM users";
$userResult = $db->query($userSql);
$users = [];
while ($row = $db->fetchAssoc($userResult)) {
    $users[] = $row;
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Giriş Yap | Sipariş Paneli</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .bg-mesh {
            background-color: #0f172a;
            background-image:
                radial-gradient(at 20% 30%, rgba(37,99,235,0.35) 0px, transparent 55%),
                radial-gradient(at 80% 10%, rgba(99,102,241,0.3) 0px, transparent 50%),
                radial-gradient(at 60% 80%, rgba(16,185,129,0.2) 0px, transparent 50%);
        }
        .glass {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        select option { color: #0f172a; }
    </style>
</head>

<body class="bg-mesh min-h-screen font-sans flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <a href="index.php">
                <img src="assets/images/logo-dark.png" alt="Logo" class="h-8 mx-auto brightness-0 invert mb-4">
            </a>
            <h1 class="text-3xl font-bold text-white tracking-tight">Tekrar Hoş Geldiniz</h1>
            <p class="text-slate-400 mt-1 text-sm">Devam etmek için oturum açın.</p>
        </div>

        <!-- Card -->
        <div class="glass rounded-2xl p-8 shadow-2xl">

            <?php if ($error): ?>
            <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl px-4 py-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-5">

                <!-- User Select -->
                <div>
                    <label for="user_name" class="block text-sm font-medium text-slate-300 mb-1.5">Kullanıcı</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <select
                            id="user_name"
                            name="user_name"
                            required
                            class="w-full bg-white/5 border border-white/10 text-white text-sm rounded-xl pl-10 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition placeholder-slate-500"
                        >
                            <option value="" class="bg-slate-800">Kullanıcı Seçin</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['email']) ?>" class="bg-slate-800">
                                    <?= htmlspecialchars($user['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Şifre</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="Şifrenizi girin"
                            class="w-full bg-white/5 border border-white/10 text-white text-sm rounded-xl pl-10 pr-12 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition placeholder-slate-500"
                        >
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-200 transition">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 active:scale-[0.98] text-white font-semibold text-sm rounded-xl py-3 mt-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-transparent shadow-lg shadow-brand-600/30"
                >
                    Giriş Yap
                </button>

            </form>
        </div>

        <p class="text-center text-slate-600 text-xs mt-6">&copy; <?= date('Y') ?> Sipariş Paneli</p>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeOffSVG = `<path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>`;
        const eyeSVG = `<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>`;

        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.innerHTML = isPassword ? eyeOffSVG : eyeSVG;
        });
    </script>

</body>
</html>
