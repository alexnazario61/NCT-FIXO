<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCT Fixo - Gerenciamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">NCT Fixo</a>
            <div class="d-flex gap-2">
                <button onclick="showAddClientModal()" class="btn btn-light">
                    Adicionar Cliente
                </button>
                <button onclick="showAssignNumbersModal()" class="btn btn-light">
                    Atribuir Números
                </button>
                <button onclick="showAddNumbersModal()" class="btn btn-light">
                    Adicionar Números
                </button>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Clientes -->
        <div class="row g-4">
            <!-- Empresas -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Empresas</h5>
                    </div>
                    <div class="card-body">
                        <table id="companies-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Sub-clientes</th>
                                    <th>Números</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Clientes Individuais -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Clientes Individuais</h5>
                    </div>
                    <div class="card-body">
                        <table id="individuals-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Números</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Templates -->
    <template id="add-client-modal">
        <div class="p-6">
            <form id="addClientForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nome do Cliente</label>
                    <input type="text" name="name" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tipo</label>
                    <select name="type" class="w-full p-2 border rounded" required>
                        <option value="individual">Cliente Individual</option>
                        <option value="company">Empresa</option>
                    </select>
                </div>
            </form>
        </div>
    </template>

    <template id="add-numbers-modal">
        <div class="p-6">
            <form id="addNumbersForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Números (separados por vírgula)</label>
                    <textarea name="numbers" class="w-full p-2 border rounded" rows="4" required></textarea>
                    <p class="text-sm text-gray-500 mt-1">Ex: 1151972777, 1151972786, 1151972788</p>
                </div>
            </form>
        </div>
    </template>

    <!-- Template de Edição -->
    <template id="edit-client-modal">
        <div class="p-6">
            <form id="editClientForm">
                <input type="hidden" name="id">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nome</label>
                    <input type="text" name="name" class="w-full p-2 border rounded" required>
                </div>
            </form>
        </div>
    </template>

    <template id="assign-reserved-number-modal">
        <div class="p-6">
            <form id="assignReservedForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Sub-cliente</label>
                    <select name="sub_client_id" class="w-full p-2 border rounded" required>
                        <!-- Opções serão carregadas dinamicamente -->
                    </select>
                </div>
            </form>
        </div>
    </template>

    <!-- Template Atribuir Números -->
    <template id="assign-numbers-modal">
        <div class="modal-body">
            <div class="mb-3">
                <input type="text" id="client-search" class="form-control" placeholder="Buscar cliente...">
                <div id="client-list" class="mt-2"></div>
            </div>
            <div class="mb-3">
                <h6>Números Disponíveis</h6>
                <div id="available-numbers-list" class="border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                    <!-- Números serão carregados aqui -->
                </div>
            </div>
        </div>
    </template>

    <script src="js/main.js"></script>
</body>
</html>
