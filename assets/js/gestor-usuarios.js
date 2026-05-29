(function () {
    'use strict';

    var API = 'api/usuarios.php';
    var editId = null;
    var modal = null;
    var searchTimeout = null;

    function tbody() { return document.getElementById('lista-usuarios-corpo'); }

    async function carregarLista() {
        try {
            var searchInput = document.getElementById('busca-usuarios');
            if (!searchInput) return;

            var q = searchInput.value.trim();
            var url = API + '?q=' + encodeURIComponent(q);

            // Feedback visual de consulta real ao banco
            tbody().innerHTML = '<tr><td colspan="5" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Buscando no banco de dados...</td></tr>';

            var r = await SGM.fetchJson(url, 'GET');
            
            if (!r.res.ok || !r.data || !r.data.success) {
                var errorMsg = r.data && r.data.message ? r.data.message : 'Erro ao sincronizar dados com o servidor.';
                tbody().innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-2"></i>' + SGM.escapeHtml(errorMsg) + '</td></tr>';
                return;
            }
            
            var list = r.data.data || [];
            var countEl = document.getElementById('user-contagem');
            if (countEl) countEl.textContent = list.length;
            
            if (!list.length) {
                tbody().innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Nenhum usuário encontrado para esta busca.</td></tr>';
                return;
            }

            tbody().innerHTML = list.map(function (u) {
                var badgeAtivo = u.ativo == 1 
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Ativo</span>' 
                    : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inativo</span>';
                
                return `
                    <tr data-id="${u.id_usuario}">
                        <td>
                            <div class="fw-bold text-dark">${SGM.escapeHtml(u.nome)}</div>
                            <div class="small text-muted">${SGM.escapeHtml(u.email)}</div>
                        </td>
                        <td><span class="badge bg-light text-primary border rounded-pill px-3">${SGM.escapeHtml(u.perfil.toUpperCase())}</span></td>
                        <td><div class="small fw-medium">${SGM.escapeHtml(u.departamento_nome || '—')}</div></td>
                        <td>${badgeAtivo}</td>
                        <td class="text-end actions-column">
                            <div class="btn-actions-group">
                                <button type="button" class="btn btn-sm sgm-btn-outline btn-edt" title="Editar"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-del" title="Excluir"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (err) {
            console.error('Erro na listagem de usuários:', err);
            tbody().innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Falha crítica na conexão com o sistema.</td></tr>';
        }
    }

    function debounceBusca() {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(carregarLista, 400);
    }

    async function carregarDepartamentos() {
        try {
            var r = await SGM.fetchJson(API + '?acao=listar_departamentos', 'GET');
            if (r.res.ok && r.data && r.data.success) {
                var sel = document.getElementById('user_departamento');
                if (!sel) return;
                sel.innerHTML = '<option value="">Selecione um departamento...</option>';
                r.data.data.forEach(d => {
                    sel.innerHTML += `<option value="${d.id_departamento}">${SGM.escapeHtml(d.nome)}</option>`;
                });
            }
        } catch (err) {
            console.error('Erro ao carregar departamentos:', err);
        }
    }

    function abrirModal(id) {
        editId = id;
        var form = document.getElementById('formUsuario');
        if (!form) return;
        
        form.reset();
        document.getElementById('user_id').value = id || '';
        document.getElementById('user_senha').required = !id;
        
        var hint = document.getElementById('pass-hint');
        if (hint) hint.classList.toggle('d-none', !id);
        
        if (id) {
            document.getElementById('modalTitle').textContent = 'Editar Usuário';
            SGM.fetchJson(API + '?id=' + id, 'GET').then(r => {
                if(r.data && r.data.data && r.data.data.length) {
                    var u = r.data.data[0];
                    document.getElementById('user_nome').value = u.nome;
                    document.getElementById('user_email').value = u.email;
                    document.getElementById('user_perfil').value = u.perfil;
                    document.getElementById('user_departamento').value = u.id_departamento || '';
                    document.getElementById('user_ativo').value = u.ativo;
                }
            }).catch(err => {
                SGM.toast('Erro ao buscar dados do usuário.', 'error');
            });
        } else {
            document.getElementById('modalTitle').textContent = 'Novo Usuário';
            document.getElementById('user_ativo').value = "1";
        }
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', () => {
        var modalEl = document.getElementById('modalUsuario');
        if (modalEl) modal = new bootstrap.Modal(modalEl);
        
        var btnNovo = document.getElementById('btnAbrirModalNovo');
        if (btnNovo) btnNovo.addEventListener('click', () => abrirModal(null));
        
        var searchInput = document.getElementById('busca-usuarios');
        if (searchInput) searchInput.addEventListener('input', debounceBusca);

        var form = document.getElementById('formUsuario');
        if (form) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                var data = {
                    id_usuario: editId,
                    nome: document.getElementById('user_nome').value.trim(),
                    email: document.getElementById('user_email').value.trim(),
                    perfil: document.getElementById('user_perfil').value,
                    id_departamento: document.getElementById('user_departamento').value,
                    ativo: document.getElementById('user_ativo').value,
                    senha: document.getElementById('user_senha').value
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
        }

        tbody().addEventListener('click', function (e) {
            var tr = e.target.closest('tr[data-id]');
            if (!tr) return;
            var id = tr.getAttribute('data-id');
            if (e.target.closest('.btn-edt')) abrirModal(id);
            if (e.target.closest('.btn-del')) confirmarExclusao(id);
        });

        carregarDepartamentos();
        carregarLista();
    });

    async function confirmarExclusao(id) {
        if (!confirm('Deseja realmente mover este usuário para a lixeira?')) return;
        var r = await SGM.fetchJson(API, 'DELETE', { id_usuario: id });
        if (r.res.ok && r.data && r.data.success) {
            SGM.toast(r.data.message);
            carregarLista();
        } else {
            SGM.toast(r.data ? r.data.message : 'Erro ao excluir', 'error');
        }
    }
})();
