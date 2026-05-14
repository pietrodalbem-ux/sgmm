(function () {
    'use strict';

    var API = 'api/api_blocos.php';
    var editId = null;
    var modal = null;
    var searchTimeout = null;

    function tbody() { return document.getElementById('lista-blocos-corpo'); }

    async function carregarLista() {
        var q = document.getElementById('busca-blocos').value.trim();
        var url = API + '?q=' + encodeURIComponent(q);

        tbody().innerHTML = '<tr><td colspan="3" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Sincronizando com o banco...</td></tr>';

        var r = await SGM.fetchJson(url, 'GET');
        if (!r.res.ok || !r.data || !r.data.success) {
            tbody().innerHTML = '<tr><td colspan="3" class="text-center text-danger py-4">Erro ao carregar dados.</td></tr>';
            return;
        }
        
        var list = r.data.data || [];
        document.getElementById('blocos-contagem').textContent = list.length;
        
        if (!list.length) {
            tbody().innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Nenhum bloco encontrado.</td></tr>';
            return;
        }

        tbody().innerHTML = list.map(function (b) {
            return `
                <tr data-id="${b.id_bloco}">
                    <td><div class="fw-bold text-dark">${SGM.escapeHtml(b.nome)}</div></td>
                    <td><span class="small text-muted">${SGM.escapeHtml(b.descricao || '—')}</span></td>
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

    function abrirModal(id) {
        editId = id;
        var form = document.getElementById('formBloco');
        form.reset();
        document.getElementById('bloco_id').value = id || '';
        
        if (id) {
            document.getElementById('modalTitle').textContent = 'Editar Bloco';
            // Consulta real ao banco para preencher o formulário
            SGM.fetchJson(API + '?q=' + id).then(r => {
                if(r.data && r.data.data && r.data.data.length) {
                    var b = r.data.data[0];
                    document.getElementById('bloco_nome').value = b.nome;
                    document.getElementById('bloco_descricao').value = b.descricao || '';
                }
            });
        } else {
            document.getElementById('modalTitle').textContent = 'Novo Bloco';
        }
        modal.show();
    }

    document.getElementById('btnAbrirModalNovo').addEventListener('click', () => abrirModal(null));
    document.getElementById('busca-blocos').addEventListener('input', debounceBusca);

    document.getElementById('formBloco').addEventListener('submit', async function (e) {
        e.preventDefault();
        var data = {
            id_bloco: editId,
            nome: document.getElementById('bloco_nome').value.trim(),
            descricao: document.getElementById('bloco_descricao').value.trim()
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
        if (!confirm('Deseja realmente mover este bloco para a lixeira?')) return;
        var r = await SGM.fetchJson(API, 'DELETE', { id_bloco: id });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message);
            carregarLista();
        } else {
            SGM.toast(r.data ? r.data.message : 'Erro ao excluir', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        modal = new bootstrap.Modal(document.getElementById('modalBloco'));
        carregarLista();
    });
})();
