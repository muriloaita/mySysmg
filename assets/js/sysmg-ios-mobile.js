/**
 * ============================================================================
 * SYSMG - MOTOR JAVASCRIPT DE ADAPTAÇÃO MOBILE (iOS PWA)
 * ============================================================================
 */

document.addEventListener("DOMContentLoaded", function () {
  inicializarTabelasMobile();
  destacarAbaAtiva();
});

/**
 * 1. Função que transforma tabelas comuns em Cards no celular automaticamente.
 * Ela lê os títulos do <thead> (ex: "Processo", "Prazo", "Responsável")
 * e aplica em cada célula <td> para o CSS exibir como cartão no iPhone.
 */
function inicializarTabelasMobile() {
  // Procura todas as tabelas do sistema (ou as marcadas com .tabela-responsiva-ios)
  const tabelas = document.querySelectorAll("table.tabela-responsiva-ios, .table");

  tabelas.forEach((tabela) => {
    // Adiciona a classe que ativa o layout de cartões no celular
    tabela.classList.add("tabela-responsiva-ios");

    // Coleta os nomes das colunas no cabeçalho (<th>)
    const cabecalhos = Array.from(tabela.querySelectorAll("thead th")).map((th) =>
      th.innerText.trim()
    );

    // Se a tabela tiver cabeçalho, aplica o nome correspondente em cada célula (<td>)
    if (cabecalhos.length > 0) {
      const linhas = tabela.querySelectorAll("tbody tr");
      linhas.forEach((linha) => {
        const celulas = linha.querySelectorAll("td");
        celulas.forEach((celula, indice) => {
          if (!celula.hasAttribute("data-label") && cabecalhos[indice]) {
            celula.setAttribute("data-label", cabecalhos[indice]);
          }
        });
      });
    }
  });
}

/**
 * 2. Funções para abrir e fechar a Gaveta Inferior (Bottom Sheet) no celular.
 * Parâmetro idGaveta: ID do elemento HTML da gaveta que deseja abrir.
 */
function abrirGavetaIOS(idGaveta) {
  const gaveta = document.getElementById(idGaveta);
  if (gaveta) {
    gaveta.classList.add("aberto");
    document.body.style.overflow = "hidden"; // Trava o fundo enquanto a gaveta está aberta
  }
}

function fecharGavetaIOS(idGaveta) {
  const gaveta = document.getElementById(idGaveta);
  if (gaveta) {
    gaveta.classList.remove("aberto");
    document.body.style.overflow = ""; // Libera a rolagem da página
  }
}

/**
 * 3. Identifica em qual módulo do SysMG o usuário está e ilumina o ícone certo na barra inferior.
 */
function destacarAbaAtiva() {
  const urlAtual = window.location.href.toLowerCase();
  const abas = document.querySelectorAll(".ios-tab-item");

  abas.forEach((aba) => {
    const rota = aba.getAttribute("data-rota");
    if (rota && urlAtual.includes(rota)) {
      abas.forEach((item) => item.classList.remove("ativo"));
      aba.classList.add("ativo");
    }
  });
}
