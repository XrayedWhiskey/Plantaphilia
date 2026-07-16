<?php
/**
 * Plantaphilia Support — KI-Chatbot (ersetzt Kontaktformular)
 * Sektion 7: AI Chatbot — Streaming + Reasoning
 */
defined('ABSPATH') or die();

get_header();
?>

<style>
.pa-support-wrap {
  min-height: 80vh;
  padding: 60px 32px 96px;
  background: var(--bg-deep);
  display: flex;
  flex-direction: column;
  align-items: center;
}
.pa-support-header {
  text-align: center;
  margin-bottom: 40px;
  max-width: 600px;
}
.pa-support-header h1 {
  font-family: var(--serif-display);
  font-size: 48px; font-weight: 500;
  color: var(--creme); letter-spacing: -0.01em;
  margin: 0 0 12px;
}
.pa-support-header h1 em { color: var(--lavender); font-weight: 400; }
.pa-support-header p {
  font-family: var(--serif-display); font-style: italic;
  font-size: 17px; color: var(--creme-dim); margin: 0; line-height: 1.6;
}

/* ── Chat window ─────────────────────────────────────────────── */
.pa-chat-window {
  width: 100%; max-width: 740px;
  background: var(--bg-surface);
  border: 1px solid var(--border-thin);
  box-shadow: var(--shadow-specimen);
  display: flex; flex-direction: column;
  min-height: 540px;
}
.pa-chat-messages {
  flex: 1; overflow-y: auto;
  padding: 28px 28px 0;
  display: flex; flex-direction: column;
  gap: 20px;
  scroll-behavior: smooth;
}

/* ── Message bubbles ─────────────────────────────────────────── */
.pa-msg { display: flex; flex-direction: column; gap: 4px; max-width: 86%; }
.pa-msg--user { align-self: flex-end; align-items: flex-end; }
.pa-msg--ai   { align-self: flex-start; align-items: flex-start; }

.pa-msg-label {
  font-family: var(--sans-body);
  font-size: 9px; font-weight: 700;
  letter-spacing: 0.22em; text-transform: uppercase;
  color: var(--lavender-dim); padding: 0 4px;
}
.pa-msg-bubble {
  padding: 13px 17px;
  border-radius: var(--r-1);
  font-family: var(--sans-body);
  font-size: 14px; line-height: 1.7;
  white-space: pre-wrap; word-break: break-word;
}
.pa-msg--user .pa-msg-bubble {
  background: var(--plum); color: var(--creme);
  border-bottom-right-radius: 0;
}
.pa-msg--ai .pa-msg-bubble {
  background: var(--bg-raised); color: var(--creme-dim);
  border: 1px solid var(--border-hair);
  border-bottom-left-radius: 0;
}
/* blinking cursor while streaming */
.pa-msg-bubble.streaming::after {
  content: '▍';
  animation: pa-blink 0.8s step-end infinite;
  color: var(--amethyst);
}
@keyframes pa-blink { 50% { opacity: 0; } }

/* ── Reasoning block ─────────────────────────────────────────── */
.pa-reasoning {
  margin-top: 6px;
  border: 1px solid var(--border-hair);
  border-radius: var(--r-1);
  overflow: hidden;
  max-width: 100%;
}
.pa-reasoning-toggle {
  display: flex; align-items: center; gap: 8px;
  padding: 7px 12px;
  background: var(--bg-inky);
  cursor: pointer;
  font-family: var(--sans-body);
  font-size: 10px; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  color: var(--amethyst);
  border: none; width: 100%; text-align: left;
  transition: color var(--t-fast) var(--ease-botanical);
}
.pa-reasoning-toggle:hover { color: var(--lavender); }
.pa-reasoning-toggle svg {
  transition: transform var(--t-fast) var(--ease-botanical);
  flex-shrink: 0;
}
.pa-reasoning-toggle.open svg { transform: rotate(90deg); }
.pa-reasoning-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--amethyst); flex-shrink: 0;
  animation: pa-pulse 2s ease-in-out infinite;
}
.pa-reasoning-dot.done { animation: none; background: var(--amethyst); opacity: 0.5; }
@keyframes pa-pulse { 0%,100%{opacity:0.4;} 50%{opacity:1;} }
.pa-reasoning-body {
  padding: 10px 14px;
  background: rgba(7,21,13,0.4);
  font-family: var(--sans-body);
  font-size: 12px; line-height: 1.65;
  color: var(--creme-muted);
  white-space: pre-wrap; word-break: break-word;
  display: none;
  max-height: 240px; overflow-y: auto;
}
.pa-reasoning-body.visible { display: block; }

