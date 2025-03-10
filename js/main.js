let availableTable, companiesTable, individualsTable;

function initializeTables() {
    // Inicializar tabela de números disponíveis
    availableTable = $('#available-numbers-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        },
        ajax: {
            url: 'api/numbers.php?action=available',
            dataSrc: ''
        },
        columns: [
            { data: 'number' },
            { 
                data: null,
                render: function(data) {
                    return `<button onclick="assignNumber('${data.number}')" 
                            class="btn btn-sm btn-primary">
                            Atribuir
                        </button>`;
                }
            }
        ],
        // Evitar duplicação de dados
        destroy: true,
        processing: true,
        serverSide: false
    });

    // Inicializar tabela de empresas
    companiesTable = $('#companies-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        },
        ajax: {
            url: 'api/clients.php?action=companies',
            dataSrc: ''
        },
        columns: [
            { data: 'name' },
            { data: 'sub_clients_count' },
            { data: 'numbers_count' },
            {
                data: null,
                render: function(data) {
                    return `
                        <div class="btn-group" role="group">
                            <button onclick="loadSubClients(${data.id})" class="btn btn-sm btn-primary">
                                Ver sub-clientes
                            </button>
                            <button onclick="editClient(${data.id})" class="btn btn-sm btn-secondary">
                                Editar
                            </button>
                            <button onclick="deleteClient(${data.id}, '${data.name}', true)" class="btn btn-sm btn-danger">
                                Excluir
                            </button>
                        </div>
                    `;
                }
            }
        ],
        // Evitar duplicação de dados
        destroy: true,
        processing: true,
        serverSide: false
    });

    // Inicializar tabela de clientes individuais
    individualsTable = $('#individuals-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        },
        ajax: {
            url: 'api/clients.php?action=individuals',
            dataSrc: ''
        },
        columns: [
            { data: 'name' },
            { data: 'numbers_count' },
            {
                data: null,
                render: function(data) {
                    return `
                        <div class="btn-group" role="group">
                            <button onclick="viewNumbers(${data.id})" class="btn btn-sm btn-primary">
                                Ver números
                            </button>
                            <button onclick="editClient(${data.id})" class="btn btn-sm btn-secondary">
                                Editar
                            </button>
                            <button onclick="deleteClient(${data.id}, '${data.name}', false)" class="btn btn-sm btn-danger">
                                Excluir
                            </button>
                        </div>
                    `;
                }
            }
        ],
        // Evitar duplicação de dados
        destroy: true,
        processing: true,
        serverSide: false
    });
    
    // Inicializar tabela de números não atribuídos corretamente
    if ($('#unassigned-numbers-table').length) {
        loadUnassignedNumbers();
    }
}

function loadAvailableNumbers() {
    // Usar o método correto para recarregar sem duplicação
    availableTable.ajax.reload(null, false);
    
    fetch('api/numbers.php?action=available')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao buscar números disponíveis');
            }
            return response.json();
        })
        .then(numbers => {
            if (document.getElementById('available-counter')) {
                document.getElementById('available-counter').textContent = numbers.length;
            }
            
            // O restante do código de loadAvailableNumbers pode ser mantido se necessário
            const container = document.getElementById('available-numbers');
            if (!container) {
                console.error('Container de números disponíveis não encontrado');
                return;
            }

            if (Array.isArray(numbers) && numbers.length > 0) {
                container.innerHTML = numbers.map(n => `
                    <div class="p-3 border rounded bg-gray-50 hover:bg-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">${n.number}</span>
                            <button onclick="assignNumber('${n.number}')" 
                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                Atribuir
                            </button>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p class="text-gray-500 text-center">Nenhum número disponível</p>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar números:', error);
            const container = document.getElementById('available-numbers');
            if (container) {
                container.innerHTML = '<p class="text-red-500 text-center">Erro ao carregar números disponíveis</p>';
            }
        });
}

function editClient(id) {
    fetch(`api/clients.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(client => {
            if (!client) return;
            
            Swal.fire({
                title: 'Editar Cliente',
                html: `
                    <form id="editClientForm">
                        <input type="hidden" name="id" value="${client.id}">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" value="${client.name}" required>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Salvar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const form = document.getElementById('editClientForm');
                    const formData = new FormData(form);
                    formData.append('action', 'update');
                    
                    return fetch('api/clients.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadClients();
                            return true;
                        }
                        throw new Error(data.error || 'Erro ao atualizar cliente');
                    });
                }
            });
        });
}

function editSubClient(id) {
    fetch(`api/clients.php?action=get_sub_client&id=${id}`)
        .then(response => response.json())
        .then(subClient => {
            if (!subClient) return;
            
            Swal.fire({
                title: 'Editar Sub-cliente',
                html: `
                    <form id="editSubClientForm">
                        <input type="hidden" name="id" value="${subClient.id}">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" value="${subClient.name}" required>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Salvar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const form = document.getElementById('editSubClientForm');
                    const formData = new FormData(form);
                    formData.append('action', 'update_sub_client');
                    
                    return fetch('api/clients.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadSubClients(subClient.company_id);
                            return true;
                        }
                        throw new Error(data.error || 'Erro ao atualizar sub-cliente');
                    });
                }
            });
        });
}

