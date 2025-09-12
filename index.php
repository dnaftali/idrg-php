<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDRG - Daftar Pasien</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/dody.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        
        .section-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin: 20px 0;
            padding: 25px;
        }
        
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            font-size: 1.2rem;
        }
        
        .section-title i {
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            font-size: 0.9rem;
        }
        
        .table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px 10px;
            font-size: 0.85rem;
        }
        
        .table td {
            vertical-align: middle;
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.85rem;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        
        .patient-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        
        .badge-custom {
            padding: 6px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-inpatient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .badge-outpatient {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .badge-jkn {
            background: #28a745;
            color: white;
        }
        
        .badge-bpjs {
            background: #17a2b8;
            color: white;
        }
        
        .badge-umum {
            background: #6c757d;
            color: white;
        }
        
        .badge-asuransi {
            background: #fd7e14;
            color: white;
        }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-view:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-coding {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .btn-coding:hover {
            background: linear-gradient(135deg, #e085e8 0%, #e54b5f 100%);
            color: white;
            transform: translateY(-1px);
        }
        
        .stats-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .stats-item {
            text-align: center;
            padding: 15px;
        }
        
        .stats-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .btn {
            font-size: 0.85rem;
        }
        
        .main-header h1 {
            font-size: 1.8rem;
        }
        
        .main-header .lead {
            font-size: 1rem;
        }
        
        .navbar-nav .nav-link {
            font-size: 0.9rem;
        }
        
        .modal-title {
            font-size: 1.1rem;
        }
        
        .modal-body {
            font-size: 0.9rem;
        }
        
        .modal-body h6 {
            font-size: 0.95rem;
        }
        
        .modal-body table {
            font-size: 0.85rem;
        }
        
        /* Tab Styling */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 0;
        }
        
        .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 12px 20px;
            font-weight: 500;
            color: #6c757d;
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #495057;
            background-color: #f8f9fa;
        }
        
        .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
            border-bottom: 2px solid #667eea;
        }
        
        .nav-tabs .nav-link .badge {
            font-size: 0.7rem;
            padding: 0.25em 0.5em;
        }
        
        .tab-content {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 0;
        }
        
        .tab-pane {
            padding: 0;
        }
        
        .tab-pane .table-responsive {
            margin: 0;
            border-radius: 0;
        }
        
        .tab-pane .table {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="main-header">
        <div class="container">
            <h1 class="mb-0">
                <i class="fas fa-hospital-alt me-3"></i>
                IDRG - Sistem Pengelolaan Pasien
            </h1>
            <p class="lead mt-3 mb-0">Daftar Pasien Rawat Inap dan Rawat Jalan</p>
        </div>
    </div>

    <div class="container">
        <!-- Tab Navigation -->
        <div class="section-container">
            <ul class="nav nav-tabs" id="patientTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="inpatient-tab" data-bs-toggle="tab" data-bs-target="#inpatient" type="button" role="tab" aria-controls="inpatient" aria-selected="true">
                        <i class="fas fa-bed text-primary me-2"></i>
                        Pasien Rawat Inap
                        <span class="badge bg-primary ms-2" id="inpatientBadge">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="outpatient-tab" data-bs-toggle="tab" data-bs-target="#outpatient" type="button" role="tab" aria-controls="outpatient" aria-selected="false">
                        <i class="fas fa-walking text-success me-2"></i>
                        Pasien Rawat Jalan
                        <span class="badge bg-success ms-2" id="outpatientBadge">0</span>
                    </button>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" id="patientTabsContent">
                <!-- Rawat Inap Tab -->
                <div class="tab-pane fade show active" id="inpatient" role="tabpanel" aria-labelledby="inpatient-tab">
                    <div class="table-responsive mt-3">
                        <table class="table" id="inpatientTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SEP</th>
                                    <th>Nama Pasien</th>
                                    <th>Jaminan</th>
                                    <th>Kelas</th>
                                    <th>DPJP</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Tanggal Pulang</th>
                                    <th>LOS</th>
                                    <th>Umur</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="inpatientContainer">
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="fas fa-bed fa-2x mb-2"></i>
                                        <p>Memuat data pasien rawat inap...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Rawat Jalan Tab -->
                <div class="tab-pane fade" id="outpatient" role="tabpanel" aria-labelledby="outpatient-tab">
                    <div class="table-responsive mt-3">
                        <table class="table" id="outpatientTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SEP</th>
                                    <th>Nama Pasien</th>
                                    <th>Jaminan</th>
                                    <th>Kelas</th>
                                    <th>DPJP</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Tanggal Pulang</th>
                                    <th>LOS</th>
                                    <th>Umur</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="outpatientContainer">
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="fas fa-walking fa-2x mb-2"></i>
                                        <p>Memuat data pasien rawat jalan...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    
                <!-- Statistics Summary -->
                <div class="stats-summary">
            <div class="row">
                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="stats-number" id="totalPatients">0</div>
                        <div class="stats-label">Total Pasien</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="stats-number" id="totalInpatient">0</div>
                        <div class="stats-label">Rawat Inap</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="stats-number" id="totalOutpatient">0</div>
                        <div class="stats-label">Rawat Jalan</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="stats-number" id="totalJKN">0</div>
                        <div class="stats-label">JKN/BPJS</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Detail Pasien -->
    <div class="modal fade" id="patientModal" tabindex="-1" aria-labelledby="patientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title" id="patientModalLabel">
                        <i class="fas fa-user-circle me-2"></i>
                        Detail Pasien
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="patientModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="goToCodingBtn">
                        <i class="fas fa-hospital me-1"></i>
                        Ke Coding iDRG
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Load patient data
            loadPatients();
        });
        
        function loadPatients() {
            // Show loading state
            $('#inpatientContainer').html(`
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p>Memuat data dari database...</p>
                    </td>
                </tr>
            `);
            
            $('#outpatientContainer').html(`
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p>Memuat data dari database...</p>
                    </td>
                </tr>
            `);
            
            // Fetch data from API
            $.ajax({
                url: 'api/patients.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const patients = response.data;
                        displayPatients(patients);
                        updateStats(patients);
                        console.log(`Berhasil memuat ${patients.length} data pasien dari database`);
                    } else {
                        showError('Gagal memuat data: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    showError('Gagal memuat data dari database. Silakan cek koneksi database.');
                }
            });
        }
        
        function displayPatients(patients) {
            // Store patients data globally for other functions to use
            window.currentPatients = patients;
            
            // Filter pasien dengan jenis_rawat yang valid (1 atau 2)
            const validPatients = patients.filter(p => p.jenis_rawat === '1' || p.jenis_rawat === '2');
            
            const inpatients = validPatients.filter(p => p.jenis_rawat === '1');
            const outpatients = validPatients.filter(p => p.jenis_rawat === '2');
            
            // Log untuk debugging
            console.log('Total patients:', patients.length);
            console.log('Valid patients:', validPatients.length);
            console.log('Inpatients:', inpatients.length);
            console.log('Outpatients:', outpatients.length);
            
            displayPatientList('inpatientContainer', inpatients, 'inpatient');
            displayPatientList('outpatientContainer', outpatients, 'outpatient');
        }
        
        function displayPatientList(containerId, patients, type) {
            const container = $(`#${containerId}`);
            
            if (patients.length === 0) {
                container.html(`
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <i class="fas fa-${type === 'inpatient' ? 'bed' : 'walking'} fa-2x mb-2"></i>
                            <p>Tidak ada data pasien ${type === 'inpatient' ? 'rawat inap' : 'rawat jalan'}</p>
                        </td>
                    </tr>
                `);
                return;
            }
            
            let html = '';
            patients.forEach((patient, index) => {
                html += createPatientRow(patient, index + 1, type);
            });
            
            container.html(html);
        }
        
        function createPatientRow(patient, index, type) {
            const jaminanBadge = getJaminanBadge(patient.jaminan_cara_bayar);
            const sepText = patient.nomor_sep || '-';
            const dpjpShort = patient.nama_dokter && patient.nama_dokter.length > 30 ? 
                patient.nama_dokter.substring(0, 30) + '...' : 
                (patient.nama_dokter || '-');
            const umur = calculateAge(patient.tgl_lahir);
            const kelasText = getKelasText(patient.kelas_rawat);
            
            return `
                <tr data-patient-id="${patient.id}">
                    <td>${index}</td>
                    <td><span class="fw-bold">${sepText}</span></td>
                    <td><strong>${patient.nama_pasien}</strong></td>
                    <td>${jaminanBadge}</td>
                    <td><span class="badge bg-secondary">${kelasText}</span></td>
                    <td title="${patient.nama_dokter || '-'}">${dpjpShort}</td>
                    <td>${formatDate(patient.tgl_masuk)}</td>
                    <td>${formatDate(patient.tgl_pulang)}</td>
                    <td><span class="badge bg-info">${patient.los_hari || 0} hari</span></td>
                    <td>${umur} tahun</td>
                    <td>
                        <div class="patient-actions">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewPatient(${patient.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="goToCoding(${patient.id})">
                                <i class="fas fa-hospital"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }
        
        function getJaminanBadge(jaminan) {
            const badgeClass = {
                'JKN': 'badge-jkn',
                'BPJS': 'badge-bpjs',
                'UMUM': 'badge-umum',
                'ASURANSI': 'badge-asuransi'
            }[jaminan] || 'badge-secondary';
            
            return `<span class="badge badge-custom ${badgeClass}">${jaminan || 'JKN'}</span>`;
        }
        
        function getKelasText(kelas) {
            const kelasMap = {
                '1': 'Kelas 1',
                '2': 'Kelas 2',
                '3': 'Kelas 3'
            };
            return kelasMap[kelas] || 'Kelas 3';
        }
        
        function calculateAge(birthDate) {
            if (!birthDate) return '-';
            const birth = new Date(birthDate);
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            return age;
        }
        
        function formatDate(dateTimeStr) {
            if (!dateTimeStr) return '-';
            const date = new Date(dateTimeStr);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
        
        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return '-';
            const date = new Date(dateTimeStr);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function updateStats(patients) {
            // Filter hanya pasien dengan jenis_rawat yang valid
            const validPatients = patients.filter(p => p.jenis_rawat === '1' || p.jenis_rawat === '2');
            
            const total = validPatients.length;
            const inpatient = validPatients.filter(p => p.jenis_rawat === '1').length;
            const outpatient = validPatients.filter(p => p.jenis_rawat === '2').length;
            const jknBpjs = validPatients.filter(p => ['JKN', 'BPJS'].includes(p.jaminan_cara_bayar)).length;
            
            $('#totalPatients').text(total);
            $('#totalInpatient').text(inpatient);
            $('#totalOutpatient').text(outpatient);
            $('#totalJKN').text(jknBpjs);
            
            // Update tab badges
            $('#inpatientBadge').text(inpatient);
            $('#outpatientBadge').text(outpatient);
        }
        
        function viewPatient(patientId) {
            // Find patient data
            const allPatients = getAllPatients();
            const patient = allPatients.find(p => p.id === patientId);
            
            if (!patient) {
                alert('Data pasien tidak ditemukan');
                return;
            }
            
            // Show modal with patient details
            showPatientModal(patient);
        }
        
        function getAllPatients() {
            // This function now returns data from the current session
            // Data is loaded via AJAX in loadPatients()
            return window.currentPatients || [];
        }
        
        function showError(message) {
            // Show error message in both tables
            const errorHtml = `
                <tr>
                    <td colspan="11" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>${message}</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="loadPatients()">
                            <i class="fas fa-redo me-1"></i>
                            Coba Lagi
                        </button>
                    </td>
                </tr>
            `;
            
            $('#inpatientContainer').html(errorHtml);
            $('#outpatientContainer').html(errorHtml);
        }
        
        function showPatientModal(patient) {
            const jaminanBadge = getJaminanBadge(patient.jaminan_cara_bayar);
            const jenisRawatBadge = patient.jenis_rawat === '1' ? 
                '<span class="badge bg-primary">Rawat Inap</span>' : 
                '<span class="badge bg-success">Rawat Jalan</span>';
            const kelasText = getKelasText(patient.kelas_rawat);
            const umur = calculateAge(patient.tgl_lahir);
            
            const modalContent = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Umum
                        </h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>ID Pasien:</strong></td>
                                <td>${patient.id}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Pasien:</strong></td>
                                <td><strong>${patient.nama_pasien}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Rawat:</strong></td>
                                <td>${jenisRawatBadge}</td>
                            </tr>
                            <tr>
                                <td><strong>Jaminan:</strong></td>
                                <td>${jaminanBadge}</td>
                            </tr>
                            <tr>
                                <td><strong>Kelas:</strong></td>
                                <td><span class="badge bg-secondary">${kelasText}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Umur:</strong></td>
                                <td>${umur} tahun</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Informasi Kunjungan
                        </h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Tanggal Masuk:</strong></td>
                                <td>${formatDateTime(patient.tgl_masuk)}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pulang:</strong></td>
                                <td>${formatDateTime(patient.tgl_pulang)}</td>
                            </tr>
                            <tr>
                                <td><strong>LOS:</strong></td>
                                <td><span class="badge bg-info">${patient.los_hari || 0} hari</span></td>
                            </tr>
                            <tr>
                                <td><strong>Status Pulang:</strong></td>
                                <td>${getDischargeStatusText(patient.discharge_status)}</td>
                            </tr>
                            <tr>
                                <td><strong>ADL Score:</strong></td>
                                <td>${patient.adl_sub_acute || 0}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user-md me-2"></i>
                            Informasi Medis
                        </h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>DPJP:</strong></td>
                                <td>${patient.nama_dokter || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>No. SEP:</strong></td>
                                <td>${patient.nomor_sep || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>No. Kartu:</strong></td>
                                <td>${patient.nomor_kartu || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>No. RM:</strong></td>
                                <td>${patient.nomor_rm || '-'}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-stethoscope me-2"></i>
                            Diagnosa & Prosedur
                        </h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Diagnosa:</strong></td>
                                <td>${patient.diagnosa ? patient.diagnosa.replace(/#/g, ', ') : '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Prosedur:</strong></td>
                                <td>${patient.procedures ? patient.procedures.replace(/#/g, ', ') : '-'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            `;
            
            $('#patientModalBody').html(modalContent);
            
            // Set patient ID for coding button
            $('#goToCodingBtn').attr('onclick', `goToCoding(${patient.id})`);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('patientModal'));
            modal.show();
        }
        
        function getDischargeStatusText(status) {
            const statusMap = {
                '1': 'Atas persetujuan dokter',
                '2': 'Dirujuk',
                '3': 'Atas permintaan sendiri',
                '4': 'Meninggal',
                '5': 'Lain-lain'
            };
            return statusMap[status] || 'Atas persetujuan dokter';
        }
        
        function goToCoding(patientId) {
            // Show loading state
            const btn = $('#goToCodingBtn');
            const originalText = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin me-1"></i>Mendaftarkan SEP ke E-Klaim...');
            btn.prop('disabled', true);
            
            // Register new claim in E-Klaim first
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    action: 'new_claim',
                    patient_id: patientId
                }),
                success: function(response) {
                    if (response.success) {
                        console.log('E-Klaim SEP registered successfully:', response);
                        
                        // Show success message
                        showSuccess('SEP berhasil didaftarkan ke E-Klaim!');
                        
                        // Redirect to coding page after short delay
                        setTimeout(() => {
                            window.location.href = `coding_idrg.php?patient_id=${patientId}`;
                        }, 1500);
                    } else {
                        console.error('E-Klaim SEP registration failed:', response);
                        
                        // Show error but still allow to proceed
                        showWarning('Gagal mendaftarkan SEP ke E-Klaim: ' + (response.error || 'Unknown error') + 
                                   '<br><br>Anda tetap dapat melanjutkan ke halaman coding.');
                        
                        // Reset button
                        btn.html(originalText);
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('E-Klaim request error:', error);
                    
                    // Show error but still allow to proceed
                    showWarning('Gagal terhubung ke server E-Klaim: ' + error + 
                               '<br><br>Anda tetap dapat melanjutkan ke halaman coding.');
                    
                    // Reset button
                    btn.html(originalText);
                    btn.prop('disabled', false);
                }
            });
        }
        
        function showSuccess(message) {
            // Create success alert
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Insert at top of page
            $('body').prepend(alertHtml);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                $('.alert-success').fadeOut();
            }, 5000);
        }
        
        function showWarning(message) {
            // Create warning alert
            const alertHtml = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Insert at top of page
            $('body').prepend(alertHtml);
            
            // Auto dismiss after 8 seconds
            setTimeout(() => {
                $('.alert-warning').fadeOut();
            }, 8000);
        }
    </script>
</body>
</html>
