(function () {
    'use strict';

    var API = 'api/api_ambientes.php';
    var editId = null;
    var modal = null;
    var searchTimeout = null;

    function tbody() { return document.getElementById('lista-ambientes-corpo'); }

    async function carregarLista() {
        var q = document.getElementById('busca-ambientes').value.trim();
        var url = API + '?q=' + encodeURIComponent(q);

        tbody().innerHTML = '<tr><td colspan="3" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Buscando no banco...</td></tr>';

        var r = await SGM.fetchJson(url, 'GET');
        if (!r.res.ok || !r.data || !r.data.success) {
            tbody().innerHTML = '<tr><td colspan="3" class="text-center text-danger py-4">Erro ao carregar dados.</td></tr>';
            return;
        }
        
        var list = r.data.data || [];
        document.getElementById('amb-contagem').textContent = list.length;
        
        if (!list.length) {
            tbody().innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Nenhum ambiente encontrado.</td></tr>';
            return;
        }

        tbody().innerHTML = list.map(function (a) {
            return `
                <tr data-id="${a.id_ambiente}">
                    <td><div class="fw-bold text-dark">${SGM.escapeHtml(a.nome)}</div></td>
                    <td><span class="badge bg-light text-primary border rounded-pill px-3">${SGM.escapeHtml(a.bloco_nome)}</span></td>
                    <td class="text-end actions-column">
                        <div class="btn-actions-group">
                            <button type="button" class="btn btn-sm sgm-btn-outline btn-edt" title="Editar"><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-del" title="Excluir"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function debounceBusca() {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(carregarLista, 400);
    }

    async function carregarBlocos() {
        var r = await SGM.fetchJson('api/api_blocos.php', 'GET');
        if (r.res.ok && r.data && r.data.success) {
            var sel = document.getElementById('amb_bloco');
            sel.innerHTML = '<option value="" disabled selected>Selecione um bloco...</option>';
            r.data.data.forEach(b => {
                sel.innerHTML += `<option value="${b.id_bloco}">${SGM.escapeHtml(b.nome)}</option>`;
            });
        }
    }

    function abrirModal(id) {
        editId = id;
        var form = document.getElementById('formAmbiente');
        form.reset();
        document.getElementById('amb_id').value = id || '';
        
        if (id) {
            document.getElementById('modalTitle').textContent = 'Editar Ambiente';
            // Consulta real ao banco para preencher o formulário
            SGM.fetchJson(API + '?q=' + id).then(r => {
                if(r.data && r.data.data && r.data.data.length) {
                    var a = r.data.data[0];
                    document.getElementById('amb_nome').value = a.nome;
                    document.getElementById('amb_bloco').value = a.id_bloco;
                }
            });
        } else {
            document.getElementById('modalTitle').textContent = 'Novo Ambiente';
        }
        modal.show();
    }

    document.getElementById('btnAbrirModalNovo').addEventListener('click', () => abrirModal(null));
    document.getElementById('busca-ambientes').addEventListener('input', debounceBusca);

    document.getElementById('formAmbiente').addEventListener('submit', async function (e) {
        e.preventDefault();
        var data = {
            id_ambiente: editId,
            id_bloco: document.getElementById('amb_bloco').value,
            nome: document.getElementById('amb_nome').value.trim()
        };

        var method = editId ? 'PUT' : 'POST';
        var r = await SGM.fetchJson(API, method, data);

        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message);
            modal.hide();
            carregarLista();
        } else {
            SGM.toast(r.data ? r.data.message : 'Erro ao salvar', 'error');
        }
    });

    tbody().addEventListener('click', function (e) {
        var tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        var id = tr.getAttribute('data-id');
        if (e.target.closest('.btn-edt')) abrirModal(id);
        if (e.target.closest('.btn-del')) confirmarExclusao(id);
    });

    async function confirmarExclusao(id) {
        if (!confirm('Deseja realmente mover este ambiente para a lixeira?')) return;
        var r = await SGM.fetchJson(API, 'DELETE', { id_ambiente: id });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message);
            carregarLista();
        } else {
            SGM.toast(r.data ? r.data.message : 'Erro ao excluir', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        modal = new bootstrap.Modal(document.getElementById('modalAmbiente'));
        carregarBlocos();
        carregarLista();
    });
})();