function assignReservedNumber(companyId, number) {
    fetch(`api/clients.php?action=sub_clients&company_id=${companyId}`)
        .then(response => response.json())
        .then(subClients => {
            const template = document.getElementById('assign-reserved-number-modal').innerHTML;
            Swal.fire({
                title: 'Atribuir Número Reservado',
                html: template,
                didOpen: () => {
                    const select = document.querySelector('#assignReservedForm select');
                    select.innerHTML = subClients.map(sc => 
                        `<option value="${sc.id}">${sc.name}</option>`
                    ).join('');
                },
                preConfirm: () => {
                    const form = document.getElementById('assignReservedForm');
                    const formData = new FormData(form);
                    formData.append('action', 'assign_reserved');
                    formData.append('number', number);
                    
                    return fetch('api/numbers.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json());
                }
            });
        });
}

// Carrega números disponíveis
function loadAvailableNumbers() {
    availableTable.ajax.reload();
    fetch('api/numbers.php?action=available')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao buscar números disponíveis');
            }
            return response.json();
        })
        .then(numbers => {
            const container = document.getElementById('available-numbers');
            if (!container) {
                console.error('Container de números disponíveis não encontrado');
                return;
            }

            if (Array.isArray(numbers) && numbers.length > 0) {
                container.innerHTML = numbers.map(n => `
                    <div class="p-3 border rounded bg-gray-50 hover:bg-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">${n.number}</span>
                            <button onclick="assignNumber('${n.number}')" 
                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                Atribuir
                            </button>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p class="text-gray-500 text-center">Nenhum número disponível</p>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar números:', error);
            const container = document.getElementById('available-numbers');
            if (container) {
                container.innerHTML = '<p class="text-red-500 text-center">Erro ao carregar números disponíveis</p>';
            }
        });
}

// Atribuir número
function assignNumber(number) {
    // Buscar clientes para exibir no modal
    fetch('api/clients.php?action=all')
        .then(response => response.json())
        .then(clients => {
            // Separar empresas e clientes individuais
            const companies = clients.filter(c => c.type === 'company');
            const individuals = clients.filter(c => c.type === 'individual');
            
            Swal.fire({
                title: '<i class="fas fa-link me-2"></i>Atribuir Número',
                html: `
                    <div class="mb-3 modal-form">
                        <p class="mb-2 fw-bold">Número: <span class="badge bg-secondary">${number}</span></p>
                        <div class="form-check custom-radio mb-2">
                            <input class="form-check-input" type="radio" name="clientType" id="typeIndividual" checked>
                            <label class="form-check-label" for="typeIndividual">
                                Cliente Individual
                            </label>
                        </div>
                        <div class="form-check custom-radio mb-2">
                            <input class="form-check-input" type="radio" name="clientType" id="typeCompany">
                            <label class="form-check-label" for="typeCompany">
                                Empresa
                            </label>
                        </div>
                        
                        <div id="individualOptions" class="mb-3">
                            <label for="individualSelect" class="form-label">Selecione um cliente individual</label>
                            <select class="form-select" id="individualSelect">
                                <option value="">-- Selecione --</option>
                                ${individuals.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                                <option value="new">+ Novo cliente individual</option>
                            </select>
                        </div>
                        
                        <div id="companyOptions" class="mb-3 d-none">
                            <label for="companySelect" class="form-label">Selecione uma empresa</label>
                            <select class="form-select mb-2" id="companySelect">
                                <option value="">-- Selecione --</option>
                                ${companies.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                                <option value="new">+ Nova empresa</option>
                            </select>
                            
                            <div id="subClientSelect" class="d-none mt-3">
                                <label for="subClientOptions" class="form-label">Selecione um sub-cliente</label>
                                <select class="form-select" id="subClientOptions">
                                    <option value="">-- Atribuir diretamente à empresa --</option>
                                    <option value="new">+ Novo sub-cliente</option>
                                </select>
                            </div>
                        </div>
                        
                        <div id="newClientForm" class="d-none mt-3">
                            <label for="newClientName" class="form-label">Nome do novo cliente</label>
                            <input type="text" id="newClientName" class="form-control" placeholder="Digite o nome">
                        </div>
                    </div>
                `,
                didOpen: () => {
                    // Adicionar eventos para alternar entre os tipos
                    document.getElementById('typeIndividual').addEventListener('change', function() {
                        document.getElementById('individualOptions').classList.remove('d-none');
                        document.getElementById('companyOptions').classList.add('d-none');
                        document.getElementById('newClientForm').classList.add('d-none');
                    });
                    
                    document.getElementById('typeCompany').addEventListener('change', function() {
                        document.getElementById('individualOptions').classList.add('d-none');
                        document.getElementById('companyOptions').classList.remove('d-none');
                        document.getElementById('newClientForm').classList.add('d-none');
                    });
                    
                    // Eventos para a seleção de clientes
                    document.getElementById('individualSelect').addEventListener('change', function() {
                        if (this.value === 'new') {
                            document.getElementById('newClientForm').classList.remove('d-none');
                        } else {
                            document.getElementById('newClientForm').classList.add('d-none');
                        }
                    });
                    
                    document.getElementById('companySelect').addEventListener('change', function() {
                        const subClientSelect = document.getElementById('subClientSelect');
                        
                        if (this.value === 'new') {
                            subClientSelect.classList.add('d-none');
                            document.getElementById('newClientForm').classList.remove('d-none');
                        } else if (this.value) {
                            // Carregar sub-clientes da empresa selecionada
                            subClientSelect.classList.remove('d-none');
                            document.getElementById('newClientForm').classList.add('d-none');
                            
                            fetch(`api/clients.php?action=sub_clients&company_id=${this.value}`)
                                .then(response => response.json())
                                .then(subClients => {
                                    const select = document.getElementById('subClientOptions');
                                    select.innerHTML = `
                                        <option value="">-- Atribuir diretamente à empresa --</option>
                                        ${subClients.map(sc => `<option value="${sc.id}">${sc.name}</option>`).join('')}
                                        <option value="new">+ Novo sub-cliente</option>
                                    `;
                                });
                        } else {
                            subClientSelect.classList.add('d-none');
                            document.getElementById('newClientForm').classList.add('d-none');
                        }
                    });
                    
                    document.getElementById('subClientOptions').addEventListener('change', function() {
                        if (this.value === 'new') {
                            document.getElementById('newClientForm').classList.remove('d-none');
                        } else {
                            document.getElementById('newClientForm').classList.add('d-none');
                        }
                    });
                },
                showCancelButton: true,
                confirmButtonText: 'Atribuir',
                cancelButtonText: 'Cancelar',
                preConfirm: async () => {
                    const isIndividual = document.getElementById('typeIndividual').checked;
                    let clientId = null;
                    let subClientId = null;
                    
                    if (isIndividual) {
                        const individualSelect = document.getElementById('individualSelect');
                        
                        if (individualSelect.value === 'new') {
                            const newName = document.getElementById('newClientName').value;
                            if (!newName) return Swal.showValidationMessage('Digite o nome do cliente');
                            
                            // Criar novo cliente individual
                            const formData = new FormData();
                            formData.append('action', 'create');
                            formData.append('name', newName);
                            formData.append('type', 'individual');
                            
                            const response = await fetch('api/clients.php', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await response.json();
                            
                            if (!data.success) {
                                return Swal.showValidationMessage(data.error || 'Erro ao criar cliente');
                            }
                            
                            clientId = data.id;
                        } else {
                            if (!individualSelect.value) {
                                return Swal.showValidationMessage('Selecione um cliente');
                            }
                            clientId = individualSelect.value;
                        }
                    } else {
                        // Empresa selecionada
                        const companySelect = document.getElementById('companySelect');
                        
                        if (companySelect.value === 'new') {
                            const newName = document.getElementById('newClientName').value;
                            if (!newName) return Swal.showValidationMessage('Digite o nome da empresa');
                            
                            // Criar nova empresa
                            const formData = new FormData();
                            formData.append('action', 'create');
                            formData.append('name', newName);
                            formData.append('type', 'company');
                            
                            const response = await fetch('api/clients.php', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await response.json();
                            
                            if (!data.success) {
                                return Swal.showValidationMessage(data.error || 'Erro ao criar empresa');
                            }
                            
                            clientId = data.id;
                        } else {
                            if (!companySelect.value) {
                                return Swal.showValidationMessage('Selecione uma empresa');
                            }
                            
                            // Verificar sub-cliente
                            const subClientOptions = document.getElementById('subClientOptions');
                            
                            if (subClientOptions.value === 'new') {
                                const newName = document.getElementById('newClientName').value;
                                if (!newName) return Swal.showValidationMessage('Digite o nome do sub-cliente');
                                
                                // Criar novo sub-cliente
                                const formData = new FormData();
                                formData.append('action', 'create_sub_client');
                                formData.append('name', newName);
                                formData.append('company_id', companySelect.value);
                                
                                const response = await fetch('api/clients.php', {
                                    method: 'POST',
                                    body: formData
                                });
                                const data = await response.json();
                                
                                if (!data.success) {
                                    return Swal.showValidationMessage(data.error || 'Erro ao criar sub-cliente');
                                }
                                
                                subClientId = data.id;
                            } else if (subClientOptions.value) {
                                subClientId = subClientOptions.value;
                            } else {
                                clientId = companySelect.value;
                            }
                        }
                    }
                    
                    return { clientId, subClientId };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const { clientId, subClientId } = result.value;
                    
                    // Atribuir número ao cliente ou sub-cliente
                    const formData = new FormData();
                    formData.append('action', 'assign');
                    formData.append('numbers[]', number);
                    
                    if (subClientId) {
                        formData.append('sub_client_id', subClientId);
                    } else {
                        formData.append('client_id', clientId);
                    }
                    
                    fetch('api/numbers.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Sucesso', 'Número atribuído com sucesso', 'success');
                            loadAvailableNumbers();
                            loadClients();
                        } else {
                            Swal.fire('Erro', data.error || 'Erro ao atribuir número', 'error');
                        }
                    });
                }
            });
        });
}

function assignNumbersToClient(numbers, clientId) {
    const formData = new FormData();
    formData.append('action', 'assign');
    formData.append('client_id', clientId);
    
    numbers.forEach(number => {
        formData.append('numbers[]', number);
    });
    
    fetch('api/numbers.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Sucesso', 'Números atribuídos com sucesso', 'success');
            loadAvailableNumbers();
            loadClients();
        } else {
            Swal.fire('Erro', data.error || 'Erro ao atribuir números', 'error');
        }
    });
}

// Carregar usuários
function loadUsers() {
    fetch('api/users.php?action=list')
        .then(response => response.json())
        .then(users => {
            const container = document.getElementById('users-list');
            container.innerHTML = users.map(u => `
                <div class="p-2 border rounded mb-2">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">${u.name}</span>
                        <span class="text-sm text-gray-500">${u.phone_count} números</span>
                    </div>
                    <div class="mt-2 flex gap-2">
                        <button onclick="viewUser(${u.id})" 
                                class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            Ver números
                        </button>
                        <button onclick="deleteUser(${u.id})" 
                                class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                            Excluir
                        </button>
                    </div>
                </div>
            `).join('');
        });
}

function viewUser(id) {
    fetch(`api/users.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: data[0].name,
                html: `
                    <div class="text-left">
                        <h3 class="font-semibold mb-2">Números associados:</h3>
                        ${data.map(item => `
                            <div class="flex justify-between items-center mb-2">
                                <span>${item.number || 'Sem números'}</span>
                                ${item.number ? `
                                    <button onclick="removeNumber('${item.number}')" 
                                            class="text-red-500 hover:text-red-700">
                                        Remover
                                    </button>
                                ` : ''}
                            </div>
                        `).join('')}
                    </div>
                `
            });
        });
}

function loadClients() {
    // Usar o método correto para recarregar sem duplicação
    companiesTable.ajax.reload(null, false);
    individualsTable.ajax.reload(null, false);
}

// Atualizar função loadSubClients para o tema escuro
function loadSubClients(companyId) {
    // Mostrar indicador de carregamento
    Swal.fire({
        title: 'Carregando sub-clientes...',
        didOpen: () => {
            Swal.showLoading();
        },
        background: '#1e1e1e',
        color: '#e0e0e0',
        allowOutsideClick: false,
        showConfirmButton: false
    });

    fetch(`api/clients.php?action=sub_clients&company_id=${companyId}`)
        .then(response => response.json())
        .then(subClients => {
            Swal.close();
            
            const modalContent = `
                <div class="modal-body bg-dark">
                    <div class="table-responsive">
                        <table class="table table-striped table-dark">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Números</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${subClients.length ? subClients.map(client => `
                                    <tr>
                                        <td>${client.name}</td>
                                        <td>
                                            <span class="badge bg-primary">${client.numbers_count}</span>
                                        </td>
                                        <td>
                                            <button onclick="viewNumbers(${client.id}, true)" 
                                                    class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i>Ver
                                            </button>
                                            <button onclick="editSubClient(${client.id})" 
                                                    class="btn btn-sm btn-secondary">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </button>
                                            <button onclick="deleteSubClient(${client.id}, '${client.name}', ${companyId})" 
                                                    class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash me-1"></i>Excluir
                                            </button>
                                        </td>
                                    </tr>
                                `).join('') : '<tr><td colspan="3" class="text-center">Nenhum sub-cliente encontrado</td></tr>'}
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <button onclick="createSubClient(${companyId})" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Novo Sub-cliente
                        </button>
                    </div>
                </div>
            `;

            Swal.fire({
                title: '<i class="fas fa-users me-2"></i>Sub-clientes',
                html: modalContent,
                width: '800px',
                background: '#1e1e1e',
                color: '#e0e0e0',
                showConfirmButton: false,
                showCloseButton: true
            });
        })
        .catch(error => {
            Swal.close();
            console.error('Erro ao carregar sub-clientes:', error);
            Swal.fire({
                title: 'Erro',
                text: 'Não foi possível carregar os sub-clientes.',
                icon: 'error',
                background: '#1e1e1e',
                color: '#e0e0e0'
            });
        });
}

function hideSubClients(companyId) {
    const container = document.getElementById(`company-${companyId}-subclients`);
    if (container) {
        container.innerHTML = '';
    }
}

function viewNumbers(id, isSubClient = false) {
    const endpoint = isSubClient ? 
        'api/numbers.php?action=get_by_client&id=' + id + '&is_sub_client=true' :
        'api/clients.php?action=get_numbers&id=' + id;

    fetch(endpoint)
        .then(response => response.json())
        .then(numbers => {
            if (!Array.isArray(numbers)) {
                console.error('Resposta inválida:', numbers);
                return;
            }

            Swal.fire({
                title: 'Números Associados',
                html: `
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${numbers.length ? numbers.map(n => `
                                    <tr>
                                        <td>${n.number}</td>
                                        <td>
                                            <button onclick="removeNumber('${n.number}')" 
                                                    class="btn btn-sm btn-danger">
                                                Remover
                                            </button>
                                        </td>
                                    </tr>
                                `).join('') : '<tr><td colspan="2" class="text-center">Nenhum número associado</td></tr>'}
                            </tbody>
                        </table>
                    </div>
                `,
                width: '600px',
                showConfirmButton: false,
                showCloseButton: true
            });
        })
        .catch(error => {
            console.error('Erro ao buscar números:', error);
            Swal.fire('Erro', 'Não foi possível carregar os números', 'error');
        });
}

function removeNumber(number) {
    Swal.fire({
        title: 'Confirmar remoção',
        text: `Deseja remover o número ${number}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/numbers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&number=${number}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Sucesso', 'Número removido com sucesso', 'success');
                    loadClients();
                    loadAvailableNumbers();
                    loadUnassignedNumbers(); // Adicionar esta chamada para atualizar números não atribuídos
                }
            });
        }
    });
}

function showAddClientModal() {
    const template = document.getElementById('add-client-modal').innerHTML;
    Swal.fire({
        title: '<i class="fas fa-user-plus me-2"></i>Adicionar Novo Cliente',
        html: template,
        background: '#1e1e1e',
        color: '#e0e0e0',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save me-1"></i>Adicionar',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancelar',
        preConfirm: () => {
            const form = document.getElementById('addClientForm');
            const formData = new FormData(form);
            formData.append('action', 'create');
            
            return fetch('api/clients.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadClients();
                    return true;
                }
                throw new Error(data.error || 'Erro ao adicionar cliente');
            })
            .catch(error => {
                Swal.showValidationMessage(`Erro: ${error.message}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Cliente adicionado com sucesso',
                icon: 'success',
                background: '#1e1e1e',
                color: '#e0e0e0'
            });
        }
    });
}

