<?php
/**
 * ============================================================================
 * SYSMG — CAMADA MOBILE iOS (PWA) — SHELL + ROTEADOR LEVE
 * ============================================================================
 *
 * Ponto único de entrada da camada mobile. Carregado a partir do index.php
 * raiz do SysMG quando a requisição é identificada como PWA/Mobile.
 *
 * COMO INTEGRAR AO index.php DO SYSMG (edição mínima, zero regressão):
 *
 *     // --- início do index.php, logo após session_start() ---
 *     $__ehPwa    = (isset($_GET['origem']) && $_GET['origem'] === 'pwa');
 *     $__ehMobile = isset($_SERVER['HTTP_USER_AGENT'])
 *                   && preg_match('/iPhone|iPad|iPod|Android|Mobile/i', $_SERVER['HTTP_USER_AGENT']);
 *     if ($__ehPwa || $__ehMobile) {
 *         // Mantém as classes/sessões/instâncias já criadas aqui no escopo.
 *         include __DIR__ . '/views/mobile/layout-mobile.php';
 *         exit;
 *     }
 *     // --- fluxo DESKTOP ORIGINAL segue aqui, inalterado ---
 *
 * Preserva integralmente: $_SESSION, autoloader, /class/*, banco, controllers.
 * Nada aqui duplica regra de negócio: os módulos de telas/ apenas renderizam
 * dados que o index.php (ou funções do SysMG já existentes) deixaram no
 * escopo global. Fallback seguro quando o dado não está disponível.
 * ============================================================================
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ------------------------------------------------------------------------
 * ROTEAMENTO LEVE
 * ---------------------------------------------------------------------- */
$modulosDisponiveis = [
    'painel'        => __DIR__ . '/telas/painel.php',
    'demandas'      => __DIR__ . '/telas/demandas.php',
    'prazos'        => __DIR__ . '/telas/prazos.php',
    'controladoria' => __DIR__ . '/telas/controladoria.php',
    'aprovacoes'    => __DIR__ . '/telas/aprovacoes.php',
    'conversa'      => __DIR__ . '/telas/conversa.php',
    'processos'     => __DIR__ . '/telas/processos.php',
    'agenda'        => __DIR__ . '/telas/agenda.php',
    'comunica'      => __DIR__ . '/telas/comunica.php',

    /* Aliases pedidos no briefing (compatibilidade de nomes). */
    'dashboard'     => __DIR__ . '/telas/painel.php',
    'chat'          => __DIR__ . '/telas/conversa.php',
];

$moduloAtual  = (isset($_GET['modulo']) && isset($modulosDisponiveis[$_GET['modulo']]))
    ? $_GET['modulo']
    : 'painel';
$arquivoTela  = $modulosDisponiveis[$moduloAtual];

/* Títulos legíveis para o header. */
$titulosHeader = [
    'painel'        => 'Meu Painel',
    'demandas'      => 'Minhas Demandas',
    'prazos'        => 'Meus Prazos',
    'controladoria' => 'Controladoria',
    'aprovacoes'    => 'Aprovações',
    'conversa'      => 'Conversa Reservada',
    'processos'     => 'Central de Processos',
    'agenda'        => 'Agenda',
    'comunica'      => 'Comunica',
    'dashboard'     => 'Meu Painel',
    'chat'          => 'Conversa Reservada',
];
$tituloAtual = $titulosHeader[$moduloAtual] ?? 'SysMG';
?><!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">

<!-- Viewport iOS/PWA: trava zoom, respeita notch. -->
<meta name="viewport"
      content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

<!-- Metas Apple exigidas para funcionamento como app na Tela de Início. -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="SysMG">
<meta name="theme-color" content="#F2F2F7" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
<meta name="format-detection" content="telephone=no">

<title>SysMG — <?= htmlspecialchars($tituloAtual, ENT_QUOTES, 'UTF-8') ?></title>

<link rel="stylesheet" href="assets/css/sysmg-ios-mobile.css">
<link rel="apple-touch-icon" href="assets/img/apple-touch-icon-180.png">
<link rel="manifest" href="manifest.json">
</head>
<body data-rota-atual="<?= htmlspecialchars($moduloAtual, ENT_QUOTES, 'UTF-8') ?>">

<?php include __DIR__ . '/_header.php'; ?>

<main class="ios-main">
<?php
if (file_exists($arquivoTela)) {
    include $arquivoTela;
} else {
    include __DIR__ . '/telas/_404.php';
}
?>
</main>

<?php include __DIR__ . '/_bottom-sheets.php'; ?>
<?php include __DIR__ . '/_bottom-tabbar.php'; ?>

<script src="assets/js/sysmg-ios-mobile.js"></script>
</body>
</html>
