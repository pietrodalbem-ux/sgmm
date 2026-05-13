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
        if (!r.res.ok || !Array.isArray(r.data)) {
            body.innerHTML =
                '<tr><td colspan="5" class="text-center text-danger py-4">Erro ao carregar solicitações.</td></tr>';
            return;
        }
        var list = r.data;
        var em = 0;
        var fim = 0;
        list.forEach(function (c) {
            var st = c.status || '';
            if (st === 'concluido' || st === 'cancelado') fim++;
            else em++;
        });
        document.getElementById('sol-res-total').textContent = list.length;
        document.getElementById('sol-res-and').textContent = em;
        document.getElementById('sol-res-fim').textContent = fim;

        if (!list.length) {
            body.innerHTML =
                '<tr><td colspan="5" class="text-center text-muted py-4">Você ainda não possui solicitações.</td></tr>';
            return;
        }
        body.innerHTML = list
            .map(function (c) {
                var resumo = (c.titulo || c.descricao_problema || '').toString();
                if (resumo.length > 56) resumo = resumo.substring(0, 56) + '…';
                return (
                    '<tr>' +
                    '<td><span class="badge text-bg-light border">#' +
                    SGM.escapeHtml(String(c.id_chamado)) +
                    '</span></td>' +
                    '<td class="fw-medium small">' +
                    SGM.escapeHtml(resumo) +
                    '</td>' +
                    '<td class="small"><span class="text-muted">' +
                    SGM.escapeHtml(c.bloco_nome || '') +
                    '</span><br><span class="fw-medium">' +
                    SGM.escapeHtml(c.ambiente_nome || '') +
                    '</span></td>' +
                    '<td>' +
                    SGM.badgeStatus(c.status || 'aberto') +
                    '</td>' +
                    '<td class="text-end small text-muted">' +
                    SGM.escapeHtml(fmt(c.data_abertura)) +
                    '</td></tr>'
                );
            })
            .join('');
    }

    document.addEventListener('DOMContentLoaded', carregar);
})();
