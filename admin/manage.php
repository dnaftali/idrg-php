<?php
require_once '../config/database.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            addCode();
            break;
        case 'edit':
            editCode();
            break;
        case 'delete':
            deleteCode();
            break;
    }
}

function addCode() {
    $pdo = getConnection();
    
    $code = $_POST['code'] ?? '';
    $code2 = $_POST['code2'] ?? '';
    $description = $_POST['description'] ?? '';
    $system = $_POST['system'] ?? '';
    $validcode = $_POST['validcode'] ?? 1;
    $accpdx = $_POST['accpdx'] ?? '';
    $asterisk = $_POST['asterisk'] ?? 0;
    $im = $_POST['im'] ?? 0;
    
    try {
        $sql = "INSERT INTO idr_codes (code, code2, description, system, validcode, accpdx, asterisk, im) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code, $code2, $description, $system, $validcode, $accpdx, $asterisk, $im]);
        
        header('Location: manage.php?success=added');
        exit;
    } catch(PDOException $e) {
        header('Location: manage.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

function editCode() {
    $pdo = getConnection();
    
    $id = $_POST['id'] ?? '';
    $code = $_POST['code'] ?? '';
    $code2 = $_POST['code2'] ?? '';
    $description = $_POST['description'] ?? '';
    $system = $_POST['system'] ?? '';
    $validcode = $_POST['validcode'] ?? 1;
    $accpdx = $_POST['accpdx'] ?? '';
    $asterisk = $_POST['asterisk'] ?? 0;
    $im = $_POST['im'] ?? 0;
    
    try {
        $sql = "UPDATE idr_codes SET code=?, code2=?, description=?, system=?, validcode=?, accpdx=?, asterisk=?, im=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code, $code2, $description, $system, $validcode, $accpdx, $asterisk, $im, $id]);
        
        header('Location: manage.php?success=updated');
        exit;
    } catch(PDOException $e) {
        header('Location: manage.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

function deleteCode() {
    $pdo = getConnection();
    
    $id = $_POST['id'] ?? '';
    
    try {
        $sql = "DELETE FROM idr_codes WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        header('Location: manage.php?success=deleted');
        exit;
    } catch(PDOException $e) {
        header('Location: manage.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

// Get data for listing
$pdo = getConnection();
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$system = $_GET['system'] ?? '';

$whereClause = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $whereClause .= " AND (code LIKE ? OR code2 LIKE ? OR description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($system)) {
    $whereClause .= " AND system = ?";
    $params[] = $system;
}

// Get total count
$countSql = "SELECT COUNT(*) FROM idr_codes $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get records
$sql = "SELECT * FROM idr_codes $whereClause ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pengelolaan Kode ICD</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-hospital-alt me-2"></i>
                IDRG - Admin Panel
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-home me-1"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Alerts -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php
                switch ($_GET['success']) {
                    case 'added': echo 'Kode ICD berhasil ditambahkan!'; break;
                    case 'updated': echo 'Kode ICD berhasil diperbarui!'; break;
                    case 'deleted': echo 'Kode ICD berhasil dihapus!'; break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2><i class="fas fa-cogs me-2"></i>Pengelolaan Kode ICD</h2>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus me-2"></i>Tambah Kode Baru
                </button>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Kode atau deskripsi...">
                    </div>
                    <div class="col-md-3">
                        <label for="system" class="form-label">Sistem</label>
                        <select class="form-select" id="system" name="system">
                            <option value="">Semua Sistem</option>
                            <option value="ICD-10" <?php echo $system === 'ICD-10' ? 'selected' : ''; ?>>ICD-10</option>
                            <option value="ICD-9-CM" <?php echo $system === 'ICD-9-CM' ? 'selected' : ''; ?>>ICD-9-CM</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <a href="manage.php" class="btn btn-secondary">
                                <i class="fas fa-refresh me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    Data Kode ICD (<?php echo $totalRecords; ?> total)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kode</th>
                                <th>Kode 2</th>
                                <th>Deskripsi</th>
                                <th>Sistem</th>
                                <th>Status</th>
                                <th>Flags</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record): ?>
                            <tr>
                                <td><?php echo $record['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($record['code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($record['code2']); ?></td>
                                <td><?php echo htmlspecialchars($record['description']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $record['system'] === 'ICD-10' ? 'success' : 'warning'; ?>">
                                        <?php echo $record['system']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $record['validcode'] ? 'success' : 'danger'; ?>">
                                        <?php echo $record['validcode'] ? 'Valid' : 'Tidak Valid'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($record['accpdx']): ?>
                                        <span class="badge bg-info">ACCPDX</span>
                                    <?php endif; ?>
                                    <?php if ($record['asterisk']): ?>
                                        <span class="badge bg-secondary">*</span>
                                    <?php endif; ?>
                                    <?php if ($record['im']): ?>
                                        <span class="badge bg-dark">IM</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editRecord(<?php echo htmlspecialchars(json_encode($record)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteRecord(<?php echo $record['id']; ?>, '<?php echo htmlspecialchars($record['code']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&system=<?php echo urlencode($system); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Tambah Kode ICD Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Kode *</label>
                                <input type="text" class="form-control" id="code" name="code" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="code2" class="form-label">Kode 2</label>
                                <input type="text" class="form-control" id="code2" name="code2">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="system" class="form-label">Sistem *</label>
                                <select class="form-select" id="system" name="system" required>
                                    <option value="">Pilih Sistem</option>
                                    <option value="ICD-10">ICD-10</option>
                                    <option value="ICD-9-CM">ICD-9-CM</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validcode" class="form-label">Status Valid</label>
                                <select class="form-select" id="validcode" name="validcode">
                                    <option value="1">Valid</option>
                                    <option value="0">Tidak Valid</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="accpdx" class="form-label">ACCPDX</label>
                                <input type="text" class="form-control" id="accpdx" name="accpdx" maxlength="1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="asterisk" class="form-label">Asterisk</label>
                                <select class="form-select" id="asterisk" name="asterisk">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="im" class="form-label">IM</label>
                                <select class="form-select" id="im" name="im">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Kode ICD
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_code" class="form-label">Kode *</label>
                                <input type="text" class="form-control" id="edit_code" name="code" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_code2" class="form-label">Kode 2</label>
                                <input type="text" class="form-control" id="edit_code2" name="code2">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Deskripsi *</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_system" class="form-label">Sistem *</label>
                                <select class="form-select" id="edit_system" name="system" required>
                                    <option value="">Pilih Sistem</option>
                                    <option value="ICD-10">ICD-10</option>
                                    <option value="ICD-9-CM">ICD-9-CM</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_validcode" class="form-label">Status Valid</label>
                                <select class="form-select" id="edit_validcode" name="validcode">
                                    <option value="1">Valid</option>
                                    <option value="0">Tidak Valid</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_accpdx" class="form-label">ACCPDX</label>
                                <input type="text" class="form-control" id="edit_accpdx" name="accpdx" maxlength="1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_asterisk" class="form-label">Asterisk</label>
                                <select class="form-select" id="edit_asterisk" name="asterisk">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_im" class="form-label">IM</label>
                                <select class="form-select" id="edit_im" name="im">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-trash me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kode ICD <strong id="delete_code"></strong>?</p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete_id" name="id">
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editRecord(record) {
            document.getElementById('edit_id').value = record.id;
            document.getElementById('edit_code').value = record.code;
            document.getElementById('edit_code2').value = record.code2;
            document.getElementById('edit_description').value = record.description;
            document.getElementById('edit_system').value = record.system;
            document.getElementById('edit_validcode').value = record.validcode;
            document.getElementById('edit_accpdx').value = record.accpdx;
            document.getElementById('edit_asterisk').value = record.asterisk;
            document.getElementById('edit_im').value = record.im;
            
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
        
        function deleteRecord(id, code) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_code').textContent = code;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
