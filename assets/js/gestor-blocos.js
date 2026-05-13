(function () {
    'use strict';

    var API = 'api/api_blocos.php';
    var blocosCache = [];
    var editId = null;

    function tbody() {
        return document.getElementById('lista-blocos-corpo');
    }

    async function carregarLista() {
        var r = await SGM.fetchJson(API, 'GET');
        if (!r.res.ok || !r.data || !r.data.success) {
            tbody().innerHTML =
                '<tr><td colspan="3" class="text-center text-danger py-4">Erro ao carregar blocos.</td></tr>';
            SGM.toast((r.data && r.data.message) || 'Erro ao carregar.', 'error');
            return;
        }
        blocosCache = r.data.data || [];
        document.getElementById('blocos-contagem').textContent = blocosCache.length + ' registro(s)';
        if (!blocosCache.length) {
            tbody().innerHTML =
                '<tr><td colspan="3" class="text-center text-muted py-4">Nenhum bloco cadastrado.</td></tr>';
            return;
        }
        tbody().innerHTML = blocosCache
            .map(function (b) {
                var d = (b.descricao || '').substring(0, 100);
                if ((b.descricao || '').length > 100) d += '…';
                return (
                    '<tr data-id="' +
                    b.id_bloco +
                    '">' +
                    '<td class="fw-semibold">' +
                    SGM.escapeHtml(b.nome) +
                    '</td>' +
                    '<td class="text-muted small">' +
                    SGM.escapeHtml(d) +
                    '</td>' +
                    '<td class="text-end">' +
                    '<button type="button" class="btn btn-sm sgm-btn-outline me-1 btn-edt" title="Editar"><i class="bi bi-pencil"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-del" title="Excluir"><i class="bi bi-trash"></i></button>' +
                    '</td></tr>'
                );
            })
            .join('');
    }

    function fecharEdicao() {
        editId = null;
        document.getElementById('painelEdicao').classList.add('d-none');
    }

    function abrirEdicao(row) {
        editId = parseInt(row.id_bloco, 10);
        document.getElementById('edit_bloco_nome').value = row.nome || '';
        document.getElementById('edit_bloco_desc').value = row.descricao || '';
        document.getElementById('painelEdicao').classList.remove('d-none');
        document.getElementById('painelEdicao').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.getElementById('btnFecharEdicao').addEventListener('click', fecharEdicao);

    document.getElementById('btnSalvarEdicaoBloco').addEventListener('click', async function () {
        if (!editId) return;
        var nome = document.getElementById('edit_bloco_nome').value.trim();
        var desc = document.getElementById('edit_bloco_desc').value.trim();
        if (!nome) {
            SGM.toast('Informe o nome.', 'error');
            return;
        }
        var r = await SGM.fetchJson(API, 'PUT', { id_bloco: editId, nome: nome, descricao: desc });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message || 'Atualizado.');
            fecharEdicao();
            carregarLista();
        } else {
            SGM.toast((r.data && r.data.message) || 'Erro ao salvar.', 'error');
        }
    });

    document.getElementById('formNovoBloco').addEventListener('submit', async function (e) {
        e.preventDefault();
        var nome = document.getElementById('bloco_nome').value.trim();
        var desc = document.getElementById('bloco_descricao').value.trim();
        if (!nome) return;
        var r = await SGM.fetchJson(API, 'POST', { nome: nome, descricao: desc });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message || 'Bloco criado.');
            e.target.reset();
            carregarLista();
        } else {
            SGM.toast((r.data && r.data.message) || 'Erro ao criar.', 'error');
        }
    });

    tbody().addEventListener('click', async function (e) {
        var tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        var id = parseInt(tr.getAttribute('data-id'), 10);
        var row = blocosCache.find(function (x) {
            return parseInt(x.id_bloco, 10) === id;
        });
        if (e.target.closest('.btn-edt') && row) abrirEdicao(row);
        if (e.target.closest('.btn-del')) {
            if (!confirm('Excluir este bloco? Esta ação pode falhar se existirem ambientes vinculados.')) return;
            var r = await SGM.fetchJson(API, 'DELETE', { id_bloco: id });
            if (r.res.ok && r.data && r.data.success) {
                SGM.toast(r.data.message || 'Removido.');
                tr.remove();
                carregarLista();
            } else {
                SGM.toast((r.data && r.data.message) || 'Não foi possível excluir.', 'error');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', carregarLista);
})();
