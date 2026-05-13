(function () {
    'use strict';

    var API = 'api/api_tipo_servico.php';
    var cache = [];
    var editId = null;

    function tbody() {
        return document.getElementById('lista-tipos-corpo');
    }

    async function carregar() {
        var r = await SGM.fetchJson(API, 'GET');
        if (!r.res.ok || !r.data || !r.data.success) {
            tbody().innerHTML =
                '<tr><td colspan="3" class="text-center text-danger py-4">Erro ao carregar tipos.</td></tr>';
            return;
        }
        cache = r.data.data || [];
        document.getElementById('tipo-contagem').textContent = cache.length + ' registro(s)';
        if (!cache.length) {
            tbody().innerHTML =
                '<tr><td colspan="3" class="text-center text-muted py-4">Nenhum tipo cadastrado.</td></tr>';
            return;
        }
        tbody().innerHTML = cache
            .map(function (t) {
                var d = (t.descricao || '').substring(0, 100);
                if ((t.descricao || '').length > 100) d += '…';
                return (
                    '<tr data-id="' +
                    t.id_tipo +
                    '">' +
                    '<td class="fw-semibold">' +
                    SGM.escapeHtml(t.nome) +
                    '</td>' +
                    '<td class="text-muted small">' +
                    SGM.escapeHtml(d) +
                    '</td>' +
                    '<td class="text-end">' +
                    '<button type="button" class="btn btn-sm sgm-btn-outline me-1 btn-edt"><i class="bi bi-pencil"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-del"><i class="bi bi-trash"></i></button>' +
                    '</td></tr>'
                );
            })
            .join('');
    }

    function fechar() {
        editId = null;
        document.getElementById('painelEdicao').classList.add('d-none');
    }

    document.getElementById('btnFecharEdicao').addEventListener('click', fechar);

    document.getElementById('btnSalvarEdicaoTipo').addEventListener('click', async function () {
        if (!editId) return;
        var nome = document.getElementById('edit_tipo_nome').value.trim();
        var desc = document.getElementById('edit_tipo_desc').value.trim();
        if (!nome) {
            SGM.toast('Informe o nome.', 'error');
            return;
        }
        var r = await SGM.fetchJson(API, 'PUT', { id_tipo: editId, nome: nome, descricao: desc });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message || 'Atualizado.');
            fechar();
            carregar();
        } else {
            SGM.toast((r.data && r.data.message) || 'Erro ao salvar.', 'error');
        }
    });

    document.getElementById('formNovoTipo').addEventListener('submit', async function (e) {
        e.preventDefault();
        var nome = document.getElementById('tipo_nome').value.trim();
        var desc = document.getElementById('tipo_descricao').value.trim();
        if (!nome) return;
        var r = await SGM.fetchJson(API, 'POST', { nome: nome, descricao: desc });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message || 'Tipo criado.');
            e.target.reset();
            carregar();
        } else {
            SGM.toast((r.data && r.data.message) || 'Erro ao criar.', 'error');
        }
    });

    tbody().addEventListener('click', async function (e) {
        var tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        var id = parseInt(tr.getAttribute('data-id'), 10);
        var row = cache.find(function (x) {
            return parseInt(x.id_tipo, 10) === id;
        });
        if (e.target.closest('.btn-edt') && row) {
            editId = id;
            document.getElementById('edit_tipo_nome').value = row.nome || '';
            document.getElementById('edit_tipo_desc').value = row.descricao || '';
            document.getElementById('painelEdicao').classList.remove('d-none');
            document.getElementById('painelEdicao').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        if (e.target.closest('.btn-del')) {
            if (!confirm('Excluir este tipo? Pode falhar se houver chamados vinculados.')) return;
            var r = await SGM.fetchJson(API, 'DELETE', { id_tipo: id });
            if (r.res.ok && r.data && r.data.success) {
                SGM.toast(r.data.message || 'Removido.');
                carregar();
            } else {
                SGM.toast((r.data && r.data.message) || 'Não foi possível excluir.', 'error');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', carregar);
})();