function showAddNumbersModal() {
    const template = document.getElementById('add-numbers-modal').innerHTML;
    Swal.fire({
        title: '<i class="fas fa-plus-circle me-2"></i>Adicionar Números',
        html: template,
        background: '#1e1e1e',
        color: '#e0e0e0',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save me-1"></i>Adicionar',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancelar',
        preConfirm: () => {
            const form = document.getElementById('addNumbersForm');
            const formData = new FormData(form);
            
            Swal.showLoading();
            return fetch('api/numbers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAvailableNumbers();
                    return data;
                }
                throw new Error(data.error || 'Erro ao adicionar números');
            })
            .catch(error => {
                Swal.showValidationMessage(`Erro: ${error.message}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;
            Swal.fire({
                title: 'Sucesso!',
                html: `Números adicionados com sucesso.<br>
                      Inseridos: <b>${data.inserted}</b><br>
                      Duplicados: <b>${data.duplicates}</b>`,
                icon: 'success',
                background: '#1e1e1e',
                color: '#e0e0e0'
            });
        }
    });
}

// Atualizar função showAssignNumbersModal para ter um formato mais consistente
function showAssignNumbersModal() {
    fetch('api/numbers.php?action=available')
        .then(response => response.json())
        .then(numbers => {
            const template = document.getElementById('assign-numbers-modal').innerHTML;
            Swal.fire({
                title: '<i class="fas fa-link me-2"></i>Atribuir Números',
                html: template,
                width: '600px',
                background: '#1e1e1e',
                color: '#e0e0e0',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-save me-1"></i>Atribuir',
                cancelButtonText: '<i class="fas fa-times me-1"></i>Cancelar',
                didOpen: () => {
                    const numbersList = document.getElementById('available-numbers-list');
                    numbersList.innerHTML = numbers.map(n => `
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="number-${n.number}" value="${n.number}">
                            <label class="form-check-label" for="number-${n.number}">${n.number}</label>
                        </div>
                    `).join('');

                    const searchInput = document.getElementById('client-search');
                    searchInput.addEventListener('input', debounce(() => {
                        if (searchInput.value.length >= 3) {
                            searchClients(searchInput.value);
                        }
                    }, 300));
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const selectedNumbers = [...document.querySelectorAll('#available-numbers-list input:checked')]
                        .map(input => input.value);
                    const selectedClient = document.querySelector('#client-list input:checked');
                    
                    if (selectedNumbers.length && selectedClient) {
                        assignNumbersToClient(selectedNumbers, selectedClient.value);
                    }
                }
            });
        });
}

function searchClients(query) {
    fetch(`api/clients.php?action=search&q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(clients => {
            const clientList = document.getElementById('client-list');
            clientList.innerHTML = clients.map(c => `
                <div class="form-check">
                    <input type="radio" name="client" class="form-check-input" id="client-${c.id}" value="${c.id}">
                    <label class="form-check-label" for="client-${c.id}">${c.name}</label>
                </div>
            `).join('');
        });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function deleteClient(id, name, isCompany = false) {
    Swal.fire({
        title: 'Confirmar exclusão',
        text: `Deseja excluir ${isCompany ? 'a empresa' : 'o cliente'} "${name}"? Esta ação não pode ser desfeita.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_client');
            formData.append('id', id);
            
            fetch('api/clients.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Sucesso', `${isCompany ? 'Empresa' : 'Cliente'} excluído com sucesso`, 'success');
                    loadClients();
                } else {
                    Swal.fire('Erro', data.error || `Erro ao excluir ${isCompany ? 'empresa' : 'cliente'}`, 'error');
                }
            });
        }
    });
}

function deleteSubClient(id, name, companyId) {
    Swal.fire({
        title: 'Confirmar exclusão',
        text: `Deseja excluir o sub-cliente "${name}"? Esta ação não pode ser desfeita.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_sub_client');
            formData.append('id', id);
            
            fetch('api/clients.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Sucesso', 'Sub-cliente excluído com sucesso', 'success');
                    loadSubClients(companyId);
                } else {
                    Swal.fire('Erro', data.error || 'Erro ao excluir sub-cliente', 'error');
                }
            });
        }
    });
}

function loadUnassignedNumbers() {
    fetch('api/numbers.php?action=unassigned')
        .then(response => response.json())
        .then(numbers => {
            console.log('Números não atribuídos:', numbers); // Debug
            
            const container = document.getElementById('unassigned-numbers-table');
            if (!container) {
                console.error('Container de números não atribuídos não encontrado');
                return;
            }

            // Agrupar números por empresa
            const numbersByCompany = {};
            numbers.forEach(n => {
                if (!numbersByCompany[n.company_name]) {
                    numbersByCompany[n.company_name] = {
                        company_id: n.user_id,
                        company_name: n.company_name,
                        numbers: []
                    };
                }
                numbersByCompany[n.company_name].numbers.push(n.number);
            });

            const groupedNumbers = Object.values(numbersByCompany);

            // Inicializar DataTable ou recarregar se já existir
            if ($.fn.DataTable.isDataTable('#unassigned-numbers-table')) {
                const table = $('#unassigned-numbers-table').DataTable();
                table.clear().rows.add(groupedNumbers).draw();
            } else {
                $('#unassigned-numbers-table').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                    },
                    data: groupedNumbers,
                    columns: [
                        { 
                            data: 'company_name',
                            title: 'Empresa'
                        },
                        { 
                            data: 'numbers',
                            title: 'Números Reservados',
                            render: function(data) {
                                return `<span title="${data.join(', ')}">${data.length} números</span>`;
                            }
                        },
                        {
                            data: null,
                            title: 'Ações',
                            render: function(data) {
                                return `
                                    <button onclick="viewCompanyNumbers(${data.company_id})" 
                                            class="btn btn-sm btn-primary">
                                        Ver Números
                                    </button>
                                `;
                            }
                        }
                    ],
                    destroy: true,
                    processing: true,
                    serverSide: false
                });
            }
        })
        .catch(error => {
            console.error('Erro ao carregar números não atribuídos:', error);
        });
}

// Atualizar a função viewCompanyNumbers para manter o tema escuro

function viewCompanyNumbers(companyId) {
    // Mostrar indicador de carregamento
    Swal.fire({
        title: 'Carregando números...',
        didOpen: () => {
            Swal.showLoading();
        },
        background: '#1e1e1e',
        color: '#e0e0e0',
        allowOutsideClick: false,
        showConfirmButton: false
    });

    fetch(`api/numbers.php?action=company_numbers&company_id=${companyId}`)
        .then(response => response.json())
        .then(numbers => {
            Swal.close();
            
            if (!Array.isArray(numbers) || numbers.length === 0) {
                Swal.fire({
                    title: 'Informação',
                    text: 'Esta empresa não possui números reservados.',
                    icon: 'info',
                    background: '#1e1e1e',
                    color: '#e0e0e0'
                });
                return;
            }

            Swal.fire({
                title: `<i class="fas fa-phone-alt me-2"></i>Números Reservados - ${numbers[0].company_name}`,
                html: `
                    <div class="table-responsive">
                        <table class="table table-striped table-dark">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${numbers.map(n => `
                                    <tr>
                                        <td><span class="badge bg-secondary">${n.number}</span></td>
                                        <td>
                                            <button onclick="assignToSubClient('${n.number}', ${companyId})" 
                                                    class="btn btn-sm btn-primary">
                                                <i class="fas fa-user-plus me-1"></i>Atribuir
                                            </button>
                                            <button onclick="removeNumber('${n.number}')" 
                                                    class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash me-1"></i>Remover
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button onclick="manageSubClients(${companyId})" class="btn btn-primary">
                            <i class="fas fa-users-cog me-1"></i>Gerenciar Sub-clientes
                        </button>
                    </div>
                `,
                width: '800px',
                background: '#1e1e1e',
                color: '#e0e0e0',
                showConfirmButton: false,
                showCloseButton: true
            });
        })
        .catch(error => {
            Swal.close();
            console.error('Erro ao buscar números da empresa:', error);
            Swal.fire({
                title: 'Erro', 
                text: 'Não foi possível carregar os números reservados.', 
                icon: 'error',
                background: '#1e1e1e',
                color: '#e0e0e0'
            });
        });
}

// Nova função para ver números reservados de uma empresa
function viewCompanyNumbers(companyId) {
    fetch(`api/numbers.php?action=company_numbers&company_id=${companyId}`)
        .then(response => response.json())
        .then(numbers => {
            if (!Array.isArray(numbers) || numbers.length === 0) {
                Swal.fire('Informação', 'Esta empresa não possui números reservados.', 'info');
                return;
            }

            Swal.fire({
                title: `Números Reservados - ${numbers[0].company_name}`,
                html: `
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${numbers.map(n => `
                                    <tr>
                                        <td>${n.number}</td>
                                        <td>
                                            <button onclick="assignToSubClient('${n.number}', ${companyId})" 
                                                    class="btn btn-sm btn-primary">
                                                Atribuir a sub-cliente
                                            </button>
                                            <button onclick="removeNumber('${n.number}')" 
                                                    class="btn btn-sm btn-danger">
                                                Remover
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button onclick="manageSubClients(${companyId})" class="btn btn-primary">
                            Gerenciar Sub-clientes
                        </button>
                    </div>
                `,
                width: '800px',
                showConfirmButton: false,
                showCloseButton: true
            });
        })
        .catch(error => {
            console.error('Erro ao buscar números da empresa:', error);
            Swal.fire('Erro', 'Não foi possível carregar os números reservados.', 'error');
        });
}

// Nova função para gerenciar sub-clientes diretamente da tela de números reservados
function manageSubClients(companyId) {
    loadSubClients(companyId);
}

// Atualizar função de atribuição para sub-cliente
function assignToSubClient(number, companyId) {
    fetch(`api/clients.php?action=sub_clients&company_id=${companyId}`)
        .then(response => response.json())
        .then(subClients => {
            if (subClients.length === 0) {
                Swal.fire({
                    title: 'Sem sub-clientes',
                    text: 'Esta empresa não possui sub-clientes. Deseja criar um agora?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, criar sub-cliente',
                    cancelButtonText: 'Não'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar modal para criar sub-cliente
                        createSubClient(companyId);
                    }
                });
                return;
            }
            
            Swal.fire({
                title: 'Atribuir a sub-cliente',
                html: `
                    <p>Número: ${number}</p>
                    <div class="mb-3">
                        <select id="subClientSelect" class="form-select">
                            <option value="">Selecione um sub-cliente</option>
                            ${subClients.map(sc => `<option value="${sc.id}">${sc.name}</option>`).join('')}
                            <option value="new">+ Criar novo sub-cliente</option>
                        </select>
                    </div>
                    <div id="newSubClientName" class="mb-3 d-none">
                        <input type="text" id="newSubClientNameInput" class="form-control" placeholder="Nome do novo sub-cliente">
                    </div>
                `,
                didOpen: () => {
                    // Mostrar/esconder campo de novo sub-cliente
                    document.getElementById('subClientSelect').addEventListener('change', function() {
                        const newSubClientDiv = document.getElementById('newSubClientName');
                        if (this.value === 'new') {
                            newSubClientDiv.classList.remove('d-none');
                        } else {
                            newSubClientDiv.classList.add('d-none');
                        }
                    });
                },
                showCancelButton: true,
                confirmButtonText: 'Atribuir',
                cancelButtonText: 'Cancelar',
                preConfirm: async () => {
                    const select = document.getElementById('subClientSelect');
                    if (select.value === '') {
                        Swal.showValidationMessage('Selecione um sub-cliente ou crie um novo');
                        return false;
                    }
                    
                    let subClientId = select.value;
                    
                    if (subClientId === 'new') {
                        const newName = document.getElementById('newSubClientNameInput').value.trim();
                        if (!newName) {
                            Swal.showValidationMessage('Digite o nome do novo sub-cliente');
                            return false;
                        }
                        
                        // Criar novo sub-cliente
                        const formData = new FormData();
                        formData.append('action', 'create_sub_client');
                        formData.append('name', newName);
                        formData.append('company_id', companyId);
                        
                        try {
                            const response = await fetch('api/clients.php', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await response.json();
                            
                            if (data.success) {
                                subClientId = data.id;
                            } else {
                                Swal.showValidationMessage(data.error || 'Erro ao criar sub-cliente');
                                return false;
                            }
                        } catch (error) {
                            Swal.showValidationMessage('Erro ao criar sub-cliente');
                            return false;
                        }
                    }
                    
                    return { subClientId };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const { subClientId } = result.value;
                    
                    const formData = new FormData();
                    formData.append('action', 'assign_reserved');
                    formData.append('number', number);
                    formData.append('sub_client_id', subClientId);
                    
                    fetch('api/numbers.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Sucesso', 'Número atribuído com sucesso', 'success');
                            // Atualizar tabelas
                            loadUnassignedNumbers();
                            viewCompanyNumbers(companyId);
                        } else {
                            Swal.fire('Erro', data.error || 'Erro ao atribuir número', 'error');
                        }
                    });
                }
            });
        });
}

// Nova função para criar sub-cliente
function createSubClient(companyId) {
    Swal.fire({
        title: 'Criar novo sub-cliente',
        html: `
            <form id="newSubClientForm">
                <div class="mb-3">
                    <label class="form-label">Nome do sub-cliente</label>
                    <input type="text" id="subClientName" class="form-control" required>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Criar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const name = document.getElementById('subClientName').value.trim();
            if (!name) {
                Swal.showValidationMessage('Nome é obrigatório');
                return false;
            }
            
            const formData = new FormData();
            formData.append('action', 'create_sub_client');
            formData.append('name', name);
            formData.append('company_id', companyId);
            
            return fetch('api/clients.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    return data;
                }
                throw new Error(data.error || 'Erro ao criar sub-cliente');
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Sucesso', 'Sub-cliente criado com sucesso', 'success');
            // Recarregar sub-clientes
            loadSubClients(companyId);
        }
    });
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    initializeTables();
    loadAvailableNumbers();
    // Carregar números não atribuídos corretamente
    loadUnassignedNumbers();
});
