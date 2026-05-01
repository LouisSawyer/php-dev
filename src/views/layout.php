<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'PHP Dev') ?> - PHP Dev</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
        }

        /* Nav */
        .site-nav {
            background: #161b26;
            border-bottom: 1px solid #21273a;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-inner {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 50px;
            gap: 16px;
        }
        .nav-brand a { font-size: 0.95rem; font-weight: 600; color: #f0f3f6; text-decoration: none; letter-spacing: 0.02em; }
        .nav-brand a:hover { color: #58a6ff; }
        .nav-links { display: flex; gap: 2px; flex: 1; padding-left: 16px; }
        .nav-links a { color: #8b9fc6; text-decoration: none; font-size: 0.875rem; padding: 6px 12px; border-radius: 6px; transition: background 0.15s, color 0.15s; }
        .nav-links a:hover { background: #1c2333; color: #e1e4e8; }
        .nav-links a.nav-active { background: #1c2333; color: #f0f3f6; font-weight: 500; }
        .nav-user { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .nav-username { font-size: 0.82rem; color: #6b7b9e; }
        .nav-logout { color: #f48771; text-decoration: none; font-size: 0.82rem; padding: 5px 12px; border: 1px solid #3d1f28; border-radius: 6px; transition: background 0.15s; }
        .nav-logout:hover { background: #1a1115; }

        /* Layout */
        .container { max-width: 860px; margin: 0 auto; padding: 32px 20px 40px; }
        .container-wide { max-width: 1100px; margin: 0 auto; padding: 32px 20px 40px; }

        /* Cards */
        .card { background: #161b26; border: 1px solid #21273a; border-radius: 12px; padding: 24px; margin-bottom: 16px; }
        .card h2 { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7b9e; margin-bottom: 16px; }

        /* Headings */
        h1 { font-size: 1.5rem; font-weight: 600; color: #f0f3f6; margin-bottom: 24px; }

        /* Alerts */
        .alert { border-radius: 8px; padding: 12px 16px; font-size: 0.875rem; margin-bottom: 16px; }
        .alert-success { background: #0d2818; border: 1px solid #1b4332; color: #2ea043; }
        .alert-error { background: #1a1115; border: 1px solid #3d1f28; color: #f48771; }

        /* Buttons */
        .btn { padding: 7px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; font-family: inherit; cursor: pointer; border: 1px solid; text-decoration: none; display: inline-block; }
        .btn-primary { background: #1c2d40; border-color: #2d4a6a; color: #58a6ff; }
        .btn-primary:hover { background: #213550; }
        .btn-ghost { background: transparent; border-color: #2d3548; color: #8b9fc6; }
        .btn-ghost:hover { background: #1c2333; }
        .btn-submit { padding: 9px 20px; background: #238636; border: 1px solid #2ea043; border-radius: 8px; color: #fff; font-size: 0.875rem; font-weight: 600; font-family: inherit; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background: #2ea043; }
        .btn-edit { padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; font-family: inherit; cursor: pointer; text-decoration: none; border: 1px solid; background: #1c2d40; border-color: #2d4a6a; color: #58a6ff; display: inline-block; }
        .btn-edit:hover { background: #213550; }
        .btn-delete { padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; font-family: inherit; cursor: pointer; text-decoration: none; border: 1px solid; background: transparent; border-color: #3d1f28; color: #f48771; display: inline-block; }
        .btn-delete:hover { background: #1a1115; }
        .btn-cancel { padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; font-family: inherit; cursor: pointer; text-decoration: none; border: 1px solid #2d3548; color: #8b9fc6; background: transparent; display: inline-block; }
        .btn-cancel:hover { background: #1c2333; }

        /* Forms */
        .form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #6b7b9e; }
        input[type="text"], input[type="email"], input[type="password"] {
            padding: 9px 13px; background: #0f1117; border: 1px solid #2d3548;
            border-radius: 8px; color: #e1e4e8; font-size: 0.9rem; font-family: inherit;
            outline: none; transition: border-color 0.2s;
        }
        input:focus { border-color: #58a6ff; }
        .hint { font-size: 0.75rem; color: #4a5568; }
        .form-actions { display: flex; gap: 10px; margin-top: 4px; }
        select { padding: 7px 10px; background: #0f1117; border: 1px solid #2d3548; border-radius: 8px; color: #e1e4e8; font-size: 0.875rem; font-family: inherit; outline: none; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead th { text-align: left; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: #6b7b9e; padding: 0 12px 10px 0; border-bottom: 1px solid #21273a; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #1a2030; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #1c2333; }
        tbody td { padding: 10px 12px 10px 0; color: #c9d1d9; vertical-align: middle; }

        /* Misc */
        .mono { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 0.82rem; }
        .badge { display: inline-block; background: #1c2333; border: 1px solid #2d3548; color: #8b9fc6; font-family: 'SF Mono', 'Fira Code', monospace; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; margin: 2px 3px 2px 0; }
        .chip { background: #1c2333; border: 1px solid #2d3548; border-radius: 10px; padding: 3px 10px; font-family: 'SF Mono', 'Fira Code', monospace; font-size: 0.78rem; color: #8b9fc6; }
        .meta-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 14px; }
        .info-grid { display: grid; grid-template-columns: 160px 1fr; gap: 10px 16px; }
        .info-key { font-size: 0.85rem; color: #6b7b9e; }
        .info-value { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 0.85rem; color: #c9d1d9; }
        .status-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .status-dot.green { background: #2ea043; box-shadow: 0 0 8px rgba(46,160,67,0.4); }
        .status-dot.red { background: #da3633; box-shadow: 0 0 8px rgba(218,54,51,0.4); }
        .status-row { display: flex; align-items: center; gap: 10px; }
        footer { text-align: center; margin-top: 24px; }
        footer a { color: #58a6ff; text-decoration: none; font-size: 0.85rem; margin: 0 10px; }
        footer a:hover { text-decoration: underline; }

        @media (max-width: 600px) {
            .nav-username { display: none; }
            .nav-links a { padding: 6px 8px; font-size: 0.8rem; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: 1; }
            .info-grid { grid-template-columns: 1fr; }
        }

        <?php if (!empty($pageStyles)) echo $pageStyles; ?>
    </style>
</head>
<body>
    <?php if (empty($hideNav)): ?>
        <?php include __DIR__ . '/partials/nav.php'; ?>
    <?php endif; ?>
    <?= $content ?>
</body>
</html>
