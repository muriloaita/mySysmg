<?php
/**
 * Tela: Central de Processos.
 * Dados esperados: $listaProcessos array<[id,numero,cliente,parte_contraria,fase,status,status_slug,ultima_mov]>
 * Busca por texto via ?q=
 */
$processos = $listaProcessos ?? [];
$q = trim((string)($_GET['q'] ?? ''));
?>
<section class="ios-secao" style="padding-top:12px;padding-bottom:0;">
  <form method="get" action="index.php" style="margin:0;">
    <input type="hidden" name="origem" value="pwa">
    <input type="hidden" name="modulo" value="processos">
    <input type="search" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
           placeholder="Buscar por número, cliente ou parte..."
           aria-label="Buscar processos">
  </form>
</section>

<section class="ios-secao">
  <?php if (empty($processos)): ?>
    <div class="ios-vazio">
      <?= $q !== '' ? 'Nenhum processo encontrado para "' . htmlspecialchars($q, ENT_QUOTES, 'UTF-8') . '".' : 'Nenhum processo cadastrado.' ?>
    </div>
  <?php else: foreach ($processos as $p): ?>
    <article class="ios-card-demanda"
             onclick="location.href='index.php?origem=pwa&modulo=processo&id=<?= (int)($p['id'] ?? 0) ?>'">
      <div class="ios-card-demanda-topo">
        <span class="ios-pill ios-pill--<?= htmlspecialchars($p['status_slug'] ?? 'info', ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($p['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="ios-card-demanda-prazo"><?= htmlspecialchars($p['fase'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <p class="ios-card-demanda-descricao" style="font-weight:600;">
        <?= htmlspecialchars($p['numero'] ?? '', ENT_QUOTES, 'UTF-8') ?>
      </p>
      <div class="ios-card-demanda-rodape">
        <div style="font-size:13px;color:var(--ios-text-primary);">
          <?= htmlspecialchars($p['cliente'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
          <?php if (!empty($p['parte_contraria'])): ?>
            <span style="color:var(--ios-text-secondary);"> vs. <?= htmlspecialchars($p['parte_contraria'], ENT_QUOTES, 'UTF-8') ?></span>
          <?php endif; ?>
        </div>
        <?php if (!empty($p['ultima_mov'])): ?>
          <div style="font-size:12px;color:var(--ios-text-secondary);margin-top:4px;">
            Última mov.: <?= htmlspecialchars($p['ultima_mov'], ENT_QUOTES, 'UTF-8') ?>
          </div>
        <?php endif; ?>
      </div>
    </article>
  <?php endforeach; endif; ?>
</section>
