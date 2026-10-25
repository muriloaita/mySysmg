# SYSMG — Shell Mobile iOS-first (PWA)

Módulo aditivo. **Zero regressão desktop**: todo o CSS mobile está envelopado em
`@media (max-width: 768px)` e todos os componentes de celular (Bottom Tab Bar,
Bottom Sheet, header mobile) ficam `display:none !important` em telas ≥ 769px.
Nenhuma rota PHP de negócio nem o esquema do banco são alterados.

---

## 1. Arquivos adicionados (nenhum arquivo existente é modificado)

| Arquivo | Função |
|---|---|
| `assets/css/sysmg-ios-mobile.css` | Estilos exclusivos de celular (ativos apenas em `@media max-width:768px`). |
| `assets/js/sysmg-ios-mobile.js`   | Motor JS: auto-rotulagem de tabelas, abertura/fechamento de gavetas, destaque da aba ativa. Dormente no PC. |
| `assets/mobile/_shell.php`        | Parcial PHP com Bottom Tab Bar + duas gavetas prontas (`iosGavetaPrincipal` e `iosGavetaMais`). Inclua UMA VEZ no `index.php`. |

---

## 2. Integração ao `index.php` (3 edições pontuais)

### 2.1. Dentro do `<head>` (após os CSS existentes)
```html
<link rel="stylesheet" href="assets/css/sysmg-ios-mobile.css">
```

### 2.2. Imediatamente antes de `</body>`
```php
<?php include __DIR__ . '/assets/mobile/_shell.php'; ?>
<script src="assets/js/sysmg-ios-mobile.js" defer></script>
```

### 2.3. (Opcional) Rota corrente server-side
Antes do include, se já houver uma variável de rota no seu `index.php`:
```php
<?php $moduloAtual = $_GET['modulo'] ?? 'dashboard'; ?>
```
Se omitido, o parcial e o JS detectam a rota sozinhos. O JS compara o
atributo `data-rota` das abas contra `window.location.href` por substring,
então vale para `?modulo=producao`, `?p=producao`, `/producao.php` etc.

### 2.4. Garanta o `<meta viewport>` com `viewport-fit=cover`
No `<head>` do `index.php`, confirme que existe:
```html
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
```
Sem `viewport-fit=cover` o `env(safe-area-inset-*)` retorna 0 e o layout
subirá por baixo do notch / Dynamic Island.

### 2.5. (Opcional, melhor acabamento no PWA instalado)
No `<head>`:
```html
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="theme-color" content="#F2F2F7" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
```

---

## 3. Adaptando as tabelas do Dashboard (Produção/Gestão)

### 3.1. Automático para quem já usa Bootstrap
Toda `<table class="table">` **já é convertida em cartões no celular sem precisar fazer nada** — o JS adiciona `.tabela-responsiva-ios` automaticamente.

### 3.2. Para tabelas que não usam `.table`
Basta adicionar a classe diretamente:
```html
<table class="tabela-responsiva-ios">
  <thead>
    <tr><th>Cliente</th><th>Processo</th><th>Status</th></tr>
  </thead>
  <tbody>
    <tr><td>João Silva</td><td>0001234-56.2025</td><td>Em andamento</td></tr>
  </tbody>
</table>
```
No PC a tabela segue clássica (colunas). No iPhone cada `<tr>` vira um cartão
com o nome da coluna à esquerda e o valor à direita — o JS lê os `<th>` e
preenche `data-label` em cada `<td>`.

---

## 4. Abrindo um Bottom Sheet (filtros, detalhes, formulários rápidos)

### 4.1. Gaveta genérica reutilizável (`iosGavetaPrincipal`)
Já incluída no `_shell.php`. Preencha o corpo via JS e abra:
```javascript
// 1) preenche o título e o conteúdo
document.getElementById('iosGavetaPrincipalTitulo').textContent = 'Detalhes do Processo';
document.getElementById('iosGavetaPrincipalCorpo').innerHTML = '<p>Autor: ...</p><p>Réu: ...</p>';

// 2) abre
abrirGavetaIOS('iosGavetaPrincipal');

// 3) fecha
fecharGavetaIOS('iosGavetaPrincipal');
```

Em qualquer `<button>` ou link:
```html
<button type="button" onclick="abrirGavetaIOS('iosGavetaPrincipal')">
  Ver detalhes
</button>
```

