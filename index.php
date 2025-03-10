<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCT Fixo - Gerenciamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
        }
        
        .card {
            background-color: #1e1e1e;
            border-color: #333;
            transition: transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.35);
        }
        
        .card-header {
            background-color: #272727;
            border-bottom-color: #333;
        }
        
        .table {
            color: #e0e0e0;
            border-color: #333;
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.075);
        }
        
        .form-control, .form-select {
            background-color: #2b2b2b;
            border-color: #444;
            color: #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: #333;
            color: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .modal-content {
            background-color: #1e1e1e;
            color: #e0e0e0;
            border-color: #333;
        }
        
        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        /* DataTables customization */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #e0e0e0;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #e0e0e0 !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0d6efd;
            color: white !important;
            border-color: #0d6efd;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #0b5ed7;
            color: white !important;
            border-color: #0b5ed7;
        }
        
        /* Button styling */
        .btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .btn:active {
            transform: translateY(1px);
        }
        
        .btn-dark {
            background-color: #2d2d2d;
            border-color: #444;
        }
        
        .btn-dark:hover {
            background-color: #3a3a3a;
            border-color: #555;
        }
        
        /* Navbar styling */
        .navbar {
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #0d6efd, #0a3f95) !important;
        }
        
        .navbar-brand {
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        
        .navbar .btn {
            margin-left: 5px;
            transition: all 0.3s;
        }
        
        .navbar .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        
        /* Loading effect */
        .btn-loading {
            pointer-events: none;
            position: relative;
            color: transparent !important;
        }
        
        .btn-loading:after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: calc(50% - 10px);
            left: calc(50% - 10px);
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease infinite;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        /* Form improvements */
        .modal-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #e0e0e0;
        }
        
        .modal-form input,
        .modal-form select,
        .modal-form textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            background-color: #2d2d2d;
            color: #e0e0e0;
            border: 1px solid #444;
            border-radius: 4px;
        }
        
        /* Sweet Alert customizations */
        .swal2-popup {
            background: #222 !important;
            color: #e0e0e0 !important;
            border: 1px solid #444 !important;
        }
        
        .swal2-title, .swal2-content {
            color: #e0e0e0 !important;
        }
        
        .swal2-input, .swal2-textarea, .swal2-select {
            background: #333 !important;
            color: #e0e0e0 !important;
            border-color: #555 !important;
        }
        
        /* Highlight row on hover */
        .table-hover tbody tr {
            transition: all 0.2s;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }
        
        /* Correção para checkbox e radio buttons */
        .form-check-input {
            width: 1em;
            height: 1em;
            margin-top: 0.25em;
            vertical-align: top;
            appearance: none;
            -webkit-appearance: none;
            background-color: #2b2b2b;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .form-check-input[type="checkbox"] {
            border-radius: 0.2em;
        }
        
        .form-check-input[type="radio"] {
            border-radius: 50%;
        }
        
        /* Melhorar a aparência do form-check dentro de modais */
        .modal-body .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            padding-left: 0;
        }
        
        .modal-body .form-check-input {
            margin-right: 0.5rem;
            margin-left: 0;
            position: static;
            float: none;
            flex-shrink: 0;
            /* Ajustar tamanho para que não fique desproporcional */
            width: 1em !important;
            height: 1em !important;
            min-width: 1em !important;
            min-height: 1em !important;
            max-width: 1em !important;
            max-height: 1em !important;
        }
        
        .modal-body .form-check-label {
            padding-left: 0;
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        
        /* Ajuste específico para o modal Atribuir Números */
        #available-numbers-list .form-check {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
        }
        
        #available-numbers-list .form-check-input, 
        #client-list .form-check-input {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            margin-right: 8px;
            position: static;
            flex-shrink: 0;
        }
        
        #available-numbers-list .form-check:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        #client-list .form-check {
            padding: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
        }
        
        #client-list .form-check:last-child {
            border-bottom: none;
        }
        
        /* Corrigir problema no modal Atribuir Número */
        .swal2-html-container .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            padding-left: 0;
        }
        
        .swal2-html-container .form-check-input {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            flex-shrink: 0;
            margin-right: 8px;
            position: static;
        }
        
        .swal2-html-container .form-check-label {
            margin-bottom: 0;
            padding-left: 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-phone-alt me-2"></i>NCT Fixo
            </a>
            <div class="d-flex gap-2">
                <button onclick="showAddClientModal()" class="btn btn-dark">
                    <i class="fas fa-user-plus me-2"></i>Adicionar Cliente
                </button>
                <button onclick="showAssignNumbersModal()" class="btn btn-dark">
                    <i class="fas fa-link me-2"></i>Atribuir Números
                </button>
                <button onclick="showAddNumbersModal()" class="btn btn-dark">
                    <i class="fas fa-plus-circle me-2"></i>Adicionar Números
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
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>Empresas
                        </h5>
                    </div>
                    <div class="card-body">
                        <table id="companies-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Sub-clientes</th>
                                    <th>Números</th>
                                    <th style="width: 210px;">Ações</th>
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
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Clientes Individuais
                        </h5>
                    </div>
                    <div class="card-body">
                        <table id="individuals-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Números</th>
                                    <th style="width: 185px;">Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Números reservados para empresas -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bookmark me-2"></i>Números Reservados para Empresas
                        </h5>
                    </div>
                    <div class="card-body">
                        <table id="unassigned-numbers-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>Números Reservados</th>
                                    <th style="width: 80px;">Ações</th>
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
        <div class="p-3 modal-form">
            <form id="addClientForm">
                <div class="mb-3">
                    <label for="clientName" class="form-label">Nome do Cliente</label>
                    <input type="text" id="clientName" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="clientType" class="form-label">Tipo</label>
                    <select id="clientType" name="type" class="form-select" required>
                        <option value="individual">Cliente Individual</option>
                        <option value="company">Empresa</option>
                    </select>
                </div>
            </form>
        </div>
    </template>

    <template id="add-numbers-modal">
        <div class="p-3 modal-form">
            <form id="addNumbersForm">
                <div class="mb-3">
                    <label for="numbersInput" class="form-label">Números (separados por vírgula)</label>
                    <textarea id="numbersInput" name="numbers" class="form-control" rows="4" required></textarea>
                    <div class="form-text text-light opacity-75">Ex: 1151972777, 1151972786, 1151972788</div>
                </div>
                <input type="hidden" name="action" value="create">
            </form>
        </div>
    </template>

    <!-- Template de Edição -->
    <template id="edit-client-modal">
        <div class="p-3 modal-form">
            <form id="editClientForm">
                <input type="hidden" name="id">
                <div class="mb-3">
                    <label for="editClientName" class="form-label">Nome</label>
                    <input type="text" id="editClientName" name="name" class="form-control" required>
                </div>
            </form>
        </div>
    </template>

    <template id="assign-reserved-number-modal">
        <div class="p-3 modal-form">
            <form id="assignReservedForm">
                <div class="mb-3">
                    <label for="subClientSelect" class="form-label">Sub-cliente</label>
                    <select id="subClientSelect" name="sub_client_id" class="form-select" required>
                        <!-- Opções serão carregadas dinamicamente -->
                    </select>
                </div>
            </form>
        </div>
    </template>

    <!-- Template Atribuir Números -->
    <template id="assign-numbers-modal">
        <div class="modal-body">
            <div class="mb-3 modal-form">
                <label for="client-search" class="form-label">Buscar cliente</label>
                <input type="text" id="client-search" class="form-control" placeholder="Digite o nome do cliente...">
                <div id="client-list" class="mt-2 border p-2 rounded" style="max-height: 150px; overflow-y: auto;"></div>
            </div>
            <div class="mb-3">
                <h6 class="mb-2">
                    <i class="fas fa-list-ul me-2"></i>Números Disponíveis
                </h6>
                <div id="available-numbers-list" class="border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                    <!-- Números serão carregados aqui -->
                </div>
            </div>
        </div>
    </template>

    <script src="js/main.js"></script>
    <script>
        // SweetAlert2 dark theme
        document.addEventListener('DOMContentLoaded', function() {
            // Configuração do SweetAlert2 para tema escuro
            Swal = Swal.mixin({
                background: '#222',
                color: '#e0e0e0',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#2d2d2d',
                buttonsStyling: true,
                customClass: {
                    popup: 'swal2-dark',
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-secondary'
                }
            });
            
            // Configuração do DataTables
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                },
                initComplete: function() {
                    $('.dataTables_filter input').addClass('bg-dark text-light border-secondary');
                    $('.dataTables_length select').addClass('bg-dark text-light border-secondary');
                }
            });
            
            // Adicionar classe de carregamento aos botões quando clicados
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Não adicionar efeito de loading para alguns botões específicos
                    if (!this.classList.contains('no-loading')) {
                        const originalContent = this.innerHTML;
                        this.classList.add('btn-loading');
                        
                        setTimeout(() => {
                            this.classList.remove('btn-loading');
                            this.innerHTML = originalContent;
                        }, 500); // Removendo após 500ms para efeito visual
                    }
                });
            });
        });
    </script>
</body>
</html>
