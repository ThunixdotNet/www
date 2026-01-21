// thunix terminal UI powered by xterm.js (ESM via jsDelivr).

import { Terminal } from "https://cdn.jsdelivr.net/npm/@xterm/xterm@6.0.0/+esm";
import { FitAddon } from "https://cdn.jsdelivr.net/npm/@xterm/addon-fit@0.11.0/+esm";
import { WebLinksAddon } from "https://cdn.jsdelivr.net/npm/@xterm/addon-web-links@0.12.0/+esm";

const MODULE_BASE = new URL(".", import.meta.url);
const urlFromBase = (path) => new URL(path, MODULE_BASE).toString();

const DEFAULT_WEBMAIL_PATH = "/webmail/";

const ESC = "\x1b";
const CSI = `${ESC}[`;
const OSC = `${ESC}]`;

const ANSI = {
  reset: `${CSI}0m`,
  bold: `${CSI}1m`,
  dim: `${CSI}2m`,
  underline: `${CSI}4m`,
  noUnderline: `${CSI}24m`,
  clrLine: `${CSI}2K`,
  fgBrightYellow: `${CSI}93m`,
  fgWhite: `${CSI}37m`,
};

function osc8(url, text) {
  const BEL = "\x07";
  return `${OSC}8;;${url}${BEL}${text}${OSC}8;;${BEL}`;
}

function absolutizeUrl(href) {
  if (!href) return href;
  if (/^[a-zA-Z][a-zA-Z0-9+.-]*:/.test(href)) return href;
  if (href.startsWith("//")) return `${location.protocol}${href}`;
  if (href.startsWith("/")) return `${location.origin}${href}`;
  return new URL(href, location.href).toString();
}

function renderInlineLinks(line) {
  return line.replace(/\[([^\]]+)\]\(([^)]+)\)/g, (_m, text, href) => {
    const url = absolutizeUrl(String(href).trim());
    const label = `${ANSI.underline}${ANSI.fgBrightYellow}${text}${ANSI.reset}`;
    return osc8(url, label);
  });
}