/* ── Tool indicator ──────────────────────────────────────────── */
.pa-tool-indicator {
  align-self: flex-start;
  display: flex; align-items: center; gap: 8px;
  padding: 8px 14px;
  background: var(--bg-inky);
  border: 1px solid var(--border-hair);
  border-radius: var(--r-1);
  font-family: var(--sans-body);
  font-size: 11px; color: var(--amethyst);
  letter-spacing: 0.06em;
}
.pa-tool-indicator .pa-tool-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--amethyst);
  animation: pa-pulse 1s ease-in-out infinite;
  flex-shrink: 0;
}
.pa-tool-indicator.done .pa-tool-dot {
  animation: none; background: var(--forest);
}
.pa-tool-indicator.done { color: var(--creme-muted); }

/* ── Typing indicator ────────────────────────────────────────── */
.pa-typing {
  align-self: flex-start;
  display: flex; align-items: center; gap: 6px;
  padding: 12px 16px;
  background: var(--bg-raised);
  border: 1px solid var(--border-hair);
  border-radius: var(--r-1);
  border-bottom-left-radius: 0;
}
.pa-typing span {
  display: block; width: 7px; height: 7px;
  border-radius: 50%; background: var(--lavender);
  animation: pa-bounce 1.2s infinite ease-in-out;
}
.pa-typing span:nth-child(2) { animation-delay: .2s; }
.pa-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes pa-bounce {
  0%,80%,100%{ transform:translateY(0); opacity:.4; }
  40%{ transform:translateY(-6px); opacity:1; }
}
.pa-typing.hidden { display: none; }

/* ── Input area ──────────────────────────────────────────────── */
.pa-chat-input-area {
  padding: 20px 28px 24px;
  border-top: 1px solid var(--border-hair);
  display: flex; gap: 12px; align-items: flex-end;
}
.pa-chat-input {
  flex: 1;
  background: var(--bg-deep);
  border: 1px solid var(--border-thin);
  border-radius: var(--r-1);
  color: var(--creme);
  font-family: var(--sans-body); font-size: 14px;
  padding: 12px 16px;
  resize: none; min-height: 46px; max-height: 140px; line-height: 1.5;
  transition: border-color var(--t-base) var(--ease-botanical);
}
.pa-chat-input::placeholder { color: var(--lavender-dim); }
.pa-chat-input:focus {
  outline: none; border-color: var(--plum-hot);
  box-shadow: 0 0 0 3px rgba(156,63,126,0.12);
}
.pa-chat-send {
  flex-shrink: 0;
  background: var(--plum-hot); color: var(--creme);
  border: 1px solid var(--plum-hot); border-radius: var(--r-1);
  padding: 12px 20px;
  font-family: var(--sans-body); font-size: 10px; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  cursor: pointer;
  transition: all var(--t-base) var(--ease-botanical);
}
.pa-chat-send:hover:not(:disabled) { background: var(--plum); border-color: var(--plum); }
.pa-chat-send:disabled { opacity: 0.4; cursor: not-allowed; }

.pa-chat-disclaimer {
  font-family: var(--sans-body); font-size: 11px;
  color: var(--creme-muted); text-align: center;
  margin-top: 16px; letter-spacing: 0.04em;
}

@media (max-width: 600px) {
  .pa-support-wrap { padding: 40px 16px 80px; }
  .pa-support-header h1 { font-size: 32px; }
  .pa-chat-messages { padding: 20px 16px 0; }
  .pa-chat-input-area { padding: 14px 16px 18px; }
  .pa-msg { max-width: 92%; }
}
</style>

