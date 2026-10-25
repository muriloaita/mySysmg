<?php
require 'class/Usuarios.php';
require 'class/Uteis.php';
$usuarios=new Usuarios();
$usuarios->protege();
$pagina = new Pagina();
$prazo = new Prazo();
$demanda = new Demanda();
$usuario = new Usuario();
$financeiro = new Financeiro();
$empresa = new Empresa();
$profissional = new Profissional();


//tenho que ver se o cara tem permissão e gravar o log
$usuario = new Usuario();
$log = new Log();
$autoriza = $usuario->buscaPaginaPorUsuario($_SESSION['usuario']['id'],str_replace(".php","",basename($_SERVER['PHP_SELF'])));
if ($autoriza > 0){
    //quer dizer que tem permissão, gravo log
    $log->cadLog("Index", " acessado por ".$_SESSION["usuario"]["nome"]." ");
}else{
    //aqui expulso o cara aqui não vou fazer isso.
    $usuarios->logout();
}

/* ===============================================================
 * SYSMG MOBILE iOS-FIRST (PWA) - gatilho de roteamento
 * Insercao automatica em 25/10/2026. Removivel sem efeito colateral.
 * =============================================================== */
$__ehPwa    = (isset($_GET['origem']) && $_GET['origem'] === 'pwa');
$__ehMobile = isset($_SERVER['HTTP_USER_AGENT'])
              && preg_match('/iPhone|iPad|iPod|Android|Mobile/i', $_SERVER['HTTP_USER_AGENT']);
if ($__ehPwa || $__ehMobile) {
    include __DIR__ . '/views/mobile/layout-mobile.php';
    exit;
}
/* === fim do gatilho mobile === */


