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

        if (!alumnos || !fechaInput || !funcionario || !descripcion) {
            mostrarError('Todos los campos son obligatorios. Revisa que no haya espacios en blanco.');
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
        if (descripcion.length < 20) {
            mostrarError('La descripción es muy corta. Por favor, detalla mejor lo ocurrido (mínimo 20 caracteres).');
            return;
        }

        const formData = {
            alumnos: alumnos,
            fecha: fechaInput,
            funcionario: funcionario,
            descripcion: descripcion
        };

        const operacionExitosa = true; 

        setTimeout(() => {
            if (operacionExitosa) {
                // Mostrar confirmaciion
                msgSuccess.classList.remove('hidden');
                form.reset(); // Limpiar el formulario 
            } else {
                // Mostrar error de servidor
                mostrarError('Error al conectar con la base de datos. Intenta nuevamente.');
            }
        }, 500); 
    });

    function mostrarError(mensaje) {
        msgError.textContent = `❌ Error: ${mensaje}`;
        msgError.classList.remove('hidden');
    }
});