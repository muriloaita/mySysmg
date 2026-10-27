<?php
require 'class/Usuarios.php';
require 'class/Uteis.php';
$usuarios=new Usuarios();
$usuarios->protege();
$pagina = new Pagina();
$demanda = new Demanda();

//tenho que ver se o cara tem permissão e gravar o log
$usuario = new Usuario();
$log = new Log();
$autoriza = $usuario->buscaPaginaPorUsuario($_SESSION['usuario']['id'],str_replace(".php","",basename($_SERVER['PHP_SELF'])));
if ($autoriza > 0){
    //quer dizer que tem permissão, gravo log
    $log->cadLog("Listagen de Demanda", " acessado por ".$_SESSION["usuario"]["nome"]." ");
}else{
    //aqui expulso o cara aqui não vou fazer isso.
    $usuarios->logout();
}

$rsPref = $demanda->buscaPrefsDemanda($_SESSION['usuario']['id']);

?>
<!DOCTYPE html>
<html lang="pr_BR">
<?php
echo $pagina->getHead("Consulta de Demandas - ");
?>
<header>
    <link href="assets/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/css/jquery.datatables_themeroller.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/toastr/toastr.min.css" rel="stylesheet">
</header>

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
                <h3 class="breadcrumb-header"><a href="index.php">Inicio</a> > Consulta de Demandas</h3>

            </div>
            <div class="panel panel-white">

                <div class="panel-body">
                    <?php //aqui tenho que ver se ele tem permissão para cadastrar
                    $rsPodeCad = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'],"cad_demanda");
                    if ($rsPodeCad > 0){ //quer  dizer que pode!
                        ?>
                        <button type="button" onclick="location.href='cad_demanda.php'" class="btn btn-success btn-addon"><i class="fa fa-comment-o"></i> Cadastrar Nova Demanda</button>
                    <?php } ?>
                    <label>
                        <?php if ($rsPref->lista_concluidos_demanda == 1){
                            $check = "checked";
                        }else{
                            $check = "";
                        }
                        ?>
                        | Ocultar Concluidos ? <input type="checkbox" name="lista_concluidos_demanda" class="lista_concluidos_demanda" value="1" <?php echo $check;?> >
                    </label>
                    <label style="margin-left: 10px;">
                        | Status:
                        <select id="filtro_status" name="filtro_status" class="form-control input-sm" style="display: inline-block; width: auto; margin-left: 5px;">
                            <option value="">Todos</option>
                            <option value="P">Pendente</option>
                            <option value="A">Atendendo</option>
                            <option value="C">Concluido</option>
                            <option value="R">Solicitado Protocolo</option>
                            <option value="S">Standby</option>
                            <option value="X">Cancelada</option>
                        </select>
                    </label>
                    <label style="margin-left: 10px;">
                        | Ordenar:
                        <select id="ordem_demanda" name="ordem_demanda" class="form-control input-sm" style="display: inline-block; width: auto; margin-left: 5px;">
                            <option value="">Data Limite (padrão)</option>
                            <option value="recentes">Mais recentes primeiro</option>
                            <option value="antigos">Mais antigos primeiro</option>
                        </select>
                    </label>
                </div>
            </div>
            <div id="main-wrapper">


                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">

                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="exampleDemanda" class="display table" style="width: 100%; cellspacing: 0;">
                                        <thead>
                                        <tr>
                                            <th>Autor</th>
                                            <th>Profissional</th>
                                            <th>Polo Ativo</th>
                                            <th>Polo Passivo</th>

                                            <th>Assunto</th>
                                            <th>Status</th>
                                            <th>Data Entrada</th>
                                            <th>Data Limite</th>
                                            <th>Ação</th>
                                        </tr>
                                        </thead>
                                        <?php /*
                                                <tbody>

                                                <?php
                                                $row = $demanda->buscaDemandas();
                                                foreach($row as $dados){
                                                    //P = Pendentente , A Atendento, C Concluido, X Cancelado
                                                    if ($dados->status == "P"){
                                                        $cor = "danger";
                                                    }elseif ($dados->status == "C"){
                                                        $cor = "success";
                                                    }elseif ($dados->status == "A"){
                                                        $cor = "active";
                                                    }else{
                                                        $cor = ""; //cancelado é sem cor
                                                    }

                                                    echo '<tr class="'.$cor.'">
                                                        <td>';
                                                        $row2 = $demanda->buscaEntidadesDemandaPorPolo($dados->id,"A");
                                                        foreach($row2 as $dadosEntidade){
                                                            echo $dadosEntidade->nomerazao;
                                                        }
                                                         echo '</td>
                                                          <td>';
                                                        $row3 = $demanda->buscaEntidadesDemandaPorPolo($dados->id,"P");
                                                        foreach($row3 as $dadosEntidade2){
                                                            echo $dadosEntidade2->nomerazao;
                                                        }
                                                         echo '</td>
                                                        <td>'.$dados->assunto.'</td>
                                                        <td>';
                                                        if($dados->status=="C"){ echo "Concluido";}elseif($dados->status=="P"){ echo "Pendente";}elseif($dados->status=="A"){ echo "Atentendo";}else{echo "Cancelado";}
                                                    echo'</td>
                                                        <td>'.dma(substr($dados->data_entrada,0,10))." ".substr($dados->data_entrada,11,9).'</td>
                                                        <td>';
                                                    $rsPodeCad = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'], "alt_demanda");
                                                    if ($rsPodeCad > 0) { //quer  dizer que pode!
                                                        echo '<button type="button" alt="Alterar" onclick="location.href=\'alt_demanda.php?id='.$dados->id.'\'" class=" btn btn-info btn-addon btn-xs"><i class="fa fa-pencil"></i> </button>';
                                                        echo '<button type="button" alt="Excluir" onclick="excluir('.$dados->id.')" class="btn btn-danger btn-addon btn-xs"><i class="fa fa-trash-o"></i> </button>';
                                                    }

                                                          echo '
                                                        </td>
                                                    </tr>
                                                    ';
                                                }

                                                ?>





                                                </tbody> <? */ ?>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </div><!-- Row -->
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

