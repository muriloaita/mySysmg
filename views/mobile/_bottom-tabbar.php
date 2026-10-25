<?php
/**
 * Bottom Tab Bar iOS — 5 destinos primários.
 * No PC o CSS esconde este <nav> (regra em @media min-width:769px).
 * Esperado em escopo: $moduloAtual.
 */
if (!function_exists('sysmg_tab_ativa_v2')) {
    function sysmg_tab_ativa_v2($rota, $atual) {
        $atual = strtolower((string)$atual);
        $rota  = strtolower($rota);
        /* Aliases: painel <-> dashboard; conversa <-> chat. */
        $equiv = [
            'painel'   => ['painel', 'dashboard'],
            'conversa' => ['conversa', 'chat'],
        ];
        if (isset($equiv[$rota]) && in_array($atual, $equiv[$rota], true)) return ' ativo';
        return $rota === $atual ? ' ativo' : '';
    }
}
?>
<nav class="ios-bottom-tabbar" role="navigation" aria-label="Navegação Principal Mobile">

  <a href="index.php?origem=pwa&amp;modulo=painel"
     class="ios-tab-item<?= sysmg_tab_ativa_v2('painel', $moduloAtual ?? '') ?>"
     data-rota="painel">
    <span class="ios-tab-icon" aria-hidden="true">&#128202;</span>
    <span>Painel</span>
  </a>

  <a href="index.php?origem=pwa&amp;modulo=demandas"
     class="ios-tab-item<?= sysmg_tab_ativa_v2('demandas', $moduloAtual ?? '') ?>"
     data-rota="demandas">
    <span class="ios-tab-icon" aria-hidden="true">&#9889;</span>
    <span>Demandas</span>
  </a>

  <a href="index.php?origem=pwa&amp;modulo=agenda"
     class="ios-tab-item<?= sysmg_tab_ativa_v2('agenda', $moduloAtual ?? '') ?>"
     data-rota="agenda">
    <span class="ios-tab-icon" aria-hidden="true">&#128197;</span>
    <span>Agenda</span>
  </a>

  <a href="index.php?origem=pwa&amp;modulo=comunica"
     class="ios-tab-item<?= sysmg_tab_ativa_v2('comunica', $moduloAtual ?? '') ?>"
     data-rota="comunica">
    <span class="ios-tab-icon" aria-hidden="true">&#128276;</span>
    <span>Comunica</span>
  </a>

  <a href="javascript:void(0);"
     class="ios-tab-item"
     data-rota="mais"
     onclick="abrirGavetaIOS('gaveta-menu-mais'); return false;">
    <span class="ios-tab-icon" aria-hidden="true">&#9776;</span>
    <span>Mais</span>
  </a>
</nav>
