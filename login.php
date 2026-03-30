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
                        crimson: {
                            50:  '#fff1f1',
                            100: '#ffe0e0',
                            200: '#ffc5c5',
                            400: '#f87171',
                            500: '#e02020',
                            600: '#c41a1a',
                            700: '#a31414',
                            800: '#7f0f0f',
                            900: '#660c0c',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c41a1a' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(196,26,26,0.1), 0 20px 60px -10px rgba(196,26,26,0.15);
        }
        .input-field {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            color: #111827;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .input-field:focus {
            border-color: #c41a1a;
            box-shadow: 0 0 0 3px rgba(196,26,26,0.12);
            outline: none;
        }
        select option { color: #111827; }
        .header-bar {
            background: linear-gradient(135deg, #c41a1a 0%, #8b0f0f 100%);
        }
    </style>
</head>

<body class="min-h-screen font-sans flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-sm">

        <!-- Card -->
        <div class="bg-white rounded-2xl overflow-hidden card-shadow">

            <!-- Kırmızı üst bant -->
            <div class="header-bar px-8 py-7 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-white/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Hoş Geldiniz</h1>
                <p class="text-red-200 mt-1 text-sm">Devam etmek için oturum açın.</p>
            </div>

            <!-- Form alanı -->
            <div class="px-8 py-7">

                <?php if ($error): ?>
                <div class="flex items-center gap-2.5 bg-red-50 border border-red-200 text-crimson-600 text-sm rounded-xl px-4 py-3 mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-crimson-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="space-y-5">

                    <!-- User Select -->
                    <div>
                        <label for="user_name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Kullanıcı</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <select
                                id="user_name"
                                name="user_name"
                                required
                                class="input-field w-full text-sm rounded-xl pl-10 pr-9 py-3 appearance-none"
                            >
                                <option value="">Kullanıcı Seçin</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= htmlspecialchars($user['email']) ?>">
                                        <?= htmlspecialchars($user['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Şifre</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="Şifrenizi girin"
                                class="input-field w-full text-sm rounded-xl pl-10 pr-11 py-3 placeholder-gray-300"
                            >
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition">
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
                        class="w-full bg-crimson-600 hover:bg-crimson-700 active:scale-[0.98] active:bg-crimson-800 text-white font-semibold text-sm rounded-xl py-3 mt-1 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-crimson-500 focus:ring-offset-2 shadow-md"
                    >
                        Giriş Yap
                    </button>

                </form>
            </div>
        </div>

        <p class="text-center text-gray-400 text-xs mt-5">&copy; <?= date('Y') ?> Sipariş Paneli</p>
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
