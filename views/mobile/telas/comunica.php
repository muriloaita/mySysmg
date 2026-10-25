<?php
/**
 * Tela: Comunica (publicações, intimações, triagem de prazos).
 * Dados esperados: $listaComunicacoes array<[id,tipo,titulo,resumo,data,status,status_slug,urgente]>
 */
$itens = $listaComunicacoes ?? [];
?>
<section class="ios-secao">
  <h2 class="ios-secao-titulo">Para triar</h2>
  <?php if (empty($itens)): ?>
    <div class="ios-vazio">Nada para triar hoje.</div>
  <?php else: foreach ($itens as $c): ?>
    <article class="ios-card-demanda"
             onclick="verDetalheComunica(<?= (int)($c['id'] ?? 0) ?>, this)">
      <div class="ios-card-demanda-topo">
        <span class="ios-pill ios-pill--<?= !empty($c['urgente']) ? 'urgente' : (htmlspecialchars($c['status_slug'] ?? 'info', ENT_QUOTES, 'UTF-8')) ?>">
          <?= htmlspecialchars($c['tipo'] ?? 'Publicação', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="ios-card-demanda-prazo"><?= htmlspecialchars($c['data'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <p class="ios-card-demanda-descricao" style="font-weight:600;">
        <?= htmlspecialchars($c['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
      </p>
      <?php if (!empty($c['resumo'])): ?>
        <p style="margin:4px 0 0;color:var(--ios-text-secondary);font-size:13px;line-height:1.35;">
          <?= htmlspecialchars($c['resumo'], ENT_QUOTES, 'UTF-8') ?>
        </p>
      <?php endif; ?>
    </article>
  <?php endforeach; endif; ?>
</section>

<script>
  function verDetalheComunica(id, elemento) {
    document.getElementById('gaveta-generica-titulo').textContent = 'Publicação #' + id;
    document.getElementById('gaveta-generica-corpo').innerHTML =
      elemento.innerHTML +
      '<div style="display:flex;gap:8px;margin-top:16px;">' +
        '<a href="ajax.comunica.php?acao=arquivar&id=' + id + '" class="btn" style="flex:1;background:rgba(142,142,147,.18);color:var(--ios-text-primary);">Arquivar</a>' +
        '<a href="ajax.comunica.php?acao=protocolar&id=' + id + '" class="btn" style="flex:1;background:var(--ios-accent);color:#fff;">Protocolar</a>' +
      '</div>';
    abrirGavetaIOS('gaveta-generica');
  }
</script>