function stripHtmlToText(html) {
  let s = String(html || "");

  s = s.replace(/<\s*br\s*\/?\s*>/gi, "\n");
  s = s.replace(/<\s*\/(p|div|tr|li|table|form|h1|h2|h3|ul|ol)\s*>/gi, "\n");
  s = s.replace(/<\s*(p|div|tr|li|table|form|h1|h2|h3|ul|ol)(\s[^>]*)?>/gi, "\n");

  s = s.replace(/<a\s+[^>]*href=['"]([^'"]+)['"][^>]*>(.*?)<\/a>/gi, (_m, href, text) => {
    const cleanText = String(text).replace(/<[^>]+>/g, "").trim() || href;
    return `[${cleanText}](${href})`;
  });

  s = s.replace(/<[^>]+>/g, "");

  s = s.replace(/&nbsp;/g, " ");
  s = s.replace(/&amp;/g, "&");
  s = s.replace(/&lt;/g, "<");
  s = s.replace(/&gt;/g, ">");
  s = s.replace(/&quot;/g, '"');
  s = s.replace(/&#39;/g, "'");

  return s;
}

function renderMarkdown(md) {
  const input = stripHtmlToText(md);
  const out = [];
  const lines = input.replace(/\r\n/g, "\n").split("\n");

  let inCode = false;

  for (const raw of lines) {
    let line = raw;

    if (/^\s*```/.test(line)) {
      inCode = !inCode;
      out.push(inCode ? `${ANSI.dim}--- code ---${ANSI.reset}` : `${ANSI.dim}--- end ---${ANSI.reset}`);
      continue;
    }
    if (inCode) {
      out.push(line);
      continue;
    }

    if (/^\s*#\s+/.test(line)) {
      const title = line.replace(/^\s*#\s+/, "").trim();
      out.push(`${ANSI.bold}${title}${ANSI.reset}`);
      out.push(`${ANSI.dim}${"=".repeat(Math.min(78, title.length || 1))}${ANSI.reset}`);
      continue;
    }
    if (/^\s*##\s+/.test(line)) {
      const title = line.replace(/^\s*##\s+/, "").trim();
      out.push(`${ANSI.bold}${title}${ANSI.reset}`);
      out.push(`${ANSI.dim}${"-".repeat(Math.min(78, title.length || 1))}${ANSI.reset}`);
      continue;
    }

    const mBullet = line.match(/^\s*[-*]\s+(.*)$/);
    if (mBullet) {
      line = `  - ${mBullet[1]}`;
    }

    line = line.replace(/\s+$/g, "");
    line = renderInlineLinks(line);
    out.push(line);
  }

  return out.join("\r\n");
}

class ThunixTerminal {
  constructor(hostEl, frameEl) {
    this.el = hostEl;
    this.frame = frameEl;

    this.term = null;
    this.fit = new FitAddon();

    this.buffer = "";
    this.history = [];
    this.historyIdx = -1;

    this.pages = new Map();
    this.menu = null;

    this.webLinks = new WebLinksAddon((ev, uri) => {
      try {
        ev?.preventDefault?.();
      } catch {
      }
      this.handleLinkActivate(uri);
    });

    this.init();
  }

  init() {
    this.term = new Terminal({
      cursorBlink: true,
      convertEol: true,
      scrollback: 4000,
      fontFamily:
        '"Departure Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace',
      fontSize: 18,
      theme: {
        background: "#111111",
        foreground: "#edb200",
        cursor: "#ffc828",
        selection: "rgba(255, 200, 40, 0.25)",
        black: "#000000",
        brightYellow: "#ffc828",
      },
    });

    this.term.loadAddon(this.fit);
    this.term.loadAddon(this.webLinks);

    this.term.open(this.el);
    this.fit.fit();

    window.addEventListener("resize", () => this.fit.fit());

    this.el.addEventListener("mousedown", () => this.term.focus());
    this.term.focus();

    this.term.onKey((e) => this.onKey(e));

    this.boot();
  }

  writeln(s = "") {
    this.term.writeln(s);
  }

  write(s = "") {
    this.term.write(s);
  }

  prompt() {
    this.write(`\r\n${ANSI.bold}guest@thunix${ANSI.reset}:${ANSI.fgWhite}~${ANSI.reset}$ `);
  }

  redrawInput(newValue) {
    this.write(`\r${ANSI.clrLine}${ANSI.bold}guest@thunix${ANSI.reset}:${ANSI.fgWhite}~${ANSI.reset}$ ${newValue}`);
  }

  async boot() {
    this.writeln(`${ANSI.bold}thunix terminal${ANSI.reset}`);
    this.writeln(`${ANSI.dim}Terminal commands + real HTML panel so forms actually work.${ANSI.reset}`);
    this.writeln("");

    await Promise.allSettled([this.loadPages(), this.loadMenu()]);

    const hash = (location.hash || "").replace(/^#/, "").trim();
    const initial = hash && this.normalizeSlug(hash);
    if (initial) {
      this.openInPanel(initial);
    } else {
      this.openInPanel("main");
    }

    this.writeln(`${ANSI.dim}Type ${ANSI.reset}${ANSI.bold}help${ANSI.reset}${ANSI.dim} for commands.${ANSI.reset}`);
    this.prompt();
  }

  async loadPages() {
    const res = await fetch(urlFromBase("api/pages.php"), { cache: "no-store" });
    if (!res.ok) return;
    const data = await res.json();
    if (!data?.pages) return;
    for (const p of data.pages) {
      this.pages.set(p.slug, p.title || p.slug);
    }
  }

  async loadMenu() {
    const res = await fetch(urlFromBase("api/menu.php"), { cache: "no-store" });
    if (!res.ok) return;
    this.menu = await res.json();
  }

  handleLinkActivate(uri) {
    const u = (() => {
      try {
        return new URL(uri, location.origin);
      } catch {
        return null;
      }
    })();

    if (!u) {
      window.open(uri, "_blank", "noopener");
      return;
    }

    if (u.pathname.endsWith("/terminal/view.php")) {
      const p = u.searchParams.get("page") || "";
      if (p) {
        this.openInPanel(p);
        return;
      }
    }

    if (u.origin === location.origin && u.pathname.startsWith("/")) {
      const slug = this.normalizeSlug(u.pathname.replace(/^\/+/, ""));
      if (slug && this.pages.has(slug)) {
        this.openInPanel(slug);
        return;
      }
    }

    window.open(u.toString(), "_blank", "noopener");
  }

  onKey({ key, domEvent }) {
    const ev = domEvent;

    if (ev.ctrlKey && ev.key.toLowerCase() === "l") {
      ev.preventDefault();
      this.cmdClear();
      return;
    }

    if (ev.ctrlKey && ev.key.toLowerCase() === "c") {
      ev.preventDefault();
      this.write("^C");
      this.buffer = "";
      this.historyIdx = -1;
      this.prompt();
      return;
    }

    if (ev.key === "Enter") {
      const line = this.buffer.trim();
      this.buffer = "";
      this.historyIdx = -1;
      this.write("\r\n");
      if (line) this.history.unshift(line);
      this.run(line);
      return;
    }

    if (ev.key === "Backspace") {
      if (this.buffer.length > 0) {
        this.buffer = this.buffer.slice(0, -1);
        this.write("\b \b");
      }
      return;
    }

    if (ev.key === "ArrowUp") {
      if (this.history.length === 0) return;
      if (this.historyIdx + 1 < this.history.length) this.historyIdx++;
      const next = this.history[this.historyIdx] ?? "";
      this.buffer = next;
      this.redrawInput(this.buffer);
      return;
    }

    if (ev.key === "ArrowDown") {
      if (this.history.length === 0) return;
      if (this.historyIdx > 0) this.historyIdx--;
      else this.historyIdx = -1;
      const next = this.historyIdx >= 0 ? (this.history[this.historyIdx] ?? "") : "";
      this.buffer = next;
      this.redrawInput(this.buffer);
      return;
    }

    if (!ev.altKey && !ev.ctrlKey && !ev.metaKey && key && key.length === 1) {
      this.buffer += key;
      this.write(key);
    }
  }

  async run(line) {
    if (!line) {
      this.prompt();
      return;
    }

    const [cmdRaw, ...args] = line.split(/\s+/);
    const cmd = cmdRaw.toLowerCase();

    switch (cmd) {
      case "help":
        this.cmdHelp();
        break;
      case "clear":
        this.cmdClear();
        break;
      case "menu":
        await this.cmdMenu();
        break;
      case "pages":
      case "ls":
        await this.cmdPages();
        break;
      case "open":
        await this.cmdOpen(args.join(" "));
        break;
      case "cat":
        await this.cmdCat(args.join(" "));
        break;
      case "web":
        this.cmdWeb(args.join(" "));
        break;
      case "webmail":
      case "mail":
        this.cmdWebmail(args.join(" "));
        break;
      case "users":
        await this.cmdOpen("users");
        break;
      case "server":
        await this.cmdOpen("server");
        break;
      case "news":
        await this.cmdOpen("news");
        break;
      case "main":
      case "home":
        await this.cmdOpen("main");
        break;
      default:
        this.writeln(`${ANSI.dim}Unknown command:${ANSI.reset} ${cmdRaw}`);
        this.writeln(`${ANSI.dim}Try:${ANSI.reset} ${ANSI.bold}help${ANSI.reset}`);
        break;
    }

    this.prompt();
  }

  cmdHelp() {
    this.writeln(`${ANSI.bold}Commands${ANSI.reset}`);
    this.writeln(`${ANSI.dim}help${ANSI.reset}            Show this help`);
    this.writeln(`${ANSI.dim}pages | ls${ANSI.reset}      List available content pages`);
    this.writeln(`${ANSI.dim}open <page>${ANSI.reset}     Load a page in the panel`);
    this.writeln(`${ANSI.dim}web <page>${ANSI.reset}      Print web URLs for a page`);
    this.writeln(`${ANSI.dim}webmail [url]${ANSI.reset}   Open webmail in a new tab (alias: mail)`);
    this.writeln(`${ANSI.dim}clear${ANSI.reset}           Clear the terminal (Ctrl+L)`);
    this.writeln("");
    this.writeln(`${ANSI.dim}Aliases:${ANSI.reset} home, main, users, server, news, mail`);
  }

  cmdClear() {
    this.term.clear();
    this.term.reset();
    this.writeln(`${ANSI.bold}thunix terminal${ANSI.reset}`);
  }

  async cmdMenu() {
    if (!this.menu) await this.loadMenu();
    const menu = this.menu;

    if (!menu?.sections?.length) {
      this.writeln(`${ANSI.dim}Menu not available.${ANSI.reset}`);
      return;
    }

    for (const section of menu.sections) {
      this.writeln("");
      this.writeln(`${ANSI.bold}${section.title}${ANSI.reset}`);
      this.writeln(`${ANSI.dim}${"-".repeat(Math.min(78, section.title.length || 1))}${ANSI.reset}`);
      for (const item of section.items) {
        const href = item.internal ? `${location.origin}/${item.slug}` : absolutizeUrl(item.href);
        const label = `${ANSI.underline}${ANSI.fgBrightYellow}${item.text}${ANSI.reset}`;
        const link = osc8(href, label);
        const hint = item.internal ? `${ANSI.dim} (open ${item.slug})${ANSI.reset}` : "";
        this.writeln(`  • ${link}${hint}`);
      }
    }
  }

  async cmdPages() {
    if (this.pages.size === 0) await this.loadPages();
    if (this.pages.size === 0) {
      this.writeln(`${ANSI.dim}No pages found.${ANSI.reset}`);
      return;
    }
    this.writeln(`${ANSI.bold}Pages${ANSI.reset}`);
    const slugs = [...this.pages.keys()].sort();
    this.writeln(slugs.map((s) => `  - ${s}`).join("\r\n"));
  }

  cmdWeb(arg) {
    const slug = this.normalizeSlug(arg);
    if (!slug) {
      this.writeln(`${ANSI.dim}Usage:${ANSI.reset} web <page>`);
      return;
    }
    const classic = `${location.origin}/${slug}`;
    const panelUrl = new URL("view.php", MODULE_BASE);
    panelUrl.searchParams.set("page", slug);
    const panel = panelUrl.toString();
    this.writeln(`${ANSI.dim}Classic:${ANSI.reset} ${osc8(classic, `${ANSI.underline}${ANSI.fgBrightYellow}${classic}${ANSI.reset}`)}`);
    this.writeln(`${ANSI.dim}Panel:${ANSI.reset}   ${osc8(panel, `${ANSI.underline}${ANSI.fgBrightYellow}${panel}${ANSI.reset}`)}`);
  }

  cmdWebmail(arg) {
    const raw = String(arg || "").trim();

    const configured = (() => {
      try {
        const v = window.THUNIX_WEBMAIL_URL;
        return typeof v === "string" ? v.trim() : "";
      } catch {
        return "";
      }
    })();

    const target = raw || configured || DEFAULT_WEBMAIL_PATH;
    const url = absolutizeUrl(target);

    window.open(url, "_blank", "noopener");

    const label = `${ANSI.underline}${ANSI.fgBrightYellow}${url}${ANSI.reset}`;
    this.writeln(`${ANSI.dim}Opened webmail:${ANSI.reset} ${osc8(url, label)}`);

    if (!raw && !configured && DEFAULT_WEBMAIL_PATH !== "/webmail/") {
      this.writeln(`${ANSI.dim}Hint:${ANSI.reset} set window.THUNIX_WEBMAIL_URL if your webmail lives elsewhere.`);
    } else if (!raw && !configured) {
      this.writeln(
        `${ANSI.dim}Hint:${ANSI.reset} if your webmail isn't at ${ANSI.bold}${DEFAULT_WEBMAIL_PATH}${ANSI.reset}${ANSI.dim}, set window.THUNIX_WEBMAIL_URL (or run: webmail <url>).${ANSI.reset}`
      );
    }
  }

  normalizeSlug(arg) {
    if (!arg) return "";
    let s = String(arg).trim();
    if (s.startsWith("/")) s = s.replace(/^\/+/, "");
    if (s.includes("?")) s = s.split("?")[0];
    if (s === "") return "";
    return s;
  }

  openInPanel(slug) {
    if (!this.frame) return;
    const clean = this.normalizeSlug(slug);
    this.frame.src = `${urlFromBase("view.php")}?page=${encodeURIComponent(clean)}`;

    try {
      history.replaceState(null, "", `#${encodeURIComponent(clean)}`);
    } catch {
    }
  }

  async cmdOpen(arg) {
    const slug = this.normalizeSlug(arg);
    if (!slug) {
      this.writeln(`${ANSI.dim}Usage:${ANSI.reset} open <page>`);
      return;
    }

    if (/^https?:\/\//i.test(slug) || slug.startsWith("//")) {
      const url = absolutizeUrl(slug);
      window.open(url, "_blank", "noopener");
      this.writeln(`${ANSI.dim}Opened externally:${ANSI.reset} ${osc8(url, `${ANSI.underline}${ANSI.fgBrightYellow}${url}${ANSI.reset}`)}`);
      return;
    }

    if (this.pages.size === 0) await this.loadPages();
    if (this.pages.size && !this.pages.has(slug) && !/^success\d+$/.test(slug)) {
      this.writeln(`${ANSI.dim}No such page:${ANSI.reset} ${slug}`);
      this.writeln(`${ANSI.dim}Try:${ANSI.reset} pages`);
      return;
    }

    this.openInPanel(slug);
    this.writeln(`${ANSI.dim}Loaded in panel:${ANSI.reset} ${ANSI.bold}${slug}${ANSI.reset}`);
  }

  async cmdCat(arg) {
    const slug = this.normalizeSlug(arg);
    if (!slug) {
      this.writeln(`${ANSI.dim}Usage:${ANSI.reset} cat <page>`);
      return;
    }

    const res = await fetch(`${urlFromBase("api/page.php")}?p=${encodeURIComponent(slug)}`, { cache: "no-store" });
    if (!res.ok) {
      this.writeln(`${ANSI.dim}No such page:${ANSI.reset} ${slug}`);
      return;
    }
    const data = await res.json();
    const md = String(data?.markdown ?? "");
    this.writeln("");
    this.writeln(renderMarkdown(md));
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const host = document.getElementById("terminal");
  const frame = document.getElementById("contentFrame");
  if (!host) return;
  new ThunixTerminal(host, frame);
});
