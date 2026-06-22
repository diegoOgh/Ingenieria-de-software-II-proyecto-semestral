document.addEventListener('DOMContentLoaded', () => {
    const form          = document.getElementById('conflict-form');
    const msgSuccess    = document.getElementById('msg-success');
    const msgError      = document.getElementById('msg-error');
    const cursoSelect   = document.getElementById('curso-select');
    const alumnoInput   = document.getElementById('alumno-input');
    const dropdown      = document.getElementById('autocomplete-dropdown');
    const tagsContainer = document.getElementById('alumnos-tags');
    const tagsEmpty     = document.getElementById('tags-empty');
    const alumnosIds    = document.getElementById('alumnos-ids');
    const fechaInput    = document.getElementById('fecha');
    const rolUsuario = localStorage.getItem('rol');
    const nombreGuard = localStorage.getItem('nombre') || 'Usuario';

    //nombre
    const nomEl = document.getElementById('nombre-usuario');
    if (nomEl) nomEl.textContent = nombreGuard;


    //rol
    const rolEl = document.getElementById('rol-usuario');
    if (rolEl && rolUsuario) {
        rolEl.textContent = (rolUsuario === '2') ? 'Encargado de Convivencia' : 'Funcionario General';
    }

    // avatar
    const avatar = document.getElementById('header-avatar');
    if (avatar) {
        const iniciales = nombreGuard.split(' ').filter(Boolean).slice(0, 2).map(p => p[0].toUpperCase()).join('');
        avatar.textContent = iniciales || '?';
    }

    // Cerrar sesión
    const btnSalir = document.getElementById('btn-cerrar-sesion');
    if (btnSalir) {
        btnSalir.addEventListener('click', () => {
            localStorage.clear();
            window.location.href = 'login.html';
        });
    }

    const funcionarioSelect = document.getElementById('funcionario-select');

    //Se pide la lista completa de funcionarios a php
    fetch('buscar_funcionarios.php?q=todos')
        .then(res => res.json())
        .then(data => {
            data.forEach(f => {
                //por cada fucnionario leído se crea una nueva opción para elegir.
                const option = document.createElement('option');
                option.value = f.id;
                //Se muestra solo el nombre y apellido del funcionario.
                option.textContent = f.nombre;
                funcionarioSelect.appendChild(option);
            });
        });

    const hoy = new Date();
    const fechaHoyStr = `${hoy.getFullYear()}-${String(hoy.getMonth()+1).padStart(2,'0')}-${String(hoy.getDate()).padStart(2,'0')}`;
    fechaInput.max = fechaHoyStr;

    // Estado de alumnos seleccionados (mapea según matrícula)
    const alumnosSeleccionados = new Map();
    let debounceTimer = null;

    // seleccionar curso
    cursoSelect.addEventListener('change', () => {
        alumnoInput.value = '';
        cerrarDropdown();
        if (cursoSelect.value) {
            alumnoInput.disabled = false;
            alumnoInput.placeholder = 'Escriba el nombre del alumno...';
            alumnoInput.focus();
        } else {
            alumnoInput.disabled = true;
            alumnoInput.placeholder = 'Selecciona un curso primero...';
        }
    });

    // autocompletado nombres alumnos
    alumnoInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const q = alumnoInput.value.trim();

        if (q.length < 2) { cerrarDropdown(); return; }

        debounceTimer = setTimeout(() => buscarAlumnos(q), 280);
    });

    async function buscarAlumnos(q) {
        const curso = cursoSelect.value;
        if (!curso) return;

        try {
            const params = new URLSearchParams({ curso, q });
            const res    = await fetch(`buscar_alumnos.php?${params}`);
            const data   = await res.json();
            renderDropdown(data);
        } catch {
            cerrarDropdown();
        }
    }

    function renderDropdown(alumnos) {
        dropdown.innerHTML = '';

        if (!alumnos.length) {
            dropdown.innerHTML = '<li class="px-4 py-2 text-gray-400 text-xs italic">Sin resultados.</li>';
            dropdown.classList.remove('hidden');
            return;
        }

        alumnos.forEach(alumno => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between px-4 py-2 cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-0';

            const yaAgregado = alumnosSeleccionados.has(String(alumno.nro_matricula));

            li.innerHTML = `
                <span class="${yaAgregado ? 'text-gray-400' : 'text-gray-800'}">${alumno.nombre}</span>
                ${yaAgregado
                    ? '<span class="text-xs text-gray-400 italic">Ya agregado</span>'
                    : `<span class="text-xs bg-blue-50 text-blue-600 border border-blue-200 rounded-full px-2 py-0.5">${alumno.curso}</span>`
                }
            `;

            if (!yaAgregado) {
                li.addEventListener('click', () => {
                    agregarAlumno(String(alumno.nro_matricula), alumno.nombre, alumno.curso);
                    alumnoInput.value = '';
                    cerrarDropdown();
                    alumnoInput.focus();
                });
            }

            dropdown.appendChild(li);
        });

        dropdown.classList.remove('hidden');
    }

    function cerrarDropdown() {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
    }

    // etiquetas.
    function agregarAlumno(id, nombre, curso) {
        if (alumnosSeleccionados.has(id)) return;
        alumnosSeleccionados.set(id, { nombre, curso });
        actualizarTags();
        actualizarHidden();
    }

    function quitarAlumno(id) {
        alumnosSeleccionados.delete(id);
        actualizarTags();
        actualizarHidden();
    }

    function actualizarTags() {
        tagsContainer.innerHTML = '';

        if (alumnosSeleccionados.size === 0) {
            tagsContainer.appendChild(tagsEmpty);
            tagsEmpty.classList.remove('hidden');
            return;
        }

        alumnosSeleccionados.forEach(({ nombre, curso }, id) => {
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-1.5 bg-blue-50 text-blue-800 border border-blue-200 text-xs font-medium rounded-full px-3 py-1';
            tag.innerHTML = `
                ${nombre}
                <span class="text-blue-400 font-normal">(${curso})</span>
                <button type="button"
                        class="text-blue-400 hover:text-blue-700 ml-1 leading-none font-bold text-base"
                        aria-label="Quitar a ${nombre}">×</button>
            `;
            tag.querySelector('button').addEventListener('click', () => quitarAlumno(id));
            tagsContainer.appendChild(tag);
        });
    }

    function actualizarHidden() {
        // Envía los id como arreglo JSON
        alumnosIds.value = JSON.stringify([...alumnosSeleccionados.keys()].map(Number));
    }

    document.addEventListener('click', e => {
        if (!e.target.closest('#autocomplete-wrap') && !e.target.closest('#curso-select')) {
            cerrarDropdown();
        }

    });

    // ── enviar
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        msgSuccess.classList.add('hidden');
        msgError.classList.add('hidden');


        const fecha       = fechaInput.value;
        const descripcion = document.getElementById('descripcion').value.trim();
        const funcionarioID = funcionarioSelect.value;

        if (!funcionarioID || !fecha) {
            mostrarError('Los campos marcados con asterisco son obligatorios.');
            return;
        }

        if (fecha > fechaHoyStr) {
            mostrarError('La fecha del incidente no puede ser en el futuro.');
            return;
        }

        if (alumnosSeleccionados.size === 0) {
            mostrarError('Debes agregar al menos un alumno involucrado.');
            return;
        }

        if (descripcion.length > 0 && descripcion.length < 20) {
            mostrarError('La descripción es muy corta (mínimo 20 caracteres).');
            return;
        }

        const formData = {
            funcionario_id: Number(funcionarioID),
            fecha,
            descripcion,
            alumnos: [...alumnosSeleccionados.keys()].map(Number),
        };

        fetch('guardar_conflicto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                msgSuccess.classList.remove('hidden');
                form.reset();
                alumnosSeleccionados.clear();
                actualizarTags();
                actualizarHidden();
                alumnoInput.disabled = true;
            } else {
                mostrarError(data.mensaje);
            }
        })
        .catch(() => {
            mostrarError('Error al conectar con el servidor. Intenta nuevamente.');
        });
    });

    function mostrarError(mensaje) {
        msgError.innerHTML = `<span class="font-bold">✗ Error:</span> ${mensaje}`;
        msgError.classList.remove('hidden');
    }

    if (cursoSelect.value) {
        alumnoInput.disabled = false;
        alumnoInput.placeholder = 'Escriba el nombre del alumno...';
    }
});