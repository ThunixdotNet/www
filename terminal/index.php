<?php
declare(strict_types=1);

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>thunix terminal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  
  <noscript><meta http-equiv="refresh" content="0;url=/main"></noscript>
<!-- xterm core styles -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@xterm/xterm@6.0.0/css/xterm.css">

  <style>
    :root{
      --mono: "Departure Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      --bg: #111;
      --amber: #edb200;
      --amber-txt: #ffc828;
      --panel: rgba(255, 255, 255, 0.03);
      --border: rgba(255, 255, 255, 0.08);
    }

    *{ box-sizing:border-box; }

    html, body{
      margin:0;
      padding:0;
      height:100%;
      width:100%;
      overflow:hidden;
      background: var(--bg);
      color: var(--amber);
      font-family: var(--mono);
    }

    body::before{
      content:'';
      position:fixed;
      inset:0;
      pointer-events:none;
      z-index:2;
      background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.32) 51%);
      background-size: 100% 4px;
    }

    #wrap{
      position:relative;
      z-index:1;
      height:100%;
      display:flex;
      flex-direction:column;
      padding: 6vh 0 6vh 0;
      gap: 10px;
    }

    #head{
      width: min(92vw, 1400px);
      margin: 0 auto;
      font-size: clamp(18px, 3vw, 44px);
      text-shadow: 0 0 1.75rem rgba(237,178,0,0.55);
      line-height:1.1;
    }
    #head small{
      display:block;
      font-size: 0.8rem;
      opacity: 0.85;
      margin-top: 8px;
      text-shadow:none;
    }

    #main{
      width: min(92vw, 1400px);
      margin: 0 auto;
      flex: 1;
      display: grid;
      grid-template-columns: 1.05fr 1.25fr;
      gap: 12px;
      align-items: stretch;
    }

    #terminalHost,
    #contentHost{
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 6px;
      overflow: hidden;
      box-shadow: 0 0 24px rgba(237,178,0,0.09);
      min-height: 0;
    }

    #terminal{ height: 100%; width: 100%; }

    #contentFrame{
      width: 100%;
      height: 100%;
      border: 0;
      background: var(--bg);
    }

    @media (max-width: 1050px){
      #main{ grid-template-columns: 1fr; grid-template-rows: 48vh 1fr; }
    }

    #terminalHost .xterm-rows a,
    #terminalHost .xterm-screen a{
      color: var(--amber-txt) !important;
      text-decoration: underline;
      text-decoration-thickness: 2px;
      text-underline-offset: 3px;
      text-shadow: 0 0 0.85rem rgba(237,178,0,0.30);
    }
    #terminalHost .xterm-rows a:hover,
    #terminalHost .xterm-screen a:hover{
      filter: brightness(1.08);
      text-shadow: 0 0 1.25rem rgba(237,178,0,0.55);
    }
  </style>
</head>
<body>
  
  <noscript>
    <div style="max-width: 900px; margin: 2rem auto; padding: 1.25rem 1.5rem; border: 1px solid rgba(255,255,255,0.12); border-radius: 14px; background: rgba(0,0,0,0.55); color: #e6e6e6; font-family: system-ui, -apple-system, Segoe UI, sans-serif;">
      <h1 style="margin: 0 0 0.6rem 0; font-size: 1.35rem; font-weight: 700;">Terminal mode needs JavaScript</h1>
      <p style="margin: 0; opacity: 0.9; line-height: 1.4;">JavaScript is disabled, so the terminal UI can’t run. Redirecting you to the classic site… If you’re not redirected, use <a href="/main" style="color: #6ec5ff; text-decoration: underline;">the classic site</a>.</p>
    </div>
  </noscript>
<div id="wrap">
    <div id="head">
      🌻 thunix
      <small>Type <strong>help</strong>. Click inside the terminal to focus.</small>
    </div>

    <div id="main">
      <div id="terminalHost">
        <div id="terminal" aria-label="Terminal"></div>
      </div>

      <div id="contentHost" aria-label="Content">
        <iframe
          id="contentFrame"
          src="/terminal/view.php?page=main"
          title="thunix content"
          referrerpolicy="no-referrer"
          loading="eager"
        ></iframe>
      </div>
    </div>
  </div>

  <script type="module" src="/terminal/terminal.js"></script>
</body>
</html>
