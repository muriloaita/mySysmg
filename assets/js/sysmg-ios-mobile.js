/**
 * ============================================================================
 * SYSMG — MÓDULO JS MOBILE iOS-FIRST (PWA)
 * Companion do arquivo assets/css/sysmg-ios-mobile.css
 *
 * Responsabilidades:
 *   1. Auto-rotular <td> das tabelas .tabela-responsiva-ios (data-label)
 *   2. API pública window.SysMGMobile.openSheet / closeSheet (Bottom Sheet)
 *   3. Marcar automaticamente a aba ativa na Bottom Tab Bar via ?modulo=
 *   4. Fechar a gaveta por: toque fora, swipe para baixo, botão voltar
 *   5. Preservar o parâmetro ?origem=pwa nos links de navegação interna
 *
 * REGRA DE OURO: no PC (>= 769px) este script fica dormente — todas as
 * interações mobile só são anexadas quando a mídia bate (max-width:768px)
 * ou quando um elemento mobile é efetivamente encontrado no DOM.
 * ============================================================================
 */
(function () {
  'use strict';

  var MOBILE_QUERY = '(max-width: 768px)';
  var isMobile = function () {
    return window.matchMedia(MOBILE_QUERY).matches;
  };

  /* ========================================================================
   * 1. AUTO-ROTULAÇÃO DE TABELAS
   *    Para cada <table.tabela-responsiva-ios>, lê os <th> do <thead> e
   *    aplica em cada <td> correspondente o atributo data-label com o nome
   *    da coluna. Sem isso o CSS mobile exibe apenas o valor, sem rótulo.
   * ====================================================================== */
  function rotularTabela(tabela) {
    if (!tabela || tabela.__sysmgRotulada) return;
    var cabecalhos = tabela.querySelectorAll('thead th');
    if (!cabecalhos.length) return;
    var rotulos = Array.prototype.map.call(cabecalhos, function (th) {
      return (th.textContent || '').trim();
    });
    var linhas = tabela.querySelectorAll('tbody tr');
    Array.prototype.forEach.call(linhas, function (tr) {
      var celulas = tr.querySelectorAll('td');
      Array.prototype.forEach.call(celulas, function (td, i) {
        if (rotulos[i] && !td.hasAttribute('data-label')) {
          td.setAttribute('data-label', rotulos[i]);
        }
      });
    });
    tabela.__sysmgRotulada = true;
  }

  function rotularTabelasExistentes() {
    var tabelas = document.querySelectorAll('table.tabela-responsiva-ios');
    Array.prototype.forEach.call(tabelas, rotularTabela);
  }

  /* Observa adições dinâmicas (AJAX, PHP imprimindo linhas depois do load). */
  function observarMutacoes() {
    if (typeof MutationObserver === 'undefined') return;
    var mo = new MutationObserver(function (muts) {
      muts.forEach(function (m) {
        Array.prototype.forEach.call(m.addedNodes || [], function (no) {
          if (no.nodeType !== 1) return;
          if (no.matches && no.matches('table.tabela-responsiva-ios')) {
            rotularTabela(no);
          }
          if (no.querySelectorAll) {
            Array.prototype.forEach.call(
              no.querySelectorAll('table.tabela-responsiva-ios'),
              rotularTabela
            );
          }
        });
      });
    });
    mo.observe(document.body, { childList: true, subtree: true });
  }

  /* ========================================================================
   * 2. BOTTOM SHEET — API Pública
   *    SysMGMobile.openSheet({ title, html }) e SysMGMobile.closeSheet()
   * ====================================================================== */
  var sheetBackdrop, sheetTitleEl, sheetBodyEl;

  function obterSheet() {
    sheetBackdrop = sheetBackdrop || document.getElementById('iosBottomSheetBackdrop');
    sheetTitleEl = sheetTitleEl || document.getElementById('iosBottomSheetTitle');
    sheetBodyEl = sheetBodyEl || document.getElementById('iosBottomSheetBody');
    return !!sheetBackdrop;
  }

  function openSheet(opts) {
    if (!obterSheet()) return;
    opts = opts || {};
    if (sheetTitleEl) sheetTitleEl.textContent = opts.title || '';
    if (sheetBodyEl) {
      if (typeof opts.html === 'string') {
        sheetBodyEl.innerHTML = opts.html;
      } else if (opts.node instanceof Node) {
        sheetBodyEl.innerHTML = '';
        sheetBodyEl.appendChild(opts.node);
      }
    }
    sheetBackdrop.classList.add('aberto');
    sheetBackdrop.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeSheet() {
    if (!obterSheet()) return;
    sheetBackdrop.classList.remove('aberto');
    sheetBackdrop.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  /* Clique fora da gaveta -> fecha. */
  function instalarFechamentoBackdrop() {
    if (!obterSheet()) return;
    sheetBackdrop.addEventListener('click', function (ev) {
      if (ev.target === sheetBackdrop) closeSheet();
    });
  }

  /* Swipe para baixo partindo do topo da gaveta -> fecha. */
  function instalarSwipeFechar() {
    if (!obterSheet()) return;
    var sheet = sheetBackdrop.querySelector('.ios-bottom-sheet');
    if (!sheet) return;
    var startY = null;
    sheet.addEventListener('touchstart', function (ev) {
      if (sheet.scrollTop > 0) return; /* só arrasta se já estiver no topo */
      startY = ev.touches[0].clientY;
    }, { passive: true });
    sheet.addEventListener('touchmove', function (ev) {
      if (startY === null) return;
      var dy = ev.touches[0].clientY - startY;
      if (dy > 0) sheet.style.transform = 'translateY(' + dy + 'px)';
    }, { passive: true });
    sheet.addEventListener('touchend', function (ev) {
      if (startY === null) return;
      var endY = (ev.changedTouches[0] || {}).clientY || startY;
      var dy = endY - startY;
      sheet.style.transform = '';
      if (dy > 100) closeSheet();
      startY = null;
    });
  }

  /* Botão voltar do SO fecha a gaveta antes de sair da página. */
  function instalarVoltarFecha() {
    if (!obterSheet()) return;
    window.addEventListener('popstate', function () {
      if (sheetBackdrop.classList.contains('aberto')) closeSheet();
    });
  }

  /* ========================================================================
   * 3. BOTTOM TAB BAR — marca a aba corrente
   *    Lê ?modulo=xxx e aplica .ativo no .ios-tab-item[data-rota=xxx]
   * ====================================================================== */
  function marcarAbaAtiva() {
    var params = new URLSearchParams(window.location.search);
    var moduloAtual = (params.get('modulo') || 'dashboard').toLowerCase();
    var tabs = document.querySelectorAll('.ios-bottom-tabbar .ios-tab-item');
    Array.prototype.forEach.call(tabs, function (tab) {
      var rota = (tab.getAttribute('data-rota') || '').toLowerCase();
      if (rota && rota === moduloAtual) tab.classList.add('ativo');
      else tab.classList.remove('ativo');
    });
  }

  /* ========================================================================
   * 4. GATILHOS DELEGADOS
   *    [data-ios-sheet="idConteudo"]       -> abre o conteúdo na gaveta
   *    [data-ios-sheet-title="Titulo"]     -> opcional, define o título
   *    [data-ios-sheet-close]              -> fecha a gaveta ao clicar
   * ====================================================================== */
  function instalarGatilhoAbertura() {
    document.addEventListener('click', function (ev) {
      var alvo = ev.target.closest('[data-ios-sheet]');
      if (alvo) {
        if (!isMobile()) return; /* no PC o fluxo original é preservado */
        ev.preventDefault();
        var idConteudo = alvo.getAttribute('data-ios-sheet');
        var titulo = alvo.getAttribute('data-ios-sheet-title') || '';
        var fonte = document.getElementById(idConteudo);
        if (fonte) openSheet({ title: titulo, html: fonte.innerHTML });
        return;
      }
      if (ev.target.closest('[data-ios-sheet-close]')) {
        ev.preventDefault();
        closeSheet();
      }
    });
  }

  /* ========================================================================
   * 5. PWA — preservação do parâmetro ?origem=pwa nas navegações internas
   *    Garante que ao instalar na Tela de Início o usuário permaneça no
   *    modo standalone após clicar em qualquer link de módulo.
   * ====================================================================== */
  function preservarOrigemPwa() {
    var params = new URLSearchParams(window.location.search);
    if (params.get('origem') !== 'pwa') return;
    var links = document.querySelectorAll('a[href*="modulo="]');
    Array.prototype.forEach.call(links, function (a) {
      try {
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#') return;
        var u = new URL(href, window.location.origin);
        if (!u.searchParams.get('origem')) {
          u.searchParams.set('origem', 'pwa');
          var qs = u.searchParams.toString();
          a.setAttribute('href', u.pathname + (qs ? '?' + qs : ''));
        }
      } catch (e) { /* hrefs exóticos são ignorados silenciosamente */ }
    });
  }

  /* ========================================================================
   * INICIALIZAÇÃO
   * ====================================================================== */
  function init() {
    rotularTabelasExistentes();
    observarMutacoes();
    instalarFechamentoBackdrop();
    instalarSwipeFechar();
    instalarVoltarFecha();
    instalarGatilhoAbertura();
    marcarAbaAtiva();
    preservarOrigemPwa();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  /* API pública exposta para o restante do SysMG. */
  window.SysMGMobile = {
    openSheet: openSheet,
    closeSheet: closeSheet,
    isMobile: isMobile,
    rotularTabela: rotularTabela
  };
})();
