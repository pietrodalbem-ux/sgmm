(function () {
    'use strict';

    var API = 'api/api_tipo_servico.php';
    var tiposCache = [];
    var editId = null;
    var modal = null;

    function tbody() { return document.getElementById('lista-tipos-corpo'); }

    async function carregarLista() {
        var r = await SGM.fetchJson(API, 'GET');
        if (!r.res.ok || !r.data || !r.data.success) {
            tbody().innerHTML = '<tr><td colspan="3" class="text-center text-danger py-4">Erro ao carregar categorias.</td></tr>';
            return;
        }
        tiposCache = r.data.data || [];
        document.getElementById('tipo-contagem').textContent = tiposCache.length;
        
        if (!tiposCache.length) {
            tbody().innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Nenhuma categoria cadastrada.</td></tr>';
            return;
        }

        tbody().innerHTML = tiposCache.map(function (t) {
            var d = (t.descricao || '').substring(0, 100);
            if ((t.descricao || '').length > 100) d += '…';
            return `
                <tr data-id="${t.id_tipo_servico}">
                    <td class="fw-bold text-dark">${SGM.escapeHtml(t.nome)}</td>
                    <td class="text-muted small">${SGM.escapeHtml(d || '—')}</td>
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

    function abrirModal(id) {
        editId = id;
        var form = document.getElementById('formTipo');
        form.reset();
        document.getElementById('tipo_id').value = id || '';
        
        if (id) {
            var row = tiposCache.find(x => x.id_tipo_servico == id);
            document.getElementById('modalTitle').textContent = 'Editar Categoria';
            document.getElementById('tipo_nome').value = row.nome;
            document.getElementById('tipo_descricao').value = row.descricao || '';
        } else {
            document.getElementById('modalTitle').textContent = 'Nova Categoria';
        }
        
        modal.show();
    }

    document.getElementById('btnAbrirModalNovo').addEventListener('click', function() {
        abrirModal(null);
    });

    document.getElementById('formTipo').addEventListener('submit', async function (e) {
        e.preventDefault();
        var data = {
            id_tipo_servico: editId,
            nome: document.getElementById('tipo_nome').value.trim(),
            descricao: document.getElementById('tipo_descricao').value.trim()
        };

        if (!data.nome) return;

        var method = editId ? 'PUT' : 'POST';
        var r = await SGM.fetchJson(API, method, data);

        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message);
            modal.hide();
            carregarLista();
        } else {
            SGM.toast(r.data ? r.data.message : 'Erro ao processar requisição', 'error');
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
        if (!confirm('Tem certeza que deseja excluir esta categoria? Chamados vinculados podem ser afetados.')) return;
        var r = await SGM.fetchJson(API, 'DELETE', { id_tipo_servico: id });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message);
            carregarLista();
        } else {
            SGM.toast(r.data ? r.data.message : 'Erro ao excluir', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        modal = new bootstrap.Modal(document.getElementById('modalTipo'));
        carregarLista();
    });
})();
