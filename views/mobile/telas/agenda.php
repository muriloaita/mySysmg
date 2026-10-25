<?php
/**
 * Tela: Agenda (listagem compacta por dia — nunca grade estilo FullCalendar
 * no celular). Dados esperados:
 *   $eventosAgenda  array<[id,data,hora,titulo,local,tipo]>
 *   O campo 'data' deve ser string legível já formatada (ex.: "Seg, 27/10").
 */
$eventos = $eventosAgenda ?? [];
$porDia = [];
foreach ($eventos as $ev) {
    $dia = $ev['data'] ?? 'Sem data';
    $porDia[$dia][] = $ev;
}
?>
<?php if (empty($porDia)): ?>
<section class="ios-secao">
  <div class="ios-vazio">Nenhum compromisso programado.</div>
</section>
<?php else: foreach ($porDia as $dia => $itens): ?>
<section class="ios-secao" style="padding-bottom:4px;">
  <div class="ios-agenda-dia">
    <h3 class="ios-agenda-dia-titulo"><?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') ?></h3>
    <?php foreach ($itens as $ev): ?>
      <article class="ios-agenda-item"
               onclick="verDetalheAgenda(<?= (int)($ev['id'] ?? 0) ?>, this)">
        <span class="ios-agenda-hora"><?= htmlspecialchars($ev['hora'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        <div class="ios-agenda-corpo">
          <strong><?= htmlspecialchars($ev['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
          <?php if (!empty($ev['local'])): ?>
            <div style="color:var(--ios-text-secondary);font-size:13px;margin-top:2px;">
              <?= htmlspecialchars($ev['local'], ENT_QUOTES, 'UTF-8') ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($ev['tipo'])): ?>
            <span class="ios-pill ios-pill--info" style="margin-top:6px;">
              <?= htmlspecialchars($ev['tipo'], ENT_QUOTES, 'UTF-8') ?>
            </span>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endforeach; endif; ?>

<a href="index.php?origem=pwa&amp;modulo=agenda-novo" class="ios-fab" aria-label="Novo compromisso">+</a>

<script>
  function verDetalheAgenda(id, elemento) {
    var titulo = elemento.querySelector('strong').textContent;
    var corpo = elemento.innerHTML;
    document.getElementById('gaveta-generica-titulo').textContent = titulo;
    document.getElementById('gaveta-generica-corpo').innerHTML =
      '<div style="padding:4px 0;">' + corpo + '</div>' +
      '<a href="index.php?origem=pwa&modulo=agenda&evento=' + id + '" class="btn" style="width:100%;margin-top:16px;background:var(--ios-accent);color:#fff;">Abrir completo</a>';
    abrirGavetaIOS('gaveta-generica');
  }
</script>
