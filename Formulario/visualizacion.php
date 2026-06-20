<?php
require_once 'conexion.php';

$conn = obtenerConexion();

$funcionario = trim($_GET['funcionario'] ?? '');
$alumno = trim($_GET['alumno'] ?? '');
$estado = trim($_GET['estado'] ?? '');
$fechaDesde  = trim($_GET['fecha_desde'] ?? '');
$fechaHasta  = trim($_GET['fecha_hasta'] ?? '');

$condiciones = [];

if (!empty($funcionario)) {
    $condiciones[] =
        "CONCAT(f.nombres, ' ', f.apellidos)
         LIKE '%" . $conn->real_escape_string($funcionario) . "%'";
}

if (!empty($alumno)) {
    $condiciones[] = "
        cc.id_conflicto IN (
            SELECT dac2.id_conflicto
            FROM gestion_conflictos.detalle_alumnos_conflicto dac2
            JOIN gestion_escolar.alumnos a2
                ON dac2.nro_matricula_alumno = a2.nro_matricula
            WHERE CONCAT(a2.nombre, ' ', a2.apellidos)
            LIKE '%" . $conn->real_escape_string($alumno) . "%'
        )
    ";
}

if (!empty($fechaDesde)) {
    $condiciones[] =
        "DATE(cc.fecha_registro) >= '" .
        $conn->real_escape_string($fechaDesde) .
        "'";
}

if (!empty($fechaHasta)) {
    $condiciones[] =
        "DATE(cc.fecha_registro) <= '" .
        $conn->real_escape_string($fechaHasta) .
        "'";
}

if (!empty($estado)) {
    $condiciones[] =
        "cc.estado_caso = '" . $conn->real_escape_string($estado) . "'";
}

$where = '';

if (!empty($condiciones)) {
    $where = 'WHERE ' . implode(' AND ', $condiciones);
}

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

    $where

    GROUP BY
        cc.id_conflicto,
        cc.fecha_registro,
        cc.descripcion,
        cc.estado_caso,   
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

            <div class="mb-8">
                <form
                    method="GET"
                    class="bg-gray-50 border border-gray-200 rounded-2xl p-5 shadow-sm"
                >

                    <div class="flex flex-wrap items-end gap-4">

                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">
                                Funcionario
                            </label>

                            <input
                                type="text"
                                name="funcionario"
                                value="<?= htmlspecialchars($funcionario) ?>"
                                placeholder="Buscar funcionario..."
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            >
                        </div>

                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">
                                Alumno
                            </label>

                            <input
                                type="text"
                                name="alumno"
                                value="<?= htmlspecialchars($alumno) ?>"
                                placeholder="Buscar alumno..."
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            >
                        </div>

                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">
                                Desde
                            </label>

                            <input
                                type="date"
                                name="fecha_desde"
                                value="<?= htmlspecialchars($fechaDesde) ?>"
                                class="px-4 py-2 border border-gray-300 rounded-lg
                                    focus:ring-2 focus:ring-blue-500
                                    focus:border-blue-500 outline-none"
                            >
                        </div>

                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">
                                Hasta
                            </label>

                            <input
                                type="date"
                                name="fecha_hasta"
                                value="<?= htmlspecialchars($fechaHasta) ?>"
                                class="px-4 py-2 border border-gray-300 rounded-lg
                                    focus:ring-2 focus:ring-blue-500
                                    focus:border-blue-500 outline-none"
                            >
                        </div>

                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">
                                Estado
                            </label>

                            <select
                                name="estado"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            >
                                <option value="">Todos</option>

                                <option value="abierto"
                                    <?= $estado === 'abierto' ? 'selected' : '' ?>>
                                    Abierto
                                </option>

                                <option value="en proceso"
                                    <?= $estado === 'en proceso' ? 'selected' : '' ?>>
                                    En proceso
                                </option>

                                <option value="cerrado"
                                    <?= $estado === 'cerrado' ? 'selected' : '' ?>>
                                    Cerrado
                                </option>

                            </select>
                        </div>

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg shadow transition"
                        >
                            Filtrar
                        </button>

                        <a
                            href="visualizacion.php"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg transition"
                        >
                            Limpiar
                        </a>

                    </div>

                </form>
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
                            <th scope="col" class="px-6 py-3.5 font-semibold text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while($fila = $resultado->fetch_assoc()): ?>

                                <?php
                                $estadoClase = match($fila['estado_caso']) {
                                    'abierto' => 'bg-red-100 text-red-700 border-red-200',
                                    'en proceso' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'cerrado' => 'bg-green-100 text-green-700 border-green-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200'
                                };
                                ?>

                                <tr class="hover:bg-blue-50
                                hover:shadow-md
                                cursor-pointer
                                transition-all
                                duration-200" 
                                data-id="<?= $fila['id_conflicto'] ?>"
                                data-funcionario="<?= htmlspecialchars($fila['funcionario']) ?>"
                                data-alumnos="<?= htmlspecialchars($fila['alumnos']) ?>"
                                data-fecha="<?= htmlspecialchars($fila['fecha_registro']) ?>"
                                data-estado="<?= htmlspecialchars($fila['estado_caso']) ?>"
                                data-descripcion="<?= htmlspecialchars($fila['descripcion']) ?>">

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
                                        <?= date("d/m/Y", strtotime($fila['fecha_registro'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 max-w-sm break-words" id="desc-<?= $fila['id_conflicto'] ?>">
                                        <span class="desc-texto"><?= htmlspecialchars($fila['descripcion'] ?? '') ?></span>
                                    </td>

                                    <td class="text-center">
                                        <span class="<?= $estadoClase ?> px-3 py-1 rounded-full text-xs font-semibold border">
                                            <?= htmlspecialchars($fila['estado_caso']) ?>
                                        </span>
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



 
<div
    id="modal"
    class="hidden fixed inset-0 bg-black/50 flex items-center justify-center"
>
    <div class="bg-white p-6 rounded-xl max-w-2xl w-full">

        <h2 id="modalTitulo"></h2>

        <div id="modalContenido"></div>

        <button
            id="cerrarModal"
            class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
        >
            Cerrar
        </button>

    </div>
</div>

<script>
document.querySelectorAll("tbody tr").forEach(fila => {

    fila.addEventListener("click", (e) => {
        if (e.target.tagName === "BUTTON" || e.target.tagName === "TEXTAREA") {
            return;
        }

        const id = fila.dataset.id;

        document.getElementById("modalTitulo").textContent = "Conflicto #" + id;

        document.getElementById("modalContenido").innerHTML = `
            <div class="space-y-3">

                <p><strong>Funcionario:</strong> ${fila.dataset.funcionario}</p>
                <p><strong>Alumnos:</strong> ${fila.dataset.alumnos}</p>
                <p><strong>Fecha:</strong> ${fila.dataset.fecha}</p>

                <div>
                    <label class="block font-semibold mb-1">Estado:</label>
                    <select id="modal-estado" class="w-full border border-gray-300 rounded p-2 text-sm">
                        <option value="abierto" ${fila.dataset.estado === 'abierto' ? 'selected' : ''}>Abierto</option>
                        <option value="en proceso" ${fila.dataset.estado === 'en proceso' ? 'selected' : ''}>En proceso</option>
                        <option value="cerrado" ${fila.dataset.estado === 'cerrado' ? 'selected' : ''}>Cerrado</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Descripción:</label>
                    <textarea id="modal-descripcion" rows="4" class="w-full border border-gray-300 rounded p-2 text-sm">${fila.dataset.descripcion}</textarea>
                </div>

                <div id="modal-mensaje" class="hidden text-sm font-medium"></div>

                <button id="guardarCambios"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded font-medium">
                    Guardar Cambios
                </button>

            </div>
        `;

        document.getElementById("modal").classList.remove("hidden");

        document.getElementById("guardarCambios").addEventListener("click", () => {
            const nuevaDescripcion = document.getElementById("modal-descripcion").value.trim();
            const nuevoEstado = document.getElementById("modal-estado").value;
            const msgDiv = document.getElementById("modal-mensaje");

            fetch('editar_conflicto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_conflicto: id,
                    descripcion: nuevaDescripcion,
                    estado: nuevoEstado
                })
            })
            .then(res => res.json())
            .then(data => {
                msgDiv.classList.remove('hidden');
                if (data.exito) {
                    msgDiv.className = 'text-sm font-medium text-emerald-600';
                    msgDiv.textContent = '✓ ' + data.mensaje;
                    setTimeout(() => location.reload(), 800);
                } else {
                    msgDiv.className = 'text-sm font-medium text-rose-600';
                    msgDiv.textContent = '✗ ' + data.mensaje;
                }
            })
            .catch(() => {
                msgDiv.classList.remove('hidden');
                msgDiv.className = 'text-sm font-medium text-rose-600';
                msgDiv.textContent = '✗ Error al conectar con el servidor.';
            });
        });

    });

});

document.getElementById("cerrarModal").addEventListener("click", () => {
    document.getElementById("modal").classList.add("hidden");
});

document.getElementById("modal").addEventListener("click", (e) => {
    if (e.target.id === "modal") {
        document.getElementById("modal").classList.add("hidden");
    }
});
</script>

</body>
</html>

<?php
$conn->close();
?>
