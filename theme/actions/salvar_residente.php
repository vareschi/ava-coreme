<?php
session_start();
require_once '../includes/config.php';

$pdo = getPDO();

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    die('Usuário não autenticado.');
}

// Dados recebidos via POST
$nome_completo = $_POST['nome_completo'] ?? '';
$nome_pai = $_POST['nome_pai'] ?? '';
$nome_mae = $_POST['nome_mae'] ?? '';
estado_civil = $_POST['estado_civil'] ?? '';
nome_conjuge = $_POST['nome_conjuge'] ?? '';
nacionalidade = $_POST['nacionalidade'] ?? '';
$cor_etnica = $_POST['cor_etnica'] ?? '';
grupo_sanguineo = $_POST['grupo_sanguineo'] ?? '';
fator_rh = $_POST['fator_rh'] ?? '';
naturalidade = $_POST['naturalidade'] ?? '';
$rg = $_POST['rg'] ?? '';
$orgao_expedidor = $_POST['orgao_expedidor'] ?? '';
data_expedicao = $_POST['data_expedicao'] ?? '';
$crm = $_POST['crm'] ?? '';
pis_pasep = $_POST['pis_pasep'] ?? '';
titulo_eleitor = $_POST['titulo_eleitor'] ?? '';
zona = $_POST['zona'] ?? '';
$secao = $_POST['secao'] ?? '';
cidade_eleitor = $_POST['cidade_eleitor'] ?? '';
$reservista = $_POST['reservista'] ?? '';
$email = $_POST['email'] ?? '';
$curso = $_POST['curso'] ?? '';
nome_faculdade = $_POST['nome_faculdade'] ?? '';
sigla_faculdade = $_POST['sigla_faculdade'] ?? '';
cidade_faculdade = $_POST['cidade_faculdade'] ?? '';
data_inicio = $_POST['data_inicio'] ?? '';
data_termino = $_POST['data_termino'] ?? '';
peso = $_POST['peso'] ?? '';
$altura = $_POST['altura'] ?? '';
$data_cadastro = date('Y-m-d H:i:s');

// Uploads de arquivos (exemplo com diploma)
$anexos = [];
$campos_arquivo = [
    'diploma', 'rg_frente', 'rg_verso', 'cnh', 'cpf', 'crm_doc', 'pis_doc',
    'titulo_doc', 'quitacao_militar', 'quitacao_eleitoral', 'vacina_covid',
    'vacina_tetano', 'vacina_sarampo', 'vacina_difteria', 'vacina_hepatite',
    'certidao_nascimento_frente', 'certidao_nascimento_verso', 'certidao_casamento_frente',
    'certidao_casamento_verso', 'comprovante_endereco', 'dados_bancarios',
    'seguro_rc', 'termo_compromisso', 'foto_3x4', 'tipagem_sanguinea'
];

$upload_path = '../uploads/residentes/';
foreach ($campos_arquivo as $campo) {
    if (!empty($_FILES[$campo]['name'])) {
        $ext = pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION);
        $nome_arquivo = $campo . '_' . time() . '.' . $ext;
        $destino = $upload_path . $nome_arquivo;
        if (move_uploaded_file($_FILES[$campo]['tmp_name'], $destino)) {
            $anexos[$campo] = $nome_arquivo;
        }
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO residentes (
        usuario_id, nome_completo, nome_pai, nome_mae, estado_civil, nome_conjuge, nacionalidade,
        cor_etnica, grupo_sanguineo, fator_rh, naturalidade, rg, orgao_expedidor, data_expedicao,
        crm, pis_pasep, titulo_eleitor, zona, secao, cidade_eleitor, reservista, email,
        curso, nome_faculdade, sigla_faculdade, cidade_faculdade, data_inicio, data_termino,
        peso, altura, data_cadastro
    ) VALUES (
        :usuario_id, :nome_completo, :nome_pai, :nome_mae, :estado_civil, :nome_conjuge, :nacionalidade,
        :cor_etnica, :grupo_sanguineo, :fator_rh, :naturalidade, :rg, :orgao_expedidor, :data_expedicao,
        :crm, :pis_pasep, :titulo_eleitor, :zona, :secao, :cidade_eleitor, :reservista, :email,
        :curso, :nome_faculdade, :sigla_faculdade, :cidade_faculdade, :data_inicio, :data_termino,
        :peso, :altura, :data_cadastro
    )");

    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':nome_completo' => $nome_completo,
        ':nome_pai' => $nome_pai,
        ':nome_mae' => $nome_mae,
        ':estado_civil' => $estado_civil,
        ':nome_conjuge' => $nome_conjuge,
        ':nacionalidade' => $nacionalidade,
        ':cor_etnica' => $cor_etnica,
        ':grupo_sanguineo' => $grupo_sanguineo,
        ':fator_rh' => $fator_rh,
        ':naturalidade' => $naturalidade,
        ':rg' => $rg,
        ':orgao_expedidor' => $orgao_expedidor,
        ':data_expedicao' => $data_expedicao,
        ':crm' => $crm,
        ':pis_pasep' => $pis_pasep,
        ':titulo_eleitor' => $titulo_eleitor,
        ':zona' => $zona,
        ':secao' => $secao,
        ':cidade_eleitor' => $cidade_eleitor,
        ':reservista' => $reservista,
        ':email' => $email,
        ':curso' => $curso,
        ':nome_faculdade' => $nome_faculdade,
        ':sigla_faculdade' => $sigla_faculdade,
        ':cidade_faculdade' => $cidade_faculdade,
        ':data_inicio' => $data_inicio,
        ':data_termino' => $data_termino,
        ':peso' => $peso,
        ':altura' => $altura,
        ':data_cadastro' => $data_cadastro
    ]);

    // Exemplo de salvamento de arquivos
    foreach ($anexos as $campo => $arquivo) {
        $stmtAnexo = $pdo->prepare("INSERT INTO residentes_arquivos (usuario_id, tipo, arquivo) VALUES (?, ?, ?)");
        $stmtAnexo->execute([$usuario_id, $campo, $arquivo]);
    }

    header("Location: ../residentes.php?ok=1");
    exit;
} catch (PDOException $e) {
    die("Erro ao salvar residente: " . $e->getMessage());
}
?>
