document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('conflict-form');
    const msgSuccess = document.getElementById('msg-success');
    const msgError = document.getElementById('msg-error');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        msgSuccess.classList.add('hidden');
        msgError.classList.add('hidden');
        msgError.textContent = ''; 

        const alumnos = document.getElementById('alumnos').value.trim();
        const fechaInput = document.getElementById('fecha').value;
        const funcionario = document.getElementById('funcionario').value.trim();
        const descripcion = document.getElementById('descripcion').value.trim();

        if (!alumnos || !fechaInput || !funcionario) {
            mostrarError('Los campos marcados con asterisco son obligatorios.');
            return;
        }

        const [year, month, day] = fechaInput.split('-');
        const fechaIncidenteLocal = new Date(year, month - 1, day);
        
        const fechaHoy = new Date();
        fechaHoy.setHours(0, 0, 0, 0); 

        if (fechaIncidenteLocal > fechaHoy) {
            mostrarError('La fecha del incidente no puede ser en el futuro.');
            return;
        }
        if (descripcion.length > 0 && descripcion.length < 20) {
            mostrarError('La descripción es muy corta. Por favor, detalla mejor lo ocurrido (mínimo 20 caracteres).');
            return;
        }

        const formData = {
            alumnos: alumnos,
            fecha: fechaInput,
            funcionario: funcionario,
            descripcion: descripcion
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
            } else {
                mostrarError(data.mensaje);
            }
        })
        .catch(() => {
            mostrarError('Error al conectar con la base de datos. Intenta nuevamente.');
        });
    });

    function mostrarError(mensaje) {
        msgError.textContent = `❌ Error: ${mensaje}`;
        msgError.classList.remove('hidden');
    }
});
