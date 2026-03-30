<?php
session_start(); // Required on every PHP page
// ── If already logged in, send to dashboard ──
if (isset($_SESSION['role'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin.php' : 'student.php'));
    exit;
}

$error   = '';
$success = '';

// ── Handle form submission ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect and sanitise inputs
    $full_name        = trim($_POST['full_name']        ?? '');
    $class            = trim($_POST['class']            ?? '');
    $email            = trim($_POST['email']            ?? '');
    $password         = $_POST['password']              ?? '';
    $confirm_password = $_POST['confirm_password']      ?? '';
    // email_verified is set to '1' by the JS OTP flow (demo: always passes)
    $email_verified   = $_POST['email_verified']        ?? '0';

    // ── Server-side validation ──
    if (empty($full_name) || empty($class) || empty($email) || empty($password)) {
        $error = 'All fields are required.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';

    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';

    } else {

        // ── Check if email already registered (session storage) ──
        $students = $_SESSION['students'] ?? [];
        $already  = false;

        foreach ($students as $s) {
            if ($s['email'] === $email) {
                $already = true;
                break;
            }
        }

        if ($already) {
            $error = 'This email is already registered. Please log in.';

        } else {
            // ── Store new student in session ──
            // Use email prefix as username (swap with a username field later)
            $username = strtolower(explode('@', $email)[0]);

            // NOTE: When upgrading to DB, hash password with password_hash()
            $students[] = [
                'full_name' => $full_name,
                'class'     => $class,
                'email'     => $email,
                'username'  => $username,
                'password'  => $password,   // plain-text for session demo
            ];

            $_SESSION['students'] = $students; // Save back to session

            // Redirect to login with success message
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SCMS — Create Account</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    body {
  font-family: "DM Sans", sans-serif;
}
.bg-mesh {
  background-color: #0f172a;
  background-image:
    radial-gradient(
      ellipse 80% 50% at 20% 40%,
      rgba(99, 102, 241, 0.18) 0%,
      transparent 60%
    ),
    radial-gradient(
      ellipse 60% 60% at 80% 70%,
      rgba(139, 92, 246, 0.12) 0%,
      transparent 55%
    );
}
.card-shadow {
  box-shadow:
    0 25px 60px rgba(0, 0, 0, 0.35),
    0 4px 16px rgba(0, 0, 0, 0.2);
}
.input-base {
  width: 100%;
  padding: 0.65rem 1rem 0.65rem 2.6rem;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #1e293b;
  font-size: 0.875rem;
  transition:
    border-color 0.2s,
    box-shadow 0.2s;
  font-family: "DM Sans", sans-serif;
}
.input-base::placeholder {
  color: #94a3b8;
}
.input-base:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  background: #fff;
}
.input-base.error {
  border-color: #f87171;
  background: #fff5f5;
}
.input-base.success {
  border-color: #4ade80;
}
select.input-base {
  cursor: pointer;
  -webkit-appearance: none;
  appearance: none;
}
.input-no-icon {
  padding-left: 1rem;
}
.btn-primary {
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
  transition: all 0.2s;
}
.btn-primary:hover {
  background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
}
.btn-primary:active {
  transform: translateY(0);
}
.right-panel {
  background: linear-gradient(160deg, #1e1b4b 0%, #0f172a 50%, #1a0533 100%);
}
.dots-pattern {
  background-image: radial-gradient(
    circle,
    rgba(255, 255, 255, 0.06) 1px,
    transparent 1px
  );
  background-size: 24px 24px;
}
#strength-fill {
  height: 100%;
  border-radius: 99px;
  transition:
    width 0.3s,
    background-color 0.3s;
}
.form-scroll::-webkit-scrollbar {
  width: 4px;
}
.form-scroll::-webkit-scrollbar-track {
  background: transparent;
}
.form-scroll::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 99px;
}

  </style>
  <script> 
    tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        sans: ["DM Sans", "sans-serif"],
        mono: ["DM Mono", "monospace"],
      },
      colors: {
        brand: {
          50: "#eef2ff",
          100: "#e0e7ff",
          200: "#c7d2fe",
          300: "#a5b4fc",
          400: "#818cf8",
          500: "#6366f1",
          600: "#4f46e5",
          700: "#4338ca",
          800: "#3730a3",
          900: "#312e81",
        },
      },
      animation: {
        "fade-up": "fadeUp 0.5s ease both",
        "slide-down": "slideDown 0.3s ease both",
      },
      keyframes: {
        fadeUp: {
          "0%": { opacity: "0", transform: "translateY(20px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        slideDown: {
          "0%": { opacity: "0", transform: "translateY(-8px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
      },
    },
  },
};

  </script>
</head>

<body class="bg-mesh min-h-screen flex items-center justify-center p-4 py-8">

  <div class="w-full max-w-5xl animate-fade-up">

    <div class="flex rounded-3xl overflow-hidden card-shadow">

      <!-- ══ LEFT — REGISTRATION FORM ══ -->
      <div class="w-full lg:w-3/5 bg-white px-8 sm:px-12 py-10 form-scroll overflow-y-auto max-h-screen">

        <!-- Logo -->
        <div class="flex items-center gap-3 mb-8">
          <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
              <path d="M12 14l9-5-9-5-9 5 9 5z"/>
              <path d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z"/>
            </svg>
          </div>
          <span class="font-bold text-slate-800 text-lg tracking-tight">SCMS</span>
        </div>

        <!-- Heading -->
        <div class="mb-7">
          <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-1.5">Create account</h1>
          <p class="text-slate-500 text-sm">Register to access the Smart Campus portal.</p>
        </div>

        <!-- ── Server-side error banner ── -->
        <?php if (!empty($error)): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-6 text-sm">
          <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- ── JS alert banner ── -->
        <div id="js-alert" class="hidden items-start gap-3 rounded-xl px-4 py-3 mb-6 text-sm border">
          <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" id="js-alert-icon">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <span id="js-alert-msg"></span>
        </div>

        <!-- Registration Form -->
        <form id="reg-form" method="POST" action="register.php" novalidate>

          <!-- Hidden field: email verified flag (set by JS OTP) -->
          <!-- In demo mode JS sets this to '1' automatically -->
          <input type="hidden" name="email_verified" id="email_verified" value="0"/>

          <!-- Full Name -->
          <div class="mb-4">
            <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
              </span>
              <input type="text" id="full_name" name="full_name"
                value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                placeholder="Enter your full name"
                class="input-base"/>
            </div>
            <p id="err-full_name" class="hidden mt-1 text-xs text-red-600 font-medium">Full name is required.</p>
          </div>

          <!-- Class / Department -->
          <div class="mb-4">
            <label for="class" class="block text-sm font-semibold text-slate-700 mb-1.5">Class / Department</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z"/>
                </svg>
              </span>
              <select id="class" name="class" class="input-base">
                <option value="">Select your class</option>
                <?php
                $classes = ['CS-A','CS-B','IT-A','IT-B','EC-A','EC-B','ME-A','ME-B','CE-A','CE-B'];
                $selected = $_POST['class'] ?? '';
                foreach ($classes as $c):
                ?>
                <option value="<?= $c ?>" <?= $selected === $c ? 'selected' : '' ?>><?= $c ?></option>
                <?php endforeach; ?>
              </select>
              <!-- Dropdown chevron -->
              <span class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M19 9l-7 7-7-7"/>
                </svg>
              </span>
            </div>
            <p id="err-class" class="hidden mt-1 text-xs text-red-600 font-medium">Please select your class.</p>
          </div>

          <!-- Email + OTP section -->
          <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
            <div class="flex gap-2">
              <div class="relative flex-1">
                <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </span>
                <input type="email" id="email" name="email"
                  value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                  placeholder="you@example.com"
                  class="input-base"/>
              </div>
              <!-- Send OTP button -->
              <button type="button" id="btn-send-otp" onclick="sendOTP()"
                class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold border border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100 transition-colors">
                Send OTP
              </button>
            </div>
            <p id="err-email" class="hidden mt-1 text-xs text-red-600 font-medium">A valid email is required.</p>
          </div>

          <!-- OTP Input (hidden until OTP is sent) -->
          <div id="otp-section" class="hidden mb-4">
            <label for="otp_code" class="block text-sm font-semibold text-slate-700 mb-1.5">
              Enter OTP
              <span class="font-normal text-slate-400 text-xs ml-1">(sent to your email)</span>
            </label>
            <div class="flex gap-2 items-center">
              <input type="text" id="otp_code" maxlength="6" placeholder="6-digit code"
                class="input-base input-no-icon flex-1" style="padding-left:1rem;letter-spacing:0.2em;font-size:1.1rem;"/>
              <button type="button" id="btn-verify-otp" onclick="verifyOTP()"
                class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold border border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100 transition-colors">
                Verify
              </button>
            </div>
            <!-- Resend -->
            <div id="resend-wrap" class="mt-2 text-xs text-slate-500">
              Didn't receive it?
              <button type="button" id="btn-resend" onclick="sendOTP(true)"
                class="text-brand-600 font-semibold hover:underline disabled:opacity-40 disabled:cursor-not-allowed">
                Resend<span id="resend-timer"></span>
              </button>
            </div>
            <!-- OTP error / success -->
            <div id="verify-err" class="hidden mt-1 text-xs text-red-600 font-medium">
              <span id="verify-err-msg"></span>
            </div>
            <div id="verify-ok" class="hidden mt-1 text-xs text-emerald-600 font-semibold">
              ✓ Email verified successfully!
            </div>
          </div>

          <!-- Password -->
          <div class="mb-4">
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
              </span>
              <input type="password" id="password" name="password"
                placeholder="Min. 8 characters"
                oninput="checkStrength(this.value)"
                class="input-base pr-12"/>
              <button type="button" onclick="togglePw('password','eye-pw')"
                class="absolute inset-y-0 right-3 flex items-center px-1 text-slate-400 hover:text-slate-600">
                <svg id="eye-pw" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <!-- Strength bar -->
            <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div id="strength-fill" style="width:0%;"></div>
            </div>
            <p id="strength-label" class="mt-1 text-xs" style="color:#94a3b8">Enter a password</p>
            <p id="err-password" class="hidden mt-1 text-xs text-red-600 font-medium">Min. 8 characters required.</p>
          </div>

          <!-- Confirm Password -->
          <div class="mb-5">
            <label for="confirm_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
              </span>
              <input type="password" id="confirm_password" name="confirm_password"
                placeholder="Re-enter password"
                oninput="checkMatch()"
                class="input-base pr-12"/>
              <button type="button" onclick="togglePw('confirm_password','eye-cpw')"
                class="absolute inset-y-0 right-3 flex items-center px-1 text-slate-400 hover:text-slate-600">
                <svg id="eye-cpw" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            <p id="err-confirm_password" class="hidden mt-1 text-xs text-red-600 font-medium">Passwords do not match.</p>
          </div>

          <!-- Terms -->
          <div class="mb-6 flex items-start gap-3">
            <input type="checkbox" id="terms" name="terms"
              class="mt-0.5 w-4 h-4 rounded border-slate-300 text-brand-600 cursor-pointer shrink-0"/>
            <label for="terms" class="text-sm text-slate-500 cursor-pointer leading-relaxed">
              I agree to the
              <a href="#" class="text-brand-600 font-semibold hover:text-brand-700">Terms of Service</a>
              and
              <a href="#" class="text-brand-600 font-semibold hover:text-brand-700">Privacy Policy</a>
            </label>
          </div>

          <!-- Submit -->
          <button type="submit" id="reg-btn"
            class="btn-primary w-full py-3 rounded-xl text-white font-semibold text-sm tracking-wide">
            Create Account
          </button>

        </form>

        <!-- Sign in link -->
        <p class="mt-6 text-center text-sm text-slate-500">
          Already have an account?
          <a href="login.php" class="text-brand-600 font-semibold hover:text-brand-700 transition-colors">
            Sign in →
          </a>
        </p>

        <p class="mt-6 text-xs text-slate-400 text-center font-mono">
          Smart Campus Management System · v1.0
        </p>

      </div><!-- /left form -->


      <!-- ══ RIGHT — INFO PANEL ══ -->
      <div class="hidden lg:flex lg:w-2/5 right-panel flex-col justify-between p-10 relative overflow-hidden">

        <div class="dots-pattern absolute inset-0 opacity-100"></div>
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-brand-600 opacity-10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full bg-purple-700 opacity-10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
          <div class="inline-flex items-center gap-2 bg-white/8 backdrop-blur border border-white/10 rounded-full px-4 py-1.5 mb-8">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-white/70 text-xs font-medium tracking-widest uppercase">Student Portal</span>
          </div>

          <h2 class="text-white text-3xl font-bold leading-snug tracking-tight mb-3">
            Join SCMS<br/>
            <span class="text-brand-300">in minutes.</span>
          </h2>
          <p class="text-white/50 text-sm leading-relaxed mb-8">
            Register once and get instant access to your academic dashboard, marks, attendance and more.
          </p>

          <div class="space-y-3">
            <?php
            $perks = [
              ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0', 'label'=>'Instant dashboard access'],
              ['icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'label'=>'Real-time announcements'],
              ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label'=>'Track attendance & marks'],
              ['icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'label'=>'Secure & private'],
            ];
            foreach ($perks as $p):
            ?>
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-white/8 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path d="<?= $p['icon'] ?>"/>
                </svg>
              </div>
              <span class="text-white/65 text-sm"><?= $p['label'] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="relative z-10 mt-10">
          <div class="bg-white/8 backdrop-blur border border-white/12 rounded-2xl p-6">
            <p class="text-white/60 text-sm mb-1">Already registered?</p>
            <p class="text-white font-semibold text-base mb-4">Sign in to your account</p>
            <a href="login.php"
              class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-white/20 bg-white/8 hover:bg-white/15 text-white text-sm font-semibold transition-all duration-200 hover:border-white/30">
              Sign In
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M9 18l6-6-6-6"/>
              </svg>
            </a>
          </div>
        </div>

      </div><!-- /right panel -->

    </div><!-- /card -->

  </div><!-- /max-w-5xl -->

<script>
let resendTimer = null;
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

/* ── Send OTP (demo: skips real email, auto-verifies) ── */
function sendOTP(isResend = false) {
  const email = document.getElementById('email').value.trim();
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    showField('email', 'Please enter a valid email first.', true);
    return;
  }
  showField('email', '', false);

  // Show the OTP section
  document.getElementById('otp-section').classList.remove('hidden');

  const btn = isResend
    ? document.getElementById('btn-resend')
    : document.getElementById('btn-send-otp');

  if (!isResend) {
    btn.textContent = 'Sent ✓';
    btn.disabled    = true;
    btn.classList.add('opacity-60');
  }

  startResendCountdown(30, document.getElementById('btn-resend'));

  // ── DEMO MODE: auto-fill OTP and verify ──
  // In production, remove this block and connect verify_otp.php
  setTimeout(() => {
    document.getElementById('otp_code').value = '123456';
    handleOTPVerified({ success: true }, document.getElementById('btn-verify-otp'));
  }, 800);
}

function startResendCountdown(sec, btn) {
  clearInterval(resendTimer);
  btn.disabled = true;
  const timerEl = document.getElementById('resend-timer');
  let remaining = sec;
  timerEl.textContent = ` (${remaining}s)`;
  resendTimer = setInterval(() => {
    remaining--;
    timerEl.textContent = remaining > 0 ? ` (${remaining}s)` : '';
    if (remaining <= 0) {
      clearInterval(resendTimer);
      btn.disabled = false;
    }
  }, 1000);
}

/* ── Verify OTP ── */
function verifyOTP() {
  const otp   = document.getElementById('otp_code').value.trim();
  const email = document.getElementById('email').value.trim();

  if (otp.length !== 6) {
    document.getElementById('verify-err-msg').textContent = 'Please enter the 6-digit code.';
    document.getElementById('verify-err').classList.remove('hidden');
    return;
  }

  const btn = document.getElementById('btn-verify-otp');
  btn.disabled    = true;
  btn.textContent = 'Verifying…';

  // Demo: always succeeds. Replace with fetch('verify_otp.php', ...) for real OTP
  setTimeout(() => handleOTPVerified({ success: true }, btn), 500);
}

function handleOTPVerified(data, btn) {
  if (data.success) {
    document.getElementById('verify-ok').classList.remove('hidden');
    document.getElementById('verify-err').classList.add('hidden');
    document.getElementById('email_verified').value = '1'; // Tells PHP: verified
    document.getElementById('email').readOnly    = true;
    document.getElementById('otp_code').readOnly = true;
    document.getElementById('email').classList.add('success');
    btn.disabled    = true;
    btn.textContent = 'Verified ✓';
    btn.classList.add('bg-emerald-50','border-emerald-300','text-emerald-700');
    btn.classList.remove('bg-brand-50','border-brand-200','text-brand-700');
    clearInterval(resendTimer);
    document.getElementById('resend-wrap').classList.add('hidden');
  } else {
    btn.disabled    = false;
    btn.textContent = 'Verify';
    document.getElementById('verify-err-msg').textContent = data.message || 'Invalid OTP. Try again.';
    document.getElementById('verify-err').classList.remove('hidden');
  }
}

/* ── Password strength bar ── */
function checkStrength(val) {
  const fill  = document.getElementById('strength-fill');
  const label = document.getElementById('strength-label');
  let score = 0;
  if (val.length >= 8)           score++;
  if (/[A-Z]/.test(val))         score++;
  if (/[0-9]/.test(val))         score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { w:'0%',   c:'',        t:'Enter a password', tc:'#94a3b8' },
    { w:'25%',  c:'#f87171', t:'Weak',             tc:'#f87171' },
    { w:'50%',  c:'#fb923c', t:'Fair',             tc:'#fb923c' },
    { w:'75%',  c:'#6366f1', t:'Good',             tc:'#6366f1' },
    { w:'100%', c:'#4ade80', t:'Strong ✓',         tc:'#16a34a' },
  ];
  const l = val.length === 0 ? levels[0] : levels[Math.min(score, 4)];
  fill.style.width           = l.w;
  fill.style.backgroundColor = l.c;
  label.textContent          = l.t;
  label.style.color          = l.tc;
}

/* ── Confirm password match ── */
function checkMatch() {
  const pw  = document.getElementById('password').value;
  const cpw = document.getElementById('confirm_password');
  const err = document.getElementById('err-confirm_password');
  if (!cpw.value) return;
  if (cpw.value !== pw) {
    cpw.classList.add('error');
    err.classList.remove('hidden');
  } else {
    cpw.classList.remove('error');
    cpw.classList.add('success');
    err.classList.add('hidden');
  }
}

/* ── Alert banner helper ── */
function showAlert(msg, type) {
  const el  = document.getElementById('js-alert');
  const txt = document.getElementById('js-alert-msg');
  txt.textContent = msg;
  el.classList.remove('hidden','flex','bg-red-50','border-red-200','text-red-700','bg-green-50','border-green-200','text-green-700');
  if (type === 'success') {
    el.classList.add('flex','bg-green-50','border-green-200','text-green-700');
  } else {
    el.classList.add('flex','bg-red-50','border-red-200','text-red-700');
  }
  el.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

function showField(id, msg, show) {
  const inp = document.getElementById(id);
  const err = document.getElementById('err-' + id);
  if (show) {
    inp.classList.add('error');
    if (err) { err.textContent = msg; err.classList.remove('hidden'); }
  } else {
    inp.classList.remove('error');
    if (err) err.classList.add('hidden');
  }
}

/* ── Form submit validation ── */
document.getElementById('reg-form').addEventListener('submit', function(e) {
  let ok = true;

  const name = document.getElementById('full_name');
  showField('full_name', 'Full name is required.', !name.value.trim());
  if (!name.value.trim()) ok = false;

  const cls = document.getElementById('class');
  showField('class', 'Please select your class.', !cls.value);
  if (!cls.value) ok = false;

  if (document.getElementById('email_verified').value !== '1') {
    showAlert('Please verify your email before submitting.', 'error');
    ok = false;
  }

  const pw = document.getElementById('password');
  showField('password', 'Min. 8 characters required.', pw.value.length < 8);
  if (pw.value.length < 8) ok = false;

  const cpw = document.getElementById('confirm_password');
  if (cpw.value !== pw.value) {
    cpw.classList.add('error');
    document.getElementById('err-confirm_password').classList.remove('hidden');
    ok = false;
  }

  if (!document.getElementById('terms').checked) {
    showAlert('You must agree to the Terms of Service to continue.', 'error');
    ok = false;
  }

  if (!ok) { e.preventDefault(); return; }

  const btn = document.getElementById('reg-btn');
  btn.disabled     = true;
  btn.textContent  = 'Creating account…';
  btn.style.opacity = '0.8';
});
</script>
</body>
</html>
