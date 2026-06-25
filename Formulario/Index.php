<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es" class="bg-gray-50 text-gray-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plataforma Woldo - Módulo de registro de conflictos escolares.">
    <title>Woldo | Registro de Incidente</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        .hidden { display: none !important; }
        .fuente-woldo { font-family: 'Nunito', sans-serif; font-weight: 800; }
        .header-user { display: flex; align-items: center; gap: 10px; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 16px; margin-left: 4px; }
        .header-avatar { width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; }
        .header-user-name { font-size: 13px; font-weight: 600; color: #fff; line-height: 1.2; }
        .header-user-role { font-size: 11px; color: rgba(255,255,255,0.6); }
        .btn-nav { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.9); background: transparent; border: 1px solid rgba(255,255,255,0.35); border-radius: 8px; padding: 7px 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-nav:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.6); }
        .btn-logout { font-size: 13px; color: rgba(255,255,255,0.65); background: transparent; border: none; padding: 7px 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
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
                <p class="text-xs text-blue-200 uppercase tracking-wider mt-1">Registro de Incidente</p>
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
                <button id="btn-cerrar-sesion" class="btn-logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Salir
                </button>
            </div>
        </div>
    </div>
</header>

    <main class="flex-grow w-full max-w-4xl mx-auto px-4 pb-12">
        <section aria-labelledby="form-title" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8">
            
            <h2 id="form-title" class="fuente-woldo text-2xl text-gray-900 tracking-tight">Registrar Nuevo Incidente</h2>
            <p class="text-sm text-gray-500 mt-2 mb-6">
                Complete el siguiente formulario para ingresar un conflicto al sistema. Todos los campos marcados con asterisco (<span class="text-red-500">*</span>) son obligatorios.
            </p>

            <div id="msg-success" class="alert alert-success hidden mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-800 shadow-sm" role="alert" aria-live="polite">
                <span class="font-bold">✓ Registro Exitoso:</span> El conflicto ha sido guardado correctamente en la base de datos.
            </div>
            
            <div id="msg-error" class="alert alert-error hidden mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-rose-800 shadow-sm" role="alert" aria-live="assertive">
                <span class="font-bold">✗ Error:</span> Verifica los datos ingresados o la conexión al sistema.
            </div>

            <form id="conflict-form" novalidate class="space-y-6">
                
                <fieldset class="border border-gray-200 rounded-xl p-5 bg-gray-50/50 space-y-4">
                    <legend class="text-sm font-semibold text-gray-600 px-3 uppercase tracking-wider bg-white border border-gray-200 rounded-full shadow-sm">
                        Personal y Alumnos Involucrados
                    </legend>

                    <div class="form-group flex flex-col">
                        <label for="funcionario-select" class="text-sm font-medium text-gray-700 mb-1">
                            Funcionario que registra: <span class="text-red-500">*</span>
                        </label>
                        <select id="funcionario-select" name="funcionario_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                            <option value="">— Seleccionar funcionario —</option>
                        </select>
                    </div>

                    <div class="form-group flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-700">
                            Alumnos involucrados: <span class="text-red-500">*</span>
                        </label>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <select id="curso-select"
                                    class="w-full sm:w-56 px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">— Seleccionar curso —</option>
                                <optgroup label="Enseñanza Básica">
                                    <option value="1 Basico A">1° Básico</option>
                                    <option value="2 Basico A">2° Básico</option>
                                    <option value="3 Basico A">3° Básico</option>
                                    <option value="4 Basico A">4° Básico</option>
                                    <option value="5 Basico A">5° Básico</option>
                                    <option value="6 Basico A">6° Básico</option>
                                    <option value="7 Basico A">7° Básico</option>
                                    <option value="8 Basico A">8° Básico</option>
                                </optgroup>
                                <optgroup label="Enseñanza Media">
                                    <option value="1 Medio A">1° Medio</option>
                                    <option value="2 Medio A">2° Medio</option>
                                    <option value="3 Medio A">3° Medio</option>
                                    <option value="4 Medio A">4° Medio</option>
                                </optgroup>
                            </select>

                            <div class="relative flex-1" id="autocomplete-wrap">
                                <input type="text" id="alumno-input"
                                    placeholder="Escriba el nombre del alumno..."
                                    autocomplete="off" disabled
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <ul id="autocomplete-dropdown"
                                    class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden text-sm">
                                </ul>
                            </div>
                        </div>

                        <small class="text-xs text-gray-400 italic">
                            Selecciona el curso primero, luego escribe el nombre. Agrega todos los alumnos involucrados.
                        </small>

                        <div id="alumnos-tags" class="flex flex-wrap gap-2 mt-1 min-h-[2rem]">
                            <span id="tags-empty" class="text-xs text-gray-400 italic self-center">
                                Ningún alumno agregado aún.
                            </span>
                        </div>

                        <input type="hidden" id="alumnos-ids" name="alumnos">
                    </div>
                </fieldset>

                <fieldset class="border border-gray-200 rounded-xl p-5 bg-gray-50/50 space-y-4">
                    <legend class="text-sm font-semibold text-gray-600 px-3 uppercase tracking-wider bg-white border border-gray-200 rounded-full shadow-sm">
                        Detalles del Conflicto
                    </legend>

                    <div class="form-group flex flex-col">
                        <label for="fecha" class="text-sm font-medium text-gray-700 mb-1">
                            Fecha del incidente: <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="fecha" 
                            name="fecha" 
                            max=""
                            required
                            class="w-full sm:w-auto sm:max-w-xs px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                    </div>

                    <div class="form-group flex flex-col">
                        <label for="descripcion" class="text-sm font-medium text-gray-700 mb-1">
                            Descripción de los hechos:
                        </label>
                        <textarea 
                            id="descripcion" 
                            name="descripcion" 
                            rows="5" 
                            placeholder="Describa objetivamente lo ocurrido. Detalle el contexto y las acciones previas..." 
                            aria-describedby="desc-help"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow resize-y"></textarea>
                        <small id="desc-help" class="text-xs text-gray-400 mt-1.5 block italic">
                            La descripción debe contener un mínimo de 20 caracteres.
                        </small>
                    </div>
                </fieldset>

                <div class="form-actions flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="reset" id="btn-cancel" 
                            class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        Limpiar Formulario
                    </button>
                    
                    <button type="submit" id="btn-submit" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Guardar Incidente
                    </button>
                </div>

            </form>
        </section>
    </main>

    <footer class="text-center py-4 bg-white border-t border-gray-200 text-xs text-gray-400">
        &copy; 2026 Plataforma Woldo. Todos los derechos reservados.
    </footer>

    <script src="app.js"></script>
</body>
</html>