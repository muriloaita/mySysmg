<?php
/**
 * Tela: Meu Painel (dashboard executivo).
 *
 * Dados: variáveis OPCIONAIS que o index.php (ou qualquer bootstrap PHP
 * do SysMG anterior ao include) pode deixar no escopo global:
 *   $prazosHoje     int   — contagem de prazos vencendo hoje
 *   $pendentes      int   — demandas pendentes
 *   $aprovacoesQtd  int   — aprovações aguardando o usuário logado
 *   $comunicacoes   int   — publicações / comunicações não lidas
 *   $proximasDemandas array<[id,descricao,prazo,status,status_slug,cliente]>
 *
 * Qualquer ausência cai em fallback seguro (zero / lista vazia).
 */
$kpis = [
    ['titulo'=>'Prazos Hoje',  'valor'=>$prazosHoje     ?? 0, 'rota'=>'prazos',    'cor'=>'#FF3B30'],
    ['titulo'=>'Pendentes',    'valor'=>$pendentes      ?? 0, 'rota'=>'demandas',  'cor'=>'#FF9500'],
    ['titulo'=>'Aprovações',   'valor'=>$aprovacoesQtd  ?? 0, 'rota'=>'aprovacoes','cor'=>'#AF52DE'],
    ['titulo'=>'Comunicações', 'valor'=>$comunicacoes   ?? 0, 'rota'=>'comunica',  'cor'=>'#34C759'],
];
$proximas = $proximasDemandas ?? [];
?>
<section class="ios-secao" style="padding-top:16px;">
  <div class="sysmg-kpi-grid">
    <?php foreach ($kpis as $k): ?>
      <a href="index.php?origem=pwa&amp;modulo=<?= htmlspecialchars($k['rota'], ENT_QUOTES, 'UTF-8') ?>"
         class="sysmg-kpi-card"
         style="text-decoration:none;color:inherit;display:block;">
        <div style="font-size:11px;color:var(--ios-text-secondary);text-transform:uppercase;letter-spacing:.5px;font-weight:600;">
          <?= htmlspecialchars($k['titulo'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div style="font-size:34px;font-weight:700;color:<?= $k['cor'] ?>;line-height:1.1;margin-top:4px;">
          <?= (int)$k['valor'] ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="ios-secao">
  <h2 class="ios-secao-titulo">Próximas demandas</h2>
  <?php if (empty($proximas)): ?>
    <div class="ios-vazio">Nenhuma demanda pendente. Bom trabalho.</div>
  <?php else: foreach (array_slice($proximas, 0, 5) as $d): ?>
    <article class="ios-card-demanda"
             onclick="abrirAltDemanda(<?= (int)($d['id'] ?? 0) ?>, this)">
      <div class="ios-card-demanda-topo">
        <span class="ios-pill ios-pill--<?= htmlspecialchars($d['status_slug'] ?? 'neutro', ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($d['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="ios-card-demanda-prazo">
          <?= htmlspecialchars($d['prazo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </span>
      </div>
      <p class="ios-card-demanda-descricao">
        <?= htmlspecialchars($d['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?>
      </p>
    </article>
  <?php endforeach; endif; ?>
  <a href="index.php?origem=pwa&amp;modulo=demandas" class="ios-link-ver-todos">Ver todas &rarr;</a>
</section>