?>
<!DOCTYPE html>
<html lang="pr_BR">
    <?php 
    echo $pagina->getHead("Inicio - ");
    ?>
    <body>
        
        <!-- Page Container -->
        <div class="page-container">
            <!-- Page Sidebar -->
            <?php echo $pagina->getSideBar(); ?>
            
            <!-- Page Content -->
            <div class="page-content">
                <!-- Page Header -->
                <?php echo $pagina->getUserMenu(); ?>
                <!-- Page Inner -->
                <div class="page-inner">
                    <div class="page-title">
                        <h3 class="breadcrumb-header">Seja bem vindo, <?php echo utf8_encode($_SESSION['usuario']['nome']);?></h3>

                    </div>
                    <div id="main-wrapper">
                        <?php // === Aprovacoes (29/09/2026): CAIXA DE ENTRADA no topo da home (Dr. Ademir/gerencia aprovam em 1 clique; demais: canal reservado) ===
                        try {
                            if ((@include_once 'class/Aprovacao.php') and class_exists('Aprovacao')) {
                                $apInicio = new Aprovacao();
                                echo $apInicio->painelInicio($_SESSION['usuario']['id'], (isset($_GET['ap_rel']) and $_GET['ap_rel'] === '1'));
                            }
                        } catch (Throwable $apEx) { }
                        // === /Aprovacoes === ?>
                        <?php /// sessão do ademir para aprovação de parcerias
                        if ($_SESSION['usuario']['id'] == "40" or $_SESSION['usuario']['id'] == "1" or  $_SESSION['usuario']['id'] == "6" or  $_SESSION['usuario']['id'] == "5" or  $_SESSION['usuario']['id'] == "25"){
                        ?>
                            <div class="col-lg-12 col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading clearfix">
                                        <h4 class="panel-title">Aprovação de Parceria</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive invoice-table">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>Entidade</th>
                                                    <th>Profissional/Parceiro</th>
                                                    <th>Porcentagem </th>
                                                    <th>Valor </th>
                                                    <th>Processo</th>
                                                    <th> </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $rsParceiros = $financeiro->listParceiroAprovacao();
                                                foreach ( $rsParceiros as $rsParceiro){
                                                ?>
                                                <tr>
                                                    <th scope="row"><?php echo $rsParceiro->nomerazao; ?></th>
                                                    <td><?php if (isset($rsParceiro->profissional)){ echo $rsParceiro->profissional;} else{ echo "(Ext.)".$rsParceiro->nomerazao;  } ?></td>
                                                    <td>
                                                        <span class="txt_EdicaoParceiro_<?php echo $rsParceiro->id; ?>"><?php echo $rsParceiro->porcentagem; ?> </span>
                                                        <button type="button" style="" class="btn btn-warning btn-xs acionaEdicaoParceiro_<?php echo $rsParceiro->id; ?>" onclick="javascript:acionaEdicaoParceiro(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                                        <input type="text" class="porcentagem_<?php echo $rsParceiro->id; ?>" name="porcentagem_<?php echo $rsParceiro->id; ?>" size="8" placeholder="<?php echo $rsParceiro->porcentagem; ?>" style="display:none">
                                                        <button type="button" style="display:none" class="btn btn-success btn-xs salvaEdicaoParceiro_<?php echo $rsParceiro->id; ?>" onclick="javascript:salvaEdicaoParceiro(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-check" aria-hidden="true"></i></button>
                                                        <button type="button" style="display:none" class="btn btn-danger btn-xs cancelaEdicaoParceiro_<?php echo $rsParceiro->id; ?>" onclick="javascript:cancelaEdicaoParceiro(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-close" aria-hidden="true"></i></button>
                                                    </td>
                                                    <td>
                                                        
                                                        
                                                        <span class="txt_EdicaoParceiroValor_<?php echo $rsParceiro->id; ?>"><?php echo "R$".number_format($rsParceiro->valor,2,",","."); ?></span>
                                                        <button type="button" style="" class="btn btn-warning btn-xs acionaEdicaoParceiroValor_<?php echo $rsParceiro->id; ?>" onclick="javascript:acionaEdicaoParceiroValor(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                                        <input type="text" class="valor_processo_<?php echo $rsParceiro->id; ?>" name="valor_processo_<?php echo $rsParceiro->id; ?>" size="10"  id="money"  style="display:none">
                                                        <button type="button" style="display:none" class="btn btn-success btn-xs salvaEdicaoParceiroValor_<?php echo $rsParceiro->id; ?>" onclick="javascript:salvaEdicaoParceiroValor(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-check" aria-hidden="true"></i></button>
                                                        <button type="button" style="display:none" class="btn btn-danger btn-xs cancelaEdicaoParceiroValor_<?php echo $rsParceiro->id; ?>" onclick="javascript:cancelaEdicaoParceiroValor(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-close" aria-hidden="true"></i></button>
                                                    </td>
                                                    <td>
                                                        <span class="txt_EdicaoParceiroProcesso_<?php echo $rsParceiro->id; ?>"><?php echo $rsParceiro->processo; ?></span>
                                                        <button type="button" style="" class="btn btn-warning btn-xs acionaEdicaoParceiroProcesso_<?php echo $rsParceiro->id; ?>" onclick="javascript:acionaEdicaoParceiroProcesso(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                                        <input type="text" class="processo_<?php echo $rsParceiro->id; ?>" name="processo_<?php echo $rsParceiro->id; ?>" size="10"  style="display:none">
                                                        <button type="button" style="display:none" class="btn btn-success btn-xs salvaEdicaoParceiroProcesso_<?php echo $rsParceiro->id; ?>" onclick="javascript:salvaEdicaoParceiroProcesso(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-check" aria-hidden="true"></i></button>
                                                        <button type="button" style="display:none" class="btn btn-danger btn-xs cancelaEdicaoParceiroProcesso_<?php echo $rsParceiro->id; ?>" onclick="javascript:cancelaEdicaoParceiroProcesso(<?php echo $rsParceiro->id; ?>)"><i class="fa fa-close" aria-hidden="true"></i></button>

                                                    </td>

                                                    <td> <button type="button" class="btn btn-success btn-xs" onclick="javascript:aprovaParceiro(<?php echo $rsParceiro->id; ?>)">Aprovar</button> <button type="button" class="btn btn-info btn-xs" onclick="javascript:abreParceiro(<?php echo $rsParceiro->id; ?>)" >Ver</button> <button type="button" class="btn btn-danger btn-xs" onclick="javascript:excParceiro(<?php echo $rsParceiro->id; ?>)" >X</button> </td>

                                                </tr>
                                                <?php }
                                                if (count($rsParceiros) == 0){
                                                    ?>
                                                    <tr>
                                                        <td scope="row">Sem novos registros.</td>


                                                        <td> <button type="button" class="btn btn-info btn-xs" onclick="javascript:location.href='list_parceiros.php'" >Ver Tudo</button> </td>

                                                    </tr>
                                                <?php
                                                }
                                                ?>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php }  ?>
                        <div class="row">
                        <?php //echo print_r($_SESSION['usuario']);
                        
                        $PermCountBiometrias = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_cont_biometrias");
                        if ($PermCountBiometrias > 0){
                        ?>
                        
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-white stats-widget">
                                        <div class="panel-body">
                                            <div class="pull-left">
                                    <span class="stats-number">
                                        <?php
                                            $rsContaNiometriasGeral = $usuario->buscaContadorBiometriasHoje();
                                            echo $rsContaNiometriasGeral->total_hoje;echo " (Hoje)";
                                       ?>
                                    </span>
                                                <p class="stats-info">Total de Biometrias (<?php
                                            $rsContaNiometriasHoje = $usuario->buscaContadorBiometriasGeral();
                                            echo $rsContaNiometriasHoje->total_geral;
                                       ?>  ) </p>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>

                        
                        <?php } 

                        $PermCountBiometrias = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_cont_assinaturas");
                        if ($PermCountBiometrias > 0){
                        ?>
                        
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-white stats-widget">
                                        <div class="panel-body">
                                            <div class="pull-left">
                                    <span class="stats-number">
                                        <?php
                                            $rsContaAssinHoje = $usuario->buscaContadorAssinaturasHoje();
                                            echo $rsContaAssinHoje->hoje;echo " (Hoje)";
                                       ?>
                                    </span>
                                                <p class="stats-info">Total de Assinaturas Digitais (<?php
                                            $rsContaAssinGeral = $usuario->buscaContadorAssinaturasGeral();
                                            echo $rsContaAssinGeral->geral;
                                       ?>  ) </p>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>

                        
                        <?php } ?>
                        </div>


                        <?php if (
                                $_SESSION['usuario']['id'] <> "6" and  
                                $_SESSION['usuario']['id'] <> "40"  and  
                                $_SESSION['usuario']['id'] <> "1"  and  
                                $_SESSION['usuario']['id'] <> "5"   and  
                                $_SESSION['usuario']['id'] <> "28"   and  
                                $_SESSION['usuario']['id'] <> "21" and  
                                $_SESSION['usuario']['id'] <> "25"  and  
                                $_SESSION['usuario']['id'] <> "27"   and  
                                $_SESSION['usuario']['id'] <> "49" and
                                $_SESSION['usuario']['id'] <> "128"   
                                ){ ?>





                            <?php
                        $PermPrazo = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"list_prazos");
                        $PermAltPrazo = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"alt_prazos");
                        if ($PermPrazo > 0 or $PermAltPrazo > 0){
                        ?>
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-white stats-widget">
                                    <div class="panel-body">
                                        <div class="pull-left">
                                            <span class="stats-number"><?php
                                                //aqui vou fazer a contagem de prazos deste profissional
                                                $rsContaPrazosAbertos = $prazo->contaPrazosPorProfeStatus($_SESSION["usuario"]["profissional_id"],"P");
                                                echo $rsContaPrazosAbertos->total;
                                                ?></span>
                                            <a href="list_prazos.php"><p class="stats-info">Prazos Pendentes</p></a>
                                        </div>
                                        <!--<div class="pull-right">
                                            <i class="icon-arrow_downward stats-icon"></i>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-white stats-widget">
                                    <div class="panel-body">
                                        <div class="pull-left">
                                            <span class="stats-number"><?php
                                                //aqui vou fazer a contagem de prazos deste profissional
                                                $rsContaPrazosConclu = $prazo->contaPrazosPorProfeStatus($_SESSION["usuario"]["profissional_id"],"C");
                                                echo $rsContaPrazosConclu->total;
                                                ?></span>
                                            <a href=""><p class="stats-info">Prazos Concluidos</p></a>
                                        </div>
                                        <!--<div class="pull-right">
                                            <i class="icon-arrow_upward stats-icon"></i>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-white stats-widget">
                                    <div class="panel-body">
                                        <div class="pull-left">
                                            <span class="stats-number"><?php
                                                //aqui vou fazer a contagem de prazos deste profissional
                                                $rsContaPrazosProt = $prazo->contaPrazosPorProfeStatus($_SESSION["usuario"]["profissional_id"],"R");
                                                echo $rsContaPrazosProt->total;
                                                ?></span>
                                            <a href=""><p class="stats-info">Prazos Protocolados</p></a>
                                        </div>
                                        <!--<div class="pull-right">
                                            <i class="icon-arrow_downward stats-icon"></i>
                                        </div>-->
                                    </div>
                                </div>
                            </div>


                        </div><!-- Row -->
                        <?php }
                        $PermDemanda = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"list_demanda");
                        if ($PermDemanda > 0){
                        ?>

                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-white stats-widget">
                                    <div class="panel-body">
                                        <div class="pull-left">
                                            <span class="stats-number"><?php
                                                //aqui vou fazer a contagem de prazos deste profissional
                                                $rsContaDemandasPendente = $demanda->contaDemandasPorProfeStatus($_SESSION["usuario"]["profissional_id"],"P");
                                                echo $rsContaDemandasPendente->total;
                                                ?></span>
                                            <a href=""><p class="stats-info">Demandas pendentes</p></a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="panel panel-white stats-widget">
                                    <div class="panel-body">
                                        <div class="pull-left">
                                            <span class="stats-number"><?php
                                                //aqui vou fazer a contagem de prazos deste profissional
                                                $rsContaDemandasPendente = $demanda->contaDemandasPorProfeStatus($_SESSION["usuario"]["profissional_id"],"C");
                                                echo $rsContaDemandasPendente->total;
                                                ?></span>
                                            <a href=""><p class="stats-info">Demandas Concluidas</p></a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }
                       ?>
                        <div class="row">
                            <?php  if ($PermPrazo > 0 or $PermAltPrazo > 0){
                                ?>
                                <div class="col-lg-6 col-md-8">
                                    <div class="panel panel-white">
                                        <div class="panel-heading clearfix">
                                            <h4 class="panel-title">Prazos Pendentes</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive invoice-table">
                                                <table class="table">
                                                    <tbody>
                                                    <?php
                                                    $dsPrazosPendentes = $prazo->buscaPrazosPorProfeStatusComLimite($_SESSION["usuario"]["profissional_id"], 'P', 20);
                                                    foreach ($dsPrazosPendentes as $rsPrazosPendente) {
                                                        ?>
                                                        <tr>
                                                            <th scope="row"><?php echo dma(substr($rsPrazosPendente->data_cadastro,0,10))." ".substr($rsPrazosPendente->data_cadastro,10,9) ?></th>

                                                            <td><a href="alt_prazos.php?id=<?php echo $rsPrazosPendente->id ?>"><?php echo strtoupper($rsPrazosPendente->numero) ?></a></td>
                                                            <td><?php echo dma(substr($rsPrazosPendente->prazo,0,10)) ?></td>
                                                            <td><span class="label label-warning">Pendente</span></td>
                                                        </tr>
                                                    <?php } ?>


                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ($PermDemanda > 0){
                            ?>
                            <div class="col-lg-6 col-md-8">
                                <div class="panel panel-white">
                                    <div class="panel-heading clearfix">
                                        <h4 class="panel-title">Demandas Pendentes</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive invoice-table">
                                            <table class="table">
                                                <tbody>
                                                <?php
                                                $dsDemandaPendentes = $demanda->buscaDemandasProfStatus($_SESSION["usuario"]["profissional_id"],'P',20);
                                                foreach($dsDemandaPendentes as $dsDemandaPendente){
                                                    ?>
                                                    <tr >

                                                        <th scope="row"><?php echo dma(substr($dsDemandaPendente->data_entrada,0,10))." ".substr($dsDemandaPendente->data_entrada,11,9); ?></th>
                                                        <td><a href="alt_demanda.php?id=<?php echo $dsDemandaPendente->id ?>"><?php echo strtoupper($dsDemandaPendente->assunto) ?></a></td>
                                                        <td><?php echo $dsDemandaPendente->tipo ?></td>
                                                        <td><span class="label label-warning">Pendente</span></td>

                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <!--
                        <div class="row">
                            <div class="col-lg-6 col-md-8">
                                <div class="panel panel-white">
                                    <div class="panel-heading clearfix">
                                        <h4 class="panel-title">Ranking do Mes de Março</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="browser-stats">
                                            <ul class="list-unstyled">
                                                <li><i class="fa fa-user-circle"></i>Usuario 1 <div class="text-success pull-right">10<i class="fa fa-check-circle"></i></div><div class="text-danger pull-right">5<i class="fa fa-window-close-o"></i></div></li>
                                                <li><i class="fa fa-user-circle"></i>Usuario 1 <div class="text-success pull-right">10<i class="fa fa-check-circle"></i></div><div class="text-danger pull-right">5<i class="fa fa-window-close-o"></i></div></li>
                                                <li><i class="fa fa-user-circle"></i>Usuario 1 <div class="text-success pull-right">10<i class="fa fa-check-circle"></i></div><div class="text-danger pull-right">5<i class="fa fa-window-close-o"></i></div></li>
                                                <li><i class="fa fa-user-circle"></i>Usuario 1 <div class="text-success pull-right">10<i class="fa fa-check-circle"></i></div><div class="text-danger pull-right">5<i class="fa fa-window-close-o"></i></div></li>
                                                <li><i class="fa fa-user-circle"></i>Usuario 1 <div class="text-success pull-right">10<i class="fa fa-check-circle"></i></div><div class="text-danger pull-right">5<i class="fa fa-window-close-o"></i></div></li>
                                                <li><i class="fa fa-user-circle"></i>Usuario 1 <div class="text-success pull-right">10<i class="fa fa-check-circle"></i></div><div class="text-danger pull-right">5<i class="fa fa-window-close-o"></i></div></li>
                                                <li><i class="fa fa-user-circle"></i>Usuario 1 <div class="text-success pull-right">10<i class="fa fa-check-circle"></i></div><div class="text-danger pull-right">5<i class="fa fa-window-close-o"></i></div></li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        -->

                            <?php //gamb para funcionar 
                                $PermDashPrazosDemandas = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_demanda_prazos");
                                if ($PermDashPrazosDemandas > 0){
                                ?>

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="panel panel-white">
                                                <div class="panel-heading clearfix">
                                                    <h4 class="panel-title">Total de Demandas e Prazos Por Profissional</h4>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive invoice-table ">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th>Profissional</th>
                                                                <th style="background-color: #ed8a92;    width: 100px;"><span class="label label-danger">Protocolo/Inicial</span></th>
                                                                <th style="background-color: #ed8a92;    width: 100px;"><span class="label label-danger">Protocolo/Prazo</span></th>
                                                                <th style="background-color: #ed8a92;    width: 100px;"><span class="label label-danger">Demandas Pendentes</span></th>
                                                                
                                                                <th style="background-color: #ed8a92;    width: 100px;"><span class="label label-danger">Prazos Pendentes</span></th>
                                                                <th style="background-color: #ed8a92;    width: 100px;"><span class="label label-danger">Demandas Standby</span></th>
                                                                <th style="    width: 100px;"><span class="label label-danger">Total de Pendencias</span></th>

                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php //aqui vou fazer o ranking por topo
                                                        $rsRankingPrazosEDemandas = $demanda->rankingPrazoeDemanda();
                                                        foreach($rsRankingPrazosEDemandas as $dados){
                                                            //aqui só vou mostrar os usuarios que tem alguma informação
                                                            if (
                                                                $dados->total_prazo_inicial > 0 or 
                                                                $dados->total_prazo_protocolo > 0 or 
                                                                $dados->total_demanda_pendente > 0 or 
                                                                $dados->total_prazo_pendente > 0 or 
                                                                $dados->total_demanda_standby > 0 or 
                                                                $dados->total_pendencias > 0 or 
                                                                $dados->total_pendencias > 0 
                                                                ){
                                                            ?>
                                                                <tr>
                                                                    <th scope="row" ><?php echo $dados->nome?></th>
                                                                    <th scope="row" ><?php echo $dados->total_prazo_inicial?></th>


                                                                    <th scope="row" style="background-color: #ed8a92; color:#000; font-weight: bold;" >
                                                                    <a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id?>&status=R&tipo=&data_inicio=&data_fim=">
                                                                    <?php echo $dados->total_prazo_inicial?></a>
                                                                    </th>

                                                                    <td style="background-color: #ed8a92; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id?>&status=P&tipo=&data_inicio=&data_fim="><?php echo $dados->total_demanda_pendente?></a></td>
                                                                    
                                                                    <td style="background-color: #ed8a92; color:#000; font-weight: bold;"><a href="rel_prazos.php?empresa_id=&prof=<?php echo $dados->profissional_id; ?>&status=P"><?php echo $dados->total_prazo_pendente?></a></td>
                                                                    <td style="background-color: #ed8a92; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id?>&status=S&tipo=&data_inicio=&data_fim="><?php echo $dados->total_demanda_standby?></a></td>
                                                                    <td style="color:#000; font-weight: bold;"><?php echo $dados->total_pendencias?></td>
                                                                    

                                                                </tr>
                                                            <?php } }  ?>


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                <?php } ?>

                        
                        
                        <?php } else { ?>

                            



                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-white stats-widget">
                                        <div class="panel-body">
                                            <div class="pull-left">
                                    <span class="stats-number">
                                        <?php //aqui vou buscar o total de demandas
                                        $dsTotalDemandas = $demanda->buscaTotalDeDemandas();
                                        foreach($dsTotalDemandas as $dados){
                                            echo $dados->total;
                                        }
                                        ?>
                                    </span>
                                                <p class="stats-info">Total de Demandas</p>
                                            </div>
                                            <div class="pull-right">
                                                <i class="fa fa-navicon stats-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-white stats-widget">
                                        <div class="panel-body">
                                            <div class="pull-left">
                                    <span class="stats-number">
                                        <?php //aqui vou buscar o total de demandas
                                        $dsTotalDemandas = $demanda->buscaTotalDeDemandasPorStatus('P');
                                        foreach($dsTotalDemandas as $dados){
                                            echo $dados->total;
                                        }
                                        ?></span>
                                                <p class="stats-info">Demandas Pendentes</p>
                                            </div>
                                            <div class="pull-right">
                                                <i class="fa fa-hourglass-3 stats-icon" style="color:#8b0000"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-white stats-widget">
                                        <div class="panel-body">
                                            <div class="pull-left">
                                    <span class="stats-number"><?php //aqui vou buscar o total de demandas
                                        $dsTotalDemandas = $demanda->buscaTotalDeDemandasPorStatus('C');
                                        foreach($dsTotalDemandas as $dados){
                                            echo $dados->total;
                                        }
                                        ?></span>
                                                <p class="stats-info">Demandas Concluidas</p>
                                            </div>
                                            <div class="pull-right">
                                                <i class="fa fa-check-circle-o stats-icon" style="color:#2b542c"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- Row -->


                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="panel panel-white">

                                        <div class="panel-body">
                                            <div id="chart">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- Row -->
<?php /* CANCELANDO AS FASES
                            <div class="row">

                                <div class="col-lg-12 col-md-12">
                                    <div class="panel panel-white">
                                        <div class="panel-heading clearfix">
                                            <h4 class="panel-title">Fases por Tipo de Demanda </h4>
                                        </div>
                                        <?php
                                        $rsTipos = $demanda->buscaTipoDemandas();
                                        foreach ($rsTipos as $tipos){


                                            $countFasesDeTipo = $demanda->contaDashDeFasesPorTipo($tipos->id);

                                            if ($countFasesDeTipo->total > 0){
                                                ?>
                                                <br/>
                                                <div class="panel-body">
                                                    <div class="table-responsive invoice-table">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th>Tipo : <b><?php echo strtoupper($tipos->nome)?></b> </th>

                                                                <th style="background-color: #ed8a92;width: 100px;"><span class="label label-danger">Pendentes</span></th>
                                                                <th style="background-color: #b0f3c8;width: 100px;"><span class="label label-success">Conluidas</span></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $contaPendente=0;
                                                            $contaConcluido=0;

                                                            $rsFasesDeTipo = $demanda->buscaDashDeFasesPorTipo($tipos->id);


                                                            foreach ($rsFasesDeTipo as $fases){
                                                                ?>
                                                                <tr>
                                                                    <th scope="row" >Fase:<?php echo $fases->nome_fase; ?></th>

                                                                    <td style="background-color: #ed8a92; color:#000; font-weight: bold;"><?php echo $fases->pendente; ?></td>
                                                                    <td style="background-color: #b0f3c8; color:#000; font-weight: bold;"><?php echo $fases->concluidos; ?></td>

                                                                </tr>
                                                            <?php $contaPendente = $contaPendente + $fases->pendente;
                                                                $contaConcluido = $contaConcluido + $fases->concluidos;
                                                                $contaExec = $contaExec + $fases->executando;

                                                            }  ?>
                                                            <tr>
                                                                <th style="text-align: right; font-weight: bold;">Total:</th>

                                                                <th style="background-color: #ed8a92; color:#000; font-weight: bold;"><?php echo $contaPendente?></th>
                                                                <th style="background-color: #b0f3c8; color:#000; font-weight: bold;"><?php echo $contaConcluido?></th>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php } }  ?>
                                    </div>
                                </div>
                            </div><!-- Row -->
 <?php */ ?>

                            <div class="row">

                                <div class="col-lg-12 col-md-12">
                                    <div class="panel panel-white">
                                        <div class="panel-heading clearfix">
                                            <h4 class="panel-title">Fases por Tipo de Demanda </h4>
                                            <span>Selecione um tipo para exibir as informações:</span>
                                            <select name="tipo_demanda" class="tipo_demanda">
                                                <option >Selecione um Tipo</option>
                                                <?php
                                                $rsTipos = $demanda->buscaTipoDemandas();
                                                foreach ($rsTipos as $rsTipo){
                                                ?>
                                                <option value="<?php echo $rsTipo->id?>"><?php echo $rsTipo->nome?></option>
                                                <?php } ?>
                                            </select>
                                            <!--<button type="button" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> </button>
                                            <button type="button" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i> </button>-->

                                        </div>

                                                <br/>
                                                <div class="panel-body table_fases">

                                                </div>

                                    </div>
                                </div>
                            </div><!-- Row -->
                            <?php
                            $PermDashAtalhoDashProducao = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_atalho_producao");
                            if ($PermDashAtalhoDashProducao > 0){
                            ?>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="panel panel-white">
                                            <div class="panel-heading clearfix">
                                                <h4 class="panel-title">Dashboard de Produção por Unidade </h4>
                                                <span>Selecione uma unidade para exibir as informações:</span>
                                                <select name="unidade" class="unidade">
                                                    <option >Selecione uma unidade</option>
                                                    <?php
                                                    $rsEmpresas = $empresa->buscaEmpresas();
                                                    foreach ($rsEmpresas as $rsEmpresa){
                                                    ?>
                                                    <option value="<?php echo $rsEmpresa->id?>"><?php echo $rsEmpresa->nome?></option>
                                                    <?php } ?>
                                                </select>
                                                

                                            </div>
                                        </div>
                                    </div>
                                </div><!-- Row -->
                                

                            <?php } ?>

                            <?php
                            $PermDashAtalhoDashProducaoFunc = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_atalho_producao_funcionario");
                            if ($PermDashAtalhoDashProducaoFunc > 0){
                            ?>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="panel panel-white">
                                            <div class="panel-heading clearfix">
                                                <h4 class="panel-title">Dashboard de Produção por Profissional </h4>
                                                <span>Selecione um profissional para exibir as informações:</span>
                                                <select name="profissional" class="profissional">
                                                    <option >Selecione um profissional</option>
                                                    <?php
                                                    $rsProfs = $profissional->buscaProfissionais();
                                                    foreach($rsProfs as $rsProf){
                                                    ?>
                                                    <option value="<?php echo $rsProf->id?>"><?php echo $rsProf->nome?></option>
                                                    <?php } ?>
                                                </select>
                                                

                                            </div>
                                        </div>
                                    </div>
                                </div><!-- Row -->
                                



                            <?php } ?>
                            

                            <?php
                            $PermDashPrazosDemandas = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_demanda_prazos");
                            if ($PermDashPrazosDemandas > 0){
                            ?>

                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="panel panel-white">
                                            <div class="panel-heading clearfix">
                                                <h4 class="panel-title" alt="V1">Total de Demandas e Prazos Por Profissional</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive invoice-table ">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>Profissional</th>
                                                            <th style="background-color: #e9bec2;    width: 100px;"><span class="label label-danger">Protocolo/Inicial</span></th>
                                                            <th style="background-color: #eba5aa;    width: 100px;"><span class="label label-danger">Protocolo/Prazo</span></th>
                                                            <th style="background-color: #eb878e;    width: 100px;"><span class="label label-danger">Demandas Pendentes</span></th>
                                                            
                                                            <th style="background-color: #e46c74;    width: 100px;"><span class="label label-danger">Prazos Pendentes</span></th>
                                                            <th style="background-color: #e24654;    width: 100px;"><span class="label label-danger">Demandas Standby</span></th>
                                                            <th style="    width: 100px;"><span class="label label-danger">Total de Pendencias</span></th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php //aqui vou fazer o ranking por topo
                                                    $rsRankingPrazosEDemandas = $demanda->rankingPrazoeDemanda();
                                                    $countDemandasPendentes = 0;
                                                    $countPrazosPendentes = 0;
                                                    $countDemandasStandby = 0;
                                                    $countTotalPendencias = 0;
                                                    $countInicialPendente = 0;
                                                    $countPrazosProtocolo = 0;

                                                    foreach($rsRankingPrazosEDemandas as $dados){
                                                        //aqui só vou mostrar os usuarios que tem alguma informação
                                                        if (
                                                        $dados->total_prazo_inicial > 0 or 
                                                        $dados->total_prazo_protocolo > 0 or 
                                                        $dados->total_demanda_pendente > 0 or 
                                                        $dados->total_prazo_pendente > 0 or 
                                                        $dados->total_demanda_standby > 0 or 
                                                        $dados->total_pendencias > 0 or 
                                                        $dados->total_pendencias > 0 
                                                        ){

                                                        ?>
                                                            <tr>
                                                                <th scope="row" ><?php echo $dados->nome?></th>
                                                                <th scope="row" style="background-color: #e9bec2; color:#000; font-weight: bold;" >
                                                                <a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id?>&status=R&tipo=&data_inicio=&data_fim=">
                                                                    <?php echo $dados->total_prazo_inicial; $countInicialPendente = $countInicialPendente+$dados->total_prazo_inicial; ?>
                                                                </a>
                                                                </th>
                                                                <th scope="row" style="background-color: #eba5aa; color:#000; font-weight: bold;" >
                                                                <a href="list_protocolo.php">
                                                                    <?php echo $dados->total_prazo_protocolo ; $countPrazosProtocolo = $countPrazosProtocolo+$dados->total_prazo_protocolo ?>
                                                                </a>
                                                                </th>
                                                                
                                                                <td style="background-color: #eb878e; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id?>&status=P&tipo=&data_inicio=&data_fim="><?php echo $dados->total_demanda_pendente?></a>
                                                                <?php $countDemandasPendentes = $countDemandasPendentes+$dados->total_demanda_pendente; ?>
                                                                </td>
                                                                
                                                                <td style="background-color: #e46c74; color:#000; font-weight: bold;"><a href="rel_prazos.php?empresa_id=&prof=<?php echo $dados->profissional_id; ?>&status=P"><?php echo $dados->total_prazo_pendente?></a>
                                                                <?php $countPrazosPendentes = $countPrazosPendentes+$dados->total_prazo_pendente; ?>
                                                                </td>
                                                                <td style="background-color: #e24654; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id?>&status=S&tipo=&data_inicio=&data_fim="><?php echo $dados->total_demanda_standby?></a>
                                                                <?php $countDemandasStandby = $countDemandasStandby+$dados->total_demanda_standby; ?>
                                                                </td>
                                                                <td style="color:#000; font-weight: bold;"><?php echo $dados->total_pendencias?>
                                                                <?php //soma todas as pendencias 
                                                                    $countTotalPendencias = $countTotalPendencias+$dados->total_pendencias;
                                                                ?>
                                                                </td>
                                                                

                                                            </tr>
                                                        <?php } } ?>

                                                            <tr>
                                                                <th scope="row" style="text-align: right" >Total</th>
                                                                <td style="background-color: #e9bec2; color:#000; font-weight: bold;"><?php echo $countInicialPendente;?></td>
                                                                <td style="background-color: #eba5aa; color:#000; font-weight: bold;"><?php echo $countPrazosProtocolo;?></td>

                                                                <td style="background-color: #eb878e; color:#000; font-weight: bold;"><?php echo $countDemandasPendentes?></td>
                                                                <td style="background-color: #e46c74; color:#000; font-weight: bold;"><?php echo $countPrazosPendentes?></td>
                                                                <td style="background-color: #e24654; color:#000; font-weight: bold;"><?php echo $countDemandasStandby?></td>
                                                                <td style="color:#000; font-weight: bold;"><?php echo  $countTotalPendencias; ?></td>

                                                            </tr>


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            <?php } ?>

                            <?php
                            $PermDashDemandasTipo = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_demanda_tipo");
                            if ($PermDashDemandasTipo > 0){
                            ?>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="panel panel-white">
                                            <div class="panel-heading clearfix">
                                                <h4 class="panel-title">Total de Demandas por Tipos</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive invoice-table ">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>Tipo</th>

                                                            <th style="background-color: #ed8a92;    width: 100px;"><span class="label label-danger">Pendentes</span></th>
                                                            <th style="background-color: #91bdf7;    width: 100px;"><span class="label label-info">Protocolado</span></th>
                                                            <th style="background-color: #b0f3c8;    width: 100px;"><span class="label label-success">Conluidas</span></th>
                                                            <th style="background-color: #f7dc70;    width: 100px;"><span class="label label-warning">Cancelado</span></th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php //aqui vou fazer o ranking por topo
                                                        $rsRankingTipos = $demanda->rankingPorTipos();
                                                        foreach($rsRankingTipos as $dados){
                                                            ?>
                                                            <tr>
                                                                <th scope="row" ><a href="rel_demanda.php?empresa=&prof=&status=&tipo=<?php echo $dados->id; ?>&data_inicio=&data_fim="><?php echo $dados->nome; ?></a></th>

                                                                <td style="background-color: #ed8a92; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=&status=P&tipo=<?php echo $dados->id; ?>&data_inicio=&data_fim="><?php echo $dados->total_demandas_pendentes; ?></a></td>
                                                                <td style="background-color: #91bdf7; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=&status=R&tipo=<?php echo $dados->id; ?>&data_inicio=&data_fim="><?php echo $dados->total_demandas_protocolado; ?></a></td>
                                                                <td style="background-color: #b0f3c8; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=&status=C&tipo=<?php echo $dados->id; ?>&data_inicio=&data_fim="><?php echo $dados->total_demandas_concluidas; ?></a></td>
                                                                <td style="background-color: #f7dc70; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=&status=X&tipo=<?php echo $dados->id; ?>&data_inicio=&data_fim="><?php echo $dados->total_demandas_cancelado; ?></a></td>

                                                            </tr>
                                                        <?php } ?>


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                            } 
                            $PermDashRankPrazoProf = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_rank_prazo_prof");
                            if ($PermDashRankPrazoProf > 0){
                            ?>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="panel panel-white">
                                            <div class="panel-heading clearfix">
                                                <h4 class="panel-title">Ranking de Prazos Por Profissional</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive invoice-table ">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>Profissional</th>
                                                            <th style="background-color: #ed8a92;width: 100px;"><span class="label label-danger">Pendente</span></th>

                                                            <th style="background-color: #91bdf7;width: 100px;"><span class="label label-info">Renunciado</span></th>
                                                            <th  style="background-color: #b0f3c8;width: 100px;"><span class="label label-success">Protocolado</span></th>
                                                            <th style="background-color: #f7dc70;width: 100px;"><span class="label label-warning">Cancelado</span></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php //aqui vou fazer o ranking por topo
                                                        $rsRankingPrazos = $demanda->rankingPrazosPorProf();
                                                        foreach($rsRankingPrazos as $dados){
                                                            ?>
                                                            <tr>
                                                                <th scope="row"><?php echo $dados->profissional; ?></th>
                                                                <td style="background-color: #ed8a92; color:#000; font-weight: bold;"><a href="rel_prazos.php?empresa_id=&prof=<?php echo $dados->profissional_id; ?>&status=P"><?php echo $dados->total_prazos_pentente; ?></a></td>

                                                                <td style="background-color: #91bdf7; color:#000; font-weight: bold;"><a href="rel_prazos.php?empresa_id=&prof=<?php echo $dados->profissional_id; ?>&status=E"><?php echo $dados->total_prazos_renunciado; ?></a></td>
                                                                <td style="background-color: #b0f3c8; color:#000; font-weight: bold;"><a href="rel_prazos.php?empresa_id=&prof=<?php echo $dados->profissional_id; ?>&status=R"><?php echo $dados->total_prazos_protocolado; ?></a></td>
                                                                <td style="background-color: #f7dc70; color:#000; font-weight: bold;"><a href="rel_prazos.php?empresa_id=&prof=<?php echo $dados->profissional_id; ?>&status=X"><?php echo $dados->total_prazos_cancelado; ?></a></td>

                                                            </tr>
                                                        <?php } ?>


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                            } 
                            $PermDashRankProf = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"dash_rank_prof");
                            if ($PermDashRankProf > 0){
                            ?> 
                                <div class="row">

                                    <div class="col-lg-12 col-md-12">
                                        <div class="panel panel-white">
                                            <div class="panel-heading clearfix">
                                                <h4 class="panel-title">Ranking por Profissionais </h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive invoice-table">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>Profissional</th>
                                                            <th style="background-color: #ed8a92;width: 100px;"><span class="label label-danger">Pendentes</span></th>
                                                            <th  style="background-color: #91bdf7;width: 100px;"><span class="label label-info">Total</span></th>
                                                            <th style="background-color: #b0f3c8;width: 100px;"><span class="label label-success">Conluidas</span></th>
                                                            <th style="background-color: #f7dc70;width: 100px;"><span class="label label-warning">Solicitar Protocolo</span></th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php //aqui vou fazer o ranking por topo
                                                        $rsRankingProf = $demanda->rankingProfissional();
                                                        foreach($rsRankingProf as $dados){
                                                            ?>
                                                            <tr>
                                                                <th scope="row" ><?php echo $dados->nome; ?></th>
                                                                <td style="background-color: #ed8a92; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id; ?>&status=P&tipo=&data_inicio=&data_fim="><?php  echo $dados->total_demanda_pendente; ?></a></td>
                                                                <td style="background-color: #91bdf7; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id; ?>&status=&tipo=&data_inicio=&data_fim="><?php echo $dados->total_demanda; ?></td>
                                                                <td style="background-color: #b0f3c8; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id; ?>&status=C&tipo=&data_inicio=&data_fim="><?php 
                                                                
                                                                $somaCnCalculo = 0;
                                                                if ($rsProfissional->id == "11" or $rsProfissional->id == "96" or $rsProfissional->id == "61"){
                                                                    //busco a conta na tabela
                                                                    $rsCalculo = $demanda->buscaContaDemandasCalculoSemData($rsProfissional->id);
                                                                    $somaCnCalculo = $rsCalculo->contagem;
                                                                }else{
                                                                    $somaCnCalculo = 0;
                                                                }
                                                                    echo ($dados->total_demanda_concluida)+($somaCnCalculo); 
                                                                
                                                                ?></td>
                                                                <td style="background-color: #f7dc70; color:#000; font-weight: bold;"><a href="rel_demanda.php?empresa=&prof=<?php echo $dados->profissional_id; ?>&status=R&tipo=&data_inicio=&data_fim="><?php echo $dados->total_demanda_protocolo; ?></td>

                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- Row -->
                            <?php } ?>  




                        <?php } ?>

                    </div><!-- Main Wrapper -->
                    <div class="page-footer">
                        <?php echo $pagina->getFooterCopy();?>
                    </div>
                </div><!-- /Page Inner -->

                <?php echo $pagina->chatLateral(); ?>

            </div><!-- /Page Content -->
        </div><!-- /Page Container -->
        
        
        <?php echo $pagina->getFooter(); ?>
        <script src="assets/plugins/datatables/js/jquery.datatables.min.js"></script>
        <script src="assets/js/pages/table-data.js"></script>
        <script src="assets/plugins/toastr/toastr.min.js"></script>
        <script src="assets/js/pages/ui-notifications.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="assets/js/jquery.maskMoney.min.js"></script>


        <script>
            $(document).ready(function() {
                


                var options = {
                    series: [
                        <?php
                        $dsTiposDemanda = $demanda->buscaTipoDemandas();
                        foreach($dsTiposDemanda as $tipos){

                        ?>
                        {
                            name: '<?php echo $tipos->nome; ?>',
                            type: 'column',
                            data: [<?php
                                for ($i = 0; $i <= 9; $i++) {
                                    $rsTotal = $demanda->buscaTotalDeDemandasPorTipoPorData($tipos->id,date('Y-m-d', strtotime('-'.$i.' days', strtotime(date('Y-m-d')))));
                                    foreach($rsTotal as $dados){
                                        echo $dados->total;
                                    }
                                    if ($i <> 9){
                                        echo ",";
                                    }
                                }
                                ?>
                            ]
                        },
                        <?php } ?>

                        {
                            name: 'Total de Demandas',
                            type: 'line',
                            data: [<?php
                                for ($i = 0; $i <= 9; $i++) {
                                    $rsTotal = $demanda->buscaTotalDeDemandasPorData(date('Y-m-d', strtotime('-'.$i.' days', strtotime(date('Y-m-d')))));
                                    foreach($rsTotal as $dados){
                                        echo $dados->total;
                                    }
                                    if ($i <> 9){
                                        echo ",";
                                    }
                                }
                                ?>]
                        }],
                    chart: {
                        height: 350,
                        type: 'line',
                        stacked: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: [1, 1, 4]
                    },
                    title: {
                        text: 'Demostrativo de Entrada de Demandas',
                        align: 'left',
                        offsetX: 110
                    },
                    xaxis: {
                        categories: [<?php
                            $semana = array(
                                'en' => array('/Sun/', '/Mon/', '/Tue/', '/Wed/', '/Thu/', '/Fri/', '/Sat/' , '/Jan/', '/Feb/', '/Mar/', '/Apr/', '/May/', '/Jun/', '/Jul/', '/Aug/', '/Sep/', '/Oct/', '/Nov/', '/Dec/'),
                                'short' => array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez')
                            );
                            for ($i = 0; $i <= 9; $i++) {
                                if ($i==0) {
                                    echo "'HOJE'";
                                }else{
                                    echo "'" . preg_replace($semana['en'], $semana['short'], date('d/m/Y (D)', strtotime('-' . $i . ' days', strtotime(date('Y-m-d'))))) . "'";
                                }
                                if ($i <> 29){
                                    echo ",";
                                }
                            }
                            ?>],
                    },
                    yaxis: [
                        {
                            axisTicks: {
                                show: true,
                            },
                            axisBorder: {
                                show: true,
                                color: '#008FFB'
                            },
                            labels: {
                                style: {
                                    colors: '#008FFB',
                                }
                            },
                            title: {
                                text: "Demandas",
                                style: {
                                    color: '#008FFB',
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        },


                    ],
                    tooltip: {
                        fixed: {
                            enabled: true,
                            position: 'topLeft', // topRight, topLeft, bottomRight, bottomLeft
                            offsetY: 30,
                            offsetX: 60
                        },
                    },
                    legend: {
                        horizontalAlign: 'left',
                        offsetX: 40
                    }
                };

                var chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();



                $("select.tipo_demanda").change(function(){
                    var id = $(this).children("option:selected").val();
                    var text = $(this).children("option:selected").text();

                    $.ajax({
                        url: "ajax.fases.php",
                        type: 'post',
                        data: {
                            search: ''+id+'',
                            tipo: 'dash_fases_tipo_demanda'
                        },
                        success: function(response) {
                            $('.table_fases').html(response);
                            $('.nome_tipo').html(text);


                        }
                    });

                });



                                    $('.profissional').on('change', function() {                                    
                                    window.location.href = "dash_producao.php?prof="+this.value;
                                    });
                                    $('.unidade').on('change', function() {
                                        window.location.href = "dash_producao_un.php?emp="+this.value;
                                    });

                                    
                                

            });

            function aprovaParceiro(id){
                var r = confirm("Deseja aprovar?");
                var p = confirm("Deseja alterar porcentagem?");
                if (p) {
                    var nova_porcentagem = prompt("Informe nova porcentagem?");

                }
                if (r ) {
                    $.ajax({
                        url: "ajax.parceiros.php",
                        type: 'post',
                        data: {id: id, tipo: 'aprova', porcentagem: nova_porcentagem},
                        beforeSend: function () {
                            //$("#resultado").html("ENVIANDO...");
                        }
                    }).done(function (msg) {
                        alert("Aprovado!");
                        location.href = "index.php";
                    }).fail(function (jqXHR, textStatus, msg) {
                        //alert(msg);
                    });
                }
            }
            function abreParceiro(id){
                location.href = "alt_parceiros.php?id="+id;
            }

            //aqui fazemos a edição de parceiros 
            function acionaEdicaoParceiro(id){
                
                //aqui vou ter que acionar o campo de texto e o campo de salvar e cancelar.
                $('.acionaEdicaoParceiro_'+id).hide();
                $('.salvaEdicaoParceiro_'+id).show();
                $('.cancelaEdicaoParceiro_'+id).show();
                $('.porcentagem_'+id).show();
                
            }

            function salvaEdicaoParceiro(id){
                
                //ok vamos salvar o rolê 
                var porcentagem = $('.porcentagem_'+id).val();
                $.ajax({
                        url: "ajax.parceiros.php",
                        type: 'post',
                        data: {id: id, tipo: 'muda_porcentagem_parceiro', porcentagem: porcentagem},
                        beforeSend: function () {
                            //$("#resultado").html("ENVIANDO...");
                        }
                    }).done(function (msg) {
                        
                        $('.acionaEdicaoParceiro_'+id).show();
                        $('.salvaEdicaoParceiro_'+id).hide();
                        $('.cancelaEdicaoParceiro_'+id).hide();
                        $('.porcentagem_'+id).hide();
                        $('.txt_EdicaoParceiro_'+id).html(porcentagem);
                        alert("Porcentagem alterada!");
                        
                        
                    }).fail(function (jqXHR, textStatus, msg) {
                        alert("Algo de errado aconteceu, contate o suporte!");
                    });

            }
            function salvaEdicaoParceiroValor(id){

                
                
                //ok vamos salvar o rolê 
                var valor = $('.valor_processo_'+id).val();
                $.ajax({
                        url: "ajax.parceiros.php",
                        type: 'post',
                        data: {id: id, tipo: 'muda_valor_parceiro', valor: valor},
                        beforeSend: function () {
                            //$("#resultado").html("ENVIANDO...");
                        }
                    }).done(function (msg) {
                        
                        $('.acionaEdicaoParceiroValor_'+id).show();
                        $('.salvaEdicaoParceiroValor_'+id).hide();
                        $('.cancelaEdicaoParceiroValor_'+id).hide();
                        $('.valor_processo_'+id).hide();
                        $('.txt_EdicaoParceiroValor_'+id).html(valor);
                        alert("Valor alterada!");
                        
                        
                    }).fail(function (jqXHR, textStatus, msg) {
                        alert("Algo de errado aconteceu, contate o suporte!");
                    });

            }

            function cancelaEdicaoParceiro(id){
                
                $('.acionaEdicaoParceiro_'+id).show();
                $('.salvaEdicaoParceiro_'+id).hide();
                $('.cancelaEdicaoParceiro_'+id).hide();
                $('.porcentagem_'+id).hide();
            }

            function cancelaEdicaoParceiroValor(id){
                
                $('.acionaEdicaoParceiroValor_'+id).show();
                $('.salvaEdicaoParceiroValor_'+id).hide();
                $('.cancelaEdicaoParceiroValor_'+id).hide();
                $('.valor_processo_'+id).hide();
            }

            
            function acionaEdicaoParceiroProcesso(id){
                
                //aqui vou ter que acionar o campo de texto e o campo de salvar e cancelar.
                $('.acionaEdicaoParceiroProcesso_'+id).hide();
                $('.salvaEdicaoParceiroProcesso_'+id).show();
                $('.cancelaEdicaoParceiroProcesso_'+id).show();
                $('.processo_'+id).show();
                
            }

            function acionaEdicaoParceiroValor(id){

                $(".valor_processo_"+id).maskMoney({
                    prefix: "",
                    decimal: ".",
                    thousands: ""
                });
                
                //aqui vou ter que acionar o campo de texto e o campo de salvar e cancelar.
                $('.acionaEdicaoParceiroValor_'+id).hide();
                $('.salvaEdicaoParceiroValor_'+id).show();
                $('.cancelaEdicaoParceiroValor_'+id).show();
                $('.valor_processo_'+id).show();
                
            }

            function salvaEdicaoParceiroProcesso(id){
                
                //ok vamos salvar o rolê 
                var processo = $('.processo_'+id).val();
                $.ajax({
                        url: "ajax.parceiros.php",
                        type: 'post',
                        data: {id: id, tipo: 'muda_processo_parceiro', processo: processo},
                        beforeSend: function () {
                            //$("#resultado").html("ENVIANDO...");
                        }
                    }).done(function (msg) {
                        
                        $('.acionaEdicaoParceiroProcesso_'+id).show();
                        $('.salvaEdicaoParceiroProcesso_'+id).hide();
                        $('.cancelaEdicaoParceiroProcesso_'+id).hide();
                        $('.processo_'+id).hide();
                        $('.txt_EdicaoParceiroProcesso_'+id).html(processo);
                        alert("Processo do parceiro alterada!");
                        
                        
                    }).fail(function (jqXHR, textStatus, msg) {
                        alert("Algo de errado aconteceu, contate o suporte!");
                    });

            }

            function cancelaEdicaoParceiroProcesso(id){
                
                $('.acionaEdicaoParceiroProcesso_'+id).show();
                $('.salvaEdicaoParceiroProcesso_'+id).hide();
                $('.cancelaEdicaoParceiroProcesso_'+id).hide();
                $('.processo_'+id).hide();
            }

            function excParceiro(id){
                var r = confirm("Deseja realmente excluir?");
                if (r ) {
                    $.ajax({
                        url: "ajax.parceiros.php",
                        type: 'post',
                        data: {id: id, tipo: 'excluir'},
                        beforeSend: function () {
                            //$("#resultado").html("ENVIANDO...");
                        }
                    }).done(function (msg) {
                        alert("Parceiro excluido!");
                        location.href = "index.php";
                    }).fail(function (jqXHR, textStatus, msg) {
                        //alert(msg);
                    });
                }
            }


        </script>
    </body>
</html>