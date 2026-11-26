<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Kontrol Sistem Pengering</title>
<style>
:root{
  --bg:#071116;
  --bg-top:#0d212b;
  --panel:rgba(19, 39, 49, 0.72);
  --panel-solid:#132731;
  --panel2:rgba(28, 56, 69, 0.85);
  --line:rgba(255, 255, 255, 0.08);
  --line-strong:rgba(255, 255, 255, 0.15);
  --ink:#f0f7f6;
  --mute:#8aa9b3;
  --heat:#ff8a3d;
  --heat-glow:rgba(255, 138, 61, 0.28);
  --cool:#3cd6c6;
  --cool-glow:rgba(60, 214, 198, 0.26);
  --idle:#7798a4;
  --idle-glow:rgba(119, 152, 164, 0.16);
  --bad:#ff7070;
  --accent:var(--idle);
  --accent-glow:var(--idle-glow);
}

*{box-sizing:border-box; -webkit-tap-highlight-color:transparent}

body{
  margin:0;
  min-height:100vh;
  background:
    radial-gradient(circle at 50% 0%, var(--bg-top) 0%, var(--bg) 68%);
  color:var(--ink);
  font-family:"SF Pro Display","Segoe UI Variable Display","Segoe UI",system-ui,-apple-system,sans-serif;
  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding:20px 16px 44px;
  position:relative;
  overflow-x:hidden;
}

/* Latar cahaya ambient yang mengikuti status heater/kipas */
body::before{
  content:"";
  position:fixed;
  top:-120px;
  left:50%;
  transform:translateX(-50%);
  width:min(540px, 120vw);
  height:420px;
  background:radial-gradient(circle, var(--accent-glow) 0%, transparent 68%);
  pointer-events:none;
  z-index:0;
  transition:background .7s ease;
}

main{
  position:relative;
  z-index:1;
  width:100%;
  max-width:430px;
}

/* Header */
header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:12px;
  margin-bottom:18px;
  padding:2px 2px 0;
}
.brand small{
  display:block;
  font-size:10px;
  font-weight:700;
  letter-spacing:1.6px;
  text-transform:uppercase;
  color:var(--mute);
  margin-bottom:3px;
}
h1{
  font-size:clamp(17px, 4.5vw, 20px);
  font-weight:600;
  line-height:1.2;
  margin:0;
  letter-spacing:-.2px;
}

.pill{
  display:inline-flex;
  align-items:center;
  flex-shrink:0;
  white-space:nowrap;
  gap:8px;
  font-size:12px;
  font-weight:500;
  color:var(--mute);
  background:var(--panel);
  backdrop-filter:blur(14px);
  -webkit-backdrop-filter:blur(14px);
  border:1px solid var(--line);
  border-radius:99px;
  padding:7px 12px;
  box-shadow:0 4px 16px rgba(0,0,0,.22);
}
.dot{
  width:8px;
  height:8px;
  border-radius:50%;
  background:var(--bad);
  box-shadow:0 0 0 3px rgba(255,112,112,.18);
  transition:background .3s, box-shadow .3s;
}
.pill.on{color:var(--ink)}
.pill.on .dot{
  background:var(--cool);
  box-shadow:0 0 0 4px rgba(60,214,198,.2);
  animation:pulseDot 2.2s ease-in-out infinite;
}
@keyframes pulseDot{
  0%,100%{box-shadow:0 0 0 3px rgba(60,214,198,.2)}
  50%{box-shadow:0 0 0 6px rgba(60,214,198,.08)}
}

/* Banner Peringatan */
.warn{
  display:none;
  margin:0 0 16px;
  padding:12px 14px;
  border-radius:14px;
  font-size:13px;
  line-height:1.45;
  background:rgba(255,112,112,.12);
  backdrop-filter:blur(10px);
  border:1px solid rgba(255,112,112,.45);
  color:#ffb3b3;
}
.warn.show{display:block}

