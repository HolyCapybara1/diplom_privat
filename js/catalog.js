/* ===== CATALOG PAGE LOGIC ===== */
document.addEventListener('DOMContentLoaded', () => {
  if (!document.querySelector('.catalog-layout')) return;

  const grid = document.getElementById('productsGrid');
  const countEl = document.getElementById('productsCount');
  const sortSel = document.getElementById('sortSelect');
  const catTabs = document.querySelectorAll('.cat-tab');
  const filtersToggle = document.querySelector('.filters-toggle');
  const filtersSidebar = document.querySelector('.filters-sidebar');
  const filtersReset = document.querySelector('.filters-reset');
  const priceMin = document.getElementById('priceMin');
  const priceMax = document.getElementById('priceMax');
  const brandChecks = document.querySelectorAll('.brand-check');
  const listBtn = document.getElementById('listViewBtn');
  const gridBtn = document.getElementById('gridViewBtn');

  let state = {
    category: 'all',
    sort: 'default',
    priceMin: 0,
    priceMax: Infinity,
    brands: [],
    view: 'grid'
  };

  // Category tabs
  catTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      catTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      state.category = tab.dataset.cat;
      render();
    });
  });

  // Sort
  if (sortSel) sortSel.addEventListener('change', () => { state.sort = sortSel.value; render(); });

  // Price filter
  function applyPriceFilter() {
    state.priceMin = parseInt(priceMin?.value) || 0;
    state.priceMax = parseInt(priceMax?.value) || Infinity;
    render();
  }
  priceMin?.addEventListener('input', applyPriceFilter);
  priceMax?.addEventListener('input', applyPriceFilter);

  // Brand checkboxes
  brandChecks.forEach(cb => {
    cb.addEventListener('change', () => {
      state.brands = Array.from(brandChecks).filter(c => c.checked).map(c => c.value);
      render();
    });
  });

  // View toggle
  listBtn?.addEventListener('click', () => { state.view = 'list'; listBtn.classList.add('active'); gridBtn.classList.remove('active'); grid.classList.add('list-view'); });
  gridBtn?.addEventListener('click', () => { state.view = 'grid'; gridBtn.classList.add('active'); listBtn.classList.remove('active'); grid.classList.remove('list-view'); });

  // Mobile filters
  filtersToggle?.addEventListener('click', () => filtersSidebar?.classList.toggle('open'));

  // Reset filters
  filtersReset?.addEventListener('click', () => {
    state = { category: state.category, sort: 'default', priceMin: 0, priceMax: Infinity, brands: [], view: state.view };
    if (priceMin) priceMin.value = '';
    if (priceMax) priceMax.value = '';
    brandChecks.forEach(cb => cb.checked = false);
    if (sortSel) sortSel.value = 'default';
    render();
  });

  function getFiltered() {
    let items = PRODUCTS;
    if (state.category !== 'all') items = items.filter(p => p.category === state.category);
    if (state.brands.length) items = items.filter(p => state.brands.includes(p.brand));
    items = items.filter(p => p.price >= state.priceMin && p.price <= (state.priceMax || Infinity));
    switch (state.sort) {
      case 'price-asc':  items = [...items].sort((a, b) => a.price - b.price); break;
      case 'price-desc': items = [...items].sort((a, b) => b.price - a.price); break;
      case 'name':       items = [...items].sort((a, b) => a.name.localeCompare(b.name, 'ru')); break;
    }
    return items;
  }

  function render() {
    const items = getFiltered();
    if (countEl) countEl.innerHTML = `Показано <strong>${items.length}</strong> товаров`;
    if (!items.length) {
      grid.innerHTML = `<div class="no-results"><div class="icon">🔍</div><h3>Ничего не найдено</h3><p>Попробуйте изменить параметры фильтра</p></div>`;
      return;
    }
    grid.innerHTML = items.map(p => renderProductCard(p)).join('');
    attachCartButtons(grid);
  }

  render();
});
