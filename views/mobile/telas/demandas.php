<?php
/**
 * Tela: Minhas Demandas.
 * Dados esperados: $listaDemandas array<[id,descricao,prazo,status,status_slug,cliente]>
 * Filtro atual lido de ?filtro= (todas | minhas | atrasadas | concluidas).
 */
$demandas = $listaDemandas ?? [];
$filtro   = $_GET['filtro'] ?? 'todas';
$filtros  = ['todas'=>'Todas','minhas'=>'Minhas','atrasadas'=>'Atrasadas','concluidas'=>'Concluídas'];
?>
<section class="ios-secao" style="padding-top:12px;padding-bottom:0;">
  <div class="ios-filtros-segmento">
    <?php foreach ($filtros as $k => $label): ?>
      <a href="index.php?origem=pwa&amp;modulo=demandas&amp;filtro=<?= $k ?>"
         class="ios-filtro-seg<?= $filtro === $k ? ' ativo' : '' ?>">
        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="ios-secao">
  <?php if (empty($demandas)): ?>
    <div class="ios-vazio">Nenhuma demanda neste filtro.</div>
  <?php else: foreach ($demandas as $d): ?>
    <article class="ios-card-demanda"
             onclick="abrirAltDemanda(<?= (int)($d['id'] ?? 0) ?>, this)">
      <div class="ios-card-demanda-topo">
        <span class="ios-pill ios-pill--<?= htmlspecialchars($d['status_slug'] ?? 'neutro', ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($d['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="ios-card-demanda-prazo"><?= htmlspecialchars($d['prazo'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <p class="ios-card-demanda-descricao"><?= htmlspecialchars($d['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
      <div class="ios-card-demanda-rodape">
        <span style="color:var(--ios-text-secondary);font-size:13px;">
          #<?= (int)($d['id'] ?? 0) ?> &middot; <?= htmlspecialchars($d['cliente'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
        </span>
      </div>
    </article>
  <?php endforeach; endif; ?>
</section>

<a href="index.php?origem=pwa&amp;modulo=demanda-nova" class="ios-fab" aria-label="Nova demanda">+</a>
