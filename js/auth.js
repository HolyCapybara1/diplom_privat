/* ===== AUTH — PHP REST API backed ===== */
const Auth = {
  SESSION_KEY: 'ki_session',
  ADMIN_KEY:   'ki_admin_session',

  /* ---- Synchronous local cache read (used by Cart._key()) ---- */
  getCurrentUser() {
    try { return JSON.parse(localStorage.getItem(Auth.SESSION_KEY) || 'null'); }
    catch { return null; }
  },

  /* ---- Sync session from server (call on page load) ---- */
  async syncSession() {
    try {
      const res  = await fetch('/api/auth/me.php', { credentials: 'include' });
      const json = await res.json();
      if (!json.ok || !json.data) {
        localStorage.removeItem(Auth.SESSION_KEY);
        localStorage.removeItem(Auth.ADMIN_KEY);
        Auth.updateHeaderUI();
        return null;
      }
      if (json.data.is_admin) {
        localStorage.setItem(Auth.ADMIN_KEY, '1');
        localStorage.removeItem(Auth.SESSION_KEY);
        Auth.updateHeaderUI();
        return { is_admin: true };
      }
      localStorage.setItem(Auth.SESSION_KEY, JSON.stringify(json.data));
      localStorage.removeItem(Auth.ADMIN_KEY);
      Auth.updateHeaderUI();
      return json.data;
    } catch (e) {
      console.warn('Auth.syncSession error:', e);
      return null;
    }
  },

  /* ---- Login ---- */
  async login({ email, password }) {
    try {
      const res  = await fetch('/api/auth/login.php', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });
      const json = await res.json();
      if (!json.ok) return { ok: false, error: json.error };

      if (json.data && json.data.is_admin) {
        localStorage.setItem(Auth.ADMIN_KEY, '1');
        localStorage.removeItem(Auth.SESSION_KEY);
        window.location.href = 'admin.html';
        return { ok: true, is_admin: true };
      }

      localStorage.setItem(Auth.SESSION_KEY, JSON.stringify(json.data));
      localStorage.removeItem(Auth.ADMIN_KEY);
      Auth.updateHeaderUI();
      return { ok: true, user: json.data };
    } catch (e) {
      return { ok: false, error: 'Ошибка соединения с сервером' };
    }
  },

  /* ---- Register ---- */
  async register({ name, email, phone, password }) {
    try {
      const res  = await fetch('/api/auth/register.php', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, phone, password }),
      });
      const json = await res.json();
      if (!json.ok) return { ok: false, error: json.error };

      localStorage.setItem(Auth.SESSION_KEY, JSON.stringify(json.data));
      localStorage.removeItem(Auth.ADMIN_KEY);
      Auth.updateHeaderUI();
      return { ok: true, user: json.data };
    } catch (e) {
      return { ok: false, error: 'Ошибка соединения с сервером' };
    }
  },

  /* ---- Logout ---- */
  async logout() {
    try {
      await fetch('/api/auth/logout.php', {
        method: 'POST',
        credentials: 'include',
      });
    } catch (e) {
      console.warn('Logout request failed:', e);
    }
    localStorage.removeItem(Auth.SESSION_KEY);
    localStorage.removeItem(Auth.ADMIN_KEY);
    Auth.updateHeaderUI();
  },

  /* ---- Update profile ---- */
  async updateProfile({ name, phone }) {
    try {
      const res  = await fetch('/api/auth/profile.php', {
        method: 'PUT',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, phone }),
      });
      const json = await res.json();
      if (!json.ok) return { ok: false, error: json.error };

      localStorage.setItem(Auth.SESSION_KEY, JSON.stringify(json.data));
      Auth.updateHeaderUI();
      return { ok: true, user: json.data };
    } catch (e) {
      return { ok: false, error: 'Ошибка соединения с сервером' };
    }
  },

  /* ---- Update header UI ---- */
  updateHeaderUI() {
    const user = Auth.getCurrentUser();
    const authBtnEl  = document.querySelector('.auth-btn');
    const authNameEl = document.querySelector('.auth-user-name');
    if (!authBtnEl) return;
    if (user) {
      authBtnEl.href = 'account.html';
      authBtnEl.title = user.email;
      if (authNameEl) authNameEl.textContent = user.name.split(' ')[0];
      authBtnEl.classList.add('logged-in');
    } else {
      authBtnEl.href = 'account.html';
      if (authNameEl) authNameEl.textContent = 'Войти';
      authBtnEl.classList.remove('logged-in');
    }
  }
};

window.Auth = Auth;