<script>
    $( document ).ready(function() {
        const date1 = new Date();
        var date1_day = date1.getDate();
        var date1_month = date1.getMonth();
        var date1_year = date1.getFullYear();


        if($(".lista_concluidos_demanda").prop('checked')) {
            var status = 1;
        }else{
            var status = 0;
        }

        $(".lista_concluidos_demanda").change(function() {
            if($(this).prop('checked')) {
                //aqui ativei
                $.ajax({
                    url: "ajax.demanda.php",
                    type: 'post',
                    data: {id: 1, tipo: 'update_profile'},
                    beforeSend: function () {
                        console.log("Enviou a atualização 1");
                    }
                }).done(function (msg) {
                    if (msg == "ok") {
                        toastr.success("Preferencias atualizadas", "Informação do Sistema");
                        setInterval(function(){ location.href = "list_demanda.php"; }, 1000);

                    }else {
                        alert("erro ao alterar!");
                    }
                }).fail(function (jqXHR, textStatus, msg) {
                    alert(msg);
                });
            } else {
                //desativei
                $.ajax({
                    url: "ajax.demanda.php",
                    type: 'post',
                    data: {id: 0, tipo: 'update_profile'},
                    beforeSend: function () {
                        console.log("Enviou a atualização 0");
                    }
                }).done(function (msg) {
                    if (msg == "ok") {
                        toastr.success("Preferencias atualizadas", "Informação do Sistema");
                        setInterval(function(){ location.href = "list_demanda.php"; }, 1000);
                    }else {
                        alert("erro ao alterar!");
                    }
                }).fail(function (jqXHR, textStatus, msg) {
                    alert(msg);
                });
            }
        });


        // Indices das colunas usadas pela ordenacao (Data Entrada = 6, Data Limite = 7)
        var COL_DATA_ENTRADA = 6;
        var COL_DATA_LIMITE = 7;

        var tabelaDemanda = $('#exampleDemanda').DataTable( {
            "stateSave": true,
            "language": {
                "url": "assets/js/pages/Portuguese-Brasil.json"
            },
            'processing': true,
            'stateSave': true,
            'serverSide': true,
            'serverMethod': 'post',
            "pageLength": 50,
            "ordering": true,
            "order": [[ COL_DATA_LIMITE, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": false, "targets": 2 },
                { "orderable": false, "targets": 3 },
                { "orderable": false, "targets": 4 },
                { "orderable": false, "targets": 5 },
                { "orderable": true, "targets": 6 },
                { "orderable": true, "targets": 7 },
                { "orderable": false, "targets": 8 }
            ],

            "columns": [

                { data: 'autor'},
                { data: 'profissional'},
                { data: 'polo_ativo'},
                { data: 'polo_passivo'},
                { data: 'assunto'},
                { data: 'status'},
                { data: 'data_entrada'},
                { data: 'data_limite'},
                { data: 'act'}

            ],
            'ajax': {
                'url':'ajax.list_demanda.php?status='+status,
                // envia o filtro de status junto com cada requisicao do DataTables
                'data': function (d) {
                    d.filtro_status = $('#filtro_status').val();
                }
            },
            // guarda/restaura o filtro de status junto com o estado salvo da tabela (stateSave)
            "stateSaveParams": function (settings, data) {
                data.filtro_status = $('#filtro_status').val();
            },
            "stateLoadParams": function (settings, data) {
                if (typeof data.filtro_status !== 'undefined' && $('#filtro_status option[value="' + data.filtro_status + '"]').length) {
                    $('#filtro_status').val(data.filtro_status);
                }
            },
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                const date2 = new Date(aData['data_limite']);
                var date2_day = date2.getDate();
                var date2_month = date2.getMonth();
                var date2_year = date2.getFullYear();


                //console.log("Data1:"+date2.getDate()+",Data2:"+date1.getDate());
                //console.log('Debug de :'+ aData['status']);

                if ( aData['status'] == "Concluido" )
                {
                    $('td', nRow).css('background-color', '#92d1a9');
                }
                else if ( aData['status'] == "Pendente" && date2_day >= date1_day && date2_month >= date1_month && date2_year >= date1_year)
                {
                    $('td', nRow).css('background-color', '#ea7c84');
                }
                else if ( aData['status'] == "Atendendo" )
                {
                    $('td', nRow).css('background-color', '#75a0d8');
                }
                else if ( aData['status'] == "Cancelado" )
                {
                    $('td', nRow).css('background-color', '#75a0d8');
                }else if ( aData['status'] == "Standby" )
                {
                    $('td', nRow).css('background-color', '#f7aca3');
                }
            }
        });

        // Filtro por status: recarrega a tabela (volta para a primeira pagina)
        $('#filtro_status').change(function () {
            tabelaDemanda.ajax.reload();
        });

        // Ordenacao: mais antigos / mais recentes pela Data de Entrada; vazio = Data Limite (padrao)
        $('#ordem_demanda').change(function () {
            var ordem = $(this).val();
            if (ordem == 'antigos') {
                tabelaDemanda.order([COL_DATA_ENTRADA, 'asc']).draw();
            } else if (ordem == 'recentes') {
                tabelaDemanda.order([COL_DATA_ENTRADA, 'desc']).draw();
            } else {
                tabelaDemanda.order([COL_DATA_LIMITE, 'desc']).draw();
            }
        });

        // Mantem o select de ordenacao coerente com a ordem atual da tabela (estado salvo ou clique no cabecalho)
        function sincronizaOrdem() {
            var ord = tabelaDemanda.order();
            if (ord.length && ord[0][0] == COL_DATA_ENTRADA) {
                $('#ordem_demanda').val(ord[0][1] == 'asc' ? 'antigos' : 'recentes');
            } else {
                $('#ordem_demanda').val('');
            }
        }
        sincronizaOrdem();
        tabelaDemanda.on('order.dt', sincronizaOrdem);

        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "100",
            "hideDuration": "1000",
            "timeOut": "1000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }


    });

    function excluir(id){
        var r = confirm("Deseja realmente excluir?");
        if (r ) {
            $.ajax({
                url: "ajax.demanda.php",
                type: 'post',
                data: {id: id, tipo: 'excluir'},
                beforeSend: function () {
                    //$("#resultado").html("ENVIANDO...");
                }
            }).done(function (msg) {
                alert("Demanda excluida!");
                location.href = "list_demanda.php";
            }).fail(function (jqXHR, textStatus, msg) {
                //alert(msg);
            });
        }
    }


</script>
</body>
</html>
