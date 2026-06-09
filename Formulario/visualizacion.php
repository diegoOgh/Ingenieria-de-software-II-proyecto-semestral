<?php
require_once 'conexion.php';

$conn = obtenerConexion();

$sql = "
    SELECT
        cc.id_conflicto,
        cc.fecha_registro,
        cc.descripcion,
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

    GROUP BY
        cc.id_conflicto,
        cc.fecha_registro,
        cc.descripcion,
        funcionario

    ORDER BY cc.id_conflicto DESC
";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es" class="bg-gray-50 text-gray-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Woldo | Conflictos Registrados</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col justify-between font-sans antialiased">

    <header class="bg-blue-700 text-white shadow-md py-6 px-4 mb-8">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight">Plataforma Woldo</h1>
                <p class="text-sm text-blue-100 mt-1">Módulo de Organización y Resolución de Conflictos Escolares</p>
            </div>
            <a href="index.html" class="self-start md:self-center bg-blue-600 hover:bg-blue-800 text-white text-sm font-medium py-2 px-4 rounded-lg shadow border border-blue-500 transition duration-150 text-center">
                ➕ Registrar Nuevo Incidente
            </a>
        </div>
    </header>

    <main class="flex-grow w-full max-w-6xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8">
            
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Conflictos Registrados</h2>
                <p class="text-sm text-gray-500 mt-1">Historial completo de incidentes reportados en el establecimiento.</p>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                <table class="w-full text-sm text-left text-gray-600 min-w-[800px]">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 font-semibold text-center w-16">ID</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Funcionario a Cargo</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Alumnos Involucrados</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold w-40">Fecha</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Descripción de los Hechos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while($fila = $resultado->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-4 py-4 font-bold text-gray-900 text-center bg-gray-50/30">
                                        <?= $fila['id_conflicto'] ?>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        <?= htmlspecialchars($fila['funcionario']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-gray-700 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-md text-xs font-medium inline-block">
                                            <?= htmlspecialchars($fila['alumnos'] ?? 'Sin alumnos asociados') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                        <?= date("d/m/Y H:i", strtotime($fila['fecha_registro'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 max-w-sm break-words" id="desc-<?= $fila['id_conflicto'] ?>">
                                        <span class="desc-texto"><?= htmlspecialchars($fila['descripcion'] ?? '') ?></span>
                                        <button onclick="editarDescripcion(<?= $fila['id_conflicto'] ?>)" 
                                                class="ml-2 text-blue-500 hover:text-blue-700 text-xs underline">
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400 italic bg-gray-50/50">
                                    No se han encontrado incidentes registrados en el sistema.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-start">
                <a href="index.html" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors gap-1">
                    ← Volver al Panel de Registro
                </a>
            </div>

        </div>
    </main>

    <footer class="text-center py-4 bg-white border-t border-gray-200 text-xs text-gray-400">
        &copy; 2026 Plataforma Woldo. Todos los derechos reservados.
    </footer>


<script>
function editarDescripcion(id) {
    const celda = document.getElementById('desc-' + id);
    const textoActual = celda.querySelector('.desc-texto').textContent.trim();

    celda.innerHTML = `
        <textarea id="input-desc-${id}" class="w-full border border-gray-300 rounded p-1 text-sm" rows="3">${textoActual}</textarea>
        <div class="mt-1 flex gap-2">
            <button onclick="guardarDescripcion(${id})" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">Guardar</button>
            <button onclick="location.reload()" class="text-xs bg-gray-300 text-gray-700 px-2 py-1 rounded hover:bg-gray-400">Cancelar</button>
        </div>
    `;
}

function guardarDescripcion(id) {
    const nuevaDesc = document.getElementById('input-desc-' + id).value.trim();

    fetch('editar_descripcion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_conflicto: id, descripcion: nuevaDesc })
    })
    .then(res => res.json())
    .then(data => {
        if (data.exito) {
            location.reload();
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(() => alert('Error al conectar con el servidor.'));
}
</script>

</body>
</html>

<?php
$conn->close();
?>