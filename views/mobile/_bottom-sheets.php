<?php
/**
 * Gavetas globais (Bottom Sheets) reutilizáveis em qualquer tela.
 * Cada uma tem ID próprio para abrir via abrirGavetaIOS('id').
 */
?>
<!-- Gaveta "Mais" — menu secundário. -->
<div id="gaveta-menu-mais" class="ios-bottom-sheet-backdrop"
     aria-hidden="true"
     onclick="fecharGavetaIOS('gaveta-menu-mais')">
  <div class="ios-bottom-sheet"
       role="dialog" aria-modal="true" aria-labelledby="gaveta-menu-mais-titulo"
       onclick="event.stopPropagation()">
    <div class="ios-sheet-handle" aria-hidden="true"></div>
    <h3 id="gaveta-menu-mais-titulo" class="ios-sheet-titulo">Acesso Rápido SysMG</h3>
    <div class="ios-lista-botoes">
      <a href="index.php?origem=pwa&amp;modulo=prazos"        class="btn ios-btn-lista">&#9201; Meus Prazos</a>
      <a href="index.php?origem=pwa&amp;modulo=aprovacoes"    class="btn ios-btn-lista">&#9989; Central de Aprovações</a>
      <a href="index.php?origem=pwa&amp;modulo=processos"     class="btn ios-btn-lista">&#128193; Central de Processos</a>
      <a href="index.php?origem=pwa&amp;modulo=controladoria" class="btn ios-btn-lista">&#128181; Controladoria</a>
      <a href="index.php?origem=pwa&amp;modulo=conversa"      class="btn ios-btn-lista">&#128172; Conversa Reservada</a>
      <button type="button" class="btn ios-btn-lista-sair"
              onclick="if (confirm('Encerrar a sessão?')) location.href='?logout=1';">
        Sair
      </button>
    </div>
  </div>
</div>

<!-- Gaveta genérica reutilizável — conteúdo preenchido via JS. -->
<div id="gaveta-generica" class="ios-bottom-sheet-backdrop"
     aria-hidden="true"
     onclick="fecharGavetaIOS('gaveta-generica')">
  <div class="ios-bottom-sheet"
       role="dialog" aria-modal="true" aria-labelledby="gaveta-generica-titulo"
       onclick="event.stopPropagation()">
    <div class="ios-sheet-handle" aria-hidden="true"></div>
    <h3 id="gaveta-generica-titulo" class="ios-sheet-titulo">Detalhes</h3>
    <div id="gaveta-generica-corpo"></div>
  </div>
</div>

<!-- Gaveta Alt_demanda — formulário rápido de alteração de status. -->
<div id="gaveta-alt-demanda" class="ios-bottom-sheet-backdrop"
     aria-hidden="true"
     onclick="fecharGavetaIOS('gaveta-alt-demanda')">
  <div class="ios-bottom-sheet"
       role="dialog" aria-modal="true" aria-labelledby="gaveta-alt-demanda-titulo"
       onclick="event.stopPropagation()">
    <div class="ios-sheet-handle" aria-hidden="true"></div>
    <h3 id="gaveta-alt-demanda-titulo" class="ios-sheet-titulo">Alterar Demanda</h3>
    <?php include __DIR__ . '/telas/alt-demanda.php'; ?>
  </div>
</div>

<!-- Gaveta Perfil do usuário. -->
<?php
$__perfilNome  = $_SESSION['usuario_nome']  ?? ($_SESSION['nome']  ?? 'Usuário');
$__perfilEmail = $_SESSION['usuario_email'] ?? ($_SESSION['email'] ?? '');
$__perfilIni   = strtoupper(mb_substr((string)$__perfilNome, 0, 1, 'UTF-8') ?: 'U');
?>
<div id="gaveta-perfil" class="ios-bottom-sheet-backdrop"
     aria-hidden="true"
     onclick="fecharGavetaIOS('gaveta-perfil')">
  <div class="ios-bottom-sheet"
       role="dialog" aria-modal="true"
       onclick="event.stopPropagation()">
    <div class="ios-sheet-handle" aria-hidden="true"></div>
    <div style="text-align:center;padding:12px 0 16px;">
      <div aria-hidden="true"
           style="width:72px;height:72px;border-radius:50%;background:var(--ios-accent);color:#fff;font-size:30px;font-weight:600;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
        <?= htmlspecialchars($__perfilIni, ENT_QUOTES, 'UTF-8') ?>
      </div>
      <h3 class="ios-sheet-titulo" style="margin:0 0 2px;"><?= htmlspecialchars($__perfilNome, ENT_QUOTES, 'UTF-8') ?></h3>
      <p style="color:var(--ios-text-secondary);margin:0;font-size:13px;"><?= htmlspecialchars($__perfilEmail, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="ios-lista-botoes">
      <a href="?modulo=perfil&amp;origem=pwa" class="btn ios-btn-lista">Editar Perfil</a>
      <button type="button" class="btn ios-btn-lista-sair"
              onclick="if (confirm('Encerrar a sessão?')) location.href='?logout=1';">
        Sair da Conta
      </button>
    </div>
  </div>
</div>
