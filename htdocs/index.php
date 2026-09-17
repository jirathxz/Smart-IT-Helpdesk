<?php
/**
 * Smart IT Helpdesk - Development Environment Dashboard
 */

if (isset($_GET['phpinfo'])) {
    phpinfo();
    exit;
}

// Check database connection
$dbHost = '127.0.0.1';
$dbPort = 3306;
$dbUser = 'root';
$dbPass = '';
$dbStatus = false;
$dbError = '';
$dbVersion = '';

try {
    $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, "", $dbPort);
    if ($mysqli->connect_error) {
        $dbStatus = false;
        $dbError = $mysqli->connect_error;
    } else {
        $dbStatus = true;
        $dbVersion = $mysqli->server_info;
        $mysqli->close();
    }
} catch (Exception $e) {
    $dbStatus = false;
    $dbError = $e->getMessage();
}

$requiredExtensions = [
    'pdo_mysql' => 'PDO MySQL Driver',
    'mysqli'    => 'MySQLi Driver',
    'openssl'   => 'OpenSSL Encryption',
    'curl'      => 'cURL Client',
    'mbstring'  => 'Multibyte String',
    'session'   => 'Session Support',
    'json'      => 'JSON Parser',
    'zip'       => 'Zip Archive'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart IT Helpdesk - Server Environment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0b0f19;
            --surface: #131b2e;
            --surface-hover: #1a253f;
            --border: #1e293b;
            --primary: #3b82f6;
            --primary-glow: rgba(59, 130, 246, 0.15);
            --success: #10b981;
            --success-glow: rgba(16, 185, 129, 0.15);
            --danger: #ef4444;
            --danger-glow: rgba(239, 68, 68, 0.15);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --card-radius: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 2.5rem 1.5rem;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.08) 0px, transparent 50%);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .badge-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
            margin-bottom: 0.6rem;
        }

        .badge-brand span {
            width: 7px;
            height: 7px;
            background-color: #3b82f6;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px #3b82f6;
        }

        h1 {
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #ffffff 40%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            margin-top: 0.25rem;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.25rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--surface);
            color: var(--text-main);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background: var(--surface-hover);
            border-color: #334155;
            transform: translateY(-1px);
        }

        /* Status Grid */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--card-radius);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: #334155;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .indicator.online {
            background: var(--success-glow);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .indicator.offline {
            background: var(--danger-glow);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .indicator span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 8px currentColor;
        }

        .card-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
            color: #fff;
        }

        .card-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        /* Detail Sections */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 840px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--card-radius);
            padding: 1.75rem;
        }

        .panel h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .ext-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .ext-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 0.85rem;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .ext-item code {
            font-family: 'JetBrains Mono', monospace;
            color: #93c5fd;
        }

        .check-icon {
            color: var(--success);
            font-weight: bold;
        }

        .code-snippet {
            background: #090d16;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            color: #cbd5e1;
            line-height: 1.6;
            overflow-x: auto;
        }

        .code-snippet .keyword { color: #f472b6; }
        .code-snippet .variable { color: #60a5fa; }
        .code-snippet .string { color: #34d399; }
        .code-snippet .comment { color: #64748b; }

        footer {
            margin-top: 3rem;
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <div class="badge-brand">
                    <span></span> Local Development Environment
                </div>
                <h1>Smart IT Helpdesk</h1>
                <p class="subtitle">Apache, PHP, MariaDB & phpMyAdmin are active and ready.</p>
            </div>
            <div class="header-actions">
                <a href="/phpmyadmin/" target="_blank" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 4h16M8 4v4m8-4v4"/></svg>
                    Open phpMyAdmin
                </a>
                <a href="?phpinfo=1" target="_blank" class="btn btn-secondary">
                    PHP Info
                </a>
            </div>
        </header>

        <!-- Status Cards -->
        <div class="status-grid">
            <!-- Apache -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Web Server</span>
                    <span class="indicator online"><span></span>ONLINE</span>
                </div>
                <div class="card-value">Apache 2.4</div>
                <div class="card-meta">Port: 8090 (HTTP)</div>
            </div>

            <!-- PHP -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Script Engine</span>
                    <span class="indicator online"><span></span>ONLINE</span>
                </div>
                <div class="card-value">PHP <?= PHP_VERSION ?></div>
                <div class="card-meta">SAPI: <?= php_sapi_name() ?></div>
            </div>

            <!-- MariaDB -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Database</span>
                    <?php if ($dbStatus): ?>
                        <span class="indicator online"><span></span>ONLINE</span>
                    <?php else: ?>
                        <span class="indicator offline"><span></span>OFFLINE</span>
                    <?php endif; ?>
                </div>
                <div class="card-value"><?= $dbStatus ? 'MariaDB' : 'Disconnected' ?></div>
                <div class="card-meta">
                    <?= $dbStatus ? htmlspecialchars($dbVersion) : htmlspecialchars($dbError ?: 'Port 3306') ?>
                </div>
            </div>

            <!-- phpMyAdmin -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Database Admin</span>
                    <span class="indicator online"><span></span>READY</span>
                </div>
                <div class="card-value">phpMyAdmin</div>
                <div class="card-meta">Alias: /phpmyadmin</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Database Connection Helper -->
            <div class="panel">
                <h3>
                    <svg width="20" height="20" fill="none" stroke="#38bdf8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    Database PDO Connection
                </h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;">
                    Quick reference snippet for connecting your Smart IT Helpdesk code to MariaDB:
                </p>
                <div class="code-snippet">
<span class="comment">// Database configuration</span>
<span class="variable">$host</span> = <span class="string">'127.0.0.1'</span>;
<span class="variable">$port</span> = <span class="string">'3306'</span>;
<span class="variable">$db</span>   = <span class="string">'smart_helpdesk'</span>;
<span class="variable">$user</span> = <span class="string">'root'</span>;
<span class="variable">$pass</span> = <span class="string">''</span>;

<span class="keyword">try</span> {
    <span class="variable">$dsn</span> = <span class="string">"mysql:host=<span class="variable">$host</span>;port=<span class="variable">$port</span>;dbname=<span class="variable">$db</span>;charset=utf8mb4"</span>;
    <span class="variable">$pdo</span> = <span class="keyword">new</span> PDO(<span class="variable">$dsn</span>, <span class="variable">$user</span>, <span class="variable">$pass</span>, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} <span class="keyword">catch</span> (PDOException <span class="variable">$e</span>) {
    <span class="keyword">die</span>(<span class="string">"DB Connection Error: "</span> . <span class="variable">$e</span>->getMessage());
}
                </div>
            </div>

            <!-- PHP Extensions Status -->
            <div class="panel">
                <h3>
                    <svg width="20" height="20" fill="none" stroke="#34d399" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Installed PHP Extensions
                </h3>
                <div class="ext-list">
                    <?php foreach ($requiredExtensions as $ext => $label): ?>
                        <div class="ext-item">
                            <div>
                                <code><?= $ext ?></code>
                            </div>
                            <?php if (extension_loaded($ext)): ?>
                                <span class="check-icon">✓</span>
                            <?php else: ?>
                                <span style="color: var(--danger); font-weight: bold;">✕</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <footer>
            Smart IT Helpdesk &bull; Apache 2.4 &bull; PHP 8.3 &bull; MariaDB 11.4 &bull; phpMyAdmin &bull; Root: <?= htmlspecialchars(__DIR__) ?>
        </footer>
    </div>
</body>
</html>
