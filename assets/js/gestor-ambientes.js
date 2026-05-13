(function () {
    'use strict';

    var API_A = 'api/api_ambientes.php';
    var API_B = 'api/api_blocos.php';
    var blocosCache = [];
    var ambCache = [];
    var editId = null;

    function selNovo() {
        return document.getElementById('selectBlocoAmbiente');
    }
    function selEdit() {
        return document.getElementById('edit_amb_bloco');
    }
    function tbody() {
        return document.getElementById('lista-ambientes-corpo');
    }

    function fillBlocoSelects() {
        var opts = blocosCache
            .map(function (b) {
                return '<option value="' + b.id_bloco + '">' + SGM.escapeHtml(b.nome) + '</option>';
            })
            .join('');
        selNovo().innerHTML = '<option value="" disabled selected>Selecione o bloco</option>' + opts;
        selEdit().innerHTML = opts;
    }

    async function carregarBlocos() {
        var r = await SGM.fetchJson(API_B, 'GET');
        if (r.res.ok && r.data && r.data.success) {
            blocosCache = r.data.data || [];
            fillBlocoSelects();
        }
    }

    async function carregarAmbientes() {
        var r = await SGM.fetchJson(API_A, 'GET');
        if (!r.res.ok || !r.data || !r.data.success) {
            tbody().innerHTML =
                '<tr><td colspan="3" class="text-center text-danger py-4">Erro ao carregar ambientes.</td></tr>';
            return;
        }
        ambCache = r.data.data || [];
        document.getElementById('amb-contagem').textContent = ambCache.length + ' registro(s)';
        if (!ambCache.length) {
            tbody().innerHTML =
                '<tr><td colspan="3" class="text-center text-muted py-4">Nenhum ambiente cadastrado.</td></tr>';
            return;
        }
        tbody().innerHTML = ambCache
            .map(function (a) {
                return (
                    '<tr data-id="' +
                    a.id_ambiente +
                    '">' +
                    '<td class="fw-semibold">' +
                    SGM.escapeHtml(a.nome) +
                    '</td>' +
                    '<td>' +
                    SGM.escapeHtml(a.nome_bloco || '') +
                    '</td>' +
                    '<td class="text-end">' +
                    '<button type="button" class="btn btn-sm sgm-btn-outline me-1 btn-edt"><i class="bi bi-pencil"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-del"><i class="bi bi-trash"></i></button>' +
                    '</td></tr>'
                );
            })
            .join('');
    }

    function fecharEdicao() {
        editId = null;
        document.getElementById('painelEdicao').classList.add('d-none');
    }

    document.getElementById('btnFecharEdicao').addEventListener('click', fecharEdicao);

    document.getElementById('btnSalvarEdicaoAmb').addEventListener('click', async function () {
        if (!editId) return;
        var nome = document.getElementById('edit_amb_nome').value.trim();
        var id_bloco = parseInt(selEdit().value, 10);
        if (!nome || !id_bloco) {
            SGM.toast('Preencha nome e bloco.', 'error');
            return;
        }
        var r = await SGM.fetchJson(API_A, 'PUT', {
            id_ambiente: editId,
            nome: nome,
            id_bloco: id_bloco,
        });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message || 'Atualizado.');
            fecharEdicao();
            carregarAmbientes();
        } else {
            SGM.toast((r.data && r.data.message) || 'Erro ao salvar.', 'error');
        }
    });

    document.getElementById('formNovoAmbiente').addEventListener('submit', async function (e) {
        e.preventDefault();
        var nome = document.getElementById('ambiente_nome').value.trim();
        var id_bloco = parseInt(selNovo().value, 10);
        if (!nome || !id_bloco) {
            SGM.toast('Preencha todos os campos.', 'error');
            return;
        }
        var r = await SGM.fetchJson(API_A, 'POST', { nome: nome, id_bloco: id_bloco });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message || 'Ambiente criado.');
            e.target.reset();
            fillBlocoSelects();
            carregarAmbientes();
        } else {
            SGM.toast((r.data && r.data.message) || 'Erro ao criar.', 'error');
        }
    });

    tbody().addEventListener('click', async function (e) {
        var tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        var id = parseInt(tr.getAttribute('data-id'), 10);
        var row = ambCache.find(function (x) {
            return parseInt(x.id_ambiente, 10) === id;
        });
        if (e.target.closest('.btn-edt') && row) {
            editId = id;
            document.getElementById('edit_amb_nome').value = row.nome || '';
            selEdit().value = String(row.id_bloco);
            document.getElementById('painelEdicao').classList.remove('d-none');
            document.getElementById('painelEdicao').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        if (e.target.closest('.btn-del')) {
            if (!confirm('Excluir este ambiente?')) return;
            var r = await SGM.fetchJson(API_A, 'DELETE', { id_ambiente: id });
            if (r.res.ok && r.data && r.data.success) {
                SGM.toast(r.data.message || 'Removido.');
                carregarAmbientes();
            } else {
                SGM.toast((r.data && r.data.message) || 'Não foi possível excluir.', 'error');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', async function () {
        await carregarBlocos();
        await carregarAmbientes();
    });
})();
