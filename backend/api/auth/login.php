<?php
session_start(); // Start session on every page

// ── If already logged in, redirect to the right dashboard ──
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
        exit;
    } else {
        header('Location: student.php');
        exit;
    }
}

$error   = '';
$success = '';

// ── Show success message after registration ──
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success = 'Account created successfully! Please sign in.';
}

// ── Handle login form submission ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitise inputs
    $login_id = trim($_POST['login_id'] ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($login_id) || empty($password)) {
        $error = 'All fields are required.';

    } else {

        // ── ADMIN CHECK ──────────────────────────────────────
        // Hard-coded credentials (replace with DB query later)
        if ($login_id === 'admin' && $password === 'admin123') {
            $_SESSION['role']     = 'admin';
            $_SESSION['username'] = 'Admin';
            header('Location: admin.php');
            exit;
        }

        // ── STUDENT CHECK ────────────────────────────────────
        // Look up registered students stored in session storage
        $students = $_SESSION['students'] ?? [];
        $found    = false;

        foreach ($students as $student) {
            // Compare username (or email) and plain-text password
            // NOTE: When upgrading to DB, use password_verify() here
            if (
                ($student['username'] === $login_id || $student['email'] === $login_id)
                && $student['password'] === $password
            ) {
                $_SESSION['role']      = 'student';
                $_SESSION['username']  = $student['full_name'];
                $_SESSION['class']     = $student['class'];
                $_SESSION['email']     = $student['email'];
                $found = true;
                header('Location: student.php');
                exit;
            }
        }

        if (!$found) {
            $error = 'Invalid username/email or password.';
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SCMS — Sign In</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['DM Sans', 'sans-serif'],
            mono: ['DM Mono', 'monospace'],
          },
          colors: {
            brand: {
              50:  '#eef2ff',
              100: '#e0e7ff',
              200: '#c7d2fe',
              400: '#818cf8',
              500: '#6366f1',
              600: '#4f46e5',
              700: '#4338ca',
              800: '#3730a3',
              900: '#312e81',
            },
          },
          animation: {
            'fade-up': 'fadeUp 0.5s ease both',
          },
          keyframes: {
            fadeUp: {
              '0%': { opacity: '0', transform: 'translateY(20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
          },
        }
      }
    }
  </script>
  <style>
    body { font-family: 'DM Sans', sans-serif; }
    .bg-mesh {
      background-color: #0f172a;
      background-image:
        radial-gradient(ellipse 80% 50% at 20% 40%, rgba(99,102,241,0.18) 0%, transparent 60%),
        radial-gradient(ellipse 60% 60% at 80% 70%, rgba(139,92,246,0.12) 0%, transparent 55%);
    }
    .card-shadow { box-shadow: 0 25px 60px rgba(0,0,0,0.35), 0 4px 16px rgba(0,0,0,0.2); }
    .input-focus:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
    .btn-primary {
      background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
      transition: all 0.2s;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(99,102,241,0.4);
    }
    .btn-primary:active { transform: translateY(0); }
    .right-panel {
      background: linear-gradient(160deg, #1e1b4b 0%, #0f172a 50%, #1a0533 100%);
    }
    .dots-pattern {
      background-image: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px);
      background-size: 24px 24px;
    }
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
    .float { animation: float 4s ease-in-out infinite; }
    .float-delay { animation: float 4s ease-in-out 1.5s infinite; }
  </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-5xl animate-fade-up">

    <div class="flex rounded-3xl overflow-hidden card-shadow">

      <!-- ══ LEFT — LOGIN FORM ══ -->
      <div class="w-full lg:w-3/5 bg-white px-8 sm:px-12 py-12">

        <!-- Logo -->
        <div class="flex items-center gap-3 mb-10">
          <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
              <path d="M12 14l9-5-9-5-9 5 9 5z"/>
              <path d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z"/>
            </svg>
          </div>
          <span class="font-bold text-slate-800 text-lg tracking-tight">SCMS</span>
        </div>

        <!-- Heading -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-1.5">Welcome back</h1>
          <p class="text-slate-500 text-sm">Sign in to your Smart Campus account.</p>
        </div>

        <!-- ── PHP Error Banner ── -->
        <?php if (!empty($error)): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-6 text-sm">
          <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- ── PHP Success Banner (after registration) ── -->
        <?php if (!empty($success)): ?>
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-6 text-sm">
          <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M20 6L9 17l-5-5"/>
          </svg>
          <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form id="login-form" method="POST" action="login.php" novalidate>

          <!-- Username / Email -->
          <div class="mb-5">
            <label for="login_id" class="block text-sm font-semibold text-slate-700 mb-2">
              Username or Email
            </label>
            <div class="relative">
              <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
              </span>
              <input
                type="text"
                id="login_id"
                name="login_id"
                value="<?= htmlspecialchars($_POST['login_id'] ?? '') ?>"
                placeholder="Enter username or email"
                autocomplete="username"
                class="input-focus w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder-slate-400 transition-colors"
              />
            </div>
            <p id="err-login_id" class="hidden mt-1.5 text-xs text-red-600 font-medium">This field is required.</p>
          </div>

          <!-- Password -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
              <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
              </span>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                class="input-focus w-full pl-11 pr-12 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder-slate-400 transition-colors"
              />
              <button type="button" onclick="togglePw('password','eye-login')"
                class="absolute inset-y-0 right-3 flex items-center px-1 text-slate-400 hover:text-slate-600 transition-colors">
                <svg id="eye-login" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <p id="err-password" class="hidden mt-1.5 text-xs text-red-600 font-medium">This field is required.</p>
          </div>

          <!-- Submit Button -->
          <button type="submit" id="login-btn"
            class="btn-primary w-full py-3 rounded-xl text-white font-semibold text-sm tracking-wide">
            Sign In
          </button>

        </form>

        <!-- Mobile: Register link -->
        <p class="lg:hidden mt-6 text-center text-sm text-slate-500">
          Don't have an account?
          <a href="register.php" class="text-brand-600 font-semibold hover:text-brand-700 transition-colors">
            Create one →
          </a>
        </p>

        <p class="mt-10 text-xs text-slate-400 text-center font-mono">
          Smart Campus Management System · v1.0
        </p>

      </div><!-- /left form -->


      <!-- ══ RIGHT — INFO PANEL ══ -->
      <div class="hidden lg:flex lg:w-2/5 right-panel flex-col justify-between p-10 relative overflow-hidden">

        <!-- Dot pattern overlay -->
        <div class="dots-pattern absolute inset-0 opacity-100"></div>

        <!-- Glows -->
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-brand-600 opacity-10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full bg-purple-700 opacity-10 blur-3xl pointer-events-none"></div>

        <!-- Brand mark -->
        <div class="relative z-10">
          <div class="inline-flex items-center gap-2 bg-white/8 backdrop-blur border border-white/10 rounded-full px-4 py-1.5 mb-8">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-white/70 text-xs font-medium tracking-widest uppercase">Academic Portal</span>
          </div>

          <h2 class="text-white text-3xl font-bold leading-snug tracking-tight mb-3">
            Your campus,<br/>
            <span class="text-brand-300">all in one place.</span>
          </h2>
          <p class="text-white/50 text-sm leading-relaxed mb-8">
            Track attendance, marks, announcements and more — from a single unified dashboard.
          </p>

          <!-- Feature list -->
          <div class="space-y-3">
            <?php
            $features = [
              ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label'=>'Attendance tracking'],
              ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6M9 16h4', 'label'=>'Marks & results'],
              ['icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'label'=>'Real-time announcements'],
              ['icon'=>'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'label'=>'Admin controls'],
            ];
            foreach ($features as $f):
            ?>
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-white/8 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path d="<?= $f['icon'] ?>"/>
                </svg>
              </div>
              <span class="text-white/65 text-sm"><?= $f['label'] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Register card -->
        <div class="relative z-10 mt-10">
          <div class="bg-white/8 backdrop-blur border border-white/12 rounded-2xl p-6">
            <p class="text-white/60 text-sm mb-1">New to SCMS?</p>
            <p class="text-white font-semibold text-base mb-4">Create your account in minutes</p>
            <a href="register.php"
              class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-white/20 bg-white/8 hover:bg-white/15 text-white text-sm font-semibold transition-all duration-200 hover:border-white/30">
              Create Account
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M9 18l6-6-6-6"/>
              </svg>
            </a>
          </div>
        </div>

      </div><!-- /right panel -->

    </div><!-- /card wrapper -->

  </div><!-- /max-w-5xl -->

<script>
/* ── Toggle password visibility ── */
function togglePw(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon  = document.getElementById(iconId);
  const show  = input.type === 'password';
  input.type  = show ? 'text' : 'password';
  icon.innerHTML = show
    ? `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`
    : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
}

/* ── Client-side validation ── */
document.getElementById('login-form').addEventListener('submit', function(e) {
  let ok = true;
  const fields = [
    { id: 'login_id', errId: 'err-login_id' },
    { id: 'password', errId: 'err-password'  }
  ];
  fields.forEach(({ id, errId }) => {
    const inp = document.getElementById(id);
    const err = document.getElementById(errId);
    if (!inp.value.trim()) {
      inp.classList.add('border-red-400', 'bg-red-50');
      err.classList.remove('hidden');
      ok = false;
    } else {
      inp.classList.remove('border-red-400', 'bg-red-50');
      err.classList.add('hidden');
    }
  });
  if (!ok) e.preventDefault();
});
</script>
</body>
</html>
