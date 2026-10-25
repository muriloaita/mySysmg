<?php
/**
 * Tela: Controladoria (edição ágil de status — "Alt_demanda" em escala).
 * Dados esperados: $itensControladoria array<[id,descricao,responsavel,status,status_slug,prazo]>
 * Cada item abre a gaveta de Alt_demanda ao ser tocado.
 */
$itens = $itensControladoria ?? [];
?>
<section class="ios-secao">
  <?php if (empty($itens)): ?>
    <div class="ios-vazio">Nada sob controle hoje.</div>
  <?php else: foreach ($itens as $i): ?>
    <article class="ios-card-demanda"
             onclick="abrirAltDemanda(<?= (int)($i['id'] ?? 0) ?>, this)">
      <div class="ios-card-demanda-topo">
        <span class="ios-pill ios-pill--<?= htmlspecialchars($i['status_slug'] ?? 'neutro', ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($i['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="ios-card-demanda-prazo"><?= htmlspecialchars($i['prazo'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <p class="ios-card-demanda-descricao"><?= htmlspecialchars($i['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
      <?php if (!empty($i['responsavel'])): ?>
        <div class="ios-card-demanda-rodape">
          <span style="color:var(--ios-text-secondary);font-size:13px;">
            Responsável: <?= htmlspecialchars($i['responsavel'], ENT_QUOTES, 'UTF-8') ?>
          </span>
        </div>
      <?php endif; ?>
    </article>
  <?php endforeach; endif; ?>
</section>
