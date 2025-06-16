<?php
session_start();
    require '../API/auth.php';
    if( !isAdmin() ){
        header("Location: index.php");
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Navbar copied from index.php -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand text-danger fw-bold" href="#">Loja Online</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(function_exists('isAdmin') && isAdmin()){ ?>
                        <li class="nav-item">
                            <a class="nav-link" href="areaadmin.php">Área de Administração</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Página Inicial</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16" style="margin-right: 4px;">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 14H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5zm3.14 4l1.25 6.5h7.22l1.25-6.5H3.14zM5 12a2 2 0 1 0 4 0H5z"/>
                            </svg>
                            Carrinho
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow p-4 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0 fw-bold text-danger">Área de administração</h1>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#insertProductModal">
                            <i class="bi bi-plus-circle"></i> Inserir Novo Produto
                        </button>
                    </div>

                    <?php
                    require_once '../API/db.php';

                    $stmt = $con->prepare("SELECT id, nome, descricao, preco, imagem FROM produto ORDER BY id DESC");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $produtos = $result->fetch_all(MYSQLI_ASSOC);
                    $stmt->close();
                    $con->close();
                    ?>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-danger text-white">
                            <h2 class="h5 mb-0">Produtos</h2>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Preço</th>
                                            <th>Descrição</th>
                                            <th>Imagem</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produtos as $produto): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($produto['id']) ?></td>
                                                <td><?= htmlspecialchars($produto['nome']) ?></td>
                                                <td><span class="badge bg-danger"><?= number_format($produto['preco'], 2, ',', '.') ?> €</span></td>
                                                <td><?= htmlspecialchars($produto['descricao']) ?></td>
                                                <td>
                                                    <?php if (!empty($produto['imagem'])): ?>
                                                        <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="img-thumbnail" style="width: 80px; height: auto;">
                                                    <?php else: ?>
                                                        <span class="text-muted">Sem imagem</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-danger btn-sm me-1" title="Eliminar"
                                                        onclick="if(confirm('Tem a certeza que deseja eliminar este produto?')) { fetch('../API/delete_product.php?id=<?= $produto['id'] ?>').then(r => r.json()).then(result => { if(result.status === 'success'){ location.reload(); } else { alert(result.message || 'Erro ao eliminar produto.'); } }); }">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" title="Editar"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editProductModal"
                                                        data-id="<?= htmlspecialchars($produto['id']) ?>"
                                                        data-nome="<?= htmlspecialchars($produto['nome']) ?>"
                                                        data-preco="<?= htmlspecialchars($produto['preco']) ?>"
                                                        data-descricao="<?= htmlspecialchars($produto['descricao']) ?>"
                                                    >
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content border-0">
                                <form id="editProductForm" method="post" enctype="multipart/form-data" action="../API/edit_product.php">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="editProductModalLabel">Editar Produto</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="editProductId">
                                        <div class="mb-3">
                                            <label for="editProductName" class="form-label">Nome do Produto</label>
                                            <input type="text" class="form-control" id="editProductName" name="nome" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editProductPrice" class="form-label">Preço</label>
                                            <input type="number" step="0.01" class="form-control" id="editProductPrice" name="preco" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editProductDescription" class="form-label">Descrição</label>
                                            <textarea class="form-control" id="editProductDescription" name="descricao" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editProductImage" class="form-label">Imagem (deixe em branco para não alterar)</label>
                                            <input type="file" class="form-control" id="editProductImage" name="imagem">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger">Salvar Alterações</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Insert Modal -->
                    <div class="modal fade" id="insertProductModal" tabindex="-1" aria-labelledby="insertProductModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content border-0">
                                <form method="post" action="../API/addProduct.php" enctype="multipart/form-data">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="insertProductModalLabel">Inserir Novo Produto</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="productName" class="form-label">Nome do Produto</label>
                                            <input type="text" class="form-control" id="productName" name="nome" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="productPrice" class="form-label">Preço</label>
                                            <input type="number" step="0.01" class="form-control" id="productPrice" name="preco" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="productDescription" class="form-label">Descrição</label>
                                            <textarea class="form-control" id="productDescription" name="descricao" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="productImage" class="form-label">Imagem</label>
                                            <input type="file" class="form-control" id="productImage" name="imagem" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger">Inserir Produto</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Toast for feedback -->
                    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
                        <div id="feedbackToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body" id="toastMessage"></div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>

                    <!-- Bootstrap Icons CDN -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

                    <script>
                    // Preencher modal de edição com dados do produto
                    document.addEventListener('DOMContentLoaded', function() {
                        var editModal = document.getElementById('editProductModal');
                        editModal.addEventListener('show.bs.modal', function (event) {
                            var button = event.relatedTarget;
                            document.getElementById('editProductId').value = button.getAttribute('data-id');
                            document.getElementById('editProductName').value = button.getAttribute('data-nome');
                            document.getElementById('editProductPrice').value = button.getAttribute('data-preco');
                            document.getElementById('editProductDescription').value = button.getAttribute('data-descricao');
                            document.getElementById('editProductImage').value = '';
                        });

                        // Submeter edição via AJAX
                        document.getElementById('editProductForm').addEventListener('submit', async function(e) {
                            e.preventDefault();
                            const form = e.target;
                            const formData = new FormData(form);

                            try {
                                const response = await fetch(form.action, {
                                    method: 'POST',
                                    body: formData
                                });
                                const result = await response.json();

                                let message = result.message || 'Produto atualizado com sucesso!';
                                let toastEl = document.getElementById('feedbackToast');
                                let toastMsg = document.getElementById('toastMessage');
                                toastMsg.textContent = message;

                                toastEl.classList.remove('text-bg-primary', 'text-bg-danger', 'text-bg-success');
                                if (result.status === 'success') {
                                    toastEl.classList.add('text-bg-success');
                                    var modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                                    modal.hide();
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    toastEl.classList.add('text-bg-danger');
                                }

                                var toast = new bootstrap.Toast(toastEl);
                                toast.show();

                            } catch (error) {
                                let toastEl = document.getElementById('feedbackToast');
                                let toastMsg = document.getElementById('toastMessage');
                                toastMsg.textContent = 'Erro ao atualizar produto.';
                                toastEl.classList.remove('text-bg-primary', 'text-bg-success');
                                toastEl.classList.add('text-bg-danger');
                                var toast = new bootstrap.Toast(toastEl);
                                toast.show();
                            }
                        });

                        // Submeter inserção via AJAX
                        document.querySelector('#insertProductModal form').addEventListener('submit', async function(e) {
                            e.preventDefault();

                            const form = e.target;
                            const formData = new FormData(form);

                            try {
                                const response = await fetch(form.action, {
                                    method: 'POST',
                                    body: formData
                                });

                                const result = await response.json();

                                let message = result.message || 'Produto inserido com sucesso!';
                                let toastEl = document.getElementById('feedbackToast');
                                let toastMsg = document.getElementById('toastMessage');
                                toastMsg.textContent = message;

                                toastEl.classList.remove('text-bg-primary', 'text-bg-danger', 'text-bg-success');
                                if (result.status === 'success') {
                                    toastEl.classList.add('text-bg-success');
                                    form.reset();
                                    var modal = bootstrap.Modal.getInstance(document.getElementById('insertProductModal'));
                                    modal.hide();
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    toastEl.classList.add('text-bg-danger');
                                }

                                var toast = new bootstrap.Toast(toastEl);
                                toast.show();

                            } catch (error) {
                                let toastEl = document.getElementById('feedbackToast');
                                let toastMsg = document.getElementById('toastMessage');
                                toastMsg.textContent = 'Erro ao inserir produto.';
                                toastEl.classList.remove('text-bg-primary', 'text-bg-success');
                                toastEl.classList.add('text-bg-danger');
                                var toast = new bootstrap.Toast(toastEl);
                                toast.show();
                            }
                        });
                    });
                    </script>

                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>