<?php
/**
 * Tela: Central de Aprovações.
 * Dados esperados: $listaAprovacoes array<[id,titulo,descricao,solicitante,data,endpoint]>
 * $endpoint é opcional — padrão 'ajax.aprovar.php'.
 */
$aprovs = $listaAprovacoes ?? [];
?>
<section class="ios-secao">
  <?php if (empty($aprovs)): ?>
    <div class="ios-vazio">Nenhuma aprovação pendente. Caixa limpa.</div>
  <?php else: foreach ($aprovs as $a): $endpoint = $a['endpoint'] ?? 'ajax.aprovar.php'; ?>
    <article class="ios-aprovacao-card">
      <div class="ios-aprovacao-topo">
        <div style="flex:1;">
          <h3 style="margin:0;font-size:16px;line-height:1.3;">
            <?= htmlspecialchars($a['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          </h3>
          <p style="margin:4px 0 0;color:var(--ios-text-secondary);font-size:13px;">
            por <?= htmlspecialchars($a['solicitante'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
            &middot;
            <?= htmlspecialchars($a['data'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <p style="margin:10px 0 14px;color:var(--ios-text-primary);line-height:1.4;">
        <?= htmlspecialchars($a['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?>
      </p>

      <div class="ios-aprovacao-acoes">
        <form method="post" action="<?= htmlspecialchars($endpoint, ENT_QUOTES, 'UTF-8') ?>"
              style="flex:1;"
              onsubmit="return confirm('Rejeitar esta solicitação?');">
          <input type="hidden" name="id"   value="<?= (int)($a['id'] ?? 0) ?>">
          <input type="hidden" name="acao" value="rejeitar">
          <button type="submit" class="btn ios-btn-rejeitar">Rejeitar</button>
        </form>
        <form method="post" action="<?= htmlspecialchars($endpoint, ENT_QUOTES, 'UTF-8') ?>"
              style="flex:1;"
              onsubmit="return confirm('Aprovar esta solicitação?');">
          <input type="hidden" name="id"   value="<?= (int)($a['id'] ?? 0) ?>">
          <input type="hidden" name="acao" value="aprovar">
          <button type="submit" class="btn ios-btn-aprovar">Aprovar</button>
        </form>
      </div>
    </article>
  <?php endforeach; endif; ?>
</section>