### 4.2. Gavetas dedicadas por tela (recomendado para filtros fixos)
Dentro do seu arquivo de módulo (ex.: `modulos/producao.php`), declare a
gaveta com conteúdo já pronto:

```html
<div class="ios-bottom-sheet-backdrop" id="iosGavetaFiltrosProducao"
     onclick="if(event.target===this) fecharGavetaIOS('iosGavetaFiltrosProducao');">
  <div class="ios-bottom-sheet">
    <div class="ios-sheet-handle"></div>
    <h3 style="margin:0 0 12px;font-size:17px;font-weight:600;">Filtrar Produção</h3>
    <form method="get">
      <label>De</label>  <input type="date" name="de">
      <label>Até</label> <input type="date" name="ate">
      <button type="submit" class="btn">Aplicar</button>
      <button type="button" class="btn"
              onclick="fecharGavetaIOS('iosGavetaFiltrosProducao')">Cancelar</button>
    </form>
  </div>
</div>

<!-- Botão que abre -->
<button type="button" class="btn"
        onclick="abrirGavetaIOS('iosGavetaFiltrosProducao')">
  Filtrar
</button>
```

Fechamento automático: toque fora da gaveta (no backdrop escuro). Também
funciona com qualquer `<button type="button" onclick="fecharGavetaIOS('idDaGaveta')">`.

### 4.3. Gaveta "Mais" (menu secundário)
Já pronta no `_shell.php` como `iosGavetaMais`, acionada pela aba "Mais"
da Bottom Tab Bar. Edite o `<ul>` dela para incluir/trocar atalhos.

---

## 5. Conversão de KPIs do Dashboard em grade 2×2

Em qualquer bloco de indicadores do painel:
```html
<div class="sysmg-kpi-grid">
  <div class="sysmg-kpi-card">...</div>
  <div class="sysmg-kpi-card">...</div>
  <div class="sysmg-kpi-card">...</div>
  <div class="sysmg-kpi-card">...</div>
</div>
```
- No PC: respeita o layout atual (as regras só atuam em `max-width:768px`).
- No iPhone: 2 colunas × N linhas, com cartões estilo iOS.

---

## 6. Checklist de QA no iPhone (modo PWA instalado)

- [ ] Abrir `index.php?origem=pwa` no Safari → **Compartilhar → Adicionar à Tela de Início**.
- [ ] Ao abrir pelo ícone, o SysMG inicia em **modo standalone** (sem barra do Safari).
- [ ] Em iPhone com notch / Dynamic Island: nenhum conteúdo fica escondido no topo.
- [ ] Home Indicator: a Bottom Tab Bar mantém 16–20 px livres acima da barrinha branca.
- [ ] Tocar qualquer `<input>`, `<select>` ou `<textarea>` **NÃO** causa zoom (regra de 16 px).
- [ ] Nenhum arrasto horizontal acidental em iPhone SE (375 px) nem Pro Max (430 px).
- [ ] Tabelas Bootstrap (`.table`) renderizam como cartões legíveis automaticamente.
- [ ] Bottom Sheet abre com `abrirGavetaIOS` e fecha com toque no backdrop.
- [ ] Todo botão/link alcança no mínimo 44 × 44 px de área de toque.
- [ ] Trocar iOS para tema escuro → o SysMG acompanha coerentemente.

---

## 7. Checklist de QA no PC (regressão zero)

- [ ] Resolução ≥ 1024 px: Bottom Tab Bar e gavetas não aparecem na tela.
- [ ] Tabelas sem a classe `.tabela-responsiva-ios` seguem exatamente iguais.
- [ ] Tabelas Bootstrap (`.table`) seguem com layout tradicional de colunas no PC
      (o JS adiciona a classe mas o CSS de cartão só vale em `max-width:768px`).
- [ ] Nenhum input de desktop ganha `font-size:16px` por acidente.
- [ ] Nenhum `backdrop-filter` nem `padding-bottom` extra visível no PC.

---

## 8. Rollback (desligar o módulo em segundos)

Para remover completamente o shell mobile sem apagar arquivos:
1. Comentar as 3 linhas adicionadas no `index.php` (`<link>`, `<?php include ?>` e `<script>`).
2. (Opcional) Remover a classe `tabela-responsiva-ios` das tabelas — sem o CSS
   ativo, ela é ignorada pelo navegador e não tem efeito algum.

Nenhuma tabela de banco de dados, rota PHP ou arquivo de negócio é tocado por
este módulo. Rollback total não exige backup prévio.
