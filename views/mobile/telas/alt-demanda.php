<?php
/**
 * Partial: formulário Alt_demanda — vive dentro de #gaveta-alt-demanda.
 * O ID da demanda é setado em runtime via abrirAltDemanda(id).
 * O endpoint real do SysMG é 'alt_demanda.php' (POST) ou um ajax equivalente.
 *
 * ATENÇÃO: 'alt_demanda.php' do SysMG real tem 103 KB e lógica crítica
 * (trava de aprovação, quadro roxo). Este form envia apenas os campos
 * mínimos exigidos para alteração de status; os demais campos avançados
 * continuam disponíveis no fluxo desktop original.
 */
?>
<form method="post" action="alt_demanda.php"
      onsubmit="return this.status.value !== '';">
  <input type="hidden" name="id"     id="altDemandaId"     value="">
  <input type="hidden" name="origem" value="pwa-mobile">

  <label for="altDemandaStatus"
         style="display:block;font-size:13px;color:var(--ios-text-secondary);
                text-transform:uppercase;font-weight:600;margin:4px 0 6px;">
    Novo status
  </label>
  <select name="status" id="altDemandaStatus" required>
    <option value="">Selecione...</option>
    <option value="pendente">Pendente</option>
    <option value="andamento">Em andamento</option>
    <option value="aguardando-aprov">Aguardando aprovação</option>
    <option value="urgente">Urgente</option>
    <option value="concluida">Concluída</option>
    <option value="cancelada">Cancelada</option>
  </select>

  <label for="altDemandaObs"
         style="display:block;font-size:13px;color:var(--ios-text-secondary);
                text-transform:uppercase;font-weight:600;margin:14px 0 6px;">
    Observação (opcional)
  </label>
  <textarea name="observacao" id="altDemandaObs" rows="3" maxlength="500"
            placeholder="Breve nota de contexto para o histórico..."></textarea>

  <div style="display:flex;gap:8px;margin-top:16px;">
    <button type="button" class="btn ios-btn-cancelar"
            onclick="fecharGavetaIOS('gaveta-alt-demanda')">
      Cancelar
    </button>
    <button type="submit" class="btn ios-btn-salvar">
      Salvar alteração
    </button>
  </div>
</form>

<script>
  /* Preenche o ID da demanda no form antes de abrir a gaveta.
   * Chamada pelos cards de demanda em painel.php, demandas.php, prazos.php,
   * controladoria.php. */
  function abrirAltDemanda(id, elementoOrigem) {
    var campo = document.getElementById('altDemandaId');
    if (campo) campo.value = id || 0;
    var selStatus = document.getElementById('altDemandaStatus');
    if (selStatus) selStatus.value = '';
    var obs = document.getElementById('altDemandaObs');
    if (obs) obs.value = '';
    abrirGavetaIOS('gaveta-alt-demanda');
  }
</script>