<div class="pa-support-wrap">

  <div class="pa-support-header">
    <h1>Wie können wir<br><em>helfen?</em></h1>
    <p>Unser botanischer Assistent beantwortet Fragen zu Produkten,<br>Bestellungen und Pflanzenpflege — rund um die Uhr.</p>
  </div>

  <div class="pa-chat-window">
    <div class="pa-chat-messages" id="pa-chat-messages">
      <div class="pa-msg pa-msg--ai">
        <div class="pa-msg-label">Plantaphilia Assistent</div>
        <div class="pa-msg-bubble">
          Guten Tag! Ich bin der botanische Assistent von Plantaphilia.
          Ich helfe Ihnen gerne bei Fragen zu unseren Pelargonien, Ihren Bestellungen oder der Pflanzenpflege.
          Womit darf ich Ihnen heute behilflich sein?
        </div>
      </div>
      <div class="pa-typing hidden" id="pa-typing">
        <span></span><span></span><span></span>
      </div>
    </div>
    <div class="pa-chat-input-area">
      <textarea class="pa-chat-input" id="pa-chat-input"
        placeholder="Ihre Frage…" rows="1"
        aria-label="Nachricht eingeben"></textarea>
      <button class="pa-chat-send" id="pa-chat-send">Senden</button>
    </div>
  </div>

  <p class="pa-chat-disclaimer">
    Ihre Nachrichten werden zur Beantwortung an einen KI-Dienst übermittelt.
    Keine persönlichen Daten ohne Notwendigkeit angeben. ·
    <a href="<?php echo esc_url(home_url('/datenschutzerklaerung/')); ?>" style="color:var(--lavender)">Datenschutz</a>
    &nbsp;·&nbsp; Direktkontakt:
    <a href="mailto:kontakt@plantaphilia.eu" style="color:var(--lavender)">kontakt@plantaphilia.eu</a>
  </p>

</div>

