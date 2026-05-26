/* ===== CART PAGE LOGIC ===== */
document.addEventListener('DOMContentLoaded', () => {
  if (!document.getElementById('cartItemsList')) return;

  const itemsList    = document.getElementById('cartItemsList');
  const emptyState   = document.getElementById('cartEmpty');
  const cartSection  = document.getElementById('cartSection');
  const orderSection = document.getElementById('orderSection');
  const clearBtn     = document.getElementById('clearCartBtn');
  const submitBtn    = document.getElementById('submitOrderBtn');
  const orderForm    = document.getElementById('orderForm');
  const orderSuccess = document.getElementById('orderSuccess');

  // Summary elements
  const summaryItems    = document.getElementById('summaryItems');
  const summaryDelivery = document.getElementById('summaryDelivery');
  const summaryTotal    = document.getElementById('summaryTotal');

  function renderCart() {
    const items = Cart.getItems();
    if (!items.length) {
      cartSection.style.display  = 'none';
      orderSection.style.display = 'none';
      emptyState.style.display   = 'block';
      return;
    }
    cartSection.style.display  = 'block';
    orderSection.style.display = 'block';
    emptyState.style.display   = 'none';

    itemsList.innerHTML = items.map(item => `
      <div class="cart-item" data-id="${item.id}">
        <div class="cart-item-img">${item.emoji}</div>
        <div class="cart-item-info">
          <h4>${item.name}</h4>
          <p>${item.category}</p>
          <div class="item-price">${formatPrice(item.price * item.qty)}</div>
        </div>
        <div class="qty-control">
          <button class="qty-btn qty-minus" data-id="${item.id}">−</button>
          <span class="qty-value">${item.qty}</span>
          <button class="qty-btn qty-plus" data-id="${item.id}">+</button>
        </div>
        <button class="remove-item" data-id="${item.id}" title="Удалить">✕</button>
      </div>
    `).join('');

    updateSummary(items);
  }

  function updateSummary(items) {
    const subtotal = items.reduce((s, i) => s + i.price * i.qty, 0);
    const delivery = 0;
    if (summaryItems)    summaryItems.textContent    = formatPrice(subtotal);
    if (summaryDelivery) summaryDelivery.textContent = delivery === 0 ? 'Бесплатно' : formatPrice(delivery);
    if (summaryTotal)    summaryTotal.textContent    = formatPrice(subtotal + delivery);
  }

  // Delegated events on cart list
  itemsList.addEventListener('click', e => {
    const id = parseInt(e.target.closest('[data-id]')?.dataset.id);
    if (!id) return;
    if (e.target.closest('.qty-minus')) { Cart.updateQty(id, -1); renderCart(); }
    if (e.target.closest('.qty-plus'))  { Cart.updateQty(id, +1); renderCart(); }
    if (e.target.closest('.remove-item')) { Cart.removeItem(id); renderCart(); }
  });

  clearBtn?.addEventListener('click', () => {
    if (confirm('Очистить корзину?')) { Cart.clear(); renderCart(); }
  });

  // ===== FORM VALIDATION =====
  function validateField(input) {
    const group = input.closest('.form-group');
    const error = group?.querySelector('.form-error');
    let valid = true;
    if (input.required && !input.value.trim()) {
      input.classList.add('error');
      if (error) { error.textContent = 'Обязательное поле'; error.classList.add('show'); }
      valid = false;
    } else if (input.type === 'tel' && input.value && !/^[\d\s\+\-\(\)]{7,15}$/.test(input.value)) {
      input.classList.add('error');
      if (error) { error.textContent = 'Введите корректный телефон'; error.classList.add('show'); }
      valid = false;
    } else if (input.type === 'email' && input.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
      input.classList.add('error');
      if (error) { error.textContent = 'Введите корректный email'; error.classList.add('show'); }
      valid = false;
    } else {
      input.classList.remove('error');
      if (error) error.classList.remove('show');
    }
    return valid;
  }

  orderForm?.querySelectorAll('input, textarea, select').forEach(input => {
    input.addEventListener('blur', () => validateField(input));
    input.addEventListener('input', () => validateField(input));
  });

  // ===== ORDER SUBMIT =====
  orderForm?.addEventListener('submit', async e => {
    e.preventDefault();
    const inputs = orderForm.querySelectorAll('input[required], textarea[required]');
    let valid = true;
    inputs.forEach(inp => { if (!validateField(inp)) valid = false; });
    if (!valid) return;

    const items = Cart.getItems();
    if (!items.length) { alert('Корзина пуста'); return; }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span>⏳</span> Отправляем...';

    const formData = new FormData(orderForm);
    const name    = formData.get('name');
    const phone   = formData.get('phone');
    const email   = formData.get('email') || '—';
    const address = formData.get('address') || '—';
    const comment = formData.get('comment') || '—';
    const total   = formatPrice(Cart.total());

    const itemsText = items.map(i => `${i.name} × ${i.qty} — ${formatPrice(i.price * i.qty)}`).join('\n');

    // EmailJS send
    try {
      const serviceId  = 'YOUR_SERVICE_ID';   // Replace with your EmailJS service ID
      const templateId = 'YOUR_TEMPLATE_ID';  // Replace with your EmailJS template ID
      const publicKey  = 'YOUR_PUBLIC_KEY';   // Replace with your EmailJS public key

      if (serviceId !== 'YOUR_SERVICE_ID') {
        await emailjs.send(serviceId, templateId, {
          from_name: name,
          from_phone: phone,
          from_email: email,
          address,
          comment,
          items: itemsText,
          total,
          order_date: new Date().toLocaleString('ru-RU')
        }, publicKey);
      }
      // Always show success (demo mode if keys not set)
      showSuccess(name);
    } catch (err) {
      console.error('EmailJS error:', err);
      // Even if sending fails, show success to user (save to console)
      showSuccess(name);
    }
  });

  function showSuccess(name) {
    // Save order to history if user is logged in
    const user = typeof Auth !== 'undefined' ? Auth.getCurrentUser() : null;
    if (user) {
      const key = `ki_orders_${user.id}`;
      const orders = JSON.parse(localStorage.getItem(key) || '[]');
      const formData2 = new FormData(orderForm);
      orders.push({
        id: Date.now().toString().slice(-6),
        date: new Date().toLocaleString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }),
        status: 'Новый',
        items: Cart.getItems().map(i => ({ name: i.name, qty: i.qty })),
        total: Cart.total(),
        address: formData2.get('address') || ''
      });
      localStorage.setItem(key, JSON.stringify(orders));
    }

    Cart.clear();
    orderForm.style.display = 'none';
    orderSuccess.classList.add('show');
    const nameEl = document.getElementById('successName');
    if (nameEl) nameEl.textContent = name;
    renderCart();
    window.scrollTo({ top: document.getElementById('orderSection')?.offsetTop - 100, behavior: 'smooth' });
  }

  renderCart();
});
