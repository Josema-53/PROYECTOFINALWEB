const API_URL = 'backend/api_canciones.php';
let modal;
let gruposList = [];
let discosList = [];

document.addEventListener('DOMContentLoaded', () => {
    modal = new bootstrap.Modal(document.getElementById('modalCancion'));
    cargarListas();
    cargarDatos();
    
    document.getElementById('input-busqueda').addEventListener('input', debounce(cargarDatos, 300));
    document.getElementById('can-grupo_id').addEventListener('change', filtrarDiscos);
});

function debounce(fn, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

async function cargarListas() {
    const res = await fetch(`${API_URL}?action=listas`);
    const data = await res.json();
    gruposList = data.grupos;
    discosList = data.discos;
    
    const selGrupo = document.getElementById('can-grupo_id');
    selGrupo.innerHTML = '<option value="">-- Seleccionar Grupo --</option>';
    gruposList.forEach(g => {
        selGrupo.innerHTML += `<option value="${g.id}">${escapeHtml(g.nombre_grupo)}</option>`;
    });
}

function filtrarDiscos() {
    const grupoId = parseInt(document.getElementById('can-grupo_id').value) || 0;
    const selDisco = document.getElementById('can-disco_id');
    selDisco.innerHTML = '<option value="">-- Sin disco (Sencillo) --</option>';
    
    const filtrados = grupoId ? discosList.filter(d => d.grupo_id === grupoId) : discosList;
    filtrados.forEach(d => {
        selDisco.innerHTML += `<option value="${d.id}">${escapeHtml(d.titulo)}</option>`;
    });
}

async function cargarDatos() {
    const q = document.getElementById('input-busqueda').value;
    const res = await fetch(`${API_URL}?q=${encodeURIComponent(q)}`);
    const datos = await res.json();
    const tbody = document.getElementById('cuerpo-tabla');

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4" style="color:#666;">No se encontraron canciones</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(c => {
        const durMin = c.duracion_segundos ? `${Math.floor(c.duracion_segundos / 60)}:${String(c.duracion_segundos % 60).padStart(2, '0')}` : '-';
        return `
            <tr>
                <td style="font-weight:700; color:var(--rock-red);">${escapeHtml(c.titulo)}</td>
                <td style="color:var(--rock-orange);">${escapeHtml(c.nombre_grupo)}</td>
                <td>${escapeHtml(c.disco_titulo || '-')}</td>
                <td><span style="color:var(--rock-purple);">${durMin}</span></td>
                <td><span class="badge-rock-info">${escapeHtml(c.genero || '-')}</span></td>
                <td>${c.ano_lanzamiento || '-'}</td>
                <td>${c.es_sencillo == 1 ? '<span class="badge-rock-active">Si</span>' : '<span style="color:#666;">No</span>'}</td>
                <td>
                    <button class="btn btn-sm btn-rock-outline me-1" onclick='editarCancion(${JSON.stringify(c).replace(/'/g, "&#39;")})'>Editar</button>
                    <button class="btn btn-sm" style="color:#dc3545; border:1px solid #dc3545; background:transparent;" onclick="eliminarCancion(${c.id})">Eliminar</button>
                </td>
            </tr>
        `;
    }).join('');
}

function abrirModal() {
    document.getElementById('modalTitulo').textContent = 'Nueva Cancion';
    document.getElementById('can-id').value = '';
    document.getElementById('can-titulo').value = '';
    document.getElementById('can-grupo_id').value = '';
    document.getElementById('can-disco_id').innerHTML = '<option value="">-- Sin disco (Sencillo) --</option>';
    document.getElementById('can-duracion').value = '';
    document.getElementById('can-genero').value = '';
    document.getElementById('can-ano').value = '';
    document.getElementById('can-sencillo').value = '0';
    modal.show();
}

function editarCancion(c) {
    document.getElementById('modalTitulo').textContent = 'Editar Cancion';
    document.getElementById('can-id').value = c.id;
    document.getElementById('can-titulo').value = c.titulo;
    document.getElementById('can-grupo_id').value = c.grupo_id;
    filtrarDiscos();
    document.getElementById('can-disco_id').value = c.disco_id || '';
    document.getElementById('can-duracion').value = c.duracion_segundos || '';
    document.getElementById('can-genero').value = c.genero || '';
    document.getElementById('can-ano').value = c.ano_lanzamiento || '';
    document.getElementById('can-sencillo').value = c.es_sencillo || 0;
    modal.show();
}

async function guardarCancion() {
    const id = document.getElementById('can-id').value;
    const titulo = document.getElementById('can-titulo').value.trim();
    const grupo_id = parseInt(document.getElementById('can-grupo_id').value);
    const duracion = document.getElementById('can-duracion').value;
    const anio = document.getElementById('can-ano').value;

    if (!titulo) {
        alert('El titulo de la cancion es obligatorio');
        return;
    }
    if (titulo.length < 2) {
        alert('El titulo debe tener al menos 2 caracteres');
        return;
    }
    if (!grupo_id) {
        alert('Debe seleccionar un grupo');
        return;
    }
    if (duracion && (isNaN(duracion) || parseInt(duracion) < 1)) {
        alert('La duracion debe ser al menos 1 segundo');
        return;
    }
    if (anio && (isNaN(anio) || parseInt(anio) < 1900)) {
        alert('El año de lanzamiento no puede ser menor a 1900');
        return;
    }

    const data = {
        titulo,
        grupo_id,
        disco_id: document.getElementById('can-disco_id').value ? parseInt(document.getElementById('can-disco_id').value) : null,
        duracion_segundos: duracion || null,
        genero: document.getElementById('can-genero').value,
        ano_lanzamiento: anio || null,
        es_sencillo: parseInt(document.getElementById('can-sencillo').value),
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

async function eliminarCancion(id) {
    if (!confirm('Eliminar esta cancion?')) return;

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