/* Kartu Utama Dial */
.hero-card{
  background:linear-gradient(180deg, rgba(22,45,56,.68) 0%, rgba(13,28,36,.78) 100%);
  backdrop-filter:blur(18px);
  -webkit-backdrop-filter:blur(18px);
  border:1px solid var(--line);
  border-top-color:var(--line-strong);
  border-radius:28px;
  padding:18px 16px 20px;
  box-shadow:0 20px 48px rgba(0,0,0,.38), inset 0 1px 0 rgba(255,255,255,.06);
  margin-bottom:14px;
}

.dial{
  position:relative;
  width:min(286px, 76vw);
  aspect-ratio:1;
  margin:0 auto;
  --accent:var(--idle);
}
.dial::before{
  content:"";
  position:absolute;
  inset:24%;
  border-radius:50%;
  background:radial-gradient(circle, var(--accent-glow) 0%, transparent 72%);
  transition:background .6s ease;
  pointer-events:none;
}
.dial svg{width:100%;height:100%;display:block;overflow:visible}

.bezel{
  fill:none;
  stroke:rgba(255,255,255,.05);
  stroke-width:1.2;
}
.ticks line{
  stroke:rgba(180,210,220,.22);
  stroke-width:1.4;
  stroke-linecap:round;
}
.ticks line.major{
  stroke:rgba(215,235,240,.46);
  stroke-width:1.8;
}
.scale-lbl{
  fill:var(--mute);
  font-size:10px;
  font-weight:600;
  opacity:.7;
  font-variant-numeric:tabular-nums;
}
.track,.val{
  fill:none;
  stroke-width:11;
  stroke-linecap:round;
  transform:rotate(135deg);
  transform-origin:120px 120px;
}
.track{stroke:rgba(255,255,255,.07)}
.val{
  stroke:var(--accent);
  filter:drop-shadow(0 0 8px var(--accent-glow));
  transition:stroke-dasharray .8s cubic-bezier(.22,1,.36,1), stroke .4s, filter .4s;
}
.mark{
  fill:#ffffff;
  stroke:var(--bg);
  stroke-width:3.5;
  filter:drop-shadow(0 2px 5px rgba(0,0,0,.55));
  transition:cx .35s cubic-bezier(.22,1,.36,1), cy .35s cubic-bezier(.22,1,.36,1);
}

.center{
  position:absolute;
  inset:0;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  text-align:center;
  padding-top:6px;
}
.eyebrow{
  font-size:10px;
  font-weight:700;
  letter-spacing:1.5px;
  text-transform:uppercase;
  color:var(--mute);
  margin-bottom:4px;
}
.temp{
  font-size:60px;
  font-weight:300;
  line-height:1;
  letter-spacing:-2px;
  font-variant-numeric:tabular-nums;
  text-shadow:0 4px 24px rgba(0,0,0,.3);
}
.temp small{
  font-size:22px;
  font-weight:400;
  letter-spacing:0;
  color:var(--mute);
  margin-left:3px;
  vertical-align:super;
}
.state{
  margin-top:12px;
  font-size:12.5px;
  font-weight:500;
  color:var(--accent);
  background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.07);
  border-radius:99px;
  padding:4px 12px;
  min-height:26px;
  display:inline-flex;
  align-items:center;
  transition:color .4s, border-color .4s;
}

/* Kontrol Target Suhu */
.target{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  margin:10px auto 0;
  max-width:290px;
  background:rgba(7, 17, 22, 0.48);
  border:1px solid var(--line);
  border-radius:99px;
  padding:6px;
}
.target-val{
  display:flex;
  flex-direction:column;
  align-items:center;
  line-height:1.2;
}
.target-val small{
  font-size:10px;
  text-transform:uppercase;
  letter-spacing:1.1px;
  color:var(--mute);
}
.target-val span{
  color:var(--mute);
  font-size:14px;
}
.target-val b{
  color:var(--ink);
  font-size:19px;
  font-weight:600;
  font-variant-numeric:tabular-nums;
}
.round{
  width:44px;
  height:44px;
  border-radius:50%;
  border:1px solid var(--line);
  background:var(--panel2);
  color:var(--ink);
  font-size:22px;
  font-weight:400;
  line-height:1;
  display:grid;
  place-items:center;
  cursor:pointer;
  transition:transform .12s, background .2s, border-color .2s;
  box-shadow:0 4px 12px rgba(0,0,0,.22);
}
.round:hover{
  background:rgba(44, 83, 100, .9);
  border-color:var(--line-strong);
}
.round:active{transform:scale(.92)}

