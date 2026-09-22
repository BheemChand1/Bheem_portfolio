const themeButton = document.querySelector('.theme-toggle');
function syncThemeLabel() { if (themeButton) themeButton.setAttribute('aria-label', `Switch to ${document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark'} mode`); }
syncThemeLabel();
themeButton?.addEventListener('click', () => {
    const theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = theme;
    try { localStorage.setItem('theme', theme); } catch (_) {}
    syncThemeLabel();
});
const menu = document.querySelector('.menu-toggle');
menu?.addEventListener('click', () => { const open = menu.getAttribute('aria-expanded') !== 'true'; menu.setAttribute('aria-expanded', String(open)); document.querySelector('#navigation').classList.toggle('open', open); });
document.addEventListener('keydown', event => { if (event.key === 'Escape' && menu?.getAttribute('aria-expanded') === 'true') { menu.setAttribute('aria-expanded', 'false'); document.querySelector('#navigation').classList.remove('open'); menu.focus(); } });
document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => { if (!window.confirm(form.dataset.confirm)) event.preventDefault(); }));

if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && window.matchMedia('(pointer: fine)').matches) {
    document.querySelectorAll('.spotlight-card').forEach(card => {
        card.addEventListener('pointermove', event => {
            const bounds = card.getBoundingClientRect();
            card.style.setProperty('--mouse-x', `${event.clientX - bounds.left}px`);
            card.style.setProperty('--mouse-y', `${event.clientY - bounds.top}px`);
        });
    });
}

const dialog = document.querySelector('#chat-panel');
if (dialog) {
    const launch = document.querySelector('.chat-launch');
    const form = document.querySelector('#chat-form');
    const input = document.querySelector('#chat-message');
    const messages = document.querySelector('#chat-messages');
    const feedback = document.querySelector('#chat-feedback');
    let pending = false;
    let generation = 0;
    let aborter;
    launch.addEventListener('click', () => { dialog.showModal(); launch.setAttribute('aria-expanded', 'true'); input.focus(); });
    document.querySelector('.chat-close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => { launch.setAttribute('aria-expanded', 'false'); launch.focus(); });
    function bubble(text, role) { const item = document.createElement('p'); item.className = `chat-bubble ${role}`; item.textContent = text; messages.append(item); item.scrollIntoView({ block: 'nearest' }); return item; }
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    async function post(url, data, signal) {
        const result = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(data), signal });
        const body = await result.json().catch(() => ({}));
        if (!result.ok) throw new Error(result.status === 419 ? 'Your session expired. Refresh the page and try again.' : result.status === 429 ? 'Too many requests. Please wait before trying again.' : body.message || 'Something went wrong. Please try again later.');
        return body;
    }
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (pending || !input.value.trim()) return;
        const question = input.value.trim(); input.value = ''; pending = true;
        const current = generation;
        form.querySelector('button').disabled = true; feedback.hidden = true;
        bubble(question, 'user'); const loading = bubble('Thinking…', 'assistant');
        aborter = new AbortController();
        const timer = setTimeout(() => aborter?.abort(), 32000);
        try { const result = await post('/chat', { message: question }, aborter.signal); if (current !== generation) return; loading.textContent = result.answer; feedback.hidden = false; }
        catch (error) { if (current !== generation) return; loading.textContent = error.name === 'AbortError' ? 'The request timed out. Please try again.' : error.message; loading.classList.add('error'); }
        finally { clearTimeout(timer); if (current === generation) { pending = false; form.querySelector('button').disabled = false; } }
    });
    document.querySelectorAll('.chat-starters button').forEach(button => button.addEventListener('click', () => { if (!pending) { input.value = button.textContent; form.requestSubmit(); } }));
    document.querySelector('#chat-reset').addEventListener('click', () => { generation++; aborter?.abort(); pending = false; messages.replaceChildren(); input.value = ''; feedback.hidden = true; form.querySelector('button').disabled = false; input.focus(); });
    feedback.querySelectorAll('button').forEach(button => button.addEventListener('click', async () => { feedback.hidden = true; try { const result = await post('/chat/feedback', { rating: button.dataset.rating }); bubble(result.message, 'assistant'); } catch (error) { bubble(error.message, 'error'); } }));
}
