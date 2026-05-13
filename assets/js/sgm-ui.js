/**
 * SGM — componentes de UI compartilhados (toasts, fetch JSON, sidebar mobile)
 */
(function (global) {
    'use strict';

    function escapeHtml(str) {
        if (str == null) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function toast(message, type) {
        type = type || 'success';
        var stack = document.getElementById('toastStack');
        if (!stack) return;
        var div = document.createElement('div');
        div.className = 'sgm-toast ' + (type === 'error' ? 'error' : type === 'info' ? 'info' : 'success');
        var icon =
            type === 'error'
                ? '<i class="bi bi-exclamation-circle text-danger"></i>'
                : type === 'info'
                  ? '<i class="bi bi-info-circle text-primary"></i>'
                  : '<i class="bi bi-check-circle text-success"></i>';
        div.innerHTML = icon + '<span>' + escapeHtml(String(message)) + '</span>';
        stack.appendChild(div);
        setTimeout(function () {
            div.style.opacity = '0';
            div.style.transition = 'opacity .25s ease';
            setTimeout(function () {
                div.remove();
            }, 280);
        }, 5000);
    }

    function fetchJson(url, method, body) {
        var opt = {
            method: method || 'GET',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
        };
        if (body !== undefined && method !== 'GET') opt.body = JSON.stringify(body);
        return fetch(url, opt).then(function (res) {
            return res.text().then(function (text) {
                var data = null;
                if (text) {
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        data = { parseError: true, raw: text };
                    }
                }
                return { res: res, data: data };
            });
        });
    }

    /** Status de chamado — labels e classes Bootstrap para badges */
    var statusLabels = {
        aberto: 'Aberto',
        triagem: 'Triagem',
        em_andamento: 'Em andamento',
        aguardando_peca: 'Aguardando peça',
        concluido: 'Concluído',
        cancelado: 'Cancelado',
    };

    var statusBadgeClass = {
        aberto: 'text-bg-info',
        triagem: 'text-bg-secondary',
        em_andamento: 'text-bg-warning',
        aguardando_peca: 'text-bg-warning',
        concluido: 'text-bg-success',
        cancelado: 'text-bg-danger',
    };

    var priorClass = {
        critica: 'text-danger',
        alta: 'text-warning',
        media: 'text-primary',
        baixa: 'text-secondary',
    };

    function labelStatus(s) {
        return statusLabels[s] || (s || '').replace(/_/g, ' ');
    }

    function badgeStatus(s) {
        var cls = statusBadgeClass[s] || 'text-bg-secondary';
        return '<span class="badge sgm-badge ' + cls + '">' + escapeHtml(labelStatus(s).toUpperCase()) + '</span>';
    }

    function initSidebarToggle() {
        var toggle = document.getElementById('sgmSidebarToggle');
        var sidebar = document.getElementById('sgmSidebar');
        var backdrop = document.getElementById('sgmSidebarBackdrop');
        if (!toggle || !sidebar) return;
        function close() {
            sidebar.classList.remove('is-open');
            if (backdrop) backdrop.classList.remove('is-open');
        }
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
            if (backdrop) backdrop.classList.toggle('is-open', sidebar.classList.contains('is-open'));
        });
        if (backdrop) backdrop.addEventListener('click', close);
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) close();
        });
    }

    global.SGM = {
        toast: toast,
        escapeHtml: escapeHtml,
        fetchJson: fetchJson,
        labelStatus: labelStatus,
        badgeStatus: badgeStatus,
        priorClass: priorClass,
        initSidebarToggle: initSidebarToggle,
    };
})(typeof window !== 'undefined' ? window : this);
