(function () {
    'use strict';

    var API = 'api/lixeira.php';

    async function carregarLixeira() {
        var r = await SGM.fetchJson(API, 'GET');
        if (!r.res.ok || !r.data || !r.data.success) {
            SGM.toast('Erro ao carregar lixeira.', 'error');
            return;
        }

        var d = r.data.data;
        
        render(d.usuarios, 'list-del-usuarios', 'usuarios', 'id_usuario', 'nome');
        render(d.blocos, 'list-del-blocos', 'blocos', 'id_bloco', 'nome');
        render(d.ambientes, 'list-del-ambientes', 'ambientes', 'id_ambiente', 'nome');
        render(d.tipos_servico, 'list-del-tipos', 'tipo_servico', 'id_tipo_servico', 'nome');
    }

    function render(list, targetId, table, idKey, nameKey) {
        var tbody = document.getElementById(targetId);
        if (!list || !list.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Nenhum item nesta categoria.</td></tr>';
            return;
        }

        tbody.innerHTML = list.map(function (item) {
            var data = new Date(item.deleted_at).toLocaleString('pt-BR');
            return `
                <tr>
                    <td>
                        <div class="fw-bold text-dark">${SGM.escapeHtml(item[nameKey])}</div>
                        ${item.email ? `<div class="small text-muted">${SGM.escapeHtml(item.email)}</div>` : ''}
                    </td>
                    <td><span class="small text-muted">${data}</span></td>
                    <td><span class="badge bg-light text-dark border rounded-pill px-3">${SGM.escapeHtml(item.excluido_por || 'Sistema')}</span></td>
                    <td class="text-end">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-success px-3 rounded-pill me-2" onclick="processarItem('${table}', ${item[idKey]}, 'restaurar')">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restaurar
                            </button>
                            <button class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="processarItem('${table}', ${item[idKey]}, 'limpar')">
                                <i class="bi bi-x-lg me-1"></i> Limpar
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    window.processarItem = async function (tabela, id, operacao) {
        var msg = operacao === 'restaurar' ? 'Deseja restaurar este item?' : 'ATENÇÃO: Deseja excluir permanentemente este item? Esta ação NÃO pode ser desfeita.';
        if (!confirm(msg)) return;

        var r = await SGM.fetchJson(API, 'POST', { tabela: tabela, id: id, operacao: operacao });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message);
            carregarLixeira();
        } else {
            SGM.toast(r.data ? r.data.message : 'Erro ao processar.', 'error');
        }
    };

    window.carregarLixeira = carregarLixeira;

    document.addEventListener('DOMContentLoaded', carregarLixeira);
})();
