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

    /** Status de chamado — labels e classes Bootstrap para badges modernizadas */
    var statusLabels = {
        aberto: 'Aberto',
        triagem: 'Triagem',
        em_andamento: 'Em atendimento',
        aguardando_peca: 'Aguardando peça',
        concluido: 'Concluído',
        cancelado: 'Cancelado',
    };

    var statusBadgeClass = {
        aberto: 'bg-info-subtle text-info border border-info-subtle',
        triagem: 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        em_andamento: 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        aguardando_peca: 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        concluido: 'bg-success-subtle text-success border border-success-subtle',
        cancelado: 'bg-danger-subtle text-danger border border-danger-subtle',
    };

    var priorClass = {
        critica: 'text-danger',
        alta: 'text-warning-emphasis',
        media: 'text-primary',
        baixa: 'text-muted',
    };

    function labelStatus(s) {
        return statusLabels[s] || (s || '').replace(/_/g, ' ');
    }

    function badgeStatus(s) {
        var cls = statusBadgeClass[s] || 'bg-light text-muted border';
        return '<span class="badge sgm-badge ' + cls + '">' + escapeHtml(labelStatus(s)) + '</span>';
    }

    function toast(message, type) {
        type = type || 'success';
        var stack = document.getElementById('toastStack');
        if (!stack) return;
        
        var div = document.createElement('div');
        div.className = 'sgm-toast ' + (type === 'error' ? 'error' : type === 'info' ? 'info' : 'success');
        
        var icon = '';
        if (type === 'error') icon = '<div class="bg-danger-subtle p-2 rounded-circle text-danger"><i class="bi bi-x-circle-fill"></i></div>';
        else if (type === 'info') icon = '<div class="bg-info-subtle p-2 rounded-circle text-info"><i class="bi bi-info-circle-fill"></i></div>';
        else icon = '<div class="bg-success-subtle p-2 rounded-circle text-success"><i class="bi bi-check-circle-fill"></i></div>';
        
        div.innerHTML = icon + '<div class="flex-grow-1"><div class="fw-bold small">' + (type === 'error' ? 'Erro' : 'Sucesso') + '</div><div class="small text-muted">' + escapeHtml(String(message)) + '</div></div>';
        
        stack.appendChild(div);
        
        // Entrada suave
        div.style.opacity = '0';
        div.style.transform = 'translateX(20px)';
        div.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        
        requestAnimationFrame(() => {
            div.style.opacity = '1';
            div.style.transform = 'translateX(0)';
        });

        setTimeout(function () {
            div.style.opacity = '0';
            div.style.transform = 'translateX(20px)';
            setTimeout(function () {
                div.remove();
            }, 400);
        }, 5000);
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
