/* ===== AUTH — localStorage-based auth ===== */
const Auth = {
  USERS_KEY: 'ki_users',
  SESSION_KEY: 'ki_session',

  getUsers() {
    try { return JSON.parse(localStorage.getItem(Auth.USERS_KEY) || '[]'); }
    catch { return []; }
  },

  saveUsers(users) {
    localStorage.setItem(Auth.USERS_KEY, JSON.stringify(users));
  },

  getCurrentUser() {
    try { return JSON.parse(localStorage.getItem(Auth.SESSION_KEY) || 'null'); }
    catch { return null; }
  },

  register({ name, email, phone, password }) {
    const users = Auth.getUsers();
    if (users.find(u => u.email.toLowerCase() === email.toLowerCase())) {
      return { ok: false, error: 'Пользователь с таким email уже зарегистрирован' };
    }
    const user = { id: Date.now().toString(), name, email: email.toLowerCase(), phone, password, createdAt: new Date().toISOString() };
    users.push(user);
    Auth.saveUsers(users);
    Auth._startSession(user);
    return { ok: true, user };
  },

  login({ email, password }) {
    const users = Auth.getUsers();
    const user = users.find(u => u.email.toLowerCase() === email.toLowerCase() && u.password === password);
    if (!user) return { ok: false, error: 'Неверный email или пароль' };
    Auth._startSession(user);
    return { ok: true, user };
  },

  logout() {
    localStorage.removeItem(Auth.SESSION_KEY);
    Auth.updateHeaderUI();
  },

  _startSession(user) {
    const session = { id: user.id, name: user.name, email: user.email, phone: user.phone };
    localStorage.setItem(Auth.SESSION_KEY, JSON.stringify(session));
    Auth.updateHeaderUI();
  },

  updateProfile({ name, phone }) {
    const session = Auth.getCurrentUser();
    if (!session) return;
    const users = Auth.getUsers();
    const user = users.find(u => u.id === session.id);
    if (!user) return;
    user.name = name;
    user.phone = phone;
    Auth.saveUsers(users);
    const updated = { ...session, name, phone };
    localStorage.setItem(Auth.SESSION_KEY, JSON.stringify(updated));
    Auth.updateHeaderUI();
  },

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
