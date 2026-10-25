/* SysMG - Service Worker (PWA) - v2 - 25/10/2026
 *
 * Mudancas em relacao a v1 (03/10/2026):
 *  - VERSAO bumpada para 'sysmg-pwa-v2' (purga cache antigo no activate).
 *  - PRECACHE ampliada com manifest.json, novos icones MG (180/192/512/
 *    maskable-512) e o shell mobile iOS (CSS + JS) para garantir que o
 *    app instalado abra em modo standalone mesmo sem conexao.
 *  - Mantido integralmente o legado /android-icon-192x192.png (nao tocar;
 *    cache.addAll e atomico — se 1 URL 404a, o SW inteiro quebra).
 *
 * Regras preservadas da v1 (conservadoras, nao interferem no sistema):
 *  - So trata GET da mesma origem. POST/ajax de escrita, downloads e S3 passam direto.
 *  - Paginas PHP (navegacao): sempre rede; se a rede falhar, mostra offline.html.
 *  - Arquivos estaticos (assets/, icones, fontes): cache com atualizacao em segundo plano.
 *  - Nunca guarda HTML gerado pelo PHP em cache (sessao, dados do banco).
 *
 * Para desligar tudo em caso de problema: substitua este arquivo pelo conteudo de
 * sw-desligar.js (ele se auto-remove de todos os navegadores).
 */
const VERSAO = 'sysmg-pwa-v2';
const OFFLINE = '/offline.html';
const PRECACHE = [
  OFFLINE,
  '/manifest.json',
  '/android-icon-192x192.png',
  '/assets/img/apple-touch-icon-180.png',
  '/assets/img/icon-192.png',
  '/assets/img/icon-512.png',
  '/assets/img/icon-maskable-512.png',
  '/assets/css/sysmg-ios-mobile.css',
  '/assets/js/sysmg-ios-mobile.js'
];

self.addEventListener('install', (ev) => {
  ev.waitUntil(caches.open(VERSAO).then((c) => c.addAll(PRECACHE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (ev) => {
  ev.waitUntil(
    caches.keys()
      .then((ks) => Promise.all(ks.filter((k) => k !== VERSAO).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

const ESTATICO = /^\/(assets\/|manifest\.json$|.*\.(png|ico|svg|woff2?|ttf|eot)$)/i;

self.addEventListener('fetch', (ev) => {
  const req = ev.request;
  if (req.method !== 'GET') return;                       // escrita: nao intercepta
  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return;         // S3, Google Fonts, CDNs: nao intercepta

  // Navegacao (abrir pagina): rede sempre; offline.html so se a rede cair
  if (req.mode === 'navigate') {
    ev.respondWith(fetch(req).catch(async () =>
      (await caches.match(OFFLINE)) ||
      new Response('<h1>Sem conexão com o SysMG</h1>', { headers: { 'Content-Type': 'text/html; charset=utf-8' } })
    ));
    return;
  }

  // Estaticos: entrega do cache e atualiza em segundo plano (stale-while-revalidate)
  if (ESTATICO.test(url.pathname)) {
    ev.respondWith(
      caches.open(VERSAO).then(async (cache) => {
        const emCache = await cache.match(req);
        const daRede = fetch(req).then((resp) => {
          if (resp && resp.status === 200) cache.put(req, resp.clone()); // 206 (range) nao entra no cache
          return resp;
        }).catch(() => emCache);
        return emCache || daRede;
      })
    );
    return;
  }
  // ajax.*.php, down_*.php e o resto: rede pura (sem respondWith = comportamento normal)
});
