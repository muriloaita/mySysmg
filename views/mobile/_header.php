<?php
/**
 * Header compacto estilo iOS (44 px + safe-area-top).
 * Esperado em escopo: $tituloAtual (string), $moduloAtual (string).
 */
$usuarioNome = $_SESSION['usuario_nome']
    ?? ($_SESSION['nome']        ?? null)
    ?? ($_SESSION['pro_nome']    ?? null)
    ?? 'Usuário';
$inicial = strtoupper(mb_substr((string)$usuarioNome, 0, 1, 'UTF-8') ?: 'U');
?>
<header class="ios-mobile-header">
  <div class="ios-header-row">
    <button type="button"
            class="ios-header-btn ios-header-btn--esq"
            aria-label="Voltar"
            onclick="window.history.length > 1 ? history.back() : (location.href = 'index.php?origem=pwa&modulo=painel');">
      &lsaquo;
    </button>

    <h1 class="ios-header-titulo"><?= htmlspecialchars($tituloAtual ?? 'SysMG', ENT_QUOTES, 'UTF-8') ?></h1>

    <button type="button"
            class="ios-header-btn ios-header-btn--dir"
            aria-label="Perfil do usuário"
            onclick="abrirGavetaIOS('gaveta-perfil')">
      <?= htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8') ?>
    </button>
  </div>
</header>