/* Segmented Mode Toggle */
.seg{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:4px;
  background:var(--panel);
  backdrop-filter:blur(14px);
  -webkit-backdrop-filter:blur(14px);
  border:1px solid var(--line);
  border-radius:16px;
  padding:5px;
  margin-bottom:14px;
  box-shadow:0 8px 24px rgba(0,0,0,.22);
}
.seg button{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  border:0;
  background:none;
  color:var(--mute);
  font:inherit;
  font-size:14px;
  font-weight:500;
  padding:11px 10px;
  border-radius:11px;
  cursor:pointer;
  transition:background .25s, color .25s, box-shadow .25s, transform .12s;
}
.seg button svg{width:16px;height:16px;opacity:.75;transition:opacity .25s}
.seg button:active{transform:scale(.98)}
.seg button[aria-pressed=true]{
  background:linear-gradient(180deg, rgba(42,81,97,.95) 0%, rgba(28,57,70,.95) 100%);
  color:var(--ink);
  font-weight:600;
  box-shadow:0 4px 14px rgba(0,0,0,.28), inset 0 1px 0 rgba(255,255,255,.12);
}
.seg button[aria-pressed=true] svg{opacity:1;color:var(--cool)}

/* Grid Perangkat */
.grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:12px;
}
.tile{
  position:relative;
  text-align:left;
  font:inherit;
  color:var(--ink);
  background:var(--panel);
  backdrop-filter:blur(14px);
  -webkit-backdrop-filter:blur(14px);
  border:1px solid var(--line);
  border-top-color:var(--line-strong);
  border-radius:20px;
  padding:16px;
  cursor:pointer;
  box-shadow:0 10px 26px rgba(0,0,0,.24);
  transition:border-color .3s, background .3s, opacity .3s, box-shadow .3s, transform .14s;
}
.tile:not(.locked):hover{
  border-color:rgba(255,255,255,.18);
  transform:translateY(-1px);
}
.tile:not(.locked):active{transform:scale(.97)}

.tile-top{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:16px;
}
.ico-wrap{
  width:40px;
  height:40px;
  border-radius:12px;
  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.06);
  display:grid;
  place-items:center;
  transition:background .3s, border-color .3s, box-shadow .3s;
}
.tile .ico{
  width:22px;
  height:22px;
  color:var(--idle);
  transition:color .3s;
}
.tile h2{
  margin:0 0 3px;
  font-size:15.5px;
  font-weight:600;
  letter-spacing:-.1px;
}
.tile p{
  margin:0;
  font-size:12.5px;
  color:var(--mute);
  font-weight:500;
  transition:color .3s;
}

.tile .sw{
  position:relative;
  width:42px;
  height:24px;
  border-radius:99px;
  background:rgba(7,17,22,.6);
  border:1px solid var(--line);
  transition:background .3s, border-color .3s;
}
.tile .sw::after{
  content:"";
  position:absolute;
  top:3px;
  left:3px;
  width:16px;
  height:16px;
  border-radius:50%;
  background:var(--mute);
  box-shadow:0 1px 4px rgba(0,0,0,.4);
  transition:transform .25s cubic-bezier(.22,1,.36,1), background .3s;
}

