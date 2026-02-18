<!doctype html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <?= view('partials/meta', ['title' => $title ?? t('app_name')]) ?>
    <?= view('partials/styles', ['useAuthModern' => false]) ?>

    <style>
        /* ==============================
           Unique Auth Surface — BPMS247
           ============================== */

        :root{
            --ui-bg-a:#0b1220;
            --ui-bg-b:#111b2f;
            --ui-ink:#0f172a;
            --ui-soft:#64748b;
            --ui-line:#e2e8f0;
            --ui-card:rgba(255,255,255,.90);
            --ui-card-stroke:rgba(255,255,255,.45);
            --ui-accent:#4f46e5;
            --ui-accent-2:#7c3aed;
            --ui-ok:#0ea5a0;
            --ui-shadow:0 24px 70px rgba(2, 6, 23, .30);
            --radius-xl:24px;
            --radius-lg:16px;
            --radius-md:12px;
        }

        html, body { height:100%; margin:0; }

        body.public-view.signin-page{
            font-family: Inter, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color:#fff;
            background:
                radial-gradient(1100px 480px at -5% -15%, #1d4ed8 0%, rgba(29,78,216,0) 58%),
                radial-gradient(1000px 540px at 110% 0%, #6d28d9 0%, rgba(109,40,217,0) 52%),
                linear-gradient(160deg, var(--ui-bg-a) 0%, var(--ui-bg-b) 100%);
            overflow:hidden;
        }

        body.has-signin-bg::before{
            content:"";
            position:fixed;
            inset:0;
            background:linear-gradient(150deg, rgba(7, 12, 22, .72) 0%, rgba(17, 24, 39, .58) 60%, rgba(30, 41, 59, .50) 100%);
            z-index:0;
            pointer-events:none;
        }

        .ambient-orb{
            position:fixed;
            border-radius:999px;
            filter: blur(50px);
            opacity:.40;
            pointer-events:none;
            z-index:0;
        }
        .ambient-orb.one{ width:280px;height:280px; left:-80px;bottom:8%; background:#22d3ee; }
        .ambient-orb.two{ width:360px;height:360px; right:-120px;top:10%; background:#a78bfa; }

        .signin-root{
            position:relative;
            z-index:1;
            height:100%;
            display:grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: clamp(18px, 3vw, 36px);
            align-items:center;
            padding: clamp(14px, 2.2vw, 32px);
        }

        .signin-story{ padding: clamp(8px, 1.4vw, 18px); max-width:760px; }

        .story-chip{
            display:inline-flex;
            align-items:center;
            gap:8px;
            border:1px solid rgba(255,255,255,.25);
            background:rgba(255,255,255,.10);
            color:#e2e8f0;
            border-radius:999px;
            padding:7px 12px;
            font-size:.78rem;
            letter-spacing:.02em;
            backdrop-filter: blur(5px);
        }
        .story-chip .dot{
            width:8px;height:8px;border-radius:999px;background:#34d399;
            box-shadow:0 0 0 5px rgba(52,211,153,.18);
        }

        .signin-story h1{
            margin:16px 0 10px;
            font-size: clamp(1.6rem, 3.1vw, 3rem);
            line-height:1.08;
            letter-spacing:-.02em;
            color:#f8fafc;
            max-width:14ch;
        }

        .signin-story p{
            margin:0;
            max-width:58ch;
            color:rgba(226,232,240,.92);
            line-height:1.75;
            font-size:1.02rem;
        }

        .story-grid{
            margin-top:20px;
            display:grid;
            grid-template-columns:repeat(3, minmax(120px, 1fr));
            gap:12px;
            max-width:600px;
        }

        .story-stat{
            border:1px solid rgba(255,255,255,.18);
            background:rgba(255,255,255,.08);
            border-radius:14px;
            padding:12px;
            backdrop-filter: blur(4px);
        }
        .story-stat .k{ display:block; font-weight:700; font-size:1.05rem; color:#fff; margin-bottom:2px; }
        .story-stat .v{ color:#cbd5e1; font-size:.82rem; }

        .signin-panel-wrap{ width:min(100%, 560px); justify-self:center; }

        .signin-panel{
            background:var(--ui-card);
            border:1px solid var(--ui-card-stroke);
            border-radius:var(--radius-xl);
            box-shadow:var(--ui-shadow);
            padding: clamp(16px, 2vw, 28px);
            color:var(--ui-ink);
            position:relative;
            overflow:hidden;
        }

        .signin-panel::after{
            content:"";
            position:absolute;
            top:-40px; right:-40px;
            width:150px;height:150px;border-radius:999px;
            background: radial-gradient(circle at 30% 30%, rgba(79,70,229,.24), rgba(124,58,237,.05) 70%);
            pointer-events:none;
        }

        .panel-brand{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:8px;
        }

        .panel-logo{
            width:34px;height:34px;border-radius:10px;
            background:linear-gradient(135deg,var(--ui-accent),var(--ui-accent-2));
            box-shadow:0 8px 20px rgba(79,70,229,.35);
            position:relative;
        }
        .panel-logo::before{
            content:"";
            position:absolute;
            inset:8px;
            border-radius:6px;
            border:2px solid rgba(255,255,255,.85);
        }

        .panel-title{ margin:0; font-size:1.1rem; letter-spacing:-.01em; color:#111827; font-weight:700; }
        .panel-subtitle{ margin:0; color:var(--ui-soft); font-size:.9rem; }

        .panel-divider{
            height:1px;
            background:linear-gradient(90deg, rgba(148,163,184,.45), rgba(148,163,184,.15), transparent);
            margin:12px 0 16px;
        }

        .signin-panel label{ color:#334155; font-weight:600; margin-bottom:6px; }

        .signin-panel input[type="text"],
        .signin-panel input[type="email"],
        .signin-panel input[type="password"],
        .signin-panel textarea,
        .signin-panel select{
            width:100%;
            border:1px solid #cbd5e1;
            border-radius: var(--radius-md);
            min-height:44px;
            padding:10px 12px;
            box-shadow:none;
            outline:none;
            transition:border-color .2s ease, box-shadow .2s ease, transform .05s ease;
            background:#fff;
        }

        .signin-panel input:focus,
        .signin-panel textarea:focus,
        .signin-panel select:focus{
            border-color:#93c5fd;
            box-shadow:0 0 0 4px rgba(59,130,246,.14);
        }

        .signin-panel .btn{
            border-radius:var(--radius-md);
            min-height:44px;
            font-weight:600;
            letter-spacing:.01em;
        }

        .signin-panel .btn-primary{
            border:none;
            color:#fff;
            background:linear-gradient(180deg, var(--ui-accent) 0%, #4338ca 100%);
            box-shadow:0 10px 18px rgba(67,56,202,.22);
        }

        .signin-panel .btn-primary:hover{
            transform:translateY(-1px);
            filter:brightness(1.03);
        }

        .signin-panel a{
            color:#3730a3;
            text-underline-offset:2px;
        }

        .panel-foot{
            margin-top:14px;
            padding-top:12px;
            border-top:1px dashed #cbd5e1;
            color:#94a3b8;
            font-size:.78rem;
            text-align:center;
        }

        .scrollable-page{ height:100vh; overflow:auto; }

        @media (max-width: 1060px){
            body.public-view.signin-page{ overflow:auto; }
            .signin-root{
                height:auto;
                min-height:100vh;
                grid-template-columns: 1fr;
                align-content:start;
                padding-top:18px;
                padding-bottom:18px;
            }
            .signin-story{ text-align:center; margin:0 auto; }
            .signin-story h1, .signin-story p{ margin-left:auto;margin-right:auto; }
            .story-grid{ margin-left:auto;margin-right:auto; }
        }

        @media (max-width: 640px){
            .story-grid{ grid-template-columns:1fr; max-width:340px; }
            .signin-panel{ border-radius:18px; padding:14px; }
            .panel-title{font-size:1rem;}
            .panel-subtitle{font-size:.85rem;}
        }
    </style>
</head>

<?php
    // Background image support (optional)
    $useBg = (!empty($background_url));
    $bodyClasses = 'public-view signin-page' . ($useBg ? ' has-signin-bg' : '');
?>
<body class="<?= esc($bodyClasses) ?>">

<?php if ($useBg): ?>
    <style>
        html, body {
            background-image: url('<?= esc($background_url) ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
<?php endif; ?>

<span class="ambient-orb one"></span>
<span class="ambient-orb two"></span>

<div class="scrollable-page">
    <div class="signin-root">

        <!-- Left narrative panel -->
        <section class="signin-story">
            <span class="story-chip">
                <span class="dot"></span>
                <span><?= esc(t('welcome_chip', 'Welcome')) ?></span>
            </span>

            <h1><?= esc(t('signin_hero_title', 'Secure workspace access, redesigned for speed.')) ?></h1>

            <p>
                <?= esc(t('signin_hero_subtitle', 'Sign in to continue your operations with a cleaner, faster interface.')) ?>
            </p>

            <div class="story-grid">
                <div class="story-stat">
                    <span class="k">24/7</span>
                    <span class="v"><?= esc(t('signin_metric_1', 'Protected access')) ?></span>
                </div>
                <div class="story-stat">
                    <span class="k">1 Hub</span>
                    <span class="v"><?= esc(t('signin_metric_2', 'All business flows')) ?></span>
                </div>
                <div class="story-stat">
                    <span class="k">Realtime</span>
                    <span class="v"><?= esc(t('signin_metric_3', 'Team collaboration')) ?></span>
                </div>
            </div>
        </section>

        <!-- Right form shell -->
        <section class="signin-panel-wrap">
            <div class="signin-panel">
                <div class="panel-brand">
                    <span class="panel-logo" aria-hidden="true"></span>
                    <div>
                        <h2 class="panel-title"><?= esc(t('app_name')) ?></h2>
                        <p class="panel-subtitle"><?= esc(t('auth_portal', 'Authentication Portal')) ?></p>
                    </div>
                </div>

                <div class="panel-divider"></div>

                <!-- Optional logo box -->
                <?php if (!empty($logoUrl)): ?>
                    <div class="text-center mb-3">
                        <img class="img-fluid" style="max-height:80px;object-fit:contain"
                             src="<?= esc($logoUrl) ?>" alt="Logo">
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>

                <div class="panel-foot">
                    © <?= date('Y') ?> <?= esc(t('app_name')) ?> · <?= esc(t('all_rights_reserved', 'All rights reserved')) ?>
                </div>
            </div>
        </section>

    </div>
</div>

<?= view('partials/scripts', ['pageScript' => $pageScript ?? null]) ?>
</body>
</html>
