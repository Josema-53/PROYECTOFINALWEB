const API_URL = 'backend/api_discjockeys.php';
let modal;

document.addEventListener('DOMContentLoaded', () => {
    modal = new bootstrap.Modal(document.getElementById('modalDJ'));
    cargarDatos();
    
    document.getElementById('input-busqueda').addEventListener('input', debounce(cargarDatos, 300));
});

function debounce(fn, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

async function cargarDatos() {
    const q = document.getElementById('input-busqueda').value;
    const res = await fetch(`${API_URL}?q=${encodeURIComponent(q)}`);
    const datos = await res.json();
    const tbody = document.getElementById('cuerpo-tabla');
    
    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4" style="color:#666;">No se encontraron discjockeys</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(dj => `
        <tr>
            <td style="font-weight:700; color:var(--rock-red);">${escapeHtml(dj.nombre_artistico)}</td>
            <td>${escapeHtml(dj.nombre_real || '-')}</td>
            <td><code style="color:var(--rock-orange);">${escapeHtml(dj.cedula)}</code></td>
            <td>${escapeHtml(dj.nombre_programa || '-')}</td>
            <td>${escapeHtml(dj.horario_programa || '-')}</td>
            <td><span class="badge-rock-info">${escapeHtml(dj.genero_favorito || '-')}</span></td>
            <td><span class="${dj.estado == 1 ? 'badge-rock-active' : 'badge-rock-inactive'}">${dj.estado == 1 ? 'Activo' : 'Inactivo'}</span></td>
            <td>
                <button class="btn btn-sm btn-rock-outline me-1" onclick='editarDJ(${JSON.stringify(dj)})'>Editar</button>
                <button class="btn btn-sm" style="color:#dc3545; border:1px solid #dc3545; background:transparent;" onclick="eliminarDJ(${dj.id})">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

function abrirModal() {
    document.getElementById('modalTitulo').textContent = 'Nuevo Discjockey';
    document.getElementById('dj-id').value = '';
    document.getElementById('dj-nombre_artistico').value = '';
    document.getElementById('dj-nombre_real').value = '';
    document.getElementById('dj-cedula').value = '';
    document.getElementById('dj-telefono').value = '';
    document.getElementById('dj-correo').value = '';
    document.getElementById('dj-genero').value = '';
    document.getElementById('dj-programa').value = '';
    document.getElementById('dj-horario').value = '';
    document.getElementById('dj-fecha_ingreso').value = '';
    document.getElementById('dj-estado').value = '1';
    modal.show();
}

function editarDJ(dj) {
    document.getElementById('modalTitulo').textContent = 'Editar Discjockey';
    document.getElementById('dj-id').value = dj.id;
    document.getElementById('dj-nombre_artistico').value = dj.nombre_artistico;
    document.getElementById('dj-nombre_real').value = dj.nombre_real || '';
    document.getElementById('dj-cedula').value = dj.cedula;
    document.getElementById('dj-telefono').value = dj.telefono || '';
    document.getElementById('dj-correo').value = dj.correo || '';
    document.getElementById('dj-genero').value = dj.genero_favorito || '';
    document.getElementById('dj-programa').value = dj.nombre_programa || '';
    document.getElementById('dj-horario').value = dj.horario_programa || '';
    document.getElementById('dj-fecha_ingreso').value = dj.fecha_ingreso || '';
    document.getElementById('dj-estado').value = dj.estado;
    modal.show();
}

function validarCedula(cedula) {
    if (!/^\d{10}$/.test(cedula)) return false;
    const digitoVerificador = parseInt(cedula[9]);
    let suma = 0;
    for (let i = 0; i < 9; i++) {
        let mult = (i % 2 === 0) ? 2 : 1;
        let valor = parseInt(cedula[i]) * mult;
        suma += (valor >= 10) ? valor - 9 : valor;
    }
    const esperado = (suma % 10 === 0) ? 0 : 10 - (suma % 10);
    return digitoVerificador === esperado;
}

function validarTelefono(tel) {
    return /^0\d{9}$/.test(tel);
}

function validarCorreo(correo) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
}

function validarNombre(nombre) {
    return nombre.length >= 2 && /[a-zA-ZÀ-ÿ\u00f1\u00d1]{2,}/.test(nombre);
}

function validarHorario(horario) {
    if (!horario) return true;
    const patron = /^([01]\d|2[0-3]):[0-5]\d\s*-\s*([01]\d|2[0-3]):[0-5]\d$/;
    if (!patron.test(horario)) return false;
    const [inicio, fin] = horario.split('-').map(h => h.trim());
    const [hInicio, mInicio] = inicio.split(':').map(Number);
    const [hFin, mFin] = fin.split(':').map(Number);
    return (hFin > hInicio) || (hFin === hInicio && mFin > mInicio);
}

async function guardarDJ() {
    const id = document.getElementById('dj-id').value;
    const nombre_artistico = document.getElementById('dj-nombre_artistico').value.trim();
    const nombre_real = document.getElementById('dj-nombre_real').value.trim();
    const cedula = document.getElementById('dj-cedula').value.trim();
    const telefono = document.getElementById('dj-telefono').value.trim();
    const correo = document.getElementById('dj-correo').value.trim();

    if (!nombre_artistico) {
        alert('El nombre artistico es obligatorio');
        return;
    }
    if (!validarNombre(nombre_artistico)) {
        alert('El nombre artistico debe tener al menos 2 letras');
        return;
    }
    if (nombre_real && !validarNombre(nombre_real)) {
        alert('El nombre real debe tener al menos 2 letras');
        return;
    }
    if (!cedula) {
        alert('La cedula es obligatoria');
        return;
    }
    if (!validarCedula(cedula)) {
        alert('La cedula ingresada no es valida');
        return;
    }
    if (telefono && !validarTelefono(telefono)) {
        alert('El telefono debe tener 10 digitos y comenzar con 0');
        return;
    }
    if (correo && !validarCorreo(correo)) {
        alert('El correo electronico no tiene un formato valido');
        return;
    }

    const horario = document.getElementById('dj-horario').value.trim();
    if (horario && !validarHorario(horario)) {
        alert('El horario debe estar en formato 24h (HH:MM - HH:MM), ej: 20:00 - 23:00');
        return;
    }

    const data = {
        nombre_artistico,
        nombre_real,
        cedula,
        telefono,
        correo,
        genero_favorito: document.getElementById('dj-genero').value,
        horario_programa: horario,
        nombre_programa: document.getElementById('dj-programa').value.trim(),
        fecha_ingreso: document.getElementById('dj-fecha_ingreso').value || null,
        estado: document.getElementById('dj-estado').value,
    };

    const method = id ? 'PUT' : 'POST';
    if (id) data.id = parseInt(id);

    const res = await fetch(API_URL, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    const result = await res.json();
    
    if (res.ok) {
        modal.hide();
        cargarDatos();
    } else {
        alert(result.error || 'Error al guardar');
    }
}

async function eliminarDJ(id) {
    if (!confirm('Eliminar este discjockey? Esta accion no se puede deshacer.')) return;
    
    const res = await fetch(API_URL, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    });
    const result = await res.json();
    
    if (res.ok) {
        cargarDatos();
    } else {
        alert(result.error || 'Error al eliminar');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
