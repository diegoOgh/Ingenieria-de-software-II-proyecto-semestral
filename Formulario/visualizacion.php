<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}
$rol_id = (int)$_SESSION['rol_id'];

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
    <title>Woldo | Visualización</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .hidden { display: none !important; }
        .fuente-woldo { font-family: 'Nunito', sans-serif; font-weight: 800; }
        body { font-family: 'Inter', sans-serif; background-color: #F0F2F5; }
        .header-user { display: flex; align-items: center; gap: 10px; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 16px; margin-left: 4px; }
        .header-avatar { width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; letter-spacing: 0.01em; }
        .header-user-name { font-size: 13px; font-weight: 600; color: #fff; line-height: 1.2; }
        .header-user-role { font-size: 11px; color: rgba(255,255,255,0.6); }
        .btn-nav { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.9); background: transparent; border: 1px solid rgba(255,255,255,0.35); border-radius: 8px; padding: 7px 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.15s, border-color 0.15s; }
        .btn-nav:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.6); }
        .btn-logout { font-size: 13px; color: rgba(255,255,255,0.65); background: transparent; border: none; padding: 7px 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: color 0.15s, background 0.15s; }
        .btn-logout:hover { color: #fff; background: rgba(255,255,255,0.1); border-radius: 8px; }
    </style>
</head>

<body class="min-h-screen flex flex-col justify-between font-sans antialiased">

<header class="bg-blue-700 text-white py-4 px-4 shadow-md">
    <div class="max-w-[1400px] mx-auto flex justify-between items-center gap-6 px-2">
        <div class="flex items-center gap-3 flex-shrink-0">
            <img src="img/logoWoldoW.png" alt="Logo Woldo" class="w-10 h-10 object-contain drop-shadow">
            <div>
                <h1 class="fuente-woldo text-xl tracking-tight leading-none">WOLDO</h1>
                <p class="text-xs text-blue-200 uppercase tracking-wider mt-1">Visualización</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="dashboard.html" class="btn-nav">← Volver al Panel</a>
            <div class="header-user">
                <div class="header-avatar" id="header-avatar">—</div>
                <div>
                    <div class="header-user-name" id="nombre-usuario">Cargando...</div>
                    <div class="header-user-role" id="rol-usuario"></div>
                </div>
                <button id="btn-cerrar-sesion" class="btn-logout" title="Cerrar sesión">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Salir
                </button>
            </div>
        </div>
    </div>
</header>

    <main class="flex-grow w-full max-w-6xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8">
            
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Conflictos Registrados</h2>
                <p class="text-sm text-gray-500 mt-1">Historial completo de incidentes reportados en el establecimiento.</p>
            </div>

            <div class="mb-8">
                <form method="GET" class="bg-gray-50 border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">Funcionario</label>
                            <input type="text" name="funcionario" value="<?= htmlspecialchars($funcionario) ?>" placeholder="Buscar funcionario..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">Alumno</label>
                            <input type="text" name="alumno" value="<?= htmlspecialchars($alumno) ?>" placeholder="Buscar alumno..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">Desde</label>
                            <input type="date" name="fecha_desde" value="<?= htmlspecialchars($fechaDesde) ?>" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">Hasta</label>
                            <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($fechaHasta) ?>" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="">Todos</option>
                                <option value="abierto" <?= $estado === 'abierto' ? 'selected' : '' ?>>Abierto</option>
                                <option value="en proceso" <?= $estado === 'en proceso' ? 'selected' : '' ?>>En proceso</option>
                                <option value="cerrado" <?= $estado === 'cerrado' ? 'selected' : '' ?>>Cerrado</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg shadow transition">Filtrar</button>
                        <a href="visualizacion.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg transition">Limpiar</a>
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
                                <tr class="hover:bg-blue-50 hover:shadow-md cursor-pointer transition-all duration-200" 
                                    data-id="<?= $fila['id_conflicto'] ?>"
                                    data-funcionario="<?= htmlspecialchars($fila['funcionario']) ?>"
                                    data-alumnos="<?= htmlspecialchars($fila['alumnos']) ?>"
                                    data-fecha="<?= htmlspecialchars($fila['fecha_registro']) ?>"
                                    data-estado="<?= htmlspecialchars($fila['estado_caso']) ?>"
                                    data-descripcion="<?= htmlspecialchars($fila['descripcion']) ?>">
                                    <td class="px-4 py-4 font-bold text-gray-900 text-center bg-gray-50/30"><?= $fila['id_conflicto'] ?></td>
                                    <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($fila['funcionario']) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="text-gray-700 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-md text-xs font-medium inline-block">
                                            <?= htmlspecialchars($fila['alumnos'] ?? 'Sin alumnos asociados') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap"><?= date("d/m/Y", strtotime($fila['fecha_registro'])) ?></td>
                                    <td class="px-6 py-4 text-gray-600 max-w-sm break-words">
                                        <span><?= htmlspecialchars($fila['descripcion'] ?? '') ?></span>
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
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400 italic bg-gray-50/50">No se han encontrado incidentes registrados en el sistema.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
document.addEventListener('DOMContentLoaded', () => {

    const rolUsuario = localStorage.getItem('rol');
    const nombreGuard = localStorage.getItem('nombre') || 'Usuario';
    
    // Nombre
    const nomEl = document.getElementById('nombre-usuario');
    if (nomEl) nomEl.textContent = nombreGuard;

    // Rol
    const rolEl = document.getElementById('rol-usuario');
    if (rolEl && rolUsuario) {
        rolEl.textContent = (rolUsuario === '2') ? 'Encargado de Convivencia' : 'Funcionario General';
    }

    if (document.getElementById('header-avatar')) {
        const iniciales = nombreGuard.split(' ').filter(Boolean).slice(0, 2).map(p => p[0].toUpperCase()).join('');
        document.getElementById('header-avatar').textContent = iniciales || '?';
    }

    document.querySelectorAll("tbody tr").forEach(fila => {
        fila.addEventListener("click", (e) => {
            if (e.target.tagName === "BUTTON" || e.target.tagName === "TEXTAREA") {
                return;
            }

            const id = fila.dataset.id;
            document.getElementById("modalTitulo").textContent = "Gestión de Conflicto #" + id;

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

                    <button id="guardarCambios" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded font-medium">
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
                    body: JSON.stringify({ id_conflicto: id, descripcion: nuevaDescripcion, estado: nuevoEstado })
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

    // Cierre de modales
    const cerrarM = () => document.getElementById("modal").classList.add("hidden");
    document.getElementById("cerrarModal").addEventListener("click", cerrarM);
    document.getElementById("cerrarModalX").addEventListener("click", cerrarM);
    document.getElementById("modal").addEventListener("click", (e) => { if (e.target.id === "modal") cerrarM(); });

    // Funciones globales para controlar el Lightbox (Pantalla completa)
    window.abrirImagenGrande = (ruta, nombre) => {
        document.getElementById("lightboxImagen").src = ruta;
        document.getElementById("lightboxTexto").textContent = nombre;
        document.getElementById("lightbox").classList.remove("hidden");
    };

    const cerrarL = () => document.getElementById("lightbox").classList.add("hidden");
    document.getElementById("cerrarLightbox").addEventListener("click", cerrarL);
    document.getElementById("lightbox").addEventListener("click", (e) => { if (e.target.id === "lightbox") cerrarL(); });

    const btnSalir = document.getElementById('btn-cerrar-sesion');
    if (btnSalir) {
        btnSalir.addEventListener('click', () => {
            localStorage.clear();
            window.location.href = 'login.html';
        });
    }
});
</script>
</body>
</html>

<?php
$conn->close();
?>
