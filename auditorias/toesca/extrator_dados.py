#!/usr/bin/env python3
"""
Script de Extração de Dados — Auditoria Toesca
Extrai dados de clientes, demandas, atendimentos e documentos do sistema Marques & Gameiro
"""

import asyncio
import json
import time
from pathlib import Path
from datetime import datetime
import pandas as pd
from playwright.async_api import async_playwright, Browser, Page

# Configuração
BASE_URL = "https://sistemamarquesegameiro.com"
CLIENTES = [16727, 16726, 16997]
OUTPUT_DIR = Path(__file__).parent
TIMESTAMP = datetime.now().strftime("%Y%m%d_%H%M%S")

class ExtratarToesca:
    def __init__(self):
        self.dados = {
            "clientes": [],
            "demandas": [],
            "atendimentos": [],
            "documentos": [],
            "operacoes": []
        }
        self.browser = None
        self.page = None

    async def init_browser(self):
        """Inicializa Playwright com navegador Chrome"""
        print("[*] Iniciando Playwright...")
        self.playwright = await async_playwright().start()
        self.browser = await self.playwright.chromium.launch(headless=False)
        self.context = await self.browser.new_context()
        self.page = await self.context.new_page()
        print("[✓] Browser iniciado")

    async def acessar_sistema(self):
        """Acessa o sistema base"""
        print(f"[*] Acessando {BASE_URL}...")
        try:
            await self.page.goto(f"{BASE_URL}/index.php", wait_until="networkidle")
            print("[✓] Sistema acessado")
            return True
        except Exception as e:
            print(f"[✗] Erro ao acessar: {e}")
            return False

    async def extrair_cliente(self, cliente_id: int):
        """Extrai dados de um cliente específico"""
        print(f"\n[→] Extraindo dados do cliente {cliente_id}...")

        url = f"{BASE_URL}/ver_entidade.php?id={cliente_id}"
        try:
            await self.page.goto(url, wait_until="networkidle", timeout=30000)
            await self.page.wait_for_load_state("networkidle")

            # Aguarda um pouco para conteúdo carregar
            await asyncio.sleep(2)

            # Coleta dados básicos do cliente
            cliente_info = {
                "id": cliente_id,
                "url": url,
                "timestamp_extracao": datetime.now().isoformat(),
                "html_content": await self.page.content()  # Backup do HTML
            }

            # Tenta extrair informações específicas (ajustar seletores conforme necessário)
            try:
                # Exemplo: procura por campos comuns em painéis
                nome_elem = await self.page.query_selector("input[name='nome'], [class*='nome']")
                if nome_elem:
                    cliente_info["nome"] = await nome_elem.input_value() or await nome_elem.text_content()

                cpf_elem = await self.page.query_selector("input[name='cpf'], [class*='cpf']")
                if cpf_elem:
                    cliente_info["cpf"] = await cpf_elem.input_value() or await cpf_elem.text_content()

                # Procura por tabelas de demandas/atendimentos
                tabelas = await self.page.query_selector_all("table")
                cliente_info["num_tabelas"] = len(tabelas)

            except Exception as e:
                print(f"  [!] Erro ao extrair campos: {e}")

            self.dados["clientes"].append(cliente_info)
            print(f"[✓] Cliente {cliente_id} extraído")

            # Tenta extrair demandas e atendimentos
            await self.extrair_demandas_cliente(cliente_id)
            await self.extrair_atendimentos_cliente(cliente_id)
            await self.extrair_documentos_cliente(cliente_id)

        except Exception as e:
            print(f"[✗] Erro ao extrair cliente {cliente_id}: {e}")

    async def extrair_demandas_cliente(self, cliente_id: int):
        """Extrai demandas vinculadas ao cliente"""
        print(f"  [→] Procurando demandas...")
        try:
            # Procura por links/botões que levem a demandas
            demandas_links = await self.page.query_selector_all("a[href*='demanda'], a[href*='processo']")

            for i, link in enumerate(demandas_links[:5]):  # Limite 5 para não demorar demais
                try:
                    href = await link.get_attribute("href")
                    texto = await link.text_content()

                    demanda_info = {
                        "cliente_id": cliente_id,
                        "link": href or "N/A",
                        "descricao": texto or "N/A",
                        "tipo": "demanda"
                    }
                    self.dados["demandas"].append(demanda_info)
                except:
                    pass

            print(f"  [✓] {len(demandas_links)} demandas encontradas")
        except Exception as e:
            print(f"  [!] Erro ao extrair demandas: {e}")

    async def extrair_atendimentos_cliente(self, cliente_id: int):
        """Extrai histórico de atendimentos"""
        print(f"  [→] Procurando atendimentos...")
        try:
            # Procura por seções de atendimento/histórico
            atendimentos = await self.page.query_selector_all("[class*='atendimento'], [id*='atendimento']")

            for atend in atendimentos:
                try:
                    texto = await atend.text_content()
                    atend_info = {
                        "cliente_id": cliente_id,
                        "conteudo": texto or "N/A",
                        "tipo": "atendimento"
                    }
                    self.dados["atendimentos"].append(atend_info)
                except:
                    pass

            print(f"  [✓] {len(atendimentos)} atendimentos encontrados")
        except Exception as e:
            print(f"  [!] Erro ao extrair atendimentos: {e}")

    async def extrair_documentos_cliente(self, cliente_id: int):
        """Extrai documentos e links de arquivos"""
        print(f"  [→] Procurando documentos...")
        try:
            # Procura por links de download/documentos
            docs_links = await self.page.query_selector_all("a[href*='.pdf'], a[href*='.doc'], a[href*='.xlsx'], [class*='documento']")

            for doc in docs_links:
                try:
                    href = await doc.get_attribute("href")
                    texto = await doc.text_content()

                    doc_info = {
                        "cliente_id": cliente_id,
                        "link": href or "N/A",
                        "nome": texto or "N/A",
                        "tipo": "documento"
                    }
                    self.dados["documentos"].append(doc_info)
                except:
                    pass

            print(f"  [✓] {len(docs_links)} documentos encontrados")
        except Exception as e:
            print(f"  [!] Erro ao extrair documentos: {e}")

    async def salvar_xlsx(self):
        """Salva dados em Excel com múltiplas abas"""
        print(f"\n[*] Salvando dados em Excel...")

        output_file = OUTPUT_DIR / f"inventario_toesca_{TIMESTAMP}.xlsx"

        try:
            with pd.ExcelWriter(output_file, engine='openpyxl') as writer:
                # Aba: Clientes
                df_clientes = pd.DataFrame(self.dados["clientes"])
                df_clientes.to_excel(writer, sheet_name='Clientes', index=False)

                # Aba: Demandas
                df_demandas = pd.DataFrame(self.dados["demandas"])
                df_demandas.to_excel(writer, sheet_name='Demandas', index=False)

                # Aba: Atendimentos
                df_atendimentos = pd.DataFrame(self.dados["atendimentos"])
                df_atendimentos.to_excel(writer, sheet_name='Atendimentos', index=False)

                # Aba: Documentos
                df_docs = pd.DataFrame(self.dados["documentos"])
                df_docs.to_excel(writer, sheet_name='Documentos', index=False)

                # Aba: Resumo
                resumo = pd.DataFrame([{
                    "Cliente ID": id,
                    "Demandas": len([d for d in self.dados["demandas"] if d.get("cliente_id") == id]),
                    "Atendimentos": len([a for a in self.dados["atendimentos"] if a.get("cliente_id") == id]),
                    "Documentos": len([d for d in self.dados["documentos"] if d.get("cliente_id") == id]),
                } for id in CLIENTES])
                resumo.to_excel(writer, sheet_name='Resumo', index=False)

            print(f"[✓] Excel salvo: {output_file}")
            return str(output_file)
        except Exception as e:
            print(f"[✗] Erro ao salvar Excel: {e}")
            return None

    async def salvar_json(self):
        """Salva dados em JSON para referência"""
        print(f"[*] Salvando dados em JSON...")

        output_file = OUTPUT_DIR / f"inventario_toesca_{TIMESTAMP}.json"

        try:
            # Remove conteúdo HTML grande para não poluir JSON
            for cliente in self.dados["clientes"]:
                if "html_content" in cliente:
                    del cliente["html_content"]

            with open(output_file, 'w', encoding='utf-8') as f:
                json.dump(self.dados, f, indent=2, ensure_ascii=False)

            print(f"[✓] JSON salvo: {output_file}")
            return str(output_file)
        except Exception as e:
            print(f"[✗] Erro ao salvar JSON: {e}")
            return None

    async def executar(self):
        """Executa o fluxo completo de extração"""
        try:
            await self.init_browser()

            if not await self.acessar_sistema():
                print("[✗] Falha ao acessar sistema")
                return

            # Extrai dados de cada cliente
            for cliente_id in CLIENTES:
                await self.extrair_cliente(cliente_id)
                await asyncio.sleep(1)  # Aguarda entre requisições

            # Salva dados
            xlsx_file = await self.salvar_xlsx()
            json_file = await self.salvar_json()

            print(f"\n[✓] Extração concluída!")
            print(f"  - Excel: {xlsx_file}")
            print(f"  - JSON: {json_file}")

        except Exception as e:
            print(f"[✗] Erro fatal: {e}")
        finally:
            if self.browser:
                await self.browser.close()
            if self.playwright:
                await self.playwright.stop()

async def main():
    extrator = ExtratarToesca()
    await extrator.executar()

if __name__ == "__main__":
    asyncio.run(main())
