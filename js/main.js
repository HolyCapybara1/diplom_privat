/* ===== CART STORAGE ===== */
const Cart = {
  _key() {
    const user = typeof Auth !== 'undefined' ? Auth.getCurrentUser() : null;
    return user ? `ki_cart_${user.id}` : 'ki_cart_guest';
  },
  getItems() {
    try { return JSON.parse(localStorage.getItem(Cart._key()) || '[]'); }
    catch { return []; }
  },
  saveItems(items) {
    localStorage.setItem(Cart._key(), JSON.stringify(items));
    Cart.updateUI();
  },
  addItem(product, qty = 1) {
    const items = Cart.getItems();
    const existing = items.find(i => i.id === product.id);
    if (existing) {
      existing.qty += qty;
    } else {
      items.push({ id: product.id, name: product.name, price: product.price, emoji: product.emoji, category: product.categoryLabel, qty });
    }
    Cart.saveItems(items);
    Cart.showToast(`«${product.name}» добавлен в корзину`);
  },
  removeItem(id) {
    Cart.saveItems(Cart.getItems().filter(i => i.id !== id));
  },
  updateQty(id, delta) {
    const items = Cart.getItems();
    const item = items.find(i => i.id === id);
    if (!item) return;
    item.qty = Math.max(1, item.qty + delta);
    Cart.saveItems(items);
  },
  clear() {
    Cart.saveItems([]);
  },
  total() {
    return Cart.getItems().reduce((sum, i) => sum + i.price * i.qty, 0);
  },
  count() {
    return Cart.getItems().reduce((sum, i) => sum + i.qty, 0);
  },
  updateUI() {
    const count = Cart.count();
    document.querySelectorAll('.cart-count').forEach(el => {
      el.textContent = count;
      el.classList.toggle('hidden', count === 0);
    });
    const btn = document.querySelector('.cart-btn');
    if (btn) {
      btn.classList.add('pulse');
      setTimeout(() => btn.classList.remove('pulse'), 300);
    }
  },
  showToast(msg) {
    let toast = document.querySelector('.toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'toast';
      document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toast._t);
    toast._t = setTimeout(() => toast.classList.remove('show'), 3000);
  }
};

/* ===== HEADER / NAV ===== */
function initHeader() {
  // Active nav link
  const path = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav a, .mobile-nav a').forEach(a => {
    const href = a.getAttribute('href');
    if (href === path || (path === '' && href === 'index.html')) a.classList.add('active');
  });

  // Burger menu
  const burger = document.querySelector('.burger');
  const mobileNav = document.querySelector('.mobile-nav');
  if (burger && mobileNav) {
    burger.addEventListener('click', () => mobileNav.classList.add('open'));
    const closeBtn = mobileNav.querySelector('.mobile-nav-close');
    if (closeBtn) closeBtn.addEventListener('click', () => mobileNav.classList.remove('open'));
    mobileNav.querySelectorAll('a').forEach(a => a.addEventListener('click', () => mobileNav.classList.remove('open')));
  }

  Cart.updateUI();
  if (typeof Auth !== 'undefined') Auth.updateHeaderUI();
}

/* ===== FORMAT CURRENCY ===== */
function formatPrice(n) {
  return n.toLocaleString('ru-RU') + ' ₽';
}

/* ===== RENDER PRODUCT CARD ===== */
function renderProductCard(p, inCart = false) {
  const badge = p.badge ? `<span class="product-badge ${p.badgeType || ''}">${p.badge}</span>` : '';
  const specs = p.specs.map(s => `<span class="spec-tag">${s}</span>`).join('');
  const cartItems = Cart.getItems();
  const isInCart = cartItems.some(i => i.id === p.id);
  const btnClass = isInCart ? 'add-to-cart-btn in-cart' : 'add-to-cart-btn';
  const btnText = isInCart ? '✓ В корзине' : 'В корзину';

  return `
    <div class="product-card" data-id="${p.id}">
      <div class="product-img">
        ${badge}
        <span>${p.emoji}</span>
      </div>
      <div class="product-body">
        <div class="product-category">${p.categoryLabel}</div>
        <h3>${p.name}</h3>
        <p>${p.desc}</p>
        <div class="product-specs">${specs}</div>
        <div class="product-footer">
          <div>
            <div class="product-price">${formatPrice(p.price)}</div>
            ${p.oldPrice ? `<div class="product-price-sub" style="text-decoration:line-through;color:var(--gray)">${formatPrice(p.oldPrice)}</div>` : ''}
          </div>
          <button class="${btnClass}" data-id="${p.id}">${btnText}</button>
        </div>
      </div>
    </div>`;
}

/* ===== ATTACH CART BUTTONS ===== */
function attachCartButtons(container) {
  container.addEventListener('click', e => {
    const btn = e.target.closest('.add-to-cart-btn');
    if (!btn) return;
    const id = parseInt(btn.dataset.id);
    const product = PRODUCTS.find(p => p.id === id);
    if (!product) return;
    Cart.addItem(product);
    btn.classList.add('in-cart');
    btn.textContent = '✓ В корзине';
  });
}

document.addEventListener('DOMContentLoaded', initHeader);
