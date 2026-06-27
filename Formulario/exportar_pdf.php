<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}

require_once 'conexion.php';
require_once 'fpdf.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('ID de conflicto inválido.');
}

$conn = obtenerConexion();

$sql = "
    SELECT
        cc.id_conflicto,
        cc.fecha_registro,
        cc.descripcion,
        cc.estado_caso,
        CONCAT(f.nombres, ' ', f.apellidos) AS funcionario,
        GROUP_CONCAT(
            CONCAT(a.nombre, ' ', a.apellidos)
            SEPARATOR ', '
        ) AS alumnos
    FROM gestion_conflictos.casos_conflicto cc
    JOIN gestion_escolar.funcionarios f
        ON cc.id_funcionario_cargo = f.id_funcionario
    LEFT JOIN gestion_conflictos.detalle_alumnos_conflicto dac
        ON cc.id_conflicto = dac.id_conflicto
    LEFT JOIN gestion_escolar.alumnos a
        ON dac.nro_matricula_alumno = a.nro_matricula
    WHERE cc.id_conflicto = " . $conn->real_escape_string($id) . "
    GROUP BY cc.id_conflicto, cc.fecha_registro, cc.descripcion, cc.estado_caso, funcionario
";

$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    die('Conflicto no encontrado.');
}

$datos = $res->fetch_assoc();
$conn->close();

function utf8_to_latin1($texto) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto ?? '');
}

//Generar PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);


$pdf->SetFillColor(29, 78, 216);  
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 14, 'WOLDO', 0, 0, 'L', true);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 14, 'Plataforma de Gestion de Convivencia Escolar', 0, 1, 'R', true);

$pdf->SetFillColor(239, 246, 255);
$pdf->SetTextColor(30, 58, 138);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Reporte de Conflicto #' . $datos['id_conflicto'], 0, 1, 'L', true);
$pdf->Ln(4);

$imprimirCampo = function($label, $valor) use ($pdf) {
    $pdf->SetFillColor(249, 250, 251);
    $pdf->SetTextColor(75, 85, 99);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 8, $label, 0, 0, 'L', true);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(17, 24, 39);
    $pdf->Cell(0, 8, $valor, 0, 1, 'L', true);
    $pdf->Ln(1);
};

$imprimirCampo('Fecha de Registro:', date('d/m/Y H:i', strtotime($datos['fecha_registro'])));
$imprimirCampo('Funcionario a Cargo:', utf8_to_latin1($datos['funcionario']));
$imprimirCampo('Alumnos Involucrados:', utf8_to_latin1($datos['alumnos'] ?? 'Sin alumnos asociados'));
$imprimirCampo('Estado del Caso:', strtoupper(utf8_to_latin1($datos['estado_caso'])));

// Descripción
$pdf->Ln(3);
$pdf->SetFillColor(239, 246, 255);
$pdf->SetTextColor(30, 58, 138);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 8, 'DESCRIPCION DE LOS HECHOS', 0, 1, 'L', true);
$pdf->SetFillColor(249, 250, 251);
$pdf->SetTextColor(17, 24, 39);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 6, utf8_to_latin1($datos['descripcion'] ?? 'Sin descripcion.'), 0, 'L', true);

$pdf->Ln(8);
$pdf->SetTextColor(156, 163, 175);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, 'Generado el ' . date('d/m/Y H:i') . ' | Woldo - Sistema de Gestion Escolar', 0, 1, 'C');

$pdf->Output('I', 'Conflicto_' . $id . '.pdf');
?>