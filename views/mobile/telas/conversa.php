<?php
/**
 * Tela: Conversa Reservada / Chat interno (estilo iMessage).
 * Dados esperados:
 *   $mensagens  array<[remetente_id,remetente_nome,texto,hora]>
 *   $destinatarioId  int — para quem a próxima mensagem será enviada
 *   $endpointEnvio   string — padrão 'ajax.chat.php'
 */
$msgs = $mensagens ?? [];
$usuarioId = $_SESSION['usuario_id'] ?? ($_SESSION['pro_id'] ?? 0);
$dest = $destinatarioId ?? 0;
$endpoint = $endpointEnvio ?? 'ajax.chat.php';
?>
<section class="ios-chat" id="iosChatContainer">
  <?php if (empty($msgs)): ?>
    <div class="ios-vazio" style="align-self:center;margin:40px auto;">
      Comece a conversa.
    </div>
  <?php else: foreach ($msgs as $m):
      $meu = (int)($m['remetente_id'] ?? 0) === (int)$usuarioId;
  ?>
    <div class="ios-chat-linha<?= $meu ? ' ios-chat-linha--eu' : '' ?>">
      <?php if (!$meu && !empty($m['remetente_nome'])): ?>
        <div class="ios-chat-remetente"><?= htmlspecialchars($m['remetente_nome'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
      <div class="ios-chat-bolha<?= $meu ? ' ios-chat-bolha--eu' : ' ios-chat-bolha--outro' ?>">
        <?= nl2br(htmlspecialchars($m['texto'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
      </div>
      <?php if (!empty($m['hora'])): ?>
        <div class="ios-chat-hora"><?= htmlspecialchars($m['hora'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
    </div>
  <?php endforeach; endif; ?>
</section>

<form class="ios-chat-input-bar" method="post" action="<?= htmlspecialchars($endpoint, ENT_QUOTES, 'UTF-8') ?>"
      onsubmit="return this.texto.value.trim() !== '';">
  <input type="hidden" name="destinatario" value="<?= (int)$dest ?>">
  <input type="text" name="texto" placeholder="Mensagem..." required
         maxlength="2000" autocomplete="off"
         aria-label="Texto da mensagem">
  <button type="submit" class="ios-btn-enviar" aria-label="Enviar mensagem">&uarr;</button>
</form>

<script>
  /* Mantém o fim da conversa visível ao abrir a tela. */
  (function () {
    var cont = document.getElementById('iosChatContainer');
    if (cont) { cont.scrollTop = cont.scrollHeight; window.scrollTo(0, document.body.scrollHeight); }
  })();
</script>
