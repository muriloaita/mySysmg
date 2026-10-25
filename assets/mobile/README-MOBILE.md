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
| `assets/js/sysmg-ios-mobile.js`   | Lógica mobile (Bottom Sheet, auto-rótulo de tabelas, tab bar ativa). Dormente no PC. |
| `assets/mobile/_shell.php`        | Parcial PHP com Bottom Tab Bar + skeleton do Bottom Sheet. Inclua UMA VEZ no `index.php`. |

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
Se omitido, o parcial e o JS detectam a rota sozinhos lendo `?modulo=`.

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

Em **cada `<table>`** cujo cabeçalho não cabe no celular:

```html
<table class="tabela-responsiva-ios">   <!-- única mudança: adicionar a classe -->
  <thead>
    <tr><th>Cliente</th><th>Processo</th><th>Status</th></tr>
  </thead>
  <tbody>
    <tr><td>João Silva</td><td>0001234-56.2025</td><td>Em andamento</td></tr>
    ...
  </tbody>
</table>
```

- No **PC**: continua tabela clássica em colunas (sem alteração visual).
- No **iPhone**: cada `<tr>` vira um cartão com o nome da coluna à esquerda e
  o valor à direita. O JS lê o `<thead>` e preenche `data-label` em cada `<td>`.

---

## 4. Abrindo um Bottom Sheet (filtros, detalhes, formulários rápidos)

### 4.1. Via atributos HTML (sem JS extra)
```html
<button type="button"
        data-ios-sheet="idConteudoOculto"
        data-ios-sheet-title="Filtros do Painel">
  Filtrar
</button>

<div id="idConteudoOculto" hidden>
  <!-- qualquer HTML: formulário, detalhes de processo, lista de ações -->
  <form>
    <label>Período</label>
    <input type="date" name="de"> <input type="date" name="ate">
    <button type="submit" class="btn">Aplicar</button>
  </form>
</div>
```

### 4.2. Via API JavaScript
```javascript
SysMGMobile.openSheet({
  title: 'Detalhes do Processo',
  html:  '<p>Autor: ...</p><p>Réu: ...</p>'
});
// Para fechar programaticamente:
SysMGMobile.closeSheet();
```

### 4.3. Fechamento
A gaveta fecha automaticamente por: toque no backdrop, arrasto para baixo
pela barrinha cinza, botão voltar do sistema, ou qualquer elemento com
`data-ios-sheet-close` dentro da gaveta.

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
- [ ] Tabelas com `.tabela-responsiva-ios` renderizam como cartões legíveis.
- [ ] Bottom Sheet abre/fecha com toque no backdrop, swipe para baixo e botão voltar.
- [ ] Todo botão/link alcança no mínimo 44 × 44 px de área de toque.
- [ ] Trocar iOS para tema escuro → o SysMG acompanha coerentemente.

---

## 7. Checklist de QA no PC (regressão zero)

- [ ] Resolução ≥ 1024 px: Bottom Tab Bar e Bottom Sheet não aparecem no DOM renderizado.
- [ ] Tabelas sem a classe `.tabela-responsiva-ios` seguem exatamente iguais.
- [ ] Tabelas **com** a classe seguem com layout tradicional de colunas no PC.
- [ ] Nenhum input de desktop ganha `font-size:16px` por acidente (só vale em `max-width:768px`).
- [ ] Nenhum efeito `backdrop-filter` nem `padding-bottom` extra visível no PC.

---

## 8. Rollback (desligar o módulo em segundos)

Para remover completamente o shell mobile sem apagar arquivos:
1. Comentar as 3 linhas adicionadas no `index.php` (`<link>`, `<?php include ?>` e `<script>`).
2. (Opcional) Remover a classe `tabela-responsiva-ios` das tabelas — sem o CSS
   ativo, ela é ignorada pelo navegador e não tem efeito algum.

Nenhuma tabela de banco de dados, rota PHP ou arquivo de negócio é tocado por
este módulo. Rollback total não exige backup prévio.
