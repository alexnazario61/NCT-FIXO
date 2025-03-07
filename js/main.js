let availableTable, companiesTable, individualsTable;

function initializeTables() {
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
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                            Atribuir
                        </button>`;
                }
            }
        ]
    });

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
                        </div>
                    `;
                }
            }
        ]
    });

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
                        </div>
                    `;
                }
            }
        ]
    });
}

function loadAvailableNumbers() {
    availableTable.ajax.reload();
    fetch('api/numbers.php?action=available')
        .then(response => response.json())
        .then(numbers => {
            document.getElementById('available-counter').textContent = numbers.length;
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
    Swal.fire({
        title: 'Atribuir Número',
        html: `
            <input id="userName" class="swal2-input" placeholder="Nome do usuário">
            <p class="mt-2">Número: ${number}</p>
        `,
        showCancelButton: true,
        confirmButtonText: 'Atribuir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const userName = document.getElementById('userName').value;
            if (userName) {
                // Implementar lógica de atribuição
                // ...
            }
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
    companiesTable.ajax.reload();
    individualsTable.ajax.reload();
}

function loadSubClients(companyId) {
    const modalContent = `
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Números</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="subclients-list">
                    </tbody>
                </table>
            </div>
        </div>
    `;

    Swal.fire({
        title: 'Sub-clientes',
        html: modalContent,
        width: '800px',
        showConfirmButton: false,
        showCloseButton: true
    });

    fetch(`api/clients.php?action=sub_clients&company_id=${companyId}`)
        .then(response => response.json())
        .then(subClients => {
            const container = document.getElementById('subclients-list');
            if (container) {
                container.innerHTML = subClients.map(client => `
                    <tr>
                        <td>${client.name}</td>
                        <td>${client.numbers_count}</td>
                        <td>
                            <button onclick="viewNumbers(${client.id}, true)" 
                                    class="btn btn-sm btn-primary">
                                Ver números
                            </button>
                            <button onclick="editSubClient(${client.id})" 
                                    class="btn btn-sm btn-secondary">
                                Editar
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
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
                }
            });
        }
    });
}

function showAddClientModal() {
    const template = document.getElementById('add-client-modal').innerHTML;
    Swal.fire({
        title: 'Adicionar Novo Cliente',
        html: template,
        showCancelButton: true,
        confirmButtonText: 'Adicionar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const form = document.getElementById('addClientForm');
            const formData = new FormData(form);
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
            });
        }
    });
}

function showAddNumbersModal() {
    const template = document.getElementById('add-numbers-modal').innerHTML;
    Swal.fire({
        title: 'Adicionar Números',
        html: template,
        showCancelButton: true,
        confirmButtonText: 'Adicionar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const form = document.getElementById('addNumbersForm');
            const formData = new FormData(form);
            return fetch('api/numbers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAvailableNumbers();
                    return true;
                }
                throw new Error(data.error || 'Erro ao adicionar números');
            });
        }
    });
}

function showAssignNumbersModal() {
    fetch('api/numbers.php?action=available')
        .then(response => response.json())
        .then(numbers => {
            const template = document.getElementById('assign-numbers-modal').innerHTML;
            Swal.fire({
                title: 'Atribuir Números',
                html: template,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: 'Atribuir',
                cancelButtonText: 'Cancelar',
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

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    initializeTables();
    loadAvailableNumbers();
    loadClients();
});
