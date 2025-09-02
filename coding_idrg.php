<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODING iDRG</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/dody.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
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
            padding: 15px 0;
            text-align: center;
        }
        
        .main-header h1 {
            font-size: 1.5rem;
            margin-bottom: 0;
        }
        
        .section-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 15px 0;
            padding: 15px;
        }
        
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
            font-size: 1.1rem;
        }
        
        .search-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 10px;
        }
        
        .search-label {
            font-weight: 500;
            color: #495057;
            min-width: 120px;
            font-size: 0.9rem;
        }
        
        .search-input {
            flex: 1;
        }
        
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 6px;
            border: 1px solid #e9ecef;
            min-height: 38px;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single {
            height: 38px;
            padding: 6px 10px;
        }
        
        .select2-dropdown {
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        .select2-results__option {
            padding: 8px 12px;
        }
        
        .select2-results__option--highlighted {
            background-color: #667eea !important;
        }
        
        .table {
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            font-size: 0.85rem;
        }
        
        .table th {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 8px 10px;
            font-size: 0.8rem;
        }
        
        .table td {
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            padding: 6px 10px;
        }

        /* Styling untuk input biaya */
        .cost-input {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 500;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .cost-input:focus {
            background-color: #fff;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .cost-input:hover {
            background-color: #e9ecef;
        }

        /* Styling untuk total tarif */
        #totalTarif {
            font-weight: bold;
            color: #28a745;
            font-size: 1.1rem;
            font-family: 'Courier New', monospace;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 11px;
        }
        
        .btn-remove:hover {
            background: #c82333;
        }
        
        .code-badge {
            background: #667eea;
            color: white;
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 11px;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ced4da;
            border-radius: 3px;
            padding: 3px;
            font-size: 0.8rem;
        }
        
        .empty-table {
            text-align: center;
            color: #6c757d;
            padding: 25px 15px;
        }
        
        .empty-table i {
            font-size: 2rem;
            margin-bottom: 10px;
            opacity: 0.5;
        }
        
        .empty-table p {
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        
        .invalid-code-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .form-control-plaintext {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            color: #495057;
            font-weight: 500;
        }
        
        .patient-info-grid {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .cost-section {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .form-label.fw-bold {
            color: #495057;
            font-size: 0.8rem;
            margin-bottom: 0.3rem;
        }
        
        .form-control, .form-select {
            font-size: 0.85rem;
            padding: 0.375rem 0.5rem;
            height: auto;
        }
        
        .form-control-plaintext {
            font-size: 0.85rem;
            padding: 0.375rem 0.5rem;
        }
        
        .form-control[readonly] {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 500;
        }
        
        .mb-3 {
            margin-bottom: 0.75rem !important;
        }
        
        .alert {
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }
        
        .btn {
            font-size: 0.85rem;
            padding: 0.375rem 0.75rem;
        }
        
        .btn-lg {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        
        .navbar {
            padding: 0.5rem 0;
        }
        
        .navbar-nav .nav-link {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }
        
        /* Compact layout improvements */
        .container {
            max-width: 1400px;
        }
        
        .row {
            margin-left: -10px;
            margin-right: -10px;
        }
        
        .col-md-4, .col-md-6, .col-md-12 {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        .form-check {
            margin-bottom: 0.25rem;
        }
        
        .form-check-label {
            font-size: 0.85rem;
        }
        
        .form-check-input {
            margin-top: 0.1rem;
        }
        
        /* Reduce spacing in cost section */
        .cost-section .row .col-md-4 .mb-3:last-child {
            margin-bottom: 0 !important;
        }
        
        /* Compact table improvements */
        .table-responsive {
            margin-bottom: 0;
        }
        
        /* Reduce alert padding */
        .alert-warning {
            padding: 0.5rem 0.75rem;
        }
        
        .alert-warning h5 {
            font-size: 1rem;
            margin-bottom: 0;
        }
        
        /* Patient info grid improvements */
        .patient-info-grid .row {
            margin-left: 0;
            margin-right: 0;
        }
        
        .patient-info-grid .col-12 {
            padding-left: 0;
            padding-right: 0;
        }
        
        .patient-info-grid .mb-2 {
            margin-bottom: 0.5rem !important;
        }
        
        .patient-info-grid .form-label {
            margin-bottom: 0.2rem;
            font-size: 0.75rem;
        }
        
        .patient-info-grid .form-control,
        .patient-info-grid .form-select,
        .patient-info-grid .form-control-plaintext {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            height: auto;
            min-height: 32px;
        }
        
        .patient-info-grid .form-check {
            margin-bottom: 0;
        }
        
        .patient-info-grid .form-check-label {
            font-size: 0.75rem;
            margin-left: 0.25rem;
        }
        
                 .patient-info-grid .d-flex.gap-3 {
             gap: 1rem !important;
         }
         
         /* Equal height columns */
         .patient-info-grid .col-md-4 {
             display: flex;
             flex-direction: column;
         }
         
         .patient-info-grid .col-md-4 .row {
             flex: 1;
             display: flex;
             flex-direction: column;
             justify-content: space-between;
         }
         
         /* Blood pressure input styling */
         .form-control[id="sistole"],
         .form-control[id="diastole"] {
             background-color: #fff3cd;
             border: 1px solid #ffeaa7;
             border-radius: 6px;
             text-align: center;
             font-weight: 500;
         }
         
         /* Validation status styling */
         .validation-status {
             font-size: 0.8rem;
             padding: 2px 6px;
             border-radius: 4px;
             background-color: rgba(255, 193, 7, 0.1);
             border: 1px solid rgba(255, 193, 7, 0.3);
         }
         
         .validation-status.valid {
             background-color: rgba(40, 167, 69, 0.1);
             border: 1px solid rgba(40, 167, 69, 0.3);
         }
         
         .validation-status.valid i,
         .validation-status.valid small {
             color: #28a745 !important;
         }
         
         .form-control[id="sistole"]:focus,
         .form-control[id="diastole"]:focus {
             background-color: #fff3cd;
             border-color: #ffc107;
             box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
         }
         
                   /* Action bar styling */
          .section-container:has(#hapusKlaimBtn) {
              background-color: #f8f9fa;
              border: 1px solid #dee2e6;
          }
          
          /* Make primary button styling */
          .btn-outline-primary {
              border-color: #667eea;
              color: #667eea;
              font-size: 0.75rem;
              padding: 0.25rem 0.5rem;
          }
          
          .btn-outline-primary:hover {
              background-color: #667eea;
              border-color: #667eea;
              color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="main-header">
        <div class="container">
            <h1 class="mb-0">
                <i class="fas fa-hospital-alt me-3"></i>
                CODING iDRG
            </h1>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali ke Beranda
                </a>
            </div>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="test_coding_page.php">
                    <i class="fas fa-vial me-1"></i>
                    Test Page
                </a>
                <a class="nav-link" href="admin/manage.php">
                    <i class="fas fa-cogs me-1"></i>
                    Panel Admin
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Patient Information Header -->
        <div class="section-container" id="patientInfoSection" style="display: none;">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        <h6 class="mb-0" id="patientHeader">
                            <i class="fas fa-user-circle me-2"></i>
                            <span id="patientName">Nama Pasien</span>
                        </h6>
                    </div>
                </div>
            </div>
            
            <!-- Patient Details Grid -->
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="row patient-info-grid">
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Jaminan / Cara Bayar:</label>
                                    <select class="form-select" id="jaminanSelect">
                                        <option value="JKN">JKN</option>
                                        <option value="BPJS">BPJS</option>
                                        <option value="UMUM">UMUM</option>
                                        <option value="ASURANSI">ASURANSI</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Jenis Rawat:</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="jenisRawat" id="rawatJalan" value="2" checked>
                                            <label class="form-check-label" for="rawatJalan">Jalan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="jenisRawat" id="rawatInap" value="1">
                                            <label class="form-check-label" for="rawatInap">Inap</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="kelasEksekutif">
                                            <label class="form-check-label" for="kelasEksekutif">? Kelas Eksekutif</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Tanggal & Jam Masuk:</label>
                                    <input type="datetime-local" class="form-control" id="tanggalMasuk" placeholder="Pilih tanggal dan jam masuk">
                                </div>

                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">LOS:</label>
                                    <div class="form-control-plaintext" id="losDisplay">- hari</div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">DPJP:</label>
                                    <input type="text" class="form-control" id="dpjpInput" value="BAMBANG, DR">
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Cara Masuk:</label>
                                    <select class="form-select" id="caraMasukSelect">
                                        <?php
                                        require_once 'config/cara_masuk_mapping.php';
                                        $options = getCaraMasukOptions();
                                        foreach ($options as $option) {
                                            echo '<option value="' . htmlspecialchars($option['value']) . '">' . htmlspecialchars($option['label']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Kode Tarif:</label>
                                    <select class="form-select" id="kodeTarifSelect">
                                        <?php
                                        require_once 'config/kode_tarif_mapping.php';
                                        $options = getKodeTarifOptions();
                                        foreach ($options as $option) {
                                            echo '<option value="' . htmlspecialchars($option['value']) . '">' . htmlspecialchars($option['text']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pasienTB">
                                        <label class="form-check-label" for="pasienTB">Pasien TB</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">No. Peserta:</label>
                                    <input type="text" class="form-control" id="noPeserta" value="0000097208276" readonly>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">No. SEP:</label>
                                    <input type="text" class="form-control" id="noSEP" value="UJICOBA6" readonly>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Tanggal & Jam Pulang:</label>
                                    <input type="datetime-local" class="form-control" id="tanggalPulang" placeholder="Pilih tanggal dan jam pulang">
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Chronic:</label>
                                    <div class="form-control-plaintext" id="chronicDisplay">-</div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">ADL Sub Acute Score:</label>
                                    <input type="number" class="form-control" id="adlSubAcute" placeholder="Masukkan ADL Sub Acute (12-60)" min="12" max="60" onchange="validateADLScore(this)">
                                    <small class="text-muted">Nilai harus antara 12-60</small>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">ADL Chronic Score:</label>
                                    <input type="number" class="form-control" id="adlChronic" placeholder="Masukkan ADL Chronic (12-60)" min="12" max="60" onchange="validateADLScore(this)">
                                    <small class="text-muted">Nilai harus antara 12-60</small>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Sub Acute:</label>
                                    <input type="text" class="form-control" id="subAcute" placeholder="Masukkan Sub Acute">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">COB:</label>
                                    <select class="form-select" id="cobSelect">
                                        <option value="-">-</option>
                                        <option value="COB">COB</option>
                                        <option value="Non-COB">Non-COB</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Kelas Hak:</label>
                                    <div class="form-control-plaintext" id="kelasHak">-</div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Umur:</label>
                                    <div class="form-control-plaintext" id="umurDisplay">- tahun</div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Berat Lahir (gram):</label>
                                    <input type="text" class="form-control" id="beratLahir" placeholder="">
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Cara Pulang:</label>
                                    <select class="form-select" id="caraPulangSelect">
                                        <option value="1">Atas persetujuan dokter</option>
                                        <option value="2">Dirujuk</option>
                                        <option value="3">Atas permintaan sendiri</option>
                                        <option value="4">Meninggal</option>
                                        <option value="5">Lain-lain</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hospital Cost Section -->
        <div class="section-container cost-section" id="costSection" style="display: none;">
            <h4 class="section-title">
                <i class="fas fa-money-bill-wave me-2"></i>
                Rincian Biaya Rumah Sakit
            </h4>
            
            <div class="row mb-2">
                <div class="col-12">
                    <h6 class="text-primary mb-0">Tarif Rumah Sakit : <span id="totalTarif">Rp 0</span></h6>
                </div>
            </div>
            
            <div class="row">
                <!-- Column 1 -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Prosedur Non Bedah:</label>
                        <input type="text" class="form-control cost-input" id="prosedurNonBedah" value="300.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Tenaga Ahli:</label>
                        <input type="text" class="form-control cost-input" id="tenagaAhli" value="200.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Radiologi:</label>
                        <input type="text" class="form-control cost-input" id="radiologi" value="500.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Rehabilitasi:</label>
                        <input type="text" class="form-control cost-input" id="rehabilitasi" value="100.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Obat:</label>
                        <input type="text" class="form-control cost-input" id="obat" value="100.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Alkes:</label>
                        <input type="text" class="form-control cost-input" id="alkes" value="500.000" placeholder="Masukkan jumlah">
                    </div>
                </div>
                
                <!-- Column 2 -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Prosedur Bedah:</label>
                        <input type="text" class="form-control cost-input" id="prosedurBedah" value="20.000.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Keperawatan:</label>
                        <input type="text" class="form-control cost-input" id="keperawatan" value="80.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Laboratorium:</label>
                        <input type="text" class="form-control cost-input" id="laboratorium" value="600.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Kamar / Akomodasi:</label>
                        <input type="text" class="form-control cost-input" id="kamarAkomodasi" value="6.000.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Obat Kronis:</label>
                        <input type="text" class="form-control cost-input" id="obatKronis" value="1.000.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? BMHP:</label>
                        <input type="text" class="form-control cost-input" id="bmhp" value="400.000" placeholder="Masukkan jumlah">
                    </div>
                </div>
                
                <!-- Column 3 -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Konsultasi:</label>
                        <input type="text" class="form-control cost-input" id="konsultasi" value="300.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Penunjang:</label>
                        <input type="text" class="form-control cost-input" id="penunjang" value="1.000.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Pelayanan Darah:</label>
                        <input type="text" class="form-control cost-input" id="pelayananDarah" value="150.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Rawat Intensif:</label>
                        <input type="text" class="form-control cost-input" id="rawatIntensif" value="2.500.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Obat Kemoterapi:</label>
                        <input type="text" class="form-control cost-input" id="obatKemoterapi" value="5.000.000" placeholder="Masukkan jumlah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">? Sewa Alat:</label>
                        <input type="text" class="form-control cost-input" id="sewaAlat" value="210.000" placeholder="Masukkan jumlah">
                    </div>
                </div>
            </div>
        </div>



                                   <!-- Data Klinis Section -->
          <div class="row">
              <div class="col-md-12">
                  <div class="section-container">
                      <h4 class="section-title text-center">
                          <i class="fas fa-heartbeat me-2"></i>
                          Data Klinis
                      </h4>
                      
                      <div class="row justify-content-center">
                          <div class="col-md-6">
                              <label class="form-label fw-bold text-center d-block">Tekanan Darah (mmHg):</label>
                              <div class="row">
                                  <div class="col-6">
                                      <input type="text" class="form-control" id="sistole" value="110" placeholder="Sistole">
                                      <small class="text-muted">Sistole</small>
                                  </div>
                                  <div class="col-6">
                                      <input type="text" class="form-control" id="diastole" value="60" placeholder="Diastole">
                                      <small class="text-muted">Diastole</small>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
 
        <!-- Diagnosa dan Prosedur Section -->
        <div class="row">
            <!-- Diagnosa Section - Kiri -->
            <div class="col-md-6">
                <div class="section-container">
                     <h4 class="section-title">
                        <i class="fas fa-stethoscope me-2"></i>
                        Diagnosa (ICD-10-IM)
                        <span id="diagnosisValidationStatus" class="validation-status ms-2" style="display: none;">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <small class="text-warning">Minimal 1 record</small>
                        </span>
                     </h4>
                    
                    <div class="search-row">
                        <label class="search-label">Diagnosa:</label>
                        <div class="search-input">
                            <select class="form-control" id="diagnosisSelect" style="width: 100%;">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="diagnosisTable">
                            <thead>
                                <tr>
                                    <th width="20%">Jenis</th>
                                    <th width="25%">Kode</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="diagnosisTableBody">
                                <tr>
                                    <td colspan="4" class="empty-table">
                                        <i class="fas fa-clipboard-list"></i>
                                        <p>Belum ada diagnosa yang dipilih</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Prosedur Section - Kanan -->
            <div class="col-md-6">
                <div class="section-container">
                     <h4 class="section-title">
                        <i class="fas fa-procedures me-2"></i>
                        Prosedur (ICD-9CM-IM)
                        <span id="procedureValidationStatus" class="validation-status ms-2" style="display: none;">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <small class="text-warning">Minimal 1 record</small>
                        </span>
                     </h4>
                    
                    <div class="search-row">
                        <label class="search-label">Prosedur:</label>
                        <div class="search-input">
                            <select class="form-control" id="procedureSelect" style="width: 100%;">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="procedureTable">
                            <thead>
                                <tr>
                                    <th width="20%">Jenis</th>
                                    <th width="20%">Kode</th>
                                    <th width="40%">Deskripsi</th>
                                    <th width="10%">Qty</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="procedureTableBody">
                                <tr>
                                    <td colspan="5" class="empty-table">
                                        <i class="fas fa-tools"></i>
                                        <p>Belum ada prosedur yang dipilih</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
 
         <!-- Action Bar -->
         <div class="section-container mt-3">
             <div class="row">
                 <div class="col-12 d-flex justify-content-between align-items-center">
                     <button class="btn btn-secondary" id="hapusKlaimBtn">
                         <i class="fas fa-trash me-1"></i>
                         Hapus Klaim
                     </button>
                     <div>
                         <button class="btn btn-secondary me-2" id="simpanBtn">
                             <i class="fas fa-save me-1"></i>
                             Simpan
                         </button>
                         <button class="btn btn-primary" id="grouperBtn">
                             <i class="fas fa-cogs me-1"></i>
                             Grouper
                         </button>
                     </div>
                 </div>
             
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Load patient data if patient_id is provided
            const urlParams = new URLSearchParams(window.location.search);
            const patientId = urlParams.get('patient_id');
            
            if (patientId) {
                loadPatientData(patientId);
            } else {
                // Show message if no patient selected
                showNoPatientMessage();
            }
            
            // Initialize diagnosis Select2
            $('#diagnosisSelect').select2({
                theme: 'bootstrap-5',
                placeholder: 'ICD - 10',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: 'api/search.php',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            search: params.term,
                            system: 'ICD_10_2010_IM',
                            limit: 20,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        if (!data.success) {
                            return { results: [] };
                        }
                        
                        var results = data.data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.code + ' - ' + item.description,
                                code: item.code,
                                code2: item.code2,
                                description: item.description,
                                system: item.system,
                                validcode: item.validcode,
                                accpdx: item.accpdx,
                                asterisk: item.asterisk,
                                im: item.im
                            };
                        });
                        
                        return {
                            results: results,
                            pagination: {
                                more: data.count >= 20
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatResult,
                templateSelection: formatSelection,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            // Initialize procedure Select2
            $('#procedureSelect').select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari Prosedur',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: 'api/search.php',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            search: params.term,
                            system: 'ICD_9CM_2010_IM',
                            limit: 20,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        if (!data.success) {
                            return { results: [] };
                        }
                        
                        var results = data.data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.code + ' - ' + item.description,
                                code: item.code,
                                code2: item.code2,
                                description: item.description,
                                system: item.system,
                                validcode: item.validcode,
                                accpdx: item.accpdx,
                                asterisk: item.asterisk,
                                im: item.im
                            };
                        });
                        
                        return {
                            results: results,
                            pagination: {
                                more: data.count >= 20
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatResult,
                templateSelection: formatSelection,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            // Handle diagnosis selection
            $('#diagnosisSelect').on('select2:select', function (e) {
                var data = e.params.data;
                
                // Check if validcode is 0
                if (data.validcode === 0) {
                    e.preventDefault();
                    showInvalidCodeNotification(data, 'diagnosa', 'validcode');
                    $(this).val(null).trigger('change');
                    return false;
                }
                
                // Check if accpdx is N (not allowed for primary diagnosis)
                if (data.accpdx === 'N') {
                    e.preventDefault();
                    showInvalidCodeNotification(data, 'diagnosa', 'accpdx');
                    $(this).val(null).trigger('change');
                    return false;
                }
                
                // Check if asterisk is 1 (not allowed for primary diagnosis)
                if (data.asterisk === 1) {
                    e.preventDefault();
                    showInvalidCodeNotification(data, 'diagnosa', 'asterisk');
                    $(this).val(null).trigger('change');
                    return false;
                }
                
                addDiagnosisToTable(data);
                $(this).val(null).trigger('change');
            });

            // Handle procedure selection
            $('#procedureSelect').on('select2:select', function (e) {
                var data = e.params.data;
                
                // Check if validcode is 0
                if (data.validcode === 0) {
                    e.preventDefault();
                    showInvalidCodeNotification(data, 'prosedur');
                    $(this).val(null).trigger('change');
                    return false;
                }
                
                addProcedureToTable(data);
                $(this).val(null).trigger('change');
            });
            
                         // Handle action buttons
             $('#hapusKlaimBtn').on('click', function() {
                 if (confirm('Apakah Anda yakin ingin menghapus klaim ini?')) {
                     showSuccessMessage('Klaim berhasil dihapus!');
                 }
             });
             
             $('#simpanBtn').on('click', function() {
                 saveCoding();
             });
             
             $('#grouperBtn').on('click', function() {
                 processIDRG();
            });
        });

        function formatResult(item) {
            if (item.loading) {
                return item.text;
            }
            
            if (!item.code) {
                return item.text;
            }
            
                         // Add invalid badge if validcode is 0
             var invalidBadge = '';
             if (item.validcode === 0) {
                 invalidBadge = '<span class="badge bg-danger me-2">Tidak Valid</span>';
             }
             
             // Add ACCPDX badge
             var accpdxBadge = '';
             if (item.accpdx === 'Y') {
                 accpdxBadge = '<span class="badge bg-success me-2">ACCPDX</span>';
             } else if (item.accpdx === 'N') {
                 accpdxBadge = '<span class="badge bg-warning me-2">Bukan PDX</span>';
             }
             
             // Add Asterisk badge
             var asteriskBadge = '';
             if (item.asterisk === 1) {
                 asteriskBadge = '<span class="badge bg-info me-2">Asterisk (*)</span>';
             }
             
             // Add IM badge
             var imBadge = '';
             if (item.im === 1) {
                 imBadge = '<span class="badge bg-secondary me-2">IM</span>';
             }
            
            var markup = '<div class="select2-result-item">' +
                '<div class="d-flex justify-content-between align-items-start">' +
                    '<div>' +
                        '<div class="fw-bold text-primary">' + item.code + '</div>' +
                        '<div class="text-muted small">' + item.description + '</div>' +
                    '</div>' +
                                         '<div>' + invalidBadge + accpdxBadge + asteriskBadge + imBadge + '</div>' +
                '</div>' +
            '</div>';
            
            return markup;
        }

        function formatSelection(item) {
            if (!item.code) {
                return item.text || 'Pilih kode...';
            }
            return item.code + ' - ' + item.description;
        }

        function addDiagnosisToTable(data) {
            var tbody = $('#diagnosisTableBody');
            
            // Remove empty message if exists
            tbody.find('.empty-table').closest('tr').remove();
            
            var rowCount = tbody.find('tr').length + 1;
            var jenis = rowCount === 1 ? 'Primary' : 'Secondary';
            
            var newRow = `
                <tr data-id="${data.id}">
                    <td><span class="badge ${rowCount === 1 ? 'bg-primary' : 'bg-secondary'}">${jenis}</span></td>
                    <td><span class="code-badge">${data.code}</span></td>
                    <td>${data.description}</td>
                    <td>
                         ${rowCount > 1 ? `<button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimary(${data.id})" title="Jadikan Primary">
                             <i class="fas fa-arrow-up"></i>
                         </button>` : ''}
                        <button class="btn-remove" onclick="removeDiagnosis(${data.id})">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            `;
            
            tbody.append(newRow);
        }

        function addProcedureToTable(data) {
            var tbody = $('#procedureTableBody');
            
            // Remove empty message if exists
            tbody.find('.empty-table').closest('tr').remove();
            
            var rowCount = tbody.find('tr').length + 1;
            var jenis = rowCount === 1 ? 'Primary' : 'Secondary';
            
            var newRow = `
                <tr data-id="${data.id}">
                    <td><span class="badge ${rowCount === 1 ? 'bg-primary' : 'bg-secondary'}">${jenis}</span></td>
                    <td><span class="code-badge">${data.code}</span></td>
                    <td>${data.description}</td>
                    <td>
                        <input type="number" class="quantity-input" value="1" min="1" max="99">
                    </td>
                    <td>
                         ${rowCount > 1 ? `<button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimaryProcedure(${data.id})" title="Jadikan Primary">
                             <i class="fas fa-arrow-up"></i>
                         </button>` : ''}
                        <button class="btn-remove" onclick="removeProcedure(${data.id})">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            `;
            
            tbody.append(newRow);
        }

        function removeDiagnosis(id) {
            $('tr[data-id="' + id + '"]').remove();
            renumberDiagnosisTable();
        }
         
         function makePrimary(id) {
             var tbody = $('#diagnosisTableBody');
             var targetRow = $('tr[data-id="' + id + '"]');
             
             if (targetRow.length > 0) {
                 // Move the target row to the top
                 tbody.prepend(targetRow);
                 
                 // Renumber all rows
                 renumberDiagnosisTable();
                 
                 // Show success message
                 showSuccessMessage('Diagnosa berhasil dijadikan primary!');
             }
         }
         
         function makePrimaryProcedure(id) {
             var tbody = $('#procedureTableBody');
             var targetRow = $('tr[data-id="' + id + '"]');
             
             if (targetRow.length > 0) {
                 // Move the target row to the top
                 tbody.prepend(targetRow);
                 
                 // Renumber all rows
                 renumberProcedureTable();
                 
                 // Show success message
                 showSuccessMessage('Prosedur berhasil dijadikan primary!');
             }
         }

        function removeProcedure(id) {
            $('tr[data-id="' + id + '"]').remove();
            renumberProcedureTable();
        }

        function renumberDiagnosisTable() {
            var tbody = $('#diagnosisTableBody');
            var rows = tbody.find('tr');
            
            if (rows.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="4" class="empty-table">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Belum ada diagnosa yang dipilih</p>
                        </td>
                    </tr>
                `);
                return;
            }
            
            rows.each(function(index) {
                var row = $(this);
                var rowNumber = index + 1;
                
                // Update jenis (Primary/Secondary)
                var jenisCell = row.find('td:nth-child(1)');
                var jenis = rowNumber === 1 ? 'Primary' : 'Secondary';
                var badgeClass = rowNumber === 1 ? 'bg-primary' : 'bg-secondary';
                
                jenisCell.html(`<span class="badge ${badgeClass}">${jenis}</span>`);
                 
                 // Update action buttons
                 var actionCell = row.find('td:nth-child(4)');
                 var dataId = row.attr('data-id');
                 
                 if (rowNumber === 1) {
                     // Primary diagnosis - only remove button
                     actionCell.html(`
                         <button class="btn-remove" onclick="removeDiagnosis(${dataId})">
                             <i class="fas fa-trash"></i> Hapus
                         </button>
                     `);
                 } else {
                     // Secondary diagnosis - add make primary button
                     actionCell.html(`
                         <button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimary(${dataId})" title="Jadikan Primary">
                             <i class="fas fa-arrow-up"></i>
                         </button>
                         <button class="btn-remove" onclick="removeDiagnosis(${dataId})">
                             <i class="fas fa-trash"></i> Hapus
                         </button>
                     `);
                 }
            });
        }

        function renumberProcedureTable() {
            var tbody = $('#procedureTableBody');
            var rows = tbody.find('tr');
            
            if (rows.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="5" class="empty-table">
                            <i class="fas fa-tools"></i>
                            <p>Belum ada prosedur yang dipilih</p>
                        </td>
                    </tr>
                `);
                return;
            }
            
            rows.each(function(index) {
                var row = $(this);
                var rowNumber = index + 1;
                
                // Update jenis (Primary/Secondary)
                var jenisCell = row.find('td:nth-child(1)');
                var jenis = rowNumber === 1 ? 'Primary' : 'Secondary';
                var badgeClass = rowNumber === 1 ? 'bg-primary' : 'bg-secondary';
                
                jenisCell.html(`<span class="badge ${badgeClass}">${jenis}</span>`);
                 
                 // Update action buttons
                 var actionCell = row.find('td:nth-child(5)');
                 var dataId = row.attr('data-id');
                 
                 if (rowNumber === 1) {
                     // Primary procedure - only remove button
                     actionCell.html(`
                         <button class="btn-remove" onclick="removeProcedure(${dataId})">
                             <i class="fas fa-trash"></i> Hapus
                         </button>
                     `);
                 } else {
                     // Secondary procedure - add make primary button
                     actionCell.html(`
                         <button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimaryProcedure(${dataId})" title="Jadikan Primary">
                             <i class="fas fa-arrow-up"></i>
                         </button>
                         <button class="btn-remove" onclick="removeProcedure(${dataId})">
                             <i class="fas fa-trash"></i> Hapus
                         </button>
                     `);
                 }
             });
         }
        
                 function loadPatientData(patientId) {
            // Show loading state
            $('#patientInfoSection').show();
            $('#costSection').show();
            $('#patientName').text('Memuat data pasien...');
            
            // Fetch patient data from API
            $.ajax({
                url: `api/patients.php?id=${patientId}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayPatientInfo(response.data);
                    } else {
                        showError('Gagal memuat data: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    if (xhr.status === 404) {
                        showPatientNotFound();
                    } else {
                        showError('Gagal memuat data dari database.');
                    }
                }
            });
        }
        
                 function displayPatientInfo(patient) {
             // Update patient header
             const gender = patient.gender === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN';
             const birthDate = patient.tgl_lahir ? new Date(patient.tgl_lahir).toLocaleDateString('id-ID') : 'Tidak Diketahui';
             $('#patientName').text(`${patient.nomor_kartu || 'A002122'} ${patient.nama_pasien} ${gender} ${birthDate}`);
             
             // Update form fields with patient data
             $('#jaminanSelect').val(patient.jaminan_cara_bayar || 'JKN');
             $(`input[name="jenisRawat"][value="${patient.jenis_rawat || '2'}"]`).prop('checked', true);
             $('#dpjpInput').val(patient.nama_dokter || 'BAMBANG, DR');
             $('#caraMasukSelect').val(patient.cara_masuk || 'gp');
             $('#kodeTarifSelect').val(patient.kode_tarif || 'CP');
             $('#noPeserta').val(patient.nomor_kartu || '0000097208276');
             $('#noSEP').val(patient.nomor_sep || 'UJICOBA6');
             
             // Update dates
             const tglMasuk = patient.tgl_masuk ? new Date(patient.tgl_masuk) : new Date();
             const tglPulang = patient.tgl_pulang ? new Date(patient.tgl_pulang) : new Date();
             
             // Format datetime-local input (YYYY-MM-DDTHH:MM)
             function formatDateTimeLocal(date) {
                 const year = date.getFullYear();
                 const month = String(date.getMonth() + 1).padStart(2, '0');
                 const day = String(date.getDate()).padStart(2, '0');
                 const hours = String(date.getHours()).padStart(2, '0');
                 const minutes = String(date.getMinutes()).padStart(2, '0');
                 return `${year}-${month}-${day}T${hours}:${minutes}`;
             }
             
             // Set datetime-local values
             $('#tanggalMasuk').val(formatDateTimeLocal(tglMasuk));
             $('#tanggalPulang').val(formatDateTimeLocal(tglPulang));
             
             // Update display text for reference
             $('#tanggalRawat').text(`Masuk : ${tglMasuk.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'})} ${tglMasuk.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}`);
             
             // Add hidden field for display text
             if (!$('#tanggalRawat').length) {
                 $('<div class="form-control-plaintext" id="tanggalRawat" style="display: none;"></div>').insertAfter('#tanggalMasuk');
             }
             
             // Update LOS
             $('#losDisplay').text(`${patient.los_hari || 1} hari`);
             
             // Add event listeners for date changes to calculate LOS automatically
             $('#tanggalMasuk, #tanggalPulang').on('change', function() {
                 calculateLOS();
             });
             
             // Function to calculate LOS
             function calculateLOS() {
                 const tglMasuk = $('#tanggalMasuk').val();
                 const tglPulang = $('#tanggalPulang').val();
                 
                 if (tglMasuk && tglPulang) {
                     const masuk = new Date(tglMasuk);
                     const pulang = new Date(tglPulang);
                     
                     if (pulang > masuk) {
                         const diffTime = Math.abs(pulang - masuk);
                         const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                         $('#losDisplay').text(`${diffDays} hari`);
                     } else {
                         $('#losDisplay').text('0 hari');
                     }
                 }
             }
             
             // Update ADL Score and Sub Acute
             $('#adlSubAcute').val(patient.adl_sub_acute || '');
             $('#adlChronic').val(patient.adl_chronic || '');
             $('#subAcute').val(patient.adl_sub_acute || '');
             
             // Update age
             const age = calculateAge(patient.tgl_lahir);
             $('#umurDisplay').text(`${age} tahun`);
             
             // Update discharge status
             $('#caraPulangSelect').val(patient.discharge_status || '1');
             
             // Update class
             const kelasText = getKelasText(patient.kelas_rawat);
             $('#kelasHak').text(kelasText);
             
             // Update chronic
             $('#chronicDisplay').text('-');
             
             // Update clinical data if available
             if (patient.sistole) {
                 $('#sistole').val(patient.sistole);
             }
             if (patient.diastole) {
                 $('#diastole').val(patient.diastole);
             }
             
             // Update rincian biaya dari database
             if (patient.detail_tarif) {
                 const tarif = patient.detail_tarif;
                 
                 // Format angka dengan pemisah ribuan (format Indonesia)
                 function formatCurrency(amount) {
                     return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                 }
                 
                 // Update field-field rincian biaya
                 $('#prosedurNonBedah').val(formatCurrency(tarif.prosedur_non_bedah || 0));
                 $('#prosedurBedah').val(formatCurrency(tarif.prosedur_bedah || 0));
                 $('#konsultasi').val(formatCurrency(tarif.konsultasi || 0));
                 $('#tenagaAhli').val(formatCurrency(tarif.tenaga_ahli || 0));
                 $('#keperawatan').val(formatCurrency(tarif.keperawatan || 0));
                 $('#penunjang').val(formatCurrency(tarif.penunjang || 0));
                 $('#radiologi').val(formatCurrency(tarif.radiologi || 0));
                 $('#laboratorium').val(formatCurrency(tarif.laboratorium || 0));
                 $('#pelayananDarah').val(formatCurrency(tarif.pelayanan_darah || 0));
                 $('#rehabilitasi').val(formatCurrency(tarif.rehabilitasi || 0));
                 $('#kamarAkomodasi').val(formatCurrency(tarif.kamar || 0));
                 $('#rawatIntensif').val(formatCurrency(tarif.rawat_intensif || 0));
                 $('#obat').val(formatCurrency(tarif.obat || 0));
                 $('#obatKronis').val(formatCurrency(tarif.obat_kronis || 0));
                 $('#obatKemoterapi').val(formatCurrency(tarif.obat_kemoterapi || 0));
                 $('#alkes').val(formatCurrency(tarif.alkes || 0));
                 $('#bmhp').val(formatCurrency(tarif.bmhp || 0));
                 $('#sewaAlat').val(formatCurrency(tarif.sewa_alat || 0));
                 
                 // Update total tarif
                 if (tarif.total_tarif > 0) {
                     $('#totalTarif').text(`Rp ${formatCurrency(tarif.total_tarif)}`);
                 } else {
                     // Jika total_tarif kosong, hitung dari komponen
                     calculateTotalCost();
                 }
             } else {
                 // Jika tidak ada data detail_tarif, gunakan nilai default
                 calculateTotalCost();
             }
             
             // Load saved diagnosis and procedure data
             loadSavedDiagnosisData(patient.id);
             loadSavedProcedureData(patient.id);
         }
        
        function calculateAge(birthDate) {
            if (!birthDate) return 24; // Default age
            const birth = new Date(birthDate);
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            return age;
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
        
        function getDischargeStatusCode(text) {
            const codeMap = {
                'Atas persetujuan dokter': '1',
                'Dirujuk': '2',
                'Atas permintaan sendiri': '3',
                'Meninggal': '4',
                'Lain-lain': '5'
            };
            return codeMap[text] || '1';
        }
        
        function getKelasText(kelas) {
            const kelasMap = {
                '1': 'Kelas 1',
                '2': 'Kelas 2',
                '3': 'Kelas 3'
            };
            return kelasMap[kelas] || 'Kelas 3';
        }
        
        // Function to convert currency format to number
        function parseCurrencyValue(value) {
            if (!value || value === '') return 0;
            // Remove all non-digit characters
            const cleanValue = value.toString().replace(/[^\d]/g, '');
            return parseInt(cleanValue) || 0;
        }
        
        function calculateTotalCost() {
            const costs = [
                parseCurrencyValue($('#prosedurNonBedah').val()),
                parseCurrencyValue($('#tenagaAhli').val()),
                parseCurrencyValue($('#radiologi').val()),
                parseCurrencyValue($('#rehabilitasi').val()),
                parseCurrencyValue($('#obat').val()),
                parseCurrencyValue($('#alkes').val()),
                parseCurrencyValue($('#prosedurBedah').val()),
                parseCurrencyValue($('#keperawatan').val()),
                parseCurrencyValue($('#laboratorium').val()),
                parseCurrencyValue($('#kamarAkomodasi').val()),
                parseCurrencyValue($('#obatKronis').val()),
                parseCurrencyValue($('#bmhp').val()),
                parseCurrencyValue($('#konsultasi').val()),
                parseCurrencyValue($('#penunjang').val()),
                parseCurrencyValue($('#pelayananDarah').val()),
                parseCurrencyValue($('#rawatIntensif').val()),
                parseCurrencyValue($('#obatKemoterapi').val()),
                parseCurrencyValue($('#sewaAlat').val())
            ];
            
            const total = costs.reduce((sum, cost) => sum + cost, 0);
            // Format total dengan titik sebagai separator ribuan (format Indonesia)
            $('#totalTarif').text(`Rp ${total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`);
        }

        // Format input biaya dengan separator ribuan (format Indonesia)
        function formatCurrencyInput(input) {
            let value = input.value.replace(/[^\d]/g, '');
            if (value === '') {
                input.value = '';
                return;
            }
            const numValue = parseInt(value);
            // Format dengan titik sebagai separator ribuan (format Indonesia)
            input.value = numValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Event listener untuk input biaya
        $(document).on('input', '.cost-input', function() {
            formatCurrencyInput(this);
            calculateTotalCost();
        });

        // Event listener untuk blur (ketika input kehilangan fokus)
        $(document).on('blur', '.cost-input', function() {
            if (this.value === '') {
                this.value = '0';
                calculateTotalCost();
            }
        });
        
        function showNoPatientMessage() {
            const message = `
                <div class="section-container">
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Tidak Ada Pasien yang Dipilih</h4>
                        <p class="text-muted">Silakan pilih pasien dari halaman utama untuk memulai coding IDRG.</p>
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            `;
            $('.container').prepend(message);
        }
        
        function showPatientNotFound() {
            const message = `
                <div class="section-container">
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4 class="text-warning">Data Pasien Tidak Ditemukan</h4>
                        <p class="text-muted">Pasien dengan ID yang diberikan tidak ditemukan dalam database.</p>
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            `;
            $('.container').prepend(message);
        }
        
        function showError(message) {
            const errorMessage = `
                <div class="section-container">
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h4 class="text-danger">Terjadi Kesalahan</h4>
                        <p class="text-muted">${message}</p>
                        <button class="btn btn-primary" onclick="location.reload()">
                            <i class="fas fa-redo me-2"></i>
                            Coba Lagi
                        </button>
                    </div>
                </div>
            `;
            $('.container').prepend(errorMessage);
        }
        
                         function saveCoding() {
            const patientId = new URLSearchParams(window.location.search).get('patient_id');
            
            if (!patientId) {
                showError('ID Pasien tidak ditemukan');
                return;
            }
            
            // Validate diagnosis and procedures - must have at least 1 record each
            const diagnosisCount = $('#diagnosisTableBody tr:not(.empty-table)').length;
            const procedureCount = $('#procedureTableBody tr:not(.empty-table)').length;
            
            if (diagnosisCount < 1) {
                showError('Diagnosa (ICD-10-IM) harus berisi minimal 1 record');
                return;
            }
            
            if (procedureCount < 1) {
                showError('Prosedur (ICD-9CM-IM) harus berisi minimal 1 record');
                return;
            }
            
            // Show loading message
            showProcessingMessage('Sedang menyimpan data...');
            
            // Collect diagnosis data
            const diagnosisData = [];
            $('#diagnosisTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const dataId = row.attr('data-id');
                    const order = row.index() + 1;
                    const type = order === 1 ? 'primary' : 'secondary';
                    const code = row.find('td:nth-child(2) .code-badge').text();
                    const description = row.find('td:nth-child(3)').text();
                    
                    diagnosisData.push({
                        icd_code_id: dataId,
                        diagnosis_order: order,
                        diagnosis_type: type,
                        icd_code: code,
                        icd_description: description,
                        validcode: 1,
                        accpdx: 'Y',
                        asterisk: 0,
                        im: 0
                    });
                }
            });
            
            // Collect procedure data
            const procedureData = [];
            $('#procedureTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const dataId = row.attr('data-id');
                    const order = row.index() + 1;
                    const type = order === 1 ? 'primary' : 'secondary';
                    const code = row.find('td:nth-child(2) .code-badge').text();
                    const description = row.find('td:nth-child(3)').text();
                    const quantity = row.find('td:nth-child(4) input').val() || 1;
                    
                    procedureData.push({
                        icd_code_id: dataId,
                        procedure_order: order,
                        procedure_type: type,
                        icd_code: code,
                        icd_description: description,
                        quantity: parseInt(quantity),
                        validcode: 1,
                        accpdx: 'Y',
                        asterisk: 0,
                        im: 0
                    });
                }
            });
            
            // Collect clinical data
            const clinicalData = {
                sistole: parseInt($('#sistole').val()) || null,
                diastole: parseInt($('#diastole').val()) || null,
                heart_rate: null,
                temperature: null,
                oxygen_saturation: null,
                respiratory_rate: null,
                blood_glucose: null,
                notes: null
            };
            
            // Collect detail tarif data
            const detailTarif = {
                prosedur_non_bedah: parseCurrencyValue($('#prosedurNonBedah').val()),
                prosedur_bedah: parseCurrencyValue($('#prosedurBedah').val()),
                konsultasi: parseCurrencyValue($('#konsultasi').val()),
                tenaga_ahli: parseCurrencyValue($('#tenagaAhli').val()),
                keperawatan: parseCurrencyValue($('#keperawatan').val()),
                penunjang: parseCurrencyValue($('#penunjang').val()),
                radiologi: parseCurrencyValue($('#radiologi').val()),
                laboratorium: parseCurrencyValue($('#laboratorium').val()),
                pelayanan_darah: parseCurrencyValue($('#pelayananDarah').val()),
                rehabilitasi: parseCurrencyValue($('#rehabilitasi').val()),
                kamar: parseCurrencyValue($('#kamarAkomodasi').val()),
                rawat_intensif: parseCurrencyValue($('#rawatIntensif').val()),
                obat: parseCurrencyValue($('#obat').val()),
                obat_kronis: parseCurrencyValue($('#obatKronis').val()),
                obat_kemoterapi: parseCurrencyValue($('#obatKemoterapi').val()),
                alkes: parseCurrencyValue($('#alkes').val()),
                bmhp: parseCurrencyValue($('#bmhp').val()),
                sewa_alat: parseCurrencyValue($('#sewaAlat').val()),
                total_tarif: parseCurrencyValue($('#totalTarif').text()),
                kategori_tarif: $('#kodeTarifSelect option:selected').text().split(' - ')[1] || 'TARIF RS KELAS C PEMERINTAH',
                nama_layanan: 'Layanan Medis'
            };
            
            // Save all data using single API endpoint
            $.ajax({
                url: 'api/save_all_coding_data.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    patient_id: parseInt(patientId),
                    jaminan_cara_bayar: $('#jaminanSelect').val(),
                    jenis_rawat: $('input[name="jenisRawat"]:checked').val(),
                    nama_dokter: $('#dpjpInput').val(),
                    nomor_kartu: $('#noPeserta').val(),
                    nomor_sep: $('#noSEP').val(),
                    tgl_masuk: $('#tanggalMasuk').val(),
                    tgl_pulang: $('#tanggalPulang').val(),
                    cara_masuk: $('#caraMasukSelect').val(),
                    kode_tarif: $('#kodeTarifSelect').val(),
                    adl_sub_acute: parseInt($('#adlSubAcute').val()) || 0,
                    adl_chronic: parseInt($('#adlChronic').val()) || 0,
                    discharge_status: String($('#caraPulangSelect').val()),
                    kelas_rawat: $('input[name="jenisRawat"]:checked').val() === '1' ? '1' : '3',
                    diagnosis: diagnosisData.map(d => ({
                        type: d.diagnosis_type,
                        code: d.icd_code,
                        description: d.icd_description
                    })),
                    procedures: procedureData.map(p => ({
                        code: p.icd_code,
                        description: p.icd_description,
                        date: new Date().toISOString().split('T')[0]
                    })),
                    detail_tarif: detailTarif
                })
            })
            .then(function(response) {
                if (response.success) {
                    showSuccessMessage('Coding berhasil disimpan ke database!');
                    console.log('Saved data:', response.data);
                    
                    // Setelah berhasil simpan ke database, kirim ke E-Klaim
                    sendToEklaim();
                } else {
                    throw new Error(response.error || 'Gagal menyimpan data');
                }
            })
            .catch(function(error) {
                console.error('Error saving data:', error);
                showError('Gagal menyimpan data: ' + (error.responseJSON?.error || error.message));
            });
        }
        
        function sendToEklaim() {
            const nomorSep = $('#noSEP').val();
            
            if (!nomorSep) {
                showError('Nomor SEP tidak ditemukan');
                return;
            }
            
            showProcessingMessage('Sedang mengirim data ke E-Klaim...');
            
            // Collect data untuk E-Klaim setClaimData
            const claimData = {
                nomor_kartu: $('#noPeserta').val(),
                tgl_masuk: formatDateTimeForEklaim($('#tanggalMasuk').val()),
                tgl_pulang: formatDateTimeForEklaim($('#tanggalPulang').val()),
                cara_masuk: $('#caraMasukSelect').val(),
                jenis_rawat: $('input[name="jenisRawat"]:checked').val(),
                kelas_rawat: $('input[name="jenisRawat"]:checked').val() === '1' ? '1' : '3',
                adl_sub_acute: parseInt($('#adlSubAcute').val()) || 0,
                adl_chronic: parseInt($('#adlChronic').val()) || 0,
                icu_indikator: '0',
                icu_los: '0',
                upgrade_class_ind: '0',
                add_payment_pct: '0',
                birth_weight: $('#beratLahir').val() || '0',
                sistole: parseInt($('#sistole').val()) || 0,
                diastole: parseInt($('#diastole').val()) || 0,
                discharge_status: $('#caraPulangSelect').val(),
                tarif_rs: {
                    prosedur_non_bedah: parseCurrencyValue($('#prosedurNonBedah').val()).toString(),
                    prosedur_bedah: parseCurrencyValue($('#prosedurBedah').val()).toString(),
                    konsultasi: parseCurrencyValue($('#konsultasi').val()).toString(),
                    tenaga_ahli: parseCurrencyValue($('#tenagaAhli').val()).toString(),
                    keperawatan: parseCurrencyValue($('#keperawatan').val()).toString(),
                    penunjang: parseCurrencyValue($('#penunjang').val()).toString(),
                    radiologi: parseCurrencyValue($('#radiologi').val()).toString(),
                    laboratorium: parseCurrencyValue($('#laboratorium').val()).toString(),
                    pelayanan_darah: parseCurrencyValue($('#pelayananDarah').val()).toString(),
                    rehabilitasi: parseCurrencyValue($('#rehabilitasi').val()).toString(),
                    kamar: parseCurrencyValue($('#kamarAkomodasi').val()).toString(),
                    rawat_intensif: parseCurrencyValue($('#rawatIntensif').val()).toString(),
                    obat: parseCurrencyValue($('#obat').val()).toString(),
                    obat_kronis: parseCurrencyValue($('#obatKronis').val()).toString(),
                    obat_kemoterapi: parseCurrencyValue($('#obatKemoterapi').val()).toString(),
                    alkes: parseCurrencyValue($('#alkes').val()).toString(),
                    bmhp: parseCurrencyValue($('#bmhp').val()).toString(),
                    sewa_alat: parseCurrencyValue($('#sewaAlat').val()).toString()
                },
                pemulasaraan_jenazah: '0',
                kantong_jenazah: '0',
                peti_jenazah: '0',
                plastik_erat: '0',
                desinfektan_jenazah: '0',
                mobil_jenazah: '0',
                desinfektan_mobil_jenazah: '0',
                covid19_status_cd: '0',
                nomor_kartu_t: 'nik',
                episodes: '1;1',
                akses_naat: 'C',
                isoman_ind: '0',
                bayi_lahir_status_cd: 0,
                dializer_single_use: '0',
                kantong_darah: 0,
                alteplase_ind: 0,
                tarif_poli_eks: '0',
                nama_dokter: $('#dpjpInput').val(),
                kode_tarif: $('#kodeTarifSelect').val(),
                payor_id: '3',
                payor_cd: $('#jaminanSelect').val(),
                cob_cd: $('#cobSelect').val() === 'COB' ? 1 : 0,
                coder_nik: '123123123123' // Default coder NIK
            };
            
            // Kirim data ke E-Klaim menggunakan API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    action: 'setClaimData',
                    nomor_sep: nomorSep,
                    claim_data: claimData
                })
            })
            .then(function(response) {
                if (response.success) {
                    showSuccessMessage('Data berhasil dikirim ke E-Klaim!');
                    console.log('E-Klaim response:', response.data);
                    
                    // Setelah berhasil set claim data, lanjutkan dengan set diagnosa dan prosedur
                    setDiagnosaToEklaim(nomorSep);
                } else {
                    throw new Error(response.error || 'Gagal mengirim data ke E-Klaim');
                }
            })
            .catch(function(error) {
                console.error('Error sending to E-Klaim:', error);
                showError('Gagal mengirim data ke E-Klaim: ' + (error.responseJSON?.error || error.message));
            });
        }
        
        function setDiagnosaToEklaim(nomorSep) {
            // Collect diagnosa dari tabel
            const diagnoses = [];
            $('#diagnosisTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const code = row.find('td:nth-child(2) .code-badge').text();
                    diagnoses.push(code);
                }
            });
            
            if (diagnoses.length === 0) {
                showError('Tidak ada diagnosa yang dipilih');
                return;
            }
            
            const diagnosaString = diagnoses.join('#');
            
            // Kirim diagnosa ke E-Klaim
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    action: 'setIdrgDiagnosa',
                    nomor_sep: nomorSep,
                    diagnosa: diagnosaString
                })
            })
            .then(function(response) {
                if (response.success) {
                    console.log('Diagnosa berhasil diset ke E-Klaim:', response.data);
                    
                    // Lanjutkan dengan set prosedur
                    setProcedureToEklaim(nomorSep);
                } else {
                    throw new Error(response.error || 'Gagal set diagnosa ke E-Klaim');
                }
            })
            .catch(function(error) {
                console.error('Error setting diagnosa to E-Klaim:', error);
                showError('Gagal set diagnosa ke E-Klaim: ' + (error.responseJSON?.error || error.message));
            });
        }
        
        function setProcedureToEklaim(nomorSep) {
            // Collect prosedur dari tabel
            const procedures = [];
            $('#procedureTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const code = row.find('td:nth-child(2) .code-badge').text();
                    const quantity = row.find('td:nth-child(4) input').val() || 1;
                    
                    if (parseInt(quantity) > 1) {
                        procedures.push(code + '+' + quantity + '#' + code);
                    } else {
                        procedures.push(code);
                    }
                }
            });
            
            if (procedures.length === 0) {
                showError('Tidak ada prosedur yang dipilih');
                return;
            }
            
            const procedureString = procedures.join('#');
            
            // Kirim prosedur ke E-Klaim
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    action: 'setIdrgProcedure',
                    nomor_sep: nomorSep,
                    procedure: procedureString
                })
            })
            .then(function(response) {
                if (response.success) {
                    console.log('Prosedur berhasil diset ke E-Klaim:', response.data);
                    showSuccessMessage('Semua data berhasil dikirim ke E-Klaim!');
                } else {
                    throw new Error(response.error || 'Gagal set prosedur ke E-Klaim');
                }
            })
            .catch(function(error) {
                console.error('Error setting procedure to E-Klaim:', error);
                showError('Gagal set prosedur ke E-Klaim: ' + (error.responseJSON?.error || error.message));
            });
        }
        
        function formatDateTimeForEklaim(dateTimeString) {
            if (!dateTimeString) return '';
            
            // Convert datetime-local format (YYYY-MM-DDTHH:MM) to E-Klaim format (YYYY-MM-DD HH:MM:SS)
            const date = new Date(dateTimeString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }
        
        function processIDRG() {
            // Show processing message
            showProcessingMessage('Sedang memproses IDRG...');
            
            // Simulate processing
            setTimeout(function() {
                showSuccessMessage('IDRG berhasil diproses!');
            }, 2000);
        }
        
        function previewCoding() {
            // Show preview modal
            const previewContent = `
                <div class="modal fade" id="previewModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Preview Coding IDRG</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Diagnosa:</h6>
                                        <ul id="previewDiagnosis"></ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Prosedur:</h6>
                                        <ul id="previewProcedures"></ul>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Total Biaya: <span id="previewTotalCost"></span></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(previewContent);
            
            // Populate preview data
            populatePreviewData();
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
            
            // Remove modal after closing
            $('#previewModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
        
        function getDiagnosisData() {
            const diagnoses = [];
            $('#diagnosisTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    diagnoses.push({
                        jenis: row.find('td:first').text(),
                        kode: row.find('td:nth-child(2) .code-badge').text(),
                        deskripsi: row.find('td:nth-child(3)').text()
                    });
                }
            });
            return diagnoses;
        }
        
        function getProcedureData() {
            const procedures = [];
            $('#procedureTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    procedures.push({
                        jenis: row.find('td:first').text(),
                        kode: row.find('td:nth-child(2) .code-badge').text(),
                        deskripsi: row.find('td:nth-child(3)').text(),
                        qty: row.find('td:nth-child(4) input').val()
                    });
                }
            });
            return procedures;
        }
        
        function getCostData() {
            return {
                prosedurNonBedah: $('#prosedurNonBedah').val(),
                tenagaAhli: $('#tenagaAhli').val(),
                radiologi: $('#radiologi').val(),
                rehabilitasi: $('#rehabilitasi').val(),
                obat: $('#obat').val(),
                alkes: $('#alkes').val(),
                prosedurBedah: $('#prosedurBedah').val(),
                keperawatan: $('#keperawatan').val(),
                laboratorium: $('#laboratorium').val(),
                kamarAkomodasi: $('#kamarAkomodasi').val(),
                obatKronis: $('#obatKronis').val(),
                bmhp: $('#bmhp').val(),
                konsultasi: $('#konsultasi').val(),
                penunjang: $('#penunjang').val(),
                pelayananDarah: $('#pelayananDarah').val(),
                rawatIntensif: $('#rawatIntensif').val(),
                obatKemoterapi: $('#obatKemoterapi').val(),
                sewaAlat: $('#sewaAlat').val(),
                total: $('#totalTarif').text()
            };
        }
        
        function populatePreviewData() {
            // Populate diagnosis
            const diagnosisList = $('#previewDiagnosis');
            diagnosisList.empty();
            getDiagnosisData().forEach(function(diagnosis) {
                diagnosisList.append(`<li><strong>${diagnosis.kode}</strong> - ${diagnosis.deskripsi} (${diagnosis.jenis})</li>`);
            });
            
            // Populate procedures
            const procedureList = $('#previewProcedures');
            procedureList.empty();
            getProcedureData().forEach(function(procedure) {
                procedureList.append(`<li><strong>${procedure.kode}</strong> - ${procedure.deskripsi} (${procedure.jenis}) - Qty: ${procedure.qty}</li>`);
            });
            
            // Populate total cost
            $('#previewTotalCost').text($('#totalTarif').text());
        }
        
        function showSuccessMessage(message) {
            const notification = `
                <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                    <i class="fas fa-check-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(notification);
            
            setTimeout(function() {
                $('.alert-success').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
        
        function showProcessingMessage(message) {
            const notification = `
                <div class="alert alert-info alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    ${message}
                </div>
            `;
            $('body').append(notification);
            
            setTimeout(function() {
                $('.alert-info').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
        
                 function showInvalidCodeNotification(data, type, reason) {
             // Clear any existing notifications
             $('.invalid-code-notification').remove();
             
             var typeText = type === 'diagnosa' ? 'Diagnosa' : 'Prosedur';
             var title, message;
             
             if (reason === 'validcode') {
                 title = `Kode ${typeText} Tidak Valid!`;
                 message = `Kode <strong>${data.code}</strong> tidak dapat dipilih karena status validcode = 0`;
                           } else if (reason === 'accpdx') {
                  title = `Kode ${typeText} Tidak Bisa Jadi Diagnosa Primer!`;
                  message = `Kode <strong>${data.code}</strong> tidak dapat dipilih karena status ACCPDX = N (bukan diagnosa primer)`;
              } else if (reason === 'asterisk') {
                  title = `Kode ${typeText} Asterisk Tidak Bisa Jadi Diagnosa Primer!`;
                  message = `Kode <strong>${data.code}</strong> tidak dapat dipilih karena merupakan kode asterisk (*)`;
              } else {
                 title = `Kode ${typeText} Tidak Valid!`;
                 message = `Kode <strong>${data.code}</strong> tidak dapat dipilih`;
             }
             
             var notification = `
                 <div class="alert alert-danger invalid-code-notification">
                     <div class="d-flex align-items-center">
                         <i class="fas fa-exclamation-triangle me-2"></i>
                         <div>
                             <strong>${title}</strong><br>
                             <small>${message}</small>
                         </div>
                     </div>
                 </div>
             `;
            
            $('body').append(notification);
            
            // Auto remove after 5 seconds
            setTimeout(function() {
                $('.invalid-code-notification').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
         
         function loadSavedDiagnosisData(kunjunganId) {
             $.ajax({
                 url: `api/get_diagnosis.php?kunjungan_id=${kunjunganId}`,
                 method: 'GET',
                 dataType: 'json',
                 success: function(response) {
                     if (response.success && response.data.diagnosis_list.length > 0) {
                         // Clear existing table
                         $('#diagnosisTableBody').empty();
                         
                         // Add saved diagnosis data
                         response.data.diagnosis_list.forEach(function(diagnosis) {
                             const data = {
                                 id: diagnosis.id,
                                 code: diagnosis.icd_code,
                                 description: diagnosis.icd_description,
                                 validcode: diagnosis.validcode,
                                 accpdx: diagnosis.accpdx,
                                 asterisk: diagnosis.asterisk,
                                 im: diagnosis.im
                             };
                             
                             addDiagnosisToTable(data);
                         });
                         
                         console.log('Loaded saved diagnosis data:', response.data);
                     } else {
                         // Clear existing table and add empty message
                         $('#diagnosisTableBody').empty();
                         $('#diagnosisTableBody').append(`
                             <tr>
                                 <td colspan="4" class="empty-table">
                                     <i class="fas fa-clipboard-list"></i>
                                     <p>Belum ada diagnosa yang dipilih</p>
                                 </td>
                             </tr>
                         `);
                     }
                     // Update validation status after loading data
                     updateValidationStatus();
                 },
                 error: function(xhr, status, error) {
                     console.error('Error loading saved diagnosis data:', error);
                     // Update validation status even on error
                     updateValidationStatus();
                 }
             });
         }
         
         function loadSavedProcedureData(kunjunganId) {
             $.ajax({
                 url: `api/get_procedure.php?kunjungan_id=${kunjunganId}`,
                 method: 'GET',
                 dataType: 'json',
                 success: function(response) {
                     if (response.success && response.data.procedure_list.length > 0) {
                         // Clear existing table
                         $('#procedureTableBody').empty();
                         
                         // Add saved procedure data
                         response.data.procedure_list.forEach(function(procedure) {
                             const data = {
                                 id: procedure.id,
                                 code: procedure.icd_code,
                                 description: procedure.icd_description,
                                 validcode: procedure.validcode,
                                 accpdx: procedure.accpdx,
                                 asterisk: procedure.asterisk,
                                 im: procedure.im
                             };
                             
                             addProcedureToTable(data);
                             
                             // Set quantity
                             const row = $(`tr[data-id="${procedure.id}"]`);
                             row.find('td:nth-child(4) input').val(procedure.quantity);
                         });
                         
                                                  console.log('Loaded saved procedure data:', response.data);
                     } else {
                         // Clear existing table and add empty message
                         $('#procedureTableBody').empty();
                         $('#procedureTableBody').append(`
                             <tr>
                                 <td colspan="5" class="empty-table">
                                     <i class="fas fa-tools"></i>
                                     <p>Belum ada prosedur yang dipilih</p>
                                 </td>
                             </tr>
                         `);
                     }
                     // Update validation status after loading data
                     updateValidationStatus();
                 },
                 error: function(xhr, status, error) {
                     console.error('Error loading saved procedure data:', error);
                     // Update validation status even on error
                     updateValidationStatus();
                 }
             });
         }
         
         // Validate ADL Score (12-60)
         function validateADLScore(input) {
             const value = parseInt(input.value);
             const min = 12;
             const max = 60;
             
             if (value < min || value > max) {
                 alert(`ADL Score harus antara ${min}-${max}`);
                 input.value = '';
                 input.focus();
                 return false;
             }
             return true;
         }
         
         // Update validation status for diagnosis and procedures
         function updateValidationStatus() {
             const diagnosisCount = $('#diagnosisTableBody tr:not(:has(.empty-table))').length;
             const procedureCount = $('#procedureTableBody tr:not(:has(.empty-table))').length;
             
             // Update diagnosis validation status
             const diagnosisStatus = $('#diagnosisValidationStatus');
             if (diagnosisCount < 1) {
                 diagnosisStatus.show().removeClass('valid');
                 diagnosisStatus.find('i').removeClass('fa-check-circle text-success').addClass('fa-exclamation-triangle text-warning');
                 diagnosisStatus.find('small').text('Minimal 1 record').removeClass('text-success').addClass('text-warning');
             } else {
                 diagnosisStatus.show().addClass('valid');
                 diagnosisStatus.find('i').removeClass('fa-exclamation-triangle text-warning').addClass('fa-check-circle text-success');
                 diagnosisStatus.find('small').text(`${diagnosisCount} record(s)`).removeClass('text-warning').addClass('text-success');
             }
             
             // Update procedure validation status
             const procedureStatus = $('#procedureValidationStatus');
             if (procedureCount < 1) {
                 procedureStatus.show().removeClass('valid');
                 procedureStatus.find('i').removeClass('fa-check-circle text-success').addClass('fa-exclamation-triangle text-warning');
                 procedureStatus.find('small').text('Minimal 1 record').removeClass('text-success').addClass('text-warning');
             } else {
                 procedureStatus.show().addClass('valid');
                 procedureStatus.find('i').removeClass('fa-exclamation-triangle text-warning').addClass('fa-check-circle text-success');
                 procedureStatus.find('small').text(`${procedureCount} record(s)`).removeClass('text-warning').addClass('text-success');
             }
         }
         
         // Event listeners for table changes
         // $(document).on('DOMNodeInserted DOMNodeRemoved', '#diagnosisTableBody, #procedureTableBody', function() {
         //     updateValidationStatus();
         // });
         
         // Initial validation status update
         $(document).ready(function() {
             updateValidationStatus();
         });
    </script>
</body>
</html>