<script>
(function () {
  var msgList  = document.getElementById('pa-chat-messages');
  var input    = document.getElementById('pa-chat-input');
  var sendBtn  = document.getElementById('pa-chat-send');
  var typing   = document.getElementById('pa-typing');
  var ajaxUrl  = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
  var nonce    = <?php echo wp_json_encode(wp_create_nonce('pa_chatbot_nonce')); ?>;
  var history  = [];   // [{role, content}]

  // ── auto-resize textarea ──────────────────────────────────────
  input.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 140) + 'px';
  });
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
  });
  sendBtn.addEventListener('click', sendMessage);

  function scrollBottom() { msgList.scrollTop = msgList.scrollHeight; }

  // ── append user bubble ────────────────────────────────────────
  function appendUser(text) {
    var wrap   = document.createElement('div');
    wrap.className = 'pa-msg pa-msg--user';
    var label  = document.createElement('div');
    label.className = 'pa-msg-label';
    label.textContent = 'Sie';
    var bubble = document.createElement('div');
    bubble.className = 'pa-msg-bubble';
    bubble.textContent = text;
    wrap.appendChild(label);
    wrap.appendChild(bubble);
    msgList.insertBefore(wrap, typing);
    scrollBottom();
  }

  // ── create AI message shell (returns {bubble, reasoningBody, reasoningDot}) ──
  function createAiShell() {
    var wrap   = document.createElement('div');
    wrap.className = 'pa-msg pa-msg--ai';

    var label  = document.createElement('div');
    label.className = 'pa-msg-label';
    label.textContent = 'Plantaphilia Assistent';

    var bubble = document.createElement('div');
    bubble.className = 'pa-msg-bubble streaming';

    // Reasoning block (hidden until we get reasoning tokens)
    var reasoningWrap = document.createElement('div');
    reasoningWrap.className = 'pa-reasoning';
    reasoningWrap.style.display = 'none';

    var reasoningToggle = document.createElement('button');
    reasoningToggle.className = 'pa-reasoning-toggle';
    var dot = document.createElement('span');
    dot.className = 'pa-reasoning-dot';
    var chev = document.createElementNS('http://www.w3.org/2000/svg','svg');
    chev.setAttribute('width','12'); chev.setAttribute('height','12');
    chev.setAttribute('viewBox','0 0 24 24'); chev.setAttribute('fill','none');
    chev.setAttribute('stroke','currentColor'); chev.setAttribute('stroke-width','2');
    var chevPath = document.createElementNS('http://www.w3.org/2000/svg','path');
    chevPath.setAttribute('d','M9 6l6 6-6 6');
    chev.appendChild(chevPath);
    var toggleLabel = document.createElement('span');
    toggleLabel.textContent = 'Denkprozess anzeigen';
    reasoningToggle.appendChild(dot);
    reasoningToggle.appendChild(chev);
    reasoningToggle.appendChild(toggleLabel);

    var reasoningBody = document.createElement('div');
    reasoningBody.className = 'pa-reasoning-body';

    reasoningToggle.addEventListener('click', function () {
      var open = reasoningBody.classList.toggle('visible');
      reasoningToggle.classList.toggle('open', open);
    });

    reasoningWrap.appendChild(reasoningToggle);
    reasoningWrap.appendChild(reasoningBody);

    wrap.appendChild(label);
    wrap.appendChild(bubble);
    wrap.appendChild(reasoningWrap);
    msgList.insertBefore(wrap, typing);
    scrollBottom();

    return { bubble: bubble, reasoningBody: reasoningBody, reasoningWrap: reasoningWrap, dot: dot };
  }

  // ── create tool indicator ─────────────────────────────────────
  var TOOL_LABELS = {
    'send_email_to_admin': 'Email an Chef senden',
    'get_order_history':   'Bestellhistorie abrufen',
    'get_product_info':    'Produktinfo suchen',
  };
  function createToolIndicator(name) {
    var el = document.createElement('div');
    el.className = 'pa-tool-indicator';
    var dotEl = document.createElement('span');
    dotEl.className = 'pa-tool-dot';
    el.appendChild(dotEl);
    el.appendChild(document.createTextNode(TOOL_LABELS[name] || name + '…'));
    msgList.insertBefore(el, typing);
    scrollBottom();
    return el;
  }

  // ── setLoading ────────────────────────────────────────────────
  function setLoading(on) {
    sendBtn.disabled = on;
    input.disabled   = on;
    typing.classList.toggle('hidden', !on);
    if (on) scrollBottom();
  }

  // ── main send ────────────────────────────────────────────────
  function sendMessage() {
    var text = input.value.trim();
    if (!text) return;

    appendUser(text);
    history.push({ role: 'user', content: text });
    input.value = '';
    input.style.height = 'auto';
    setLoading(true);

    var formData = new FormData();
    formData.append('action',  'pa_chatbot_stream');
    formData.append('nonce',   nonce);
    formData.append('history', JSON.stringify(history));

    var shell    = null;   // current AI message shell
    var fullText = '';     // accumulated content for history
    var toolEls  = {};     // name → indicator element

    fetch(ajaxUrl, { method: 'POST', body: formData })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        var reader  = res.body.getReader();
        var decoder = new TextDecoder();
        var lineBuf = '';

        function processEvent(json) {
          var ev;
          try { ev = JSON.parse(json); } catch(e) { return; }

          if (ev.type === 'reasoning') {
            if (!shell) { shell = createAiShell(); setLoading(false); }
            shell.reasoningWrap.style.display = '';
            shell.reasoningBody.textContent += ev.chunk;
            shell.reasoningBody.scrollTop = shell.reasoningBody.scrollHeight;
            scrollBottom();
          }
          else if (ev.type === 'content') {
            if (!shell) { shell = createAiShell(); setLoading(false); }
            fullText += ev.chunk;
            shell.bubble.textContent = fullText;
            scrollBottom();
          }
          else if (ev.type === 'tool_start') {
            toolEls[ev.name] = createToolIndicator(ev.name);
          }
          else if (ev.type === 'tool_done') {
            if (toolEls[ev.name]) {
              toolEls[ev.name].classList.add('done');
              toolEls[ev.name].querySelector('.pa-tool-dot').classList.add('done');
            }
          }
          else if (ev.type === 'new_turn') {
            // New AI turn after tool — create fresh shell
            if (shell) {
              shell.bubble.classList.remove('streaming');
              shell.dot.classList.add('done');
            }
            shell    = null;
            fullText = '';
            setLoading(true);
          }
          else if (ev.type === 'done') {
            if (shell) {
              shell.bubble.classList.remove('streaming');
              shell.dot.classList.add('done');
            }
            if (fullText) history.push({ role: 'assistant', content: fullText });
            setLoading(false);
          }
        }

        function read() {
          return reader.read().then(function (r) {
            if (r.done) {
              // Stream ended
              if (shell) shell.bubble.classList.remove('streaming');
              setLoading(false);
              return;
            }
            lineBuf += decoder.decode(r.value, { stream: true });
            var lines = lineBuf.split('\n');
            lineBuf = lines.pop();
            lines.forEach(function (line) {
              line = line.replace(/\r$/, '');
              if (line.indexOf('data: ') === 0) processEvent(line.slice(6));
            });
            return read();
          });
        }
        return read();
      })
      .catch(function (err) {
        setLoading(false);
        if (!shell) shell = createAiShell();
        shell.bubble.classList.remove('streaming');
        shell.bubble.textContent = 'Verbindungsfehler. Bitte versuche es erneut.';
        console.error(err);
      });
  }
}());
</script>

<?php get_footer(); ?>
