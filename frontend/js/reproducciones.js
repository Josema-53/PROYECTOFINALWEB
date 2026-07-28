const API_URL = 'backend/api_reproducciones.php';
let modal;

document.addEventListener('DOMContentLoaded', () => {
    modal = new bootstrap.Modal(document.getElementById('modalRepro'));
    cargarListas();
    buscarReproducciones();
    cargarStats();
});

async function cargarListas() {
    const res = await fetch(`${API_URL}?action=listas`);
    const data = await res.json();
    
    const selCancion = document.getElementById('rep-cancion_id');
    selCancion.innerHTML = '<option value="">-- Seleccionar Cancion --</option>';
    data.canciones.forEach(c => {
        const dur = c.duracion_segundos ? ` (${Math.floor(c.duracion_segundos/60)}:${String(c.duracion_segundos%60).padStart(2,'0')})` : '';
        selCancion.innerHTML += `<option value="${c.id}" data-duracion="${c.duracion_segundos || ''}">${escapeHtml(c.titulo)} - ${escapeHtml(c.nombre_grupo)}${dur}</option>`;
    });

    const selDJ = document.getElementById('rep-dj_id');
    selDJ.innerHTML = '<option value="">-- Seleccionar DJ --</option>';
    data.djs.forEach(d => {
        selDJ.innerHTML += `<option value="${d.id}">${escapeHtml(d.nombre_artistico)}</option>`;
    });

    // Auto-fill duration on song select
    selCancion.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const dur = opt.getAttribute('data-duracion');
        if (dur) document.getElementById('rep-duracion').value = dur;
    });

    document.getElementById('rep-fecha').value = new Date().toISOString().slice(0, 16);
}

async function cargarStats() {
    const fecha = document.getElementById('filtro-fecha').value;
    const dj = document.getElementById('filtro-dj').value;
    const cancion = document.getElementById('filtro-cancion').value;

    const params = new URLSearchParams({ action: 'stats' });
    if (fecha) params.set('fecha', fecha);
    if (dj) params.set('dj', dj);
    if (cancion) params.set('cancion', cancion);

    const res = await fetch(`${API_URL}?${params.toString()}`);
    const stats = await res.json();
    
    document.getElementById('stat-total').textContent = stats.total;
    document.getElementById('stat-djs').textContent = stats.djs;
    document.getElementById('stat-canciones').textContent = stats.canciones;
    document.getElementById('stat-horas').textContent = stats.horas + 'h';
}

async function buscarReproducciones() {
    const fecha = document.getElementById('filtro-fecha').value;
    const dj = document.getElementById('filtro-dj').value;
    const cancion = document.getElementById('filtro-cancion').value;

    const params = new URLSearchParams();
    if (fecha) params.set('fecha', fecha);
    if (dj) params.set('dj', dj);
    if (cancion) params.set('cancion', cancion);

    const res = await fetch(`${API_URL}?${params.toString()}`);
    const datos = await res.json();
    const tbody = document.getElementById('cuerpo-tabla');

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4" style="color:#666;">No se encontraron reproducciones</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(r => {
        const dur = r.duracion_real ? `${Math.floor(r.duracion_real/60)}:${String(r.duracion_real%60).padStart(2,'0')}` : '-';
        const fecha = new Date(r.fecha_hora).toLocaleString('es-EC');
        return `
            <tr>
                <td style="color:var(--rock-purple); font-weight:700;">#${r.id}</td>
                <td>${fecha}</td>
                <td style="font-weight:700; color:var(--rock-red);">${escapeHtml(r.cancion_titulo)}</td>
                <td style="color:var(--rock-orange);">${escapeHtml(r.nombre_grupo)}</td>
                <td><span class="on-air-badge" style="animation:none; font-size:0.7em;">&#127908; ${escapeHtml(r.dj_nombre)}</span></td>
                <td><span style="color:var(--rock-purple);">${dur}</span></td>
                <td style="color:#888; font-size:0.85em;">${escapeHtml(r.observaciones || '-')}</td>
                <td>
                    <button class="btn btn-sm" style="color:#dc3545; border:1px solid #dc3545; background:transparent;" onclick="eliminarRepro(${r.id})">Eliminar</button>
                </td>
            </tr>
        `;
    }).join('');
}

function limpiarFiltros() {
    document.getElementById('filtro-fecha').value = '';
    document.getElementById('filtro-dj').value = '';
    document.getElementById('filtro-cancion').value = '';
    buscarReproducciones();
    cargarStats();
}

function abrirModal() {
    document.getElementById('modalTitulo').textContent = 'Nueva Reproduccion';
    document.getElementById('rep-id').value = '';
    document.getElementById('rep-cancion_id').value = '';
    document.getElementById('rep-dj_id').value = '';
    document.getElementById('rep-fecha').value = new Date().toISOString().slice(0, 16);
    document.getElementById('rep-duracion').value = '';
    document.getElementById('rep-obs').value = '';
    modal.show();
}

async function guardarRepro() {
    const id = document.getElementById('rep-id').value;
    const data = {
        cancion_id: parseInt(document.getElementById('rep-cancion_id').value),
        discjockey_id: parseInt(document.getElementById('rep-dj_id').value),
        fecha_hora: document.getElementById('rep-fecha').value ? document.getElementById('rep-fecha').value.replace('T', ' ') + ':00' : null,
        duracion_real: document.getElementById('rep-duracion').value ? parseInt(document.getElementById('rep-duracion').value) : null,
        observaciones: document.getElementById('rep-obs').value.trim(),
    };

    if (!data.cancion_id || !data.discjockey_id) {
        alert('Cancion y Discjockey son obligatorios');
        return;
    }

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
        buscarReproducciones();
        cargarStats();
    } else {
        alert(result.error || 'Error al guardar');
    }
}

async function eliminarRepro(id) {
    if (!confirm('Eliminar esta reproduccion?')) return;

    const res = await fetch(API_URL, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    });
    const result = await res.json();

    if (res.ok) {
        buscarReproducciones();
        cargarStats();
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
