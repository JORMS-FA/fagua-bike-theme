/*!
 * Bicicletería Fagua — Theme JS
 * Vanilla, no deps. Defer-friendly.
 */
(function () {
  'use strict';

  /* ---------------- Header: scrolled state ---------------- */
  var header = document.getElementById('bfHeader');
  if (header) {
    var ticking = false;
    function onScroll() {
      var y = window.scrollY || window.pageYOffset;
      header.classList.toggle('bf-header--scrolled', y > 12);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) {
        window.requestAnimationFrame(onScroll);
        ticking = true;
      }
    }, { passive: true });
    onScroll();
  }

  /* ---------------- Mobile menu ---------------- */
  var menuBtn = document.getElementById('bfMenuToggle');
  var mobileMenu = document.getElementById('bfMobileMenu');
  if (menuBtn && mobileMenu) {
    function setMenu(open) {
      menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      mobileMenu.classList.toggle('is-open', open);
      if (open) {
        mobileMenu.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
      } else {
        setTimeout(function () {
          if (!mobileMenu.classList.contains('is-open')) {
            mobileMenu.setAttribute('hidden', '');
            document.body.style.overflow = '';
          }
        }, 300);
      }
    }
    menuBtn.addEventListener('click', function () {
      var open = menuBtn.getAttribute('aria-expanded') === 'true';
      setMenu(!open);
    });
    mobileMenu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { setMenu(false); });
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menuBtn.getAttribute('aria-expanded') === 'true') {
        setMenu(false);
        menuBtn.focus();
      }
    });
  }

  /* ---------------- Reveal animations ---------------- */
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.05, rootMargin: '0px 0px -10% 0px' });
    document.querySelectorAll('.bf-reveal').forEach(function (el) { io.observe(el); });
  } else {
    document.querySelectorAll('.bf-reveal').forEach(function (el) { el.classList.add('is-in'); });
  }
  setTimeout(function () {
    document.querySelectorAll('.bf-reveal:not(.is-in)').forEach(function (el) { el.classList.add('is-in'); });
  }, 2000);

  /* ---------------- Smooth scroll for in-page anchors ---------------- */
  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href');
      if (id.length > 1) {
        var el = document.querySelector(id);
        if (el) {
          e.preventDefault();
          el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });

  /* ---------------- Single product: gallery thumbs ---------------- */
  var galleryMain = document.getElementById('bfGalleryMain');
  var galleryImg  = document.getElementById('bfGalleryImg');
  var thumbs      = document.querySelectorAll('.bf-product-gallery__thumb');
  thumbs.forEach(function (t) {
    t.addEventListener('click', function () {
      thumbs.forEach(function (x) { x.classList.remove('is-active'); });
      t.classList.add('is-active');
      var src = t.getAttribute('data-thumb-src');
      if (src && galleryImg) { galleryImg.src = src; }
    });
  });

  /* ---------------- Single product: qty buttons ---------------- */
  document.querySelectorAll('.bf-qty').forEach(function (q) {
    var input = q.querySelector('input');
    var minus = q.querySelector('[data-qty-minus]');
    var plus  = q.querySelector('[data-qty-plus]');
    var setVal = function (v) {
      v = Math.max(1, Math.min(99, parseInt(v || 1, 10)));
      input.value = v;
    };
    if (minus) minus.addEventListener('click', function () { setVal(parseInt(input.value, 10) - 1); });
    if (plus)  plus.addEventListener('click',  function () { setVal(parseInt(input.value, 10) + 1); });
  });

  /* ---------------- Single product: tabs ---------------- */
  var tabBtns = document.querySelectorAll('.bf-product-tabs__nav button');
  var tabPanels = document.querySelectorAll('.bf-product-tabs__panel');
  tabBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var key = btn.getAttribute('data-tab');
      tabBtns.forEach(function (b) { b.classList.remove('is-active'); });
      tabPanels.forEach(function (p) { p.classList.remove('is-active'); });
      btn.classList.add('is-active');
      var panel = document.querySelector('.bf-product-tabs__panel[data-panel="' + key + '"]');
      if (panel) panel.classList.add('is-active');
    });
  });

  /* ---------------- Variation pills: rewrite WC selects ---------------- */
  document.querySelectorAll('.variations select').forEach(function (sel) {
    var label = sel.closest('.variations') ? sel.closest('tr') : null;
    if (label && !sel.dataset.bfPilled) {
      sel.dataset.bfPilled = '1';
      // Make selects look like pills
      sel.classList.add('bf-pill-select');
    }
  });

  /* ---------------- Add to cart: subtle animation ---------------- */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('.add_to_cart_button, .single_add_to_cart_button');
    if (!btn) return;
    btn.classList.add('is-loading');
    setTimeout(function () { btn.classList.remove('is-loading'); }, 1500);
  });

  /* ============================================================
     BF DRAWER — comportamiento genérico para Mini Cart, Wishlist, etc.
     - Abre/cierra con `data-drawer-open="cart"` / `data-drawer-close`
     - Toggle por aria-hidden (el CSS hace la animación)
     - Cierra con ESC, backdrop, botón close, o cualquier [data-drawer-close]
     - Lock scroll del body + focus trap
     - Quirúrgico: solo intercepta el link de carrito del HEADER
     ============================================================ */
  (function () {
    var drawerIds = ['cart', 'wishlist', 'compare']; // ids conocidos
    var drawers = {};
    drawerIds.forEach(function (id) {
      var el = document.getElementById('bf-drawer-' + id);
      if (el) drawers[id] = el;
    });

    var lastFocus = null; // para devolver el foco al cerrar

    function getFocusable(root) {
      return Array.prototype.slice.call(root.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
      )).filter(function (el) {
        return el.offsetParent !== null || el === document.activeElement;
      });
    }

    function openDrawer(id) {
      var drawer = drawers[id];
      if (!drawer) return;
      lastFocus = document.activeElement;
      drawer.setAttribute('aria-hidden', 'false');
      document.body.classList.add('bf-drawer-open');
      // Focus al primer elemento focuseable dentro del drawer
      setTimeout(function () {
        var f = getFocusable(drawer)[0];
        if (f) f.focus();
      }, 60);
    }

    function closeDrawer(id) {
      var drawer = drawers[id];
      if (!drawer) return;
      drawer.setAttribute('aria-hidden', 'true');
      // Solo limpiar body lock si NINGÚN drawer está abierto
      var anyOpen = Object.keys(drawers).some(function (k) {
        return drawers[k] && drawers[k].getAttribute('aria-hidden') === 'false';
      });
      if (!anyOpen) document.body.classList.remove('bf-drawer-open');
      if (lastFocus && typeof lastFocus.focus === 'function') {
        lastFocus.focus();
      }
    }

    function closeAll() {
      Object.keys(drawers).forEach(function (id) {
        if (drawers[id]) drawers[id].setAttribute('aria-hidden', 'true');
      });
      document.body.classList.remove('bf-drawer-open');
    }

    function trapFocus(e, drawer) {
      if (e.key !== 'Tab') return;
      var f = getFocusable(drawer);
      if (!f.length) return;
      var first = f[0];
      var last  = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }

    // Apertura: cualquier [data-drawer-open="id"]
    document.addEventListener('click', function (e) {
      var opener = e.target.closest && e.target.closest('[data-drawer-open]');
      if (opener) {
        e.preventDefault();
        openDrawer(opener.getAttribute('data-drawer-open'));
        return;
      }
      // Cierre: cualquier [data-drawer-close] (backdrop, X, link "Seguir comprando")
      var closer = e.target.closest && e.target.closest('[data-drawer-close]');
      if (closer) {
        var d = closer.closest('.bf-drawer');
        if (d && d.id) {
          closeDrawer(d.id.replace('bf-drawer-', ''));
        } else {
          closeAll();
        }
      }
    });

    // ESC + Focus trap
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var openId = Object.keys(drawers).find(function (k) {
          return drawers[k] && drawers[k].getAttribute('aria-hidden') === 'false';
        });
        if (openId) {
          e.preventDefault();
          closeDrawer(openId);
        }
      } else if (e.key === 'Tab') {
        var openDrawerEl = Object.keys(drawers)
          .map(function (k) { return drawers[k]; })
          .find(function (d) { return d && d.getAttribute('aria-hidden') === 'false'; });
        if (openDrawerEl) trapFocus(e, openDrawerEl);
      }
    });

    // ─── HEADER: interceptar SOLO el link de carrito del header ───
    // El link tiene href="/carrito/" o wc_get_cart_url(). Lo identificamos
    // por su contenedor (#bfHeader) + el atributo [data-bf-cart-toggle] que
    // vamos a inyectar desde el header.php (quirúrgico, no por selector global).
    var headerCartLinks = document.querySelectorAll('#bfHeader [data-bf-cart-toggle]');
    headerCartLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        openDrawer('cart');
      });
    });

    // Exponer API mínima para uso externo (otros módulos JS)
    window.bfDrawer = {
      open: openDrawer,
      close: closeDrawer,
      closeAll: closeAll
    };
  })();

  /* ============================================================
     BF TOAST — notificaciones premium
     ============================================================ */
  (function () {
    var container = document.getElementById('bf-toast-container');
    var tmpl      = document.getElementById('bf-toast-template');
    if (!container || !tmpl) return;

    var ICONS = {
      success: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
      error:   '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      info:    '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };

    window.bfToast = function (opts) {
      opts = opts || {};
      var type    = opts.type || 'info';
      var title   = opts.title || (window.bfData && bfData.i18n && bfData.i18n.added) || 'Aviso';
      var message = opts.message || '';
      var timeout = opts.timeout != null ? opts.timeout : 4000;

      var node = tmpl.content.firstElementChild.cloneNode(true);
      node.classList.add('bf-toast--' + type);
      node.querySelector('[data-toast-icon]').innerHTML = ICONS[type] || ICONS.info;
      node.querySelector('[data-toast-title]').textContent = title;
      node.querySelector('[data-toast-message]').textContent = message;

      container.appendChild(node);
      // Forzar reflow para que la animación corra
      void node.offsetWidth;
      node.classList.add('is-visible');

      var close = function () {
        node.classList.remove('is-visible');
        node.classList.add('is-leaving');
        setTimeout(function () { if (node.parentNode) node.parentNode.removeChild(node); }, 400);
      };
      node.querySelector('[data-toast-close]').addEventListener('click', close);
      if (timeout > 0) setTimeout(close, timeout);
      return close;
    };
  })();

  /* ============================================================
     BF CART — Add to Cart AJAX, refresh de drawer, contador
     ============================================================ */
  (function () {
    if (!window.bfData) return;
    var D       = window.bfData;
    var ajaxUrl = D.ajaxUrl;
    var nonce   = D.nonce;
    if (!ajaxUrl || !nonce) return;

    var $ = function (s, c) { return (c || document).querySelector(s); };
    var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

    function setCount(n, bump) {
      $$('.bf-cart-count').forEach(function (el) {
        el.textContent = n;
        el.setAttribute('data-count', n);
        if (bump) {
          el.classList.add('is-bump');
          setTimeout(function () { el.classList.remove('is-bump'); }, 400);
        }
      });
    }

    function applyFragments(fragments) {
      if (!fragments) return;
      Object.keys(fragments).forEach(function (sel) {
        $$(sel).forEach(function (el) {
          var tmp = document.createElement('div');
          tmp.innerHTML = fragments[sel];
          var fresh = tmp.firstElementChild;
          if (fresh) el.replaceWith(fresh);
        });
      });
    }

    function refreshDrawer(resp) {
      var drawer = document.getElementById('bf-drawer-cart');
      if (!drawer) return;
      var body   = drawer.querySelector('.bf-drawer__body');
      var footer = drawer.querySelector('.bf-drawer__footer');
      if (body && resp.items_html) body.innerHTML = resp.items_html;
      if (footer) {
        var subEl   = footer.querySelector('[data-cart-subtotal]');
        var totEl   = footer.querySelector('[data-cart-total]');
        var sumEl   = footer.querySelector('[data-cart-summary]');
        if (subEl) subEl.innerHTML = resp.total_html;
        if (totEl) totEl.innerHTML = resp.total_html;
        if (sumEl) sumEl.hidden = resp.count === 0;
      }
    }

    function postCart(action, extra) {
      var fd = new FormData();
      fd.append('action', action);
      fd.append('nonce', nonce);
      if (extra) Object.keys(extra).forEach(function (k) { fd.append(k, extra[k]); });
      return fetch(ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) {
          if (!r.ok) throw new Error('HTTP ' + r.status);
          return r.json();
        })
        .then(function (j) {
          if (!j || !j.success) throw new Error((j && j.data && j.data.message) || 'error');
          return j.data;
        });
    }

    function flyToCart(srcEl, targetEl) {
      if (!srcEl || !targetEl) return;
      var s = srcEl.getBoundingClientRect();
      var t = targetEl.getBoundingClientRect();
      var ghost = document.createElement('div');
      ghost.className = 'bf-fly-img';
      ghost.style.left   = s.left  + 'px';
      ghost.style.top    = s.top   + 'px';
      ghost.style.width  = s.width + 'px';
      ghost.style.height = s.height + 'px';
      // Clonar el contenido si es un <img> o el background del contenedor
      if (srcEl.tagName === 'IMG') {
        var img = document.createElement('img');
        img.src = srcEl.src;
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        ghost.appendChild(img);
      } else {
        // Captura rápida con html2canvas-like? No — usar background
        var cs = getComputedStyle(srcEl);
        if (cs.backgroundImage && cs.backgroundImage !== 'none') {
          ghost.style.backgroundImage = cs.backgroundImage;
          ghost.style.backgroundSize = 'cover';
          ghost.style.backgroundPosition = 'center';
        } else {
          ghost.style.background = 'var(--bf-dark-3, #1c1b1b)';
        }
      }
      document.body.appendChild(ghost);
      // Forzar reflow
      void ghost.offsetWidth;
      var dx = t.left + t.width / 2 - (s.left + s.width / 2);
      var dy = t.top  + t.height / 2 - (s.top  + s.height / 2);
      var scale = Math.max(0.1, 0.18);
      ghost.style.transform = 'translate(' + dx + 'px,' + dy + 'px) scale(' + scale + ')';
      ghost.style.opacity = '0.4';
      setTimeout(function () { if (ghost.parentNode) ghost.parentNode.removeChild(ghost); }, 900);
    }

    // ─── ADD TO CART: interceptar .add_to_cart_button y .single_add_to_cart_button ───
    function getProductIdFromForm(form) {
      if (!form) return 0;
      var inp = form.querySelector('input[name="add-to-cart"]') || form.querySelector('input[name="product_id"]');
      if (inp) return parseInt(inp.value, 10) || 0;
      // fallback: del botón
      var btn = form.querySelector('[data-product_id]');
      if (btn) return parseInt(btn.getAttribute('data-product_id'), 10) || 0;
      return 0;
    }

    function getQtyFromForm(form) {
      if (!form) return 1;
      var q = form.querySelector('input[name="quantity"]');
      return q ? Math.max(1, parseInt(q.value, 10) || 1) : 1;
    }

    function addToCartAjax(btn) {
      var form    = btn.closest('form.cart') || btn.form || null;
      var pid     = parseInt(btn.getAttribute('data-product_id'), 10) || getProductIdFromForm(form);
      var qty     = getQtyFromForm(form);
      if (!pid) return false;
      // Loading state
      var origHtml = btn.innerHTML;
      btn.classList.add('is-loading');
      btn.disabled  = true;
      btn.innerHTML = '<span class="bf-btn__spinner" aria-hidden="true"></span><span> ' + (window.bfData.i18n.loading || 'Cargando…') + '</span>';
      // Fly-to-cart: imagen del producto
      var flySrc = null;
      if (form) {
        var galImg = form.closest('.bf-product') || document;
        flySrc = galImg.querySelector('img');
        // Para cards de shop: buscar la imagen del card contenedor
        if (!flySrc || !flySrc.src) {
          var card = btn.closest('li.product') || btn.closest('.bf-product') || btn.closest('li');
          if (card) flySrc = card.querySelector('img');
        }
      }
      var cartIcon = document.querySelector('#bfHeader .bf-cart-btn');
      if (flySrc && cartIcon) flyToCart(flySrc, cartIcon);
      // AJAX
      var fd = new FormData();
      fd.append('action',       'woocommerce_add_to_cart');
      fd.append('nonce',        nonce);
      fd.append('product_id',   pid);
      fd.append('quantity',     qty);
      fetch(ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          // Refrescar el drawer
          return postCart('bf_get_cart').then(function (cart) {
            setCount(cart.count, true);
            applyFragments(cart.fragments);
            refreshDrawer(cart);
            if (window.bfDrawer) window.bfDrawer.open('cart');
            if (window.bfToast) {
              window.bfToast({
                type: 'success',
                title: window.bfData.i18n.addedTo || 'Añadido al carrito',
                message: window.bfData.i18n.added || 'Producto añadido'
              });
            }
          });
        })
        .catch(function (err) {
          if (window.bfToast) {
            window.bfToast({
              type: 'error',
              title: 'Error',
              message: window.bfData.i18n.error || 'Algo salió mal'
            });
          }
        })
        .finally(function () {
          btn.classList.remove('is-loading');
          btn.disabled  = false;
          btn.innerHTML = origHtml;
        });
      return true;
    }

    // Interceptar
    document.addEventListener('click', function (e) {
      var btn = e.target.closest && e.target.closest('.add_to_cart_button, .single_add_to_cart_button');
      if (!btn) return;
      // Solo interceptar si no es un link (add_to_cart puede ser <a> en archive)
      e.preventDefault();
      if (!addToCartAjax(btn)) {
        // Si no pudimos resolver product_id, dejar que WC haga lo suyo
        return;
      }
    });

    // Interceptar +/- de cantidad en el drawer
    document.addEventListener('click', function (e) {
      var minus = e.target.closest && e.target.closest('[data-cart-qty-minus]');
      var plus  = e.target.closest && e.target.closest('[data-cart-qty-plus]');
      if (!minus && !plus) return;
      var qtyBox = (minus || plus).closest('[data-cart-qty]');
      if (!qtyBox) return;
      var input = qtyBox.querySelector('[data-cart-qty-input]');
      var item  = qtyBox.closest('[data-cart-item]');
      if (!input || !item) return;
      var key = item.getAttribute('data-key');
      var v   = parseInt(input.value, 10) || 1;
      v = plus ? v + 1 : v - 1;
      if (v < 1) v = 1;
      // Optimistic update
      input.value = v;
      postCart('bf_update_qty', { cart_key: key, qty: v }).then(function (cart) {
        setCount(cart.count);
        applyFragments(cart.fragments);
        refreshDrawer(cart);
      }).catch(function () {
        // Rollback
        input.value = parseInt(input.value, 10) - (plus ? 1 : -1);
      });
    });

    // Interceptar remove
    document.addEventListener('click', function (e) {
      var rm = e.target.closest && e.target.closest('[data-cart-remove]');
      if (!rm) return;
      var key = rm.getAttribute('data-cart-remove');
      if (!key) return;
      // Optimistic: ocultar el item
      var item = rm.closest('[data-cart-item]');
      if (item) item.style.opacity = '0.4';
      postCart('bf_remove_item', { cart_key: key }).then(function (cart) {
        setCount(cart.count);
        applyFragments(cart.fragments);
        refreshDrawer(cart);
        if (window.bfToast && cart.count === 0) {
          window.bfToast({ type: 'info', title: 'Carrito vacío', message: '', timeout: 2500 });
        }
      }).catch(function () {
        if (item) item.style.opacity = '1';
      });
    });

    // Cargar carrito al inicio (para que el drawer tenga el estado actual)
    postCart('bf_get_cart').then(function (cart) {
      setCount(cart.count);
      applyFragments(cart.fragments);
      refreshDrawer(cart);
    }).catch(function () { /* falla silenciosa al cargar */ });
  })();

  /* ============================================================
     BF FILTERS — Filtros AJAX (categoría, marca, precio, stock)
     - Sin recargas
     - URL sync con query params
     - Chips de filtros activos
     - Skeleton + fade en transición
     - Compatible con shop toolbar (orden, paginación)
     ============================================================ */
  (function () {
    if (!window.bfData) return;
    var D       = window.bfData;
    var ajaxUrl = D.ajaxUrl;
    var nonce   = D.nonce;
    if (!ajaxUrl || !nonce) return;
    if (!document.querySelector('[data-bf-filters]')) return;

    var filters   = document.querySelector('[data-bf-filters]');
    var main      = document.querySelector('.bf-shop__main');
    var grid      = main ? main.querySelector('.products') : null;
    var toolbar   = document.querySelector('.bf-shop__toolbar');
    var count     = document.querySelector('.bf-shop__count');
    var pagination = document.querySelector('.bf-shop__pagination');
    var chipsWrap = document.querySelector('[data-filter-chips]');

    var state = {
      cats:   [],
      marcas: [],
      price:  { min: 0, max: 0 }, // 0,0 = cualquiera
      stock:  0,
      orderby: '',
      paged:  1,
    };

    function readState() {
      // Categoría (single selection, de momento)
      var catBtn = filters.querySelector('[data-filter-cat].is-active');
      var catVal = catBtn ? parseInt(catBtn.getAttribute('data-filter-cat'), 10) || 0 : 0;
      state.cats = catVal ? [catVal] : [];

      // Marcas (multi)
      state.marcas = [];
      filters.querySelectorAll('[data-filter-marca]:checked').forEach(function (cb) {
        state.marcas.push(cb.value);
      });

      // Precio
      var priceBtn = filters.querySelector('[data-filter-price].is-active');
      if (priceBtn) {
        var p = priceBtn.getAttribute('data-filter-price').split(',');
        state.price.min = parseInt(p[0], 10) || 0;
        state.price.max = parseInt(p[1], 10) || 0;
      }

      // Stock
      var stockCb = filters.querySelector('[data-filter-stock]');
      state.stock = stockCb && stockCb.checked ? 1 : 0;

      // Orderby
      var orderEl = document.querySelector('select[name="orderby"]');
      if (orderEl) state.orderby = orderEl.value;

      // Paged
      state.paged = 1;
    }

    function syncUrl() {
      var url = new URL(window.location.href);
      var params = url.searchParams;
      params.delete('cat'); params.delete('marca'); params.delete('min_price'); params.delete('max_price');
      params.delete('in_stock'); params.delete('orderby'); params.delete('paged');
      state.cats.forEach(function (c) { params.append('cat', c); });
      state.marcas.forEach(function (m) { params.append('marca', m); });
      if (state.price.min) params.set('min_price', state.price.min);
      if (state.price.max) params.set('max_price', state.price.max);
      if (state.stock) params.set('in_stock', '1');
      if (state.orderby) params.set('orderby', state.orderby);
      window.history.replaceState(null, '', url.toString());
    }

    function renderChips() {
      if (!chipsWrap) return;
      var chips = [];
      state.cats.forEach(function (id) {
        var btn = filters.querySelector('[data-filter-cat="' + id + '"]');
        if (btn) chips.push({ label: btn.textContent.trim().split(/\s+/)[0], key: 'cat', value: id });
      });
      state.marcas.forEach(function (slug) {
        var cb = filters.querySelector('[data-filter-marca="' + slug + '"]');
        if (cb) {
          var lab = cb.closest('label').querySelector('.bf-filters__check-text');
          chips.push({ label: lab ? lab.textContent : slug, key: 'marca', value: slug });
        }
      });
      if (state.price.min || state.price.max) {
        var pBtn = filters.querySelector('[data-filter-price].is-active');
        if (pBtn) chips.push({ label: pBtn.textContent.trim(), key: 'price', value: pBtn.getAttribute('data-filter-price') });
      }
      if (state.stock) chips.push({ label: 'En stock', key: 'stock', value: 1 });

      if (!chips.length) { chipsWrap.hidden = true; chipsWrap.innerHTML = ''; return; }
      chipsWrap.hidden = false;
      chipsWrap.innerHTML = '<span class="bf-filter-chips__label">Filtros activos:</span>' +
        chips.map(function (c) {
          return '<button type="button" class="bf-filter-chip" data-chip-key="' + c.key + '" data-chip-value="' + String(c.value).replace(/"/g, '&quot;') + '">' +
            '<span>' + c.label + '</span>' +
            '<svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
          '</button>';
        }).join('');
    }

    function buildUrl() {
      var url = ajaxUrl + '?action=bf_filter&nonce=' + encodeURIComponent(nonce);
      state.cats.forEach(function (c) { url += '&cat[]=' + c; });
      state.marcas.forEach(function (m) { url += '&marca[]=' + encodeURIComponent(m); });
      if (state.price.min) url += '&min_price=' + state.price.min;
      if (state.price.max) url += '&max_price=' + state.price.max;
      if (state.stock) url += '&in_stock=1';
      if (state.orderby) url += '&orderby=' + encodeURIComponent(state.orderby);
      url += '&paged=' + state.paged;
      return url;
    }

    function apply(animate) {
      readState();
      syncUrl();
      renderChips();
      if (!grid) return;
      // Loading overlay
      if (animate && main) {
        var ov = document.createElement('div');
        ov.className = 'bf-loading-overlay is-visible';
        ov.innerHTML = '<div class="bf-btn__spinner" style="width:28px;height:28px;border-width:2.5px;border-color:var(--bf-blue) var(--bf-blue) var(--bf-blue) transparent"></div>';
        main.appendChild(ov);
        grid.style.opacity = '0.4';
        grid.style.transition = 'opacity .2s ease';
        setTimeout(function () { if (ov.parentNode) ov.parentNode.removeChild(ov); grid.style.opacity = '1'; }, 800);
      }
      fetch(buildUrl(), { credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (!j || !j.success) throw new Error('no success');
          var data = j.data;
          if (grid) grid.innerHTML = data.html;
          if (count) {
            var total = data.count;
            count.setAttribute('data-count', total);
            count.innerHTML = (total === 1 ? '1 producto' : total + ' productos').replace(/(\d+)/, '<strong>$1</strong>');
          }
          if (pagination) {
            if (data.pagination && data.pagination.length) {
              pagination.innerHTML = data.pagination.map(function (a) { return a; }).join(' ');
            } else {
              pagination.innerHTML = '';
            }
          }
          // Scroll suave al top del grid
          if (main) main.scrollIntoView({ behavior: 'smooth', block: 'start' });
          if (window.bfToast) {
            window.bfToast({ type: 'info', title: data.count + ' productos', message: '', timeout: 1800 });
          }
        })
        .catch(function () {
          if (window.bfToast) window.bfToast({ type: 'error', title: 'Error al filtrar', message: '' });
        });
    }

    // Categoría (single)
    filters.addEventListener('click', function (e) {
      var catBtn = e.target.closest('[data-filter-cat]');
      if (catBtn) {
        e.preventDefault();
        filters.querySelectorAll('[data-filter-cat]').forEach(function (b) { b.classList.remove('is-active'); });
        catBtn.classList.add('is-active');
        apply(true);
        return;
      }
      var priceBtn = e.target.closest('[data-filter-price]');
      if (priceBtn) {
        e.preventDefault();
        filters.querySelectorAll('[data-filter-price]').forEach(function (b) { b.classList.remove('is-active'); });
        priceBtn.classList.add('is-active');
        apply(true);
        return;
      }
      // Chip remove
      var chip = e.target.closest('[data-chip-key]');
      if (chip) {
        e.preventDefault();
        var k = chip.getAttribute('data-chip-key');
        var v = chip.getAttribute('data-chip-value');
        if (k === 'cat') {
          filters.querySelectorAll('[data-filter-cat]').forEach(function (b) { b.classList.remove('is-active'); });
          var def = filters.querySelector('[data-filter-cat="0"]');
          if (def) def.classList.add('is-active');
        } else if (k === 'marca') {
          var cb = filters.querySelector('[data-filter-marca="' + v + '"]');
          if (cb) cb.checked = false;
        } else if (k === 'price') {
          filters.querySelectorAll('[data-filter-price]').forEach(function (b) { b.classList.remove('is-active'); });
          var defP = filters.querySelector('[data-filter-price="0,0"]');
          if (defP) defP.classList.add('is-active');
        } else if (k === 'stock') {
          var scb = filters.querySelector('[data-filter-stock]');
          if (scb) scb.checked = false;
        }
        apply(true);
        return;
      }
      // Reset
      var reset = e.target.closest('[data-filter-reset]');
      if (reset) {
        e.preventDefault();
        filters.querySelectorAll('[data-filter-cat]').forEach(function (b) { b.classList.remove('is-active'); });
        var defC = filters.querySelector('[data-filter-cat="0"]'); if (defC) defC.classList.add('is-active');
        filters.querySelectorAll('[data-filter-marca]').forEach(function (cb) { cb.checked = false; });
        filters.querySelectorAll('[data-filter-price]').forEach(function (b) { b.classList.remove('is-active'); });
        var defP2 = filters.querySelector('[data-filter-price="0,0"]'); if (defP2) defP2.classList.add('is-active');
        var stockC = filters.querySelector('[data-filter-stock]'); if (stockC) stockC.checked = false;
        apply(true);
      }
    });

    // Marca (multi, change) y Stock
    filters.addEventListener('change', function (e) {
      if (e.target.matches('[data-filter-marca], [data-filter-stock]')) {
        apply(true);
      }
    });

    // WC ordering select
    document.addEventListener('change', function (e) {
      if (e.target && e.target.name === 'orderby') {
        e.preventDefault();
        state.orderby = e.target.value;
        apply(true);
      }
    });

    // Inicializar desde URL (deep-linking)
    (function initFromUrl() {
      var p = new URLSearchParams(window.location.search);
      var cat = parseInt(p.get('cat') || '0', 10);
      if (cat) {
        var btn = filters.querySelector('[data-filter-cat="' + cat + '"]');
        if (btn) {
          filters.querySelectorAll('[data-filter-cat]').forEach(function (b) { b.classList.remove('is-active'); });
          btn.classList.add('is-active');
        }
      }
      var ms = p.getAll('marca');
      ms.forEach(function (m) {
        var cb = filters.querySelector('[data-filter-marca="' + m + '"]');
        if (cb) cb.checked = true;
      });
      var minP = parseFloat(p.get('min_price') || '0');
      var maxP = parseFloat(p.get('max_price') || '0');
      if (minP || maxP) {
        var pbtn = filters.querySelector('[data-filter-price="' + minP + ',' + maxP + '"]');
        if (pbtn) {
          filters.querySelectorAll('[data-filter-price]').forEach(function (b) { b.classList.remove('is-active'); });
          pbtn.classList.add('is-active');
        }
      }
      if (p.get('in_stock') === '1') {
        var sc = filters.querySelector('[data-filter-stock]');
        if (sc) sc.checked = true;
      }
      if (p.get('orderby')) {
        var sel = document.querySelector('select[name="orderby"]');
        if (sel) sel.value = p.get('orderby');
      }
    })();
  })();

  /* ============================================================
     BF SEARCH — Búsqueda instantánea premium
     - Abre con click en [data-search-toggle] o tecla "/"
     - Debounce 200ms
     - Navegación por teclado: ↑↓ Enter ESC
     - Resalta término en el nombre
     - Sugerencias (categorías) en estado vacío
     - Atajo "Ver todos" → /?s=...&post_type=product
     ============================================================ */
  (function () {
    if (!window.bfData) return;
    var D        = window.bfData;
    var ajaxUrl  = D.ajaxUrl;
    var nonce    = D.nonce;
    var minChars = 2;
    var debounce = 200;

    var root  = document.getElementById('bf-search');
    if (!root) return;
    var input  = root.querySelector('[data-search-input]');
    var body   = root.querySelector('[data-search-body]');
    var hint   = root.querySelector('[data-search-hint]');
    var list   = root.querySelector('[data-search-results]');
    var empty  = root.querySelector('[data-search-empty]');
    var skel   = root.querySelector('[data-search-skeleton]');
    var foot   = root.querySelector('[data-search-foot]');
    var viewall= root.querySelector('[data-search-viewall]');
    var clear  = root.querySelector('[data-search-clear]');

    var lastFocus = null;
    var activeIdx = -1;
    var items     = [];
    var currentTerm = '';
    var xhr       = null;
    var debounceTimer = null;

    function setState(st) {
      // st: 'idle' | 'loading' | 'results' | 'empty'
      if (hint)  hint.hidden  = (st !== 'idle');
      if (skel)  skel.hidden  = (st !== 'loading');
      if (list)  list.hidden  = (st !== 'results');
      if (empty) empty.hidden = (st !== 'empty');
      if (foot)  foot.hidden  = (st !== 'results' && st !== 'empty');
      if (body)  body.setAttribute('aria-busy', st === 'loading' ? 'true' : 'false');
    }

    function escapeHtml(s) {
      return String(s).replace(/[&<>"']/g, function (c) {
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
      });
    }

    function highlight(name, term) {
      if (!term) return escapeHtml(name);
      var t = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      var re = new RegExp('(' + t + ')', 'ig');
      return escapeHtml(name).replace(re, '<mark>$1</mark>');
    }

    function open() {
      lastFocus = document.activeElement;
      root.setAttribute('aria-hidden', 'false');
      document.body.classList.add('bf-drawer-open');
      setState('idle');
      setTimeout(function () { input && input.focus(); }, 80);
    }

    function close() {
      root.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('bf-drawer-open');
      if (input) input.value = '';
      if (clear) clear.hidden = true;
      activeIdx = -1;
      items = [];
      currentTerm = '';
      setState('idle');
      if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
    }

    function setActive(i) {
      var nodes = list ? list.querySelectorAll('.bf-search__result') : [];
      activeIdx = i;
      nodes.forEach(function (n, idx) { n.classList.toggle('is-active', idx === i); });
      if (i >= 0 && nodes[i] && nodes[i].scrollIntoView) {
        nodes[i].scrollIntoView({ block: 'nearest' });
      }
    }

    function render(itemsIn, term, total, viewallUrl) {
      items = itemsIn || [];
      activeIdx = -1;
      if (viewall && viewallUrl) viewall.setAttribute('href', viewallUrl);
      if (!items.length) {
        var emptyTitle = root.querySelector('[data-search-empty-title]');
        var emptyText  = root.querySelector('[data-search-empty-text]');
        if (emptyTitle) emptyTitle.textContent = 'Sin resultados';
        if (emptyText)  emptyText.textContent  = 'No encontramos productos con «' + term + '». Probá con otra palabra.';
        setState('empty');
        return;
      }
      var html = items.map(function (p, i) {
        var priceBlock = p.on_sale && p.regular > 0
          ? '<del>' + escapeHtml(p.regular.toLocaleString('es-CO')) + '</del>' + escapeHtml(p.price.toLocaleString('es-CO'))
          : escapeHtml(p.price.toLocaleString('es-CO'));
        return '<li>' +
                 '<a href="' + escapeHtml(p.url) + '" class="bf-search__result" data-result-index="' + i + '">' +
                   '<div class="bf-search__result-thumb">' + p.thumb + '</div>' +
                   '<div class="bf-search__result-info">' +
                     (p.category ? '<span class="bf-search__result-cat">' + escapeHtml(p.category) + '</span>' : '') +
                     '<p class="bf-search__result-name">' + highlight(p.name, term) + '</p>' +
                   '</div>' +
                   '<div class="bf-search__result-price">' + priceBlock + '</div>' +
                 '</a>' +
               '</li>';
      }).join('');
      list.innerHTML = html;
      if (foot) {
        var totalText = total > items.length ? total + ' resultados' : items.length + ' resultados';
        foot.firstElementChild && (foot.firstElementChild.textContent = '');
        var counter = foot.querySelector('.bf-search__foot-count');
        if (!counter) {
          counter = document.createElement('span');
          counter.className = 'bf-search__foot-count';
          foot.insertBefore(counter, foot.firstChild);
        }
        counter.textContent = totalText;
      }
      setState('results');
    }

    function search(term) {
      if (xhr && xhr.abort) xhr.abort();
      if (!term || term.length < minChars) {
        setState('idle');
        items = []; activeIdx = -1;
        return;
      }
      setState('loading');
      var url = ajaxUrl + '?action=bf_search&nonce=' + encodeURIComponent(nonce) + '&q=' + encodeURIComponent(term);
      xhr = fetch(url, { credentials: 'same-origin' })
        .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function (j) {
          if (!j || !j.success) throw new Error((j && j.data && j.data.message) || 'error');
          currentTerm = term;
          render(j.data.items || [], term, j.data.total || 0, j.data.viewall || '');
        })
        .catch(function (err) {
          if (err && err.name === 'AbortError') return;
          setState('empty');
          var et = root.querySelector('[data-search-empty-title]');
          var et2 = root.querySelector('[data-search-empty-text]');
          if (et) et.textContent = 'Error';
          if (et2) et2.textContent = 'No pudimos buscar. Intenta de nuevo.';
        });
    }

    function debouncedSearch(term) {
      if (debounceTimer) clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () { search(term); }, debounce);
    }

    // Apertura: click en cualquier [data-search-toggle]
    document.addEventListener('click', function (e) {
      var btn = e.target.closest && e.target.closest('[data-search-toggle]');
      if (btn) {
        e.preventDefault();
        open();
      }
      var cl = e.target.closest && e.target.closest('[data-search-close]');
      if (cl) { e.preventDefault(); close(); }
    });

    // Sugerencia → cargar resultados de esa categoría
    document.addEventListener('click', function (e) {
      var sug = e.target.closest && e.target.closest('[data-search-suggestion]');
      if (sug) {
        e.preventDefault();
        var label = sug.getAttribute('data-search-suggestion');
        if (input) input.value = label;
        if (clear) clear.hidden = false;
        search(label);
      }
    });

    // Clear input
    if (clear) {
      clear.addEventListener('click', function () {
        if (input) { input.value = ''; input.focus(); }
        clear.hidden = true;
        setState('idle');
        items = []; activeIdx = -1;
      });
    }

    // Input
    if (input) {
      input.addEventListener('input', function () {
        var v = input.value.trim();
        if (clear) clear.hidden = v.length === 0;
        debouncedSearch(v);
      });
      input.addEventListener('keydown', function (e) {
        var k = e.key;
        if (k === 'Escape') { e.preventDefault(); close(); return; }
        if (k === 'ArrowDown') {
          e.preventDefault();
          if (items.length === 0) return;
          setActive(Math.min(activeIdx + 1, items.length - 1));
        } else if (k === 'ArrowUp') {
          e.preventDefault();
          if (items.length === 0) return;
          setActive(Math.max(activeIdx - 1, 0));
        } else if (k === 'Enter') {
          if (activeIdx >= 0 && items[activeIdx]) {
            e.preventDefault();
            window.location.href = items[activeIdx].url;
          } else if (currentTerm) {
            e.preventDefault();
            window.location.href = ajaxUrl + '?s=' + encodeURIComponent(currentTerm); // fallback
            // o el viewall link:
            if (viewall && viewall.href) window.location.href = viewall.href;
          }
        }
      });
    }

    // Atajo global: "/" enfoca búsqueda, "Esc" cierra
    document.addEventListener('keydown', function (e) {
      var isOpen = root.getAttribute('aria-hidden') === 'false';
      if (e.key === 'Escape' && isOpen) { e.preventDefault(); close(); return; }
      // "/" solo si NO estamos escribiendo en un input/textarea
      if (e.key === '/' && !isOpen) {
        var t = e.target;
        var tag = t && t.tagName ? t.tagName.toLowerCase() : '';
        var editable = t && (t.isContentEditable || tag === 'input' || tag === 'textarea' || tag === 'select');
        if (!editable) { e.preventDefault(); open(); }
      }
    });
  })();
})();
