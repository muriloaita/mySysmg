<?php
/**
 * ============================================================================
 * SYSMG — Shell Mobile iOS (parcial PHP)
 *
 * Inclua UMA ÚNICA VEZ no index.php, logo antes de </body>:
 *     <?php include __DIR__ . '/assets/mobile/_shell.php'; ?>
 *
 * No PC (>= 769px) o CSS esconde todos estes elementos (display:none !important).
 * Nenhuma rota PHP de negócio ou tabela de banco é alterada por este parcial.
 *
 * Opcional: para destacar a aba corrente server-side (além do JS), defina
 * antes do include a variável $moduloAtual com uma das chaves abaixo:
 *   dashboard | producao | calendario | comunica | mais
 * Se omitida, o parcial a infere a partir de $_GET['modulo'].
 * ============================================================================
 */

$moduloAtual = isset($moduloAtual)
    ? $moduloAtual
    : (isset($_GET['modulo']) ? $_GET['modulo'] : 'dashboard');

if (!function_exists('sysmg_tab_ativa')) {
    function sysmg_tab_ativa($rota, $atual) {
        return strcasecmp($rota, (string) $atual) === 0 ? ' ativo' : '';
    }
}
?>
<!-- ============================================================
     SYSMG MOBILE SHELL — início
     ============================================================ -->

<!--
  GAVETA PRINCIPAL — reutilizável para filtros, detalhes de processo,
  formulários rápidos. Preencha o conteúdo via JS antes de chamar
  abrirGavetaIOS('iosGavetaPrincipal').
-->
<div class="ios-bottom-sheet-backdrop" id="iosGavetaPrincipal"
     aria-hidden="true"
     onclick="if(event.target===this) fecharGavetaIOS('iosGavetaPrincipal');">
  <div class="ios-bottom-sheet" role="dialog" aria-modal="true"
       aria-labelledby="iosGavetaPrincipalTitulo">
    <div class="ios-sheet-handle" aria-hidden="true"></div>
    <h3 id="iosGavetaPrincipalTitulo"
        style="margin:0 0 12px;font-size:17px;font-weight:600;color:var(--ios-text-primary);">
      Detalhes
    </h3>
    <div id="iosGavetaPrincipalCorpo"></div>
  </div>
</div>

<!--
  GAVETA "MAIS" — já carrega os atalhos de módulos secundários.
  Acrescente ou troque <li>...</li> conforme os módulos do SysMG.
-->
<div class="ios-bottom-sheet-backdrop" id="iosGavetaMais"
     aria-hidden="true"
     onclick="if(event.target===this) fecharGavetaIOS('iosGavetaMais');">
  <div class="ios-bottom-sheet" role="dialog" aria-modal="true"
       aria-labelledby="iosGavetaMaisTitulo">
    <div class="ios-sheet-handle" aria-hidden="true"></div>
    <h3 id="iosGavetaMaisTitulo"
        style="margin:0 0 12px;font-size:17px;font-weight:600;color:var(--ios-text-primary);">
      Mais opções
    </h3>
    <ul style="list-style:none;margin:0;padding:0;">
      <li>
        <a href="?modulo=integracoes&amp;origem=pwa"
           style="display:block;padding:14px 0;border-bottom:1px solid var(--ios-border);color:var(--ios-text-primary);text-decoration:none;font-size:16px;">
          Integrações
        </a>
      </li>
      <li>
        <a href="?modulo=relatorios&amp;origem=pwa"
           style="display:block;padding:14px 0;border-bottom:1px solid var(--ios-border);color:var(--ios-text-primary);text-decoration:none;font-size:16px;">
          Relatórios
        </a>
      </li>
      <li>
        <a href="?modulo=configuracoes&amp;origem=pwa"
           style="display:block;padding:14px 0;border-bottom:1px solid var(--ios-border);color:var(--ios-text-primary);text-decoration:none;font-size:16px;">
          Configurações
        </a>
      </li>
      <li>
        <a href="?logout=1"
           style="display:block;padding:14px 0;color:#FF3B30;text-decoration:none;font-size:16px;font-weight:600;">
          Sair
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- Barra de Navegação Inferior (Tab Bar iOS, 5 destinos) -->
<nav class="ios-bottom-tabbar" role="navigation" aria-label="Menu principal mobile">

  <a href="?modulo=dashboard&amp;origem=pwa"
     class="ios-tab-item<?= sysmg_tab_ativa('dashboard', $moduloAtual) ?>"
     data-rota="dashboard">
    <svg class="ios-tab-icon" viewBox="0 0 24 24" width="22" height="22"
         fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <rect x="3"  y="3"  width="7" height="9" rx="1.5"/>
      <rect x="14" y="3"  width="7" height="5" rx="1.5"/>
      <rect x="14" y="12" width="7" height="9" rx="1.5"/>
      <rect x="3"  y="16" width="7" height="5" rx="1.5"/>
    </svg>
    <span>Painel</span>
  </a>

  <a href="?modulo=producao&amp;origem=pwa"
     class="ios-tab-item<?= sysmg_tab_ativa('producao', $moduloAtual) ?>"
     data-rota="producao">
    <svg class="ios-tab-icon" viewBox="0 0 24 24" width="22" height="22"
         fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M3 3h18v4H3z"/>
      <path d="M5 7v14h14V7"/>
      <path d="M9 11h6M9 15h6"/>
    </svg>
    <span>Produção</span>
  </a>

  <a href="?modulo=calendario&amp;origem=pwa"
     class="ios-tab-item<?= sysmg_tab_ativa('calendario', $moduloAtual) ?>"
     data-rota="calendario">
    <svg class="ios-tab-icon" viewBox="0 0 24 24" width="22" height="22"
         fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <rect x="3" y="4" width="18" height="17" rx="2"/>
      <path d="M3 9h18M8 2v4M16 2v4"/>
    </svg>
    <span>Agenda</span>
  </a>

  <a href="?modulo=comunica&amp;origem=pwa"
     class="ios-tab-item<?= sysmg_tab_ativa('comunica', $moduloAtual) ?>"
     data-rota="comunica">
    <svg class="ios-tab-icon" viewBox="0 0 24 24" width="22" height="22"
         fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M21 15a2 2 0 0 1-2 2H8l-5 5V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <span>Comunica</span>
  </a>

  <!-- Aba "Mais": abre a gaveta #iosGavetaMais sem navegar. -->
  <a href="javascript:void(0);"
     class="ios-tab-item<?= sysmg_tab_ativa('mais', $moduloAtual) ?>"
     data-rota="mais"
     onclick="abrirGavetaIOS('iosGavetaMais'); return false;">
    <svg class="ios-tab-icon" viewBox="0 0 24 24" width="22" height="22"
         fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <circle cx="5"  cy="12" r="1.5"/>
      <circle cx="12" cy="12" r="1.5"/>
      <circle cx="19" cy="12" r="1.5"/>
    </svg>
    <span>Mais</span>
  </a>
</nav>

<!-- ============================================================
     SYSMG MOBILE SHELL — fim
     ============================================================ -->
