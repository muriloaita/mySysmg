<?php
require 'class/Usuarios.php';
require 'class/Uteis.php';
require_once 'class/ContratoHonorario.php';
require_once 'class/Supervisao.php';
$usuarios=new Usuarios();
$usuarios->protege();
$pagina = new Pagina();
$entidade = new Entidade();
$log = new Log();
$usuario = new Usuario();
$demanda = new Demanda();


//Jesus ajuda esse codigo!


if (isset($_POST['draw'])){

    ## Read value
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = ($_POST['search']['value']); // Search value


    $searchArray = array();
    $statusArray = array();

    ## Filtro por status (select da tela). Aceita somente os codigos validos.
    // P = Pendente, A = Atendendo, C = Concluido, X = Cancelada, R = Solicitado Protocolo, S = Standby
    $statusValidos = array('P','A','C','X','R','S');
    $filtroStatus = isset($_POST['filtro_status']) ? strtoupper(trim($_POST['filtro_status'])) : '';
    if (!in_array($filtroStatus, $statusValidos, true)) {
        $filtroStatus = '';
    }

    if ($filtroStatus != ''){
        // filtro explicito de status prevalece sobre a preferencia "Ocultar Concluidos"
        $add_status = ' de.status = :filtro_status  ';
        $add2_status = ' de.status = :filtro_status and   ';
        $statusArray = array('filtro_status'=>$filtroStatus);
    }elseif ($_GET['status'] == "1"){
        $add_status = ' de.status <> "C"  ';
        $add2_status = ' de.status <> "C" and   ';
    }else{
        $add_status = ' 1 ';
        $add2_status = '';
    }

## Search
    $searchQuery = " ";
    if($searchValue != ''){
        $searchQuery = " and
        (
        de.assunto LIKE :assunto or
            (select e.nomerazao  from demanda as de
                inner join demanda_has_entidade as h on h.demanda_id = de.id
                inner join entidade e on h.entidade_id = e.id where h.polo = 'P' limit 1
            ) LIKE :polo_passivo
        OR
            (select e.nomerazao  from demanda as de
                inner join demanda_has_entidade as h on h.demanda_id = de.id
                inner join entidade e on h.entidade_id = e.id where h.polo = 'A' limit 1
            ) LIKE :polo_ativo
        ) ";
        $searchArray = array(
            'assunto'=>"%$searchValue%",
            'polo_passivo'=>"%$searchValue%",
            'polo_ativo'=>"%$searchValue%"
        );
    }

## Total number of records without filtering
    if ($_SESSION["usuario"]["nivel"] == 10) {
        $stmt = $pdo->prepare("SELECT COUNT(de.id) AS allcount FROM demanda AS de INNER JOIN profissional AS p ON de.profissional_id = p.id LEFT JOIN profissional AS r ON de.autor = r.id  WHERE ".$add_status."  ");
    }else{
        $stmt = $pdo->prepare("SELECT COUNT(de.id) AS allcount FROM demanda AS de INNER JOIN profissional AS p ON de.profissional_id = p.id LEFT JOIN profissional AS r ON de.autor = r.id  WHERE ".$add_status." and p.id = '".$_SESSION["usuario"]["profissional_id"]."'   ");
    }
    $stmt->execute($statusArray);
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

## Total number of records with filtering
    if ($_SESSION["usuario"]["nivel"] == 10) {
        $stmt = $pdo->prepare("SELECT COUNT(de.id) AS allcount FROM demanda AS de INNER JOIN profissional AS p ON de.profissional_id = p.id LEFT JOIN profissional AS r ON de.autor = r.id  WHERE ".$add_status."   " . $searchQuery);
    }else{
        $stmt = $pdo->prepare("SELECT COUNT(de.id) AS allcount FROM demanda AS de INNER JOIN profissional AS p ON de.profissional_id = p.id LEFT JOIN profissional AS r ON de.autor = r.id WHERE ".$add_status." and p.id = '".$_SESSION["usuario"]["profissional_id"]."'   " . $searchQuery);
    }

    $stmt->execute(array_merge($statusArray, $searchArray));
    $records = $stmt->fetch();
    //$stmt->debugDumpParams();
    $totalRecordwithFilter = $records['allcount'];

## Fetch records

    if ($_SESSION["usuario"]["nivel"] == 10) {
        $stmt = $pdo->prepare("SELECT de.*, p.nome AS profissional,r.nome AS nome_autor,
(SELECT en.nomerazao FROM entidade AS en INNER JOIN demanda_has_entidade AS ha ON en.id = ha.entidade_id WHERE ha.polo = 'P' AND ha.demanda_id = de.id LIMIT 1) AS polo_passivo,
(SELECT ti.nomerazao FROM entidade AS ti INNER JOIN demanda_has_entidade AS sa ON ti.id = sa.entidade_id WHERE sa.polo = 'A' AND sa.demanda_id = de.id LIMIT 1) AS polo_ativo,
(SELECT ec.contrato_honorario FROM entidade AS ec INNER JOIN demanda_has_entidade AS ca ON ec.id = ca.entidade_id WHERE ca.polo = 'A' AND ca.demanda_id = de.id LIMIT 1) AS ch_contrato
FROM demanda AS de
INNER JOIN profissional AS p ON de.profissional_id = p.id LEFT JOIN profissional AS r ON de.autor = r.id  WHERE ".$add_status."  " . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");
    }else{

        $stmt = $pdo->prepare("SELECT de.*, p.nome AS profissional,r.nome AS nome_autor,
(SELECT en.nomerazao FROM entidade AS en INNER JOIN demanda_has_entidade AS ha ON en.id = ha.entidade_id WHERE ha.polo = 'P' AND ha.demanda_id = de.id limit 1) AS polo_passivo,
(SELECT ti.nomerazao FROM entidade AS ti INNER JOIN demanda_has_entidade AS sa ON ti.id = sa.entidade_id WHERE sa.polo = 'A' AND sa.demanda_id = de.id limit 1) AS polo_ativo,
(SELECT ec.contrato_honorario FROM entidade AS ec INNER JOIN demanda_has_entidade AS ca ON ec.id = ca.entidade_id WHERE ca.polo = 'A' AND ca.demanda_id = de.id limit 1) AS ch_contrato
FROM demanda AS de
INNER JOIN profissional AS p ON de.profissional_id = p.id LEFT JOIN profissional AS r ON de.autor = r.id  WHERE ".$add2_status." p.id = '".$_SESSION["usuario"]["profissional_id"]."'   " . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");


    }


// Bind values
    foreach(array_merge($statusArray, $searchArray) as $key=>$search){
        $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);


    $stmt->execute();
    //$stmt->debugDumpParams();
    $empRecords = $stmt->fetchAll();

    // === Supervisao: mapa demanda_id => supervisor da pagina (1 query, defensivo pre-migration) ===
    $svObj = new Supervisao();
    $svIds = array();
    foreach($empRecords as $svRow){ $svIds[] = (int)$svRow['id']; }
    $svMapa = $svObj->mapaSupervisores('D', $svIds);
    // === /Supervisao ===

    $data = array();

    foreach($empRecords as $row){

        $acao_html = "";
        //aqui tenho que ver se ele tem permissão para cadastrar
        $rsPodeCad = $usuario->buscaPaginasNomePorUsuario($_SESSION['usuario']['id'], "alt_demanda");
        if ($rsPodeCad > 0) { //quer  dizer que pode!

            //trata ação
            $acao_html = "<button type=\"button\" alt=\"Alterar\" onclick=\"location.href='alt_demanda.php?id=" . $row['id'] . "'\" class=\"btn btn-info btn-xs\"><i class=\"fa fa-pencil\"></i> </button>";

            if ($_SESSION['usuario']['id'] == "6" or  $_SESSION['usuario']['id'] == "40"  or  $_SESSION['usuario']['id'] == "1" or  $_SESSION['usuario']['id'] == "5" or  $_SESSION['usuario']['id'] == "20" or  $_SESSION['usuario']['id'] == "45" ) {
                $acao_html .= "<button type=\"button\" alt=\"Deletar\" onclick=\"javascript:excluir(" . $row['id'] . ")\" class=\"btn btn-danger btn-xs\"><i class=\"fa fa-close\"></i> </button>";
            }


        }
        if ($row['status']=="P"){
            $status = "Pendente";
        }elseif ($row['status']=="A"){
            $status = "Atendendo";
        }elseif ($row['status']=="C"){
            $status = "Concluido";
        }elseif ($row['status']=="X"){
            $status = "Cancelada";
        }elseif ($row['status']=="R"){
            $status = "Solicitado Protocolo";
        }elseif ($row['status']=="S"){
            $status = "Standby";
        }else{
            $status = "Erro";
        }
        $data[] = array(
            "autor"=>(badgeSociedade(isset($row['sociedade'])?$row['sociedade']:'')." ".$row['nome_autor']),
            "profissional"=>($row['profissional'].(isset($svMapa[(int)$row['id']]) ? " ".badgeSupervisor($svMapa[(int)$row['id']]->nome) : "")),
            "polo_ativo"=>((array_key_exists('ch_contrato',$row) and $row['polo_ativo'] != '') ? badgeContratoHonorario($row['ch_contrato'])." ".$row['polo_ativo'] : $row['polo_ativo']),
            "polo_passivo"=>($row['polo_passivo']),

            "assunto"=>strtoupper(($row['assunto'])),
            "status"=>($status),
            "data_entrada"=>(dma(substr($row['data_entrada'],0,10))),
            "data_limite"=>(dma(substr($row['data_limite'],0,10))),
            "act"=> $acao_html
        );
    }

## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    echo json_encode($response);

}
