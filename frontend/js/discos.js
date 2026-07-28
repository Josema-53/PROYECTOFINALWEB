const API_URL = 'backend/api_discos.php';
const API_GRUPOS = 'backend/api_grupos.php';
let modal;

document.addEventListener('DOMContentLoaded', () => {
    modal = new bootstrap.Modal(document.getElementById('modalDisco'));
    cargarGrupos();
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

async function cargarGrupos() {
    const res = await fetch(API_GRUPOS);
    const grupos = await res.json();
    const select = document.getElementById('dis-grupo_id');
    select.innerHTML = '<option value="">-- Seleccionar Grupo --</option>';
    grupos.forEach(g => {
        select.innerHTML += `<option value="${g.id}">${escapeHtml(g.nombre_grupo)}</option>`;
    });
}

async function cargarDatos() {
    const q = document.getElementById('input-busqueda').value;
    const res = await fetch(`${API_URL}?q=${encodeURIComponent(q)}`);
    const datos = await res.json();
    const tbody = document.getElementById('cuerpo-tabla');

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4" style="color:#666;">No se encontraron discos</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(d => `
        <tr>
            <td style="font-weight:700; color:var(--rock-red);">${escapeHtml(d.titulo)}</td>
            <td style="color:var(--rock-orange);">${escapeHtml(d.nombre_grupo)}</td>
            <td>${d.anio_lanzamiento || '-'}</td>
            <td>${escapeHtml(d.discografica || '-')}</td>
            <td>${d.num_canciones || 0}</td>
            <td>
                <button class="btn btn-sm btn-rock-outline me-1" onclick='editarDisco(${JSON.stringify(d).replace(/'/g, "&#39;")})'>Editar</button>
                <button class="btn btn-sm" style="color:#dc3545; border:1px solid #dc3545; background:transparent;" onclick="eliminarDisco(${d.id})">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

function abrirModal() {
    document.getElementById('modalTitulo').textContent = 'Nuevo Disco';
    document.getElementById('dis-id').value = '';
    document.getElementById('dis-titulo').value = '';
    document.getElementById('dis-grupo_id').value = '';
    document.getElementById('dis-anio').value = '';
    document.getElementById('dis-num_canciones').value = '';
    document.getElementById('dis-discografica').value = '';
    modal.show();
}

function editarDisco(d) {
    document.getElementById('modalTitulo').textContent = 'Editar Disco';
    document.getElementById('dis-id').value = d.id;
    document.getElementById('dis-titulo').value = d.titulo;
    document.getElementById('dis-grupo_id').value = d.grupo_id;
    document.getElementById('dis-anio').value = d.anio_lanzamiento || '';
    document.getElementById('dis-num_canciones').value = d.num_canciones || '';
    document.getElementById('dis-discografica').value = d.discografica || '';
    modal.show();
}

async function guardarDisco() {
    const id = document.getElementById('dis-id').value;
    const titulo = document.getElementById('dis-titulo').value.trim();
    const grupo_id = parseInt(document.getElementById('dis-grupo_id').value);
    const anio = document.getElementById('dis-anio').value;
    const numCanciones = document.getElementById('dis-num_canciones').value;

    if (!titulo) {
        alert('El titulo del disco es obligatorio');
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
    if (anio && (isNaN(anio) || parseInt(anio) < 1900)) {
        alert('El año de lanzamiento no puede ser menor a 1900');
        return;
    }
    if (!numCanciones || isNaN(numCanciones) || parseInt(numCanciones) < 8) {
        alert('El disco debe tener al menos 8 canciones');
        return;
    }

    const data = {
        titulo,
        grupo_id,
        anio_lanzamiento: anio || null,
        num_canciones: numCanciones || 0,
        discografica: document.getElementById('dis-discografica').value.trim(),
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

async function eliminarDisco(id) {
    if (!confirm('Eliminar este disco? Se eliminaran las canciones asociadas.')) return;

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
