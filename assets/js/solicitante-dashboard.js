(function () {
    'use strict';

    function fmt(s) {
        if (!s) return '—';
        try {
            return new Date(s).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
        } catch (e) {
            return '—';
        }
    }

    async function carregar() {
        var body = document.getElementById('tabelaChamados');
        var r = await SGM.fetchJson('api/chamado.php', 'GET');
        
        if (!r.res.ok || !r.data || !r.data.success) {
            body.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-5">Erro ao carregar dados do servidor.</td></tr>';
            return;
        }

        // Estatísticas vindas diretamente do Banco (Backend Real)
        var s = r.data.stats;
        document.getElementById('sol-res-total').textContent = s.total ?? 0;
        document.getElementById('sol-res-and').textContent = s.em_atendimento ?? 0;
        document.getElementById('sol-res-fim').textContent = s.finalizados ?? 0;

        var list = r.data.data || [];
        if (!list.length) {
            body.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-5">Você ainda não possui solicitações registradas.</td></tr>';
            return;
        }

        body.innerHTML = list.map(function (c) {
            var resumo = (c.titulo || c.descricao_problema || '').toString();
            if (resumo.length > 56) resumo = resumo.substring(0, 56) + '…';
            return `
                <tr>
                    <td><span class="badge text-bg-light border">#${SGM.escapeHtml(String(c.id_chamado))}</span></td>
                    <td class="fw-medium small">${SGM.escapeHtml(resumo)}</td>
                    <td class="small">
                        <span class="text-muted">${SGM.escapeHtml(c.bloco_nome || '')}</span><br>
                        <span class="fw-medium">${SGM.escapeHtml(c.ambiente_nome || '')}</span>
                    </td>
                    <td>${SGM.badgeStatus(c.status || 'aberto')}</td>
                    <td class="text-end small text-muted">${SGM.escapeHtml(fmt(c.data_abertura))}</td>
                </tr>
            `;
        }).join('');
    }

    document.addEventListener('DOMContentLoaded', carregar);
})();
