const API_URL = 'backend/api_grupos.php';
let modal;

document.addEventListener('DOMContentLoaded', () => {
    modal = new bootstrap.Modal(document.getElementById('modalGrupo'));
    cargarDatos();
    
    document.getElementById('input-busqueda').addEventListener('input', debounce(cargarDatos, 300));
});

function togglePaisOtro() {
    const select = document.getElementById('gr-pais');
    const inputOtro = document.getElementById('gr-pais-otro');
    if (select.value === '__otro__') {
        inputOtro.style.display = 'block';
        inputOtro.focus();
    } else {
        inputOtro.style.display = 'none';
        inputOtro.value = '';
    }
}

function obtenerPais() {
    const select = document.getElementById('gr-pais');
    if (select.value === '__otro__') {
        return document.getElementById('gr-pais-otro').value.trim();
    }
    return select.value;
}

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
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4" style="color:#666;">No se encontraron grupos</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(gr => `
        <tr>
            <td style="font-weight:700; color:var(--rock-red);">${escapeHtml(gr.nombre_grupo)}</td>
            <td>${escapeHtml(gr.pais_origen || '-')}</td>
            <td>${gr.anio_formacion || '-'}</td>
            <td><span class="badge-rock-info">${escapeHtml(gr.genero_musical || '-')}</span></td>
            <td>${gr.integrantes || '-'}</td>
            <td><span class="${gr.estado_activo == 1 ? 'badge-rock-active' : 'badge-rock-inactive'}">${gr.estado_activo == 1 ? 'Activo' : 'Inactivo'}</span></td>
            <td>
                <button class="btn btn-sm btn-rock-outline me-1" onclick='editarGrupo(${JSON.stringify(gr).replace(/'/g, "&#39;")})'>Editar</button>
                <button class="btn btn-sm" style="color:#dc3545; border:1px solid #dc3545; background:transparent;" onclick="eliminarGrupo(${gr.id})">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

function abrirModal() {
    document.getElementById('modalTitulo').textContent = 'Nuevo Grupo';
    document.getElementById('gr-id').value = '';
    document.getElementById('gr-nombre').value = '';
    document.getElementById('gr-pais').value = '';
    document.getElementById('gr-pais-otro').value = '';
    document.getElementById('gr-pais-otro').style.display = 'none';
    document.getElementById('gr-anio').value = '';
    document.getElementById('gr-genero').value = '';
    document.getElementById('gr-integrantes').value = '';
    document.getElementById('gr-biografia').value = '';
    document.getElementById('gr-estado').value = '1';
    modal.show();
}

function editarGrupo(gr) {
    document.getElementById('modalTitulo').textContent = 'Editar Grupo';
    document.getElementById('gr-id').value = gr.id;
    document.getElementById('gr-nombre').value = gr.nombre_grupo;
    document.getElementById('gr-anio').value = gr.anio_formacion || '';
    document.getElementById('gr-genero').value = gr.genero_musical || '';
    document.getElementById('gr-integrantes').value = gr.integrantes || '';
    document.getElementById('gr-biografia').value = gr.biografia || '';
    document.getElementById('gr-estado').value = gr.estado_activo;

    const pais = gr.pais_origen || '';
    const select = document.getElementById('gr-pais');
    const inputOtro = document.getElementById('gr-pais-otro');
    if ([...select.options].some(o => o.value === pais)) {
        select.value = pais;
        inputOtro.style.display = 'none';
        inputOtro.value = '';
    } else {
        select.value = '__otro__';
        inputOtro.value = pais;
        inputOtro.style.display = 'block';
    }

    modal.show();
}

async function guardarGrupo() {
    const id = document.getElementById('gr-id').value;
    const nombre_grupo = document.getElementById('gr-nombre').value.trim();
    const anio = document.getElementById('gr-anio').value;
    const integrantes = document.getElementById('gr-integrantes').value;

    if (!nombre_grupo) {
        alert('El nombre del grupo es obligatorio');
        return;
    }
    if (nombre_grupo.length < 2) {
        alert('El nombre del grupo debe tener al menos 2 caracteres');
        return;
    }
    if (anio && (isNaN(anio) || parseInt(anio) < 1900)) {
        alert('El año de formacion no puede ser menor a 1900');
        return;
    }
    if (integrantes && (isNaN(integrantes) || parseInt(integrantes) < 1)) {
        alert('El numero de integrantes debe ser al menos 1');
        return;
    }

    const data = {
        nombre_grupo,
        pais_origen: obtenerPais(),
        anio_formacion: anio || null,
        genero_musical: document.getElementById('gr-genero').value,
        integrantes: integrantes || null,
        biografia: document.getElementById('gr-biografia').value.trim(),
        estado_activo: document.getElementById('gr-estado').value,
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

async function eliminarGrupo(id) {
    if (!confirm('Eliminar este grupo? Se eliminaran todos sus discos y canciones asociadas.')) return;

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
