<?php
/**
 * Tela: Meus Prazos.
 * Dados esperados: $listaPrazos array<[id,descricao,prazo,prazo_iso,status,status_slug,urgencia]>
 * Urgência sugerida: 'hoje' | 'esta_semana' | 'futuro' | 'vencido'.
 */
$prazos = $listaPrazos ?? [];
$grupos = ['vencido'=>'Vencidos', 'hoje'=>'Hoje', 'esta_semana'=>'Esta Semana', 'futuro'=>'Próximos'];
$porGrupo = array_fill_keys(array_keys($grupos), []);
foreach ($prazos as $p) {
    $g = $p['urgencia'] ?? 'futuro';
    if (!isset($porGrupo[$g])) $g = 'futuro';
    $porGrupo[$g][] = $p;
}
?>
<?php foreach ($grupos as $chave => $rotulo): if (empty($porGrupo[$chave])) continue; ?>
<section class="ios-secao">
  <h2 class="ios-secao-titulo"><?= htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8') ?></h2>
  <?php foreach ($porGrupo[$chave] as $p): ?>
    <article class="ios-card-demanda"
             onclick="abrirAltDemanda(<?= (int)($p['id'] ?? 0) ?>, this)">
      <div class="ios-card-demanda-topo">
        <span class="ios-pill ios-pill--<?= $chave === 'vencido' ? 'urgente' : ($chave === 'hoje' ? 'atencao' : 'info') ?>">
          <?= htmlspecialchars($p['prazo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="ios-card-demanda-prazo"><?= htmlspecialchars($p['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <p class="ios-card-demanda-descricao"><?= htmlspecialchars($p['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
    </article>
  <?php endforeach; ?>
</section>
<?php endforeach; ?>

<?php if (empty($prazos)): ?>
<section class="ios-secao"><div class="ios-vazio">Nenhum prazo registrado.</div></section>
<?php endif; ?>
