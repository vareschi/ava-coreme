<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/config.php';

$pdo = getPDO();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    die('Usuário não autenticado.');
}

// Pega o usuario_id do formulário (residente a ser salvo)
$usuario_id = $_POST['usuario_id'] ?? null;
if (!$usuario_id) {
    die('Usuário alvo não informado para salvar dados do residente.');
}

// Campos do formulário
$status             = $_POST['status'] ?? '';
$nome_pai           = $_POST['nome_pai'] ?? '';
$nome_mae           = $_POST['nome_mae'] ?? '';
$estado_civil       = $_POST['estado_civil'] ?? '';
$nome_conjuge       = $_POST['nome_conjuge'] ?? '';
$nacionalidade      = $_POST['nacionalidade'] ?? '';
$cor_etnica         = $_POST['cor_etnica'] ?? '';
$naturalidade       = $_POST['naturalidade'] ?? '';
$rg                 = $_POST['rg'] ?? '';
$orgao_rg           = $_POST['orgao_rg'] ?? '';
$data_expedicao_rg  = $_POST['data_expedicao_rg'] ?? null;
$pispasep           = $_POST['pispasep'] ?? '';
$titulo_eleitor     = $_POST['titulo_eleitor'] ?? '';
$zona               = $_POST['zona'] ?? '';
$secao              = $_POST['secao'] ?? '';
$cidade_eleitor     = $_POST['cidade_eleitor'] ?? '';
$reservista         = $_POST['reservista'] ?? '';
$crm                = $_POST['crm'] ?? '';
$grupo_sanguineo    = $_POST['grupo_sanguineo'] ?? '';
$fator_rh           = $_POST['fator_rh'] ?? '';
$especialidade_id   = $_POST['especialidade_id'] ?? null;
$preceptor_id       = $_POST['preceptor_id'] ?? null;
$curso              = $_POST['curso'] ?? '';
$faculdade          = $_POST['faculdade'] ?? '';
$sigla_faculdade    = $_POST['sigla_faculdade'] ?? '';
$data_inicio        = $_POST['data_inicio'] ?? null;
$data_termino       = $_POST['data_termino'] ?? null;
$peso               = $_POST['peso'] ?? '';
$altura             = $_POST['altura'] ?? '';
$data_cadastro      = date('Y-m-d H:i:s');

try {
    // Verifica se já existe registro para o usuario_id
    $stmtVerifica = $pdo->prepare("SELECT COUNT(*) FROM residentes WHERE usuario_id = ?");
    $stmtVerifica->execute([$usuario_id]);
    $existe = $stmtVerifica->fetchColumn();

    if ($existe) {
        // Atualiza
        $sql = "UPDATE residentes SET 
            situacao_status = :status,
            nome_pai = :nome_pai,
            nome_mae = :nome_mae,
            estado_civil = :estado_civil,
            nome_conjuge = :nome_conjuge,
            nacionalidade = :nacionalidade,
            cor_etnica = :cor_etnica,
            naturalidade = :naturalidade,
            rg = :rg,
            orgao_expedidor = :orgao_rg,
            data_expedicao = :data_expedicao,
            pis_pasep = :pis_pasep,
            titulo_eleitor = :titulo_eleitor,
            zona = :zona,
            secao = :secao,
            cidade_eleitor = :cidade_eleitor,
            reservista = :reservista,
            crm = :crm,
            sistema_abo = :grupo_sanguineo,
            fator_rh = :fator_rh,
            especialidade_id = :especialidade_id,
            preceptor_id = :preceptor_id,
            curso = :curso,
            nome_faculdade = :nome_faculdade,
            sigla_faculdade = :sigla_faculdade,
            data_inicio = :data_inicio,
            data_termino = :data_termino,
            peso = :peso,
            altura = :altura
        WHERE usuario_id = :usuario_id";

    } else {
        // Insere
        $sql = "INSERT INTO residentes (
            usuario_id, situacao_status, nome_pai, nome_mae, estado_civil, nome_conjuge,
            nacionalidade, cor_etnica, naturalidade, rg, orgao_expedidor,
            data_expedicao, pis_pasep, titulo_eleitor, zona, secao, cidade_eleitor,
            reservista, crm, sistema_abo, fator_rh, especialidade_id, preceptor_id,
            curso, nome_faculdade, sigla_faculdade, data_inicio, data_termino,
            peso, altura, data_cadastro
        ) VALUES (
            :usuario_id, :status, :nome_pai, :nome_mae, :estado_civil, :nome_conjuge,
            :nacionalidade, :cor_etnica, :naturalidade, :rg, :orgao_expedidor,
            :data_expedicao, :pis_pasep, :titulo_eleitor, :zona, :secao, :cidade_eleitor,
            :reservista, :crm, :grupo_sanguineo, :fator_rh, :especialidade_id, :preceptor_id,
            :curso, :nome_faculdade, :sigla_faculdade, :data_inicio, :data_termino,
            :peso, :altura, :data_cadastro
        )";
    }

    $stmt = $pdo->prepare($sql);

    echo '<pre>';
    print_r($stmt->queryString);
    print_r($stmt->debugDumpParams());
    exit;

    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':status' => $status,
        ':nome_pai' => $nome_pai,
        ':nome_mae' => $nome_mae,
        ':estado_civil' => $estado_civil,
        ':nome_conjuge' => $nome_conjuge,
        ':nacionalidade' => $nacionalidade,
        ':cor_etnica' => $cor_etnica,
        ':naturalidade' => $naturalidade,
        ':rg' => $rg,
        ':orgao_expedidor' => $orgao_rg,
        ':data_expedicao' => $data_expedicao_rg,
        ':pis_pasep' => $pispasep,
        ':titulo_eleitor' => $titulo_eleitor,
        ':zona' => $zona,
        ':secao' => $secao,
        ':cidade_eleitor' => $cidade_eleitor,
        ':reservista' => $reservista,
        ':crm' => $crm,
        ':grupo_sanguineo' => $grupo_sanguineo,
        ':fator_rh' => $fator_rh,
        ':especialidade_id' => $especialidade_id,
        ':preceptor_id' => $preceptor_id,
        ':curso' => $curso,
        ':nome_faculdade' => $faculdade,
        ':sigla_faculdade' => $sigla_faculdade,
        ':data_inicio' => $data_inicio,
        ':data_termino' => $data_termino,
        ':peso' => $peso,
        ':altura' => $altura,
        ':data_cadastro' => $data_cadastro
    ]);

    header("Location: ../residente.php?usuario_id={$usuario_id}&ok=1");
    exit;

} catch (PDOException $e) {
    die("Erro ao salvar residente: " . $e->getMessage());
}
?>