/* Status Aktif Heater */
.tile.heater.on{
  border-color:rgba(255, 138, 61, .65);
  background:linear-gradient(145deg, rgba(64, 38, 28, .78) 0%, rgba(28, 26, 29, .85) 100%);
  box-shadow:0 12px 30px rgba(255,138,61,.14), inset 0 1px 0 rgba(255,170,115,.16);
}
.tile.heater.on .ico-wrap{
  background:rgba(255,138,61,.16);
  border-color:rgba(255,138,61,.35);
  box-shadow:0 0 16px rgba(255,138,61,.22);
}
.tile.heater.on .ico{color:var(--heat);animation:flame 1.8s ease-in-out infinite}
.tile.heater.on .sw{background:var(--heat);border-color:var(--heat)}
.tile.heater.on p{color:#ffbc8f}

/* Status Aktif Kipas */
.tile.fan.on{
  border-color:rgba(60, 214, 198, .6);
  background:linear-gradient(145deg, rgba(20, 62, 69, .78) 0%, rgba(16, 37, 46, .88) 100%);
  box-shadow:0 12px 30px rgba(60,214,198,.13), inset 0 1px 0 rgba(120,235,222,.16);
}
.tile.fan.on .ico-wrap{
  background:rgba(60,214,198,.15);
  border-color:rgba(60,214,198,.35);
  box-shadow:0 0 16px rgba(60,214,198,.2);
}
.tile.fan.on .ico{color:var(--cool)}
.tile.fan.on .sw{background:var(--cool);border-color:var(--cool)}
.tile.fan.on p{color:#9ef0e7}

.tile.on .sw::after{
  transform:translateX(18px);
  background:#071116;
}

.tile.locked{
  opacity:.62;
  cursor:not-allowed;
}
.tile.locked .sw{opacity:.65}

.fan.on .blades{
  animation:spin 1s linear infinite;
  transform-origin:12px 12px;
}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes flame{
  0%,100%{transform:scale(1)}
  50%{transform:scale(1.08)}
}

.note{
  margin:18px 8px 0;
  font-size:12.5px;
  line-height:1.55;
  color:var(--mute);
  text-align:center;
}

:focus-visible{outline:2px solid var(--cool);outline-offset:3px}
@media (prefers-reduced-motion:reduce){
  *{animation:none!important;transition:none!important}
}
</style>
</head>
<body>
<main>
  <header>
    <div class="brand">
      <small>Panel Monitoring</small>
      <h1>Kontrol Sistem Pengering</h1>
    </div>
    <div class="pill" id="pill"><span class="dot"></span><span id="pillText">Memuat</span></div>
  </header>

  <div class="warn" id="warn" role="alert"></div>

  <section class="hero-card">
    <div class="dial" id="dial" aria-label="Suhu saat ini">
      <svg viewBox="0 0 240 240" aria-hidden="true">
        <circle class="bezel" cx="120" cy="120" r="111"/>
        <g class="ticks" id="ticks"></g>
        <circle class="track" id="track" cx="120" cy="120" r="95"/>
        <circle class="val" id="val" cx="120" cy="120" r="95"/>
        <circle class="mark" id="mark" cx="120" cy="25" r="6.5"/>
        <text class="scale-lbl" x="45" y="198" text-anchor="middle">15°</text>
        <text class="scale-lbl" x="195" y="198" text-anchor="middle">55°</text>
      </svg>
      <div class="center">
        <span class="eyebrow">Suhu Aktual</span>
        <div class="temp"><span id="temp">--.-</span><small>°C</small></div>
        <div class="state" id="state">Menunggu data</div>
      </div>
    </div>

    <div class="target">
      <button class="round" id="minus" aria-label="Turunkan suhu target">&minus;</button>
      <div class="target-val">
        <small>Target Suhu</small>
        <span><b id="sp">--.-</b>°C</span>
      </div>
      <button class="round" id="plus" aria-label="Naikkan suhu target">+</button>
    </div>
  </section>

  <div class="seg" role="group" aria-label="Mode kontrol">
    <button id="bAuto" aria-pressed="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
      </svg>
      Otomatis
    </button>
    <button id="bMan" aria-pressed="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
        <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
        <line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
      </svg>
      Manual
    </button>
  </div>

  <div class="grid">
    <button class="tile heater" data-dev="h1" role="switch" aria-checked="false">
      <div class="tile-top">
        <div class="ico-wrap">
          <svg class="ico" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2c1 3.5 5 5.5 5 10a5 5 0 0 1-10 0c0-2 1-3.2 2-4.2.2 1.2.8 2 1.6 2.4C10.2 7.2 10.5 4.5 12 2z"/></svg>
        </div>
        <i class="sw"></i>
      </div>
      <h2>Heater 1</h2>
      <p>Mati</p>
    </button>

    <button class="tile heater" data-dev="h2" role="switch" aria-checked="false">
      <div class="tile-top">
        <div class="ico-wrap">
          <svg class="ico" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2c1 3.5 5 5.5 5 10a5 5 0 0 1-10 0c0-2 1-3.2 2-4.2.2 1.2.8 2 1.6 2.4C10.2 7.2 10.5 4.5 12 2z"/></svg>
        </div>
        <i class="sw"></i>
      </div>
      <h2>Heater 2</h2>
      <p>Mati</p>
    </button>

    <button class="tile fan" data-dev="f1" role="switch" aria-checked="false">
      <div class="tile-top">
        <div class="ico-wrap">
          <svg class="ico" viewBox="0 0 24 24">
            <g class="blades" fill="currentColor">
              <ellipse cx="12" cy="6.5" rx="2.8" ry="4.6"/>
              <ellipse cx="12" cy="6.5" rx="2.8" ry="4.6" transform="rotate(120 12 12)"/>
              <ellipse cx="12" cy="6.5" rx="2.8" ry="4.6" transform="rotate(240 12 12)"/>
            </g>
            <circle cx="12" cy="12" r="1.8" fill="var(--panel-solid)"/>
          </svg>
        </div>
        <i class="sw"></i>
      </div>
      <h2>Kipas 1</h2>
      <p>Mati</p>
    </button>

    <button class="tile fan" data-dev="f2" role="switch" aria-checked="false">
      <div class="tile-top">
        <div class="ico-wrap">
          <svg class="ico" viewBox="0 0 24 24">
            <g class="blades" fill="currentColor">
              <ellipse cx="12" cy="6.5" rx="2.8" ry="4.6"/>
              <ellipse cx="12" cy="6.5" rx="2.8" ry="4.6" transform="rotate(120 12 12)"/>
              <ellipse cx="12" cy="6.5" rx="2.8" ry="4.6" transform="rotate(240 12 12)"/>
            </g>
            <circle cx="12" cy="12" r="1.8" fill="var(--panel-solid)"/>
          </svg>
        </div>
        <i class="sw"></i>
      </div>
      <h2>Kipas 2</h2>
      <p>Mati</p>
    </button>
  </div>

  <p class="note" id="note"></p>
</main>

<script>
const $ = id => document.getElementById(id);
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const MIN = 15, MAX = 55, SWEEP = 270, START = 135, R = 95;
const C = 2 * Math.PI * R, ARC = C * SWEEP / 360;
let st = {online:false, sensor_ok:false, temp:null, setpoint:30, mode_auto:true, h1:false, h2:false, f1:false, f2:false};
let pendingSP = null, spTimer = null, loadError = false;
const holds = {};

/* Gambar skala tick marks pada dial (15°C - 55°C) */
(function drawTicks(){
  const g = $('ticks');
  for(let i = 0; i <= 40; i++){
    const deg = START + (i / 40) * SWEEP;
    const rad = deg * Math.PI / 180;
    const major = i % 5 === 0;
    const r1 = 104, r2 = major ? 110 : 107;
    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
    line.setAttribute('x1', (120 + r1 * Math.cos(rad)).toFixed(2));
    line.setAttribute('y1', (120 + r1 * Math.sin(rad)).toFixed(2));
    line.setAttribute('x2', (120 + r2 * Math.cos(rad)).toFixed(2));
    line.setAttribute('y2', (120 + r2 * Math.sin(rad)).toFixed(2));
    if(major) line.setAttribute('class', 'major');
    g.appendChild(line);
  }
})();

$('track').style.strokeDasharray = ARC + ' ' + C;
const frac = v => Math.min(1, Math.max(0, (v - MIN) / (MAX - MIN)));

function render(){
  const pill = $('pill');
  pill.classList.toggle('on', st.online);
  $('pillText').textContent = st.online ? 'ESP32 terhubung' : 'ESP32 terputus';

  const hasTemp = st.sensor_ok && st.temp !== null;
  $('temp').textContent = hasTemp ? Number(st.temp).toFixed(1) : '--.-';

  const sp = pendingSP ?? st.setpoint;
  $('sp').textContent = Number(sp).toFixed(1);

  const hOn = (st.h1 ? 1 : 0) + (st.h2 ? 1 : 0), fOn = (st.f1 ? 1 : 0) + (st.f2 ? 1 : 0);
  $('val').style.strokeDasharray = (hasTemp ? frac(st.temp) * ARC : 0) + ' ' + C;
  const a = frac(sp) * SWEEP + START, rad = a * Math.PI / 180;
  $('mark').setAttribute('cx', 120 + R * Math.cos(rad));
  $('mark').setAttribute('cy', 120 + R * Math.sin(rad));

  const accent = hOn ? 'var(--heat)' : (fOn ? 'var(--cool)' : 'var(--idle)');
  const glow = hOn ? 'var(--heat-glow)' : (fOn ? 'var(--cool-glow)' : 'var(--idle-glow)');
  document.documentElement.style.setProperty('--accent', accent);
  document.documentElement.style.setProperty('--accent-glow', glow);
  $('dial').style.setProperty('--accent', accent);

  $('state').textContent = !hasTemp ? 'Menunggu data sensor'
    : hOn ? 'Memanaskan (' + hOn + ' heater)'
    : fOn ? 'Mendinginkan (' + fOn + ' kipas)' : 'Suhu stabil';

  $('bAuto').setAttribute('aria-pressed', st.mode_auto);
  $('bMan').setAttribute('aria-pressed', !st.mode_auto);

  document.querySelectorAll('.tile').forEach(t => {
    const d = t.dataset.dev, on = !!st[d];
    t.classList.toggle('on', on);
    t.classList.toggle('locked', st.mode_auto);
    t.setAttribute('aria-checked', on);
    t.setAttribute('aria-disabled', st.mode_auto);
    t.querySelector('p').textContent = on ? 'Menyala' : 'Mati';
  });

  const warn = $('warn');
  const msg = loadError ? 'Tidak bisa memuat status dari server. Cek apakah php artisan serve masih berjalan.'
    : (st.online && !st.sensor_ok) ? 'Sensor suhu tidak terbaca. Heater dimatikan demi keamanan.' : '';
  warn.textContent = msg; warn.classList.toggle('show', !!msg);

  $('note').textContent = st.mode_auto
    ? 'Mode otomatis mengatur heater dan kipas sesuai suhu target. Pilih Manual untuk mengontrol sendiri.'
    : 'Perubahan dikirim ke ESP32 dalam sekitar 3 detik.';
}

async function refresh(){
  try{
    const r = await fetch('/status', {headers:{'Accept':'application/json'}});
    if(!r.ok) throw new Error(r.status);
    st = await r.json();
    loadError = false;
    for (const d in holds){
      if (Date.now() < holds[d].until && st[d] !== holds[d].val) st[d] = holds[d].val;
      else delete holds[d];
    }
  }catch(e){ loadError = true; }
  render();
}

async function send(data){
  try{
    await fetch('/control', {method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify(data)});
  }catch(e){}
  refresh();
}

function changeSP(delta){
  const base = pendingSP ?? st.setpoint;
  pendingSP = Math.min(MAX, Math.max(MIN, Math.round((base + delta) * 2) / 2));
  render();
  clearTimeout(spTimer);
  spTimer = setTimeout(async () => { const v = pendingSP; await send({setpoint:v}); pendingSP = null; render(); }, 600);
}

$('minus').onclick = () => changeSP(-0.5);
$('plus').onclick = () => changeSP(0.5);
$('bAuto').onclick = () => send({mode_auto:1});
$('bMan').onclick = () => send({mode_auto:0});
document.querySelectorAll('.tile').forEach(t => t.onclick = () => {
  if (st.mode_auto) return;
  const d = t.dataset.dev, v = !st[d];
  st[d] = v; holds[d] = {val:v, until:Date.now() + 8000};
  render();
  send({dev:d, state: v ? 1 : 0});
});

setInterval(refresh, 2000);
refresh();
</script>
</body>
</html>