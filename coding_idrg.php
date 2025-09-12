<?php
// Include konfigurasi E-Klaim untuk konstanta CODER_NIK
require_once 'config/eklaim_config.php';
?>
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
    <!-- Custom CSS -->
    <link href="assets/coding-idrg.css" rel="stylesheet">
  
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
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Kelas Rawat:</label>
                                    <select class="form-select" id="kelasRawatSelect">
                                        <option value="3" selected>Kelas 3</option>
                                        <option value="2">Kelas 2</option>
                                        <option value="1">Kelas 1</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Tanggal & Jam Masuk:</label>
                                    <input type="datetime-local" class="form-control" id="tanggalMasuk" placeholder="Pilih tanggal dan jam masuk">
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
                                    <label class="form-label fw-bold">DPJP:</label>
                                    <input type="text" class="form-control" id="dpjpInput" value="BAMBANG, DR">
                                </div>
                                <!-- Pasien TB checkbox removed, default value set to 0 -->
                                <input type="hidden" id="pasienTB" value="0">
                            </div>
                        </div>
                        
                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label fw-bold">No. Peserta:</label>
                                    <input type="text" class="form-control" id="noPeserta" value="0000097208276" readonly>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label fw-bold">No. SEP:</label>
                                    <input type="text" class="form-control" id="noSEP" value="UJICOBA6" readonly>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Tanggal & Jam Pulang:</label>
                                    <input type="datetime-local" class="form-control" id="tanggalPulang" placeholder="Pilih tanggal dan jam pulang">
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Kelas Hak:</label>
                                    <div class="form-control-plaintext" id="kelasHak">-</div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold">Chronic:</label>
                                    <div class="form-control-plaintext" id="chronicDisplay">-</div>
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
                                    <label class="form-label fw-bold">LOS:</label>
                                    <div class="form-control-plaintext" id="losDisplay">- hari</div>
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
        <div id="idrgSection" class="row mt-4" style="display: none;">
            <div class="col-12">
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
                                    <i class="fas fa-check-circle text-success"></i>
                                    <small class="text-success">0 record(s)</small>
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
            </div>
        </div>
 
         <!-- Action Bar -->
         <div class="section-container mt-3">
             <div class="row">
                 <div class="col-12 d-flex justify-content-between align-items-center">
                     <div></div>
                     <div>
                         <button class="btn btn-primary" id="grouperBtn">
                             <i class="fas fa-cogs me-1"></i>
                            Grouping iDRG
                         </button>
                     </div>
                 </div>
                
                <!-- Hasil Grouping IDRG -->
                <div id="groupingResults" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Hasil Grouping iDRG</h5>
                        </div>
                        <div class="card-body">
                            <!-- Tabel hasil sesuai lampiran -->
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width: 150px;">Info</td>
                                            <td id="groupingInfo">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Jenis Rawat</td>
                                            <td id="groupingJenisRawat">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">MDC</td>
                                            <td id="groupingMDC">-</td>
                                            <td id="groupingMDCNumber" class="text-end">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">DRG</td>
                                            <td id="groupingDRG">-</td>
                                            <td id="groupingDRGCode" class="text-end">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Status</td>
                                            <td id="groupingStatus">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Status Indicator untuk grouping sebelumnya -->
                            <div id="groupingStatusIndicator" class="mt-3" style="display: none;"></div>
                            
                            <!-- Error Message untuk diagnosa/procedure tidak benar -->
                            <div id="groupingErrorSection" class="mt-3" style="display: none;">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> MDC dan DRG menunjukkan bahwa variabel diagnosa atau procedure tidak benar. Silakan periksa kembali data yang dimasukkan.
                                </div>
                            </div>
                            
                            <!-- Final iDRG Button -->
                            <div class="mt-3 d-flex justify-content-end">
                                <!-- Final iDRG Button (hanya tampil jika MDC=31 dan DRG dimulai dengan 31) -->
                                <div id="finalDrgSection" style="display: none;">
                                    <button class="btn btn-success" id="finalDrgBtn">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Final iDRG
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hasil Grouping INACBG -->
                <div id="inacbgGroupingResults" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Hasil Grouping INACBG</h5>
                        </div>
                        <div class="card-body">
                            <!-- Tabel hasil sesuai lampiran -->
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width: 150px;">Info</td>
                                            <td id="inacbgGroupingInfo">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Jenis Rawat</td>
                                            <td id="inacbgGroupingJenisRawat">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Group</td>
                                            <td id="inacbgGroupingGroupDesc">-</td>
                                            <td id="inacbgGroupingGroupCode" class="text-end">-</td>
                                            <td id="inacbgGroupingGroupAmount" class="text-end">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Sub Acute</td>
                                            <td id="inacbgGroupingSubAcute">-</td>
                                            <td class="text-end">-</td>
                                            <td id="inacbgGroupingSubAcuteAmount" class="text-end">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Chronic</td>
                                            <td id="inacbgGroupingChronic">-</td>
                                            <td class="text-end">-</td>
                                            <td id="inacbgGroupingChronicAmount" class="text-end">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Special Procedure</td>
                                            <td>
                                                <select id="inacbgSpecialProcedure" class="form-select form-select-sm" style="width: 200px;">
                                                    <option value="">None</option>
                                                </select>
                                            </td>
                                            <td class="text-end">-</td>
                                            <td id="inacbgSpecialProcedureAmount" class="text-end">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Special Prosthesis</td>
                                            <td>
                                                <select id="inacbgSpecialProsthesis" class="form-select form-select-sm" style="width: 200px;">
                                                    <option value="">None</option>
                                                </select>
                                            </td>
                                            <td class="text-end">-</td>
                                            <td id="inacbgSpecialProsthesisAmount" class="text-end">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Special Investigation</td>
                                            <td>
                                                <select id="inacbgSpecialInvestigation" class="form-select form-select-sm" style="width: 200px;">
                                                    <option value="">None</option>
                                                </select>
                                            </td>
                                            <td class="text-end">-</td>
                                            <td id="inacbgSpecialInvestigationAmount" class="text-end">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Special Drug</td>
                                            <td>
                                                <select id="inacbgSpecialDrug" class="form-select form-select-sm" style="width: 200px;">
                                                    <option value="">None</option>
                                                </select>
                                            </td>
                                            <td class="text-end">-</td>
                                            <td id="inacbgSpecialDrugAmount" class="text-end">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Total Klaim</td>
                                            <td id="inacbgTotalKlaim" class="fw-bold">-</td>
                                            <td class="text-end">-</td>
                                            <td class="text-end">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Status</td>
                                            <td id="inacbgGroupingStatus">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Final INACBG Button -->
                            <div class="mt-3 d-flex justify-content-end">
                                <!-- Final INACBG Button -->
                                <div id="inacbgFinalDrgSection" style="display: none;">
                                    <button class="btn btn-info" id="inacbgFinalDrgBtn">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Final INACBG
                                    </button>
                                </div>
                            </div>
                        </div>
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
        // Global function to calculate LOS from dates
        function calculateLOSFromDates(tglMasuk, tglPulang) {
            if (!tglMasuk || !tglPulang) return 0;
            
            const masuk = new Date(tglMasuk);
            const pulang = new Date(tglPulang);
            
            // Hitung selisih dalam milidetik
            const diffTime = Math.abs(pulang - masuk);
            
            // Konversi ke hari
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            return diffDays;
        }

        $(document).ready(function() {
            // Initialize - hide error sections by default
            $('#groupingErrorSection').hide();
            $('#finalDrgSection').hide();
            $('#groupingResults').hide();
            $('#idrgSection').hide(); // Hide IDRG section initially
            
            // Load patient data if patient_id is provided
            const urlParams = new URLSearchParams(window.location.search);
            const patientId = urlParams.get('patient_id');
            
            if (patientId) {
                loadPatientData(patientId);
                
                // Check grouping status setelah data pasien dimuat
                setTimeout(function() {
                    const nomorSep = $('#noSEP').val();
                    if (nomorSep) {
                        checkGroupingStatus(nomorSep);
                    }
                }, 1000); // Delay 1 detik untuk memastikan form sudah terisi
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
                    // Check if there's already a primary diagnosis
                    const existingRows = $('#diagnosisTableBody tr:not(.empty-table)').length;
                    if (existingRows === 0) {
                    e.preventDefault();
                    showInvalidCodeNotification(data, 'diagnosa', 'accpdx');
                    $(this).val(null).trigger('change');
                    return false;
                    }
                }
                
                // Check if asterisk is 1 (not allowed for primary diagnosis)
                if (data.asterisk === 1) {
                    // Check if there's already a primary diagnosis
                    const existingRows = $('#diagnosisTableBody tr:not(.empty-table)').length;
                    if (existingRows === 0) {
                    e.preventDefault();
                    showInvalidCodeNotification(data, 'diagnosa', 'asterisk');
                    $(this).val(null).trigger('change');
                    return false;
                    }
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
                    showInvalidCodeNotification(data, 'prosedur', 'validcode');
                    $(this).val(null).trigger('change');
                    return false;
                }
                
                // Check if accpdx is N (not allowed for primary procedure)
                if (data.accpdx === 'N') {
                    // Check if there's already a primary procedure
                    const existingRows = $('#procedureTableBody tr:not(.empty-table)').length;
                    if (existingRows === 0) {
                        e.preventDefault();
                        showInvalidCodeNotification(data, 'prosedur', 'accpdx');
                        $(this).val(null).trigger('change');
                        return false;
                    }
                }
                
                // Check if asterisk is 1 (not allowed for primary procedure)
                if (data.asterisk === 1) {
                    // Check if there's already a primary procedure
                    const existingRows = $('#procedureTableBody tr:not(.empty-table)').length;
                    if (existingRows === 0) {
                        e.preventDefault();
                        showInvalidCodeNotification(data, 'prosedur', 'asterisk');
                        $(this).val(null).trigger('change');
                        return false;
                    }
                }
                
                addProcedureToTable(data);
                $(this).val(null).trigger('change');
            });
            
             
             $('#grouperBtn').on('click', function() {
                 processIDRG();
             });
            
            // Final DRG button handler
            $(document).on('click', '#finalDrgBtn', function() {
                performFinalIdrg();
            });
            
            // Final INACBG button handler
            $(document).on('click', '#inacbgFinalDrgBtn', function() {
                performFinalInacbg();
            });
            
            // Special CMG dropdown change handlers
            $(document).on('change', '#inacbgSpecialProcedure, #inacbgSpecialProsthesis, #inacbgSpecialInvestigation, #inacbgSpecialDrug', function() {
                performInacbgStage2();
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

        // Generic function untuk menambah diagnosa ke tabel
        function addDiagnosisToTable(data, tableId = 'diagnosisTableBody', isInacbg = false) {
            var tbody = $(`#${tableId}`);
            
            // Remove empty message if exists
            tbody.find('.empty-table').closest('tr').remove();
            
            var rowCount = tbody.find('tr').length + 1;
            var jenis = rowCount === 1 ? 'Primary' : 'Secondary';
            
            var newRow;
            if (isInacbg) {
                // Format untuk INACBG dengan styling yang sama dengan IDRG
                const uniqueId = data.id || `inacbg_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                const isValid = data.isValid !== false; // Default to true if not specified
                const codeDisplay = data.code; // Selalu tampilkan kode tanpa "(IM tidak berlaku)"
                const descriptionDisplay = isValid ? data.description : `${data.description} <span class="text-danger">(IM tidak berlaku)</span>`;
                
                newRow = `
                    <tr data-id="${uniqueId}" data-accpdx="${data.accpdx || 'Y'}" data-asterisk="${data.asterisk || 0}" data-validcode="${data.validcode || 1}">
                        <td><span class="badge ${rowCount === 1 ? 'bg-primary' : 'bg-secondary'}">${rowCount === 1 ? 'Primary' : 'Secondary'}</span></td>
                        <td><span class="code-badge">${codeDisplay}</span></td>
                        <td>${descriptionDisplay}</td>
                        <td>
                            ${rowCount > 1 ? `<button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimaryInacbg('${uniqueId}', '${tableId}')" title="Jadikan Primary">
                                <i class="fas fa-arrow-up"></i>
                            </button>` : ''}
                            <button class="btn-remove" onclick="removeTableRow(this, '${tableId}')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `;
            } else {
                // Format untuk IDRG
                newRow = `
                    <tr data-id="${data.id}" data-accpdx="${data.accpdx || 'Y'}" data-asterisk="${data.asterisk || 0}" data-validcode="${data.validcode || 1}">
                        <td><span class="badge ${rowCount === 1 ? 'bg-primary' : 'bg-secondary'}">${jenis}</span></td>
                        <td><span class="code-badge">${data.code}</span></td>
                        <td>${data.description}</td>
                        <td>
                             ${rowCount > 1 && data.accpdx !== 'N' && data.asterisk !== 1 ? `<button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimary(${data.id})" title="Jadikan Primary">
                                 <i class="fas fa-arrow-up"></i>
                             </button>` : ''}
                            <button class="btn-remove" onclick="removeDiagnosis(${data.id})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `;
            }
            
            tbody.append(newRow);
        }

        // Generic function untuk menambah prosedur ke tabel
        function addProcedureToTable(data, tableId = 'procedureTableBody', isInacbg = false) {
            var tbody = $(`#${tableId}`);
            
            // Remove empty message if exists
            tbody.find('.empty-table').closest('tr').remove();
            
            var rowCount = tbody.find('tr').length + 1;
            var jenis = rowCount === 1 ? 'Primary' : 'Secondary';
            
            var newRow;
            if (isInacbg) {
                // Format untuk INACBG tanpa quantity
                const uniqueId = data.id || `inacbg_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                const isValid = data.isValid !== false; // Default to true if not specified
                const codeDisplay = data.code; // Selalu tampilkan kode tanpa "(IM tidak berlaku)"
                const descriptionDisplay = isValid ? data.description : `${data.description} <span class="text-danger">(IM tidak berlaku)</span>`;
                
                newRow = `
                    <tr data-id="${uniqueId}" data-accpdx="${data.accpdx || 'Y'}" data-asterisk="${data.asterisk || 0}" data-validcode="${data.validcode || 1}">
                        <td><span class="badge ${rowCount === 1 ? 'bg-primary' : 'bg-secondary'}">${rowCount === 1 ? 'Primary' : 'Secondary'}</span></td>
                        <td><span class="code-badge">${codeDisplay}</span></td>
                        <td>${descriptionDisplay}</td>
                        <td>
                            ${rowCount > 1 ? `<button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimaryInacbgProcedure('${uniqueId}', '${tableId}')" title="Jadikan Primary">
                                <i class="fas fa-arrow-up"></i>
                            </button>` : ''}
                            <button class="btn-remove" onclick="removeTableRow(this, '${tableId}')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `;
            } else {
                // Format untuk IDRG
                newRow = `
                    <tr data-id="${data.id}" data-accpdx="${data.accpdx || 'Y'}" data-asterisk="${data.asterisk || 0}" data-validcode="${data.validcode || 1}">
                        <td><span class="badge ${rowCount === 1 ? 'bg-primary' : 'bg-secondary'}">${jenis}</span></td>
                        <td><span class="code-badge">${data.code}</span></td>
                        <td>${data.description}</td>
                        <td>
                            <input type="number" class="quantity-input" value="1" min="1" max="99">
                        </td>
                        <td>
                             ${rowCount > 1 && data.accpdx !== 'N' && data.asterisk !== 1 ? `<button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimaryProcedure(${data.id})" title="Jadikan Primary">
                                 <i class="fas fa-arrow-up"></i>
                             </button>` : ''}
                            <button class="btn-remove" onclick="removeProcedure(${data.id})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `;
            }
            
            tbody.append(newRow);
        }

        function removeDiagnosis(id) {
            var tbody = $('#diagnosisTableBody');
            var targetRow = $('tr[data-id="' + id + '"]');
            
            // Check if this is a primary diagnosis being removed
            var isPrimary = targetRow.find('td:nth-child(1) .badge').text().trim() === 'Primary';
            
            // Remove the target row
            targetRow.remove();
            
            // If primary diagnosis was removed, check for secondary diagnoses that can't become primary
            if (isPrimary) {
                checkAndRemoveInvalidSecondaryDiagnoses();
            }
            
            renumberDiagnosisTable();
        }
        
        // Generic function untuk menghapus row dari tabel
        function removeTableRow(button, tableId) {
            $(button).closest('tr').remove();
            
            // Show empty message if no rows left
            const tableBody = $(`#${tableId}`);
            if (tableBody.find('tr').length === 0) {
                const colspan = tableId.includes('Procedure') ? 5 : 4;
                const emptyMessage = tableId.includes('Procedure') ? 'Belum ada prosedur yang dipilih' : 'Belum ada diagnosa yang dipilih';
                
                tableBody.append(`
                    <tr>
                        <td colspan="${colspan}" class="empty-table">
                            <i class="fas fa-clipboard-list"></i>
                            <p>${emptyMessage}</p>
                        </td>
                    </tr>
                `);
            }
        }
        
        function checkAndRemoveInvalidSecondaryDiagnoses() {
            var tbody = $('#diagnosisTableBody');
            var rows = tbody.find('tr:not(.empty-table)');
            var removedDiagnoses = [];
            
            // Check if there are any remaining diagnoses
            if (rows.length === 0) {
                return;
            }
            
            // Check if the first diagnosis can become primary
            var firstRow = rows.first();
            var firstAccpdx = firstRow.attr('data-accpdx');
            var firstAsterisk = firstRow.attr('data-asterisk');
            
            // If the first diagnosis can become primary, no need to remove anything
            if (firstAccpdx === 'Y' && firstAsterisk === '0') {
                return;
            }
            
            // If the first diagnosis can't become primary, remove all diagnoses that can't become primary
            rows.each(function() {
                var row = $(this);
                var accpdx = row.attr('data-accpdx');
                var asterisk = row.attr('data-asterisk');
                var code = row.find('td:nth-child(2) .code-badge').text();
                var description = row.find('td:nth-child(3)').text();
                
                // Check if this diagnosis can't become primary
                if (accpdx === 'N' || asterisk === '1') {
                    removedDiagnoses.push({
                        code: code,
                        description: description,
                        reason: accpdx === 'N' ? 'accpdx=N' : 'asterisk=1'
                    });
                    row.remove();
                }
            });
            
            // Show notification if any diagnoses were removed
            if (removedDiagnoses.length > 0) {
                var message = 'Diagnosa berikut dihapus karena tidak dapat dijadikan primary diagnosis:\n\n';
                removedDiagnoses.forEach(function(diagnosis) {
                    message += `• ${diagnosis.code} - ${diagnosis.description} (${diagnosis.reason})\n`;
                });
                message += '\nDiagnosa dengan accpdx=N atau asterisk=1 tidak dapat menjadi primary diagnosis.';
                
                showError(message);
            }
        }
         
         function makePrimary(id) {
             var tbody = $('#diagnosisTableBody');
             var targetRow = $('tr[data-id="' + id + '"]');
             
             if (targetRow.length > 0) {
                 // Check if the diagnosis has accpdx=N or asterisk=1 (not allowed for primary)
                 var accpdx = targetRow.attr('data-accpdx');
                 var asterisk = targetRow.attr('data-asterisk');
                 
                 if (accpdx === 'N') {
                     showError('Diagnosa dengan accpdx=N tidak dapat dijadikan primary diagnosis');
                     return;
                 }
                 
                 if (asterisk === '1') {
                     showError('Diagnosa dengan asterisk=1 (kode asterisk) tidak dapat dijadikan primary diagnosis');
                     return;
                 }
                 
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
                 // Check if the procedure has accpdx=N or asterisk=1 (not allowed for primary)
                 var accpdx = targetRow.attr('data-accpdx');
                 var asterisk = targetRow.attr('data-asterisk');
                 
                 if (accpdx === 'N') {
                     showError('Prosedur dengan accpdx=N tidak dapat dijadikan primary procedure');
                     return;
                 }
                 
                 if (asterisk === '1') {
                     showError('Prosedur dengan asterisk=1 (kode asterisk) tidak dapat dijadikan primary procedure');
                     return;
                 }
                 
                 // Move the target row to the top
                 tbody.prepend(targetRow);
                 
                 // Renumber all rows
                 renumberProcedureTable();
                 
                 // Show success message
                 showSuccessMessage('Prosedur berhasil dijadikan primary!');
             }
         }

        function removeProcedure(id) {
            var tbody = $('#procedureTableBody');
            var targetRow = $('tr[data-id="' + id + '"]');
            
            // Check if this is a primary procedure being removed
            var isPrimary = targetRow.find('td:nth-child(1) .badge').text().trim() === 'Primary';
            
            // Remove the target row
            targetRow.remove();
            
            // If primary procedure was removed, check for secondary procedures that can't become primary
            if (isPrimary) {
                checkAndRemoveInvalidSecondaryProcedures();
            }
            
            renumberProcedureTable();
        }
        
        function checkAndRemoveInvalidSecondaryProcedures() {
            var tbody = $('#procedureTableBody');
            var rows = tbody.find('tr:not(.empty-table)');
            var removedProcedures = [];
            
            // Check if there are any remaining procedures
            if (rows.length === 0) {
                return;
            }
            
            // Check if the first procedure can become primary
            var firstRow = rows.first();
            var firstAccpdx = firstRow.attr('data-accpdx');
            var firstAsterisk = firstRow.attr('data-asterisk');
            
            // If the first procedure can become primary, no need to remove anything
            if (firstAccpdx === 'Y' && firstAsterisk === '0') {
                return;
            }
            
            // If the first procedure can't become primary, remove all procedures that can't become primary
            rows.each(function() {
                var row = $(this);
                var accpdx = row.attr('data-accpdx');
                var asterisk = row.attr('data-asterisk');
                var code = row.find('td:nth-child(2) .code-badge').text();
                var description = row.find('td:nth-child(3)').text();
                
                // Check if this procedure can't become primary
                if (accpdx === 'N' || asterisk === '1') {
                    removedProcedures.push({
                        code: code,
                        description: description,
                        reason: accpdx === 'N' ? 'accpdx=N' : 'asterisk=1'
                    });
                    row.remove();
                }
            });
            
            // Show notification if any procedures were removed
            if (removedProcedures.length > 0) {
                var message = 'Prosedur berikut dihapus karena tidak dapat dijadikan primary procedure:\n\n';
                removedProcedures.forEach(function(procedure) {
                    message += `• ${procedure.code} - ${procedure.description} (${procedure.reason})\n`;
                });
                message += '\nProsedur dengan accpdx=N atau asterisk=1 tidak dapat menjadi primary procedure.';
                
                showError(message);
            }
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
                     // Secondary diagnosis - check if accpdx=N or asterisk=1
                     var accpdx = row.attr('data-accpdx');
                     var asterisk = row.attr('data-asterisk');
                     var makePrimaryButton = '';
                     
                     if (accpdx !== 'N' && asterisk !== '1') {
                         makePrimaryButton = `
                         <button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimary(${dataId})" title="Jadikan Primary">
                             <i class="fas fa-arrow-up"></i>
                         </button>
                         `;
                     }
                     
                     actionCell.html(`
                         ${makePrimaryButton}
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
                     // Secondary procedure - check if accpdx=N or asterisk=1
                     var accpdx = row.attr('data-accpdx');
                     var asterisk = row.attr('data-asterisk');
                     var makePrimaryButton = '';
                     
                     if (accpdx !== 'N' && asterisk !== '1') {
                         makePrimaryButton = `
                         <button class="btn btn-sm btn-outline-primary me-1" onclick="makePrimaryProcedure(${dataId})" title="Jadikan Primary">
                             <i class="fas fa-arrow-up"></i>
                         </button>
                         `;
                     }
                     
                     actionCell.html(`
                         ${makePrimaryButton}
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
                        
                        // Load INACBG import data after patient data is loaded
                        const nomorSep = response.data.nomor_sep;
                        if (nomorSep) {
                            setTimeout(function() {
                                loadInacbgImportData(nomorSep);
                            }, 500); // Delay 500ms to ensure form is populated
                        }
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
            $('#kelasRawatSelect').val(patient.kelas_rawat || '3');
            $('#dpjpInput').val(patient.nama_dokter || 'BAMBANG, DR');
             $('#caraMasukSelect').val(patient.cara_masuk || 'gp');
             // Set kode tarif berdasarkan jenis rawat
            const jenisRawat = $('input[name="jenisRawat"]:checked').val();
            let defaultKodeTarif = 'AP'; // Default untuk rawat jalan
            
            if (jenisRawat === '1') { // Rawat Inap
                defaultKodeTarif = 'AP'; // TARIF RS KELAS A PEMERINTAH
            } else if (jenisRawat === '2') { // Rawat Jalan
                defaultKodeTarif = 'AP'; // TARIF RS KELAS A PEMERINTAH
            }
            
            $('#kodeTarifSelect').val(patient.kode_tarif || defaultKodeTarif);
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
                
                // If jenis rawat is Jalan, update tanggal pulang to match tanggal masuk
                if ($('input[name="jenisRawat"]:checked').val() === '2') {
                    $('#tanggalPulang').val($('#tanggalMasuk').val());
                }
            });
            
            // Add event listener for jenis rawat change to update kelas rawat options
            $('input[name="jenisRawat"]').on('change', function() {
                updateKelasRawatOptions();
                updateTanggalPulangStatus();
                updateKodeTarifByJenisRawat();
            });
            
            // Function to update kode tarif based on jenis rawat
            function updateKodeTarifByJenisRawat() {
                const jenisRawat = $('input[name="jenisRawat"]:checked').val();
                let defaultKodeTarif = 'AP'; // Default untuk rawat jalan
                
                if (jenisRawat === '1') { // Rawat Inap
                    defaultKodeTarif = 'AP'; // TARIF RS KELAS A PEMERINTAH
                } else if (jenisRawat === '2') { // Rawat Jalan
                    defaultKodeTarif = 'AP'; // TARIF RS KELAS A PEMERINTAH
                }
                
                // Set kode tarif jika belum ada atau jika perlu diupdate
                if (!$('#kodeTarifSelect').val() || $('#kodeTarifSelect').val() === 'DS') {
                    $('#kodeTarifSelect').val(defaultKodeTarif);
                }
            }
            
            // Function to update kelas rawat options based on jenis rawat
            function updateKelasRawatOptions() {
                const jenisRawat = $('input[name="jenisRawat"]:checked').val();
                const kelasRawatSelect = $('#kelasRawatSelect');
                
                // Clear existing options
                kelasRawatSelect.empty();
                
                if (jenisRawat === '2') { // Rawat Jalan
                    kelasRawatSelect.append('<option value="3">Kelas Regular</option>');
                    kelasRawatSelect.append('<option value="1">Kelas Eksekutif</option>');
                    kelasRawatSelect.val('3'); // Default to Regular
                } else if (jenisRawat === '1') { // Rawat Inap
                    kelasRawatSelect.append('<option value="3">Kelas 3</option>');
                    kelasRawatSelect.append('<option value="2">Kelas 2</option>');
                    kelasRawatSelect.append('<option value="1">Kelas 1</option>');
                    kelasRawatSelect.val('3'); // Default to Kelas 3
                }
                
                // Update kelas hak display
                const kelasRawat = kelasRawatSelect.val();
                const kelasText = getKelasText(kelasRawat, jenisRawat);
                $('#kelasHak').text(kelasText);
            }
            
            // Function to update tanggal pulang based on jenis rawat
            function updateTanggalPulangStatus() {
                const jenisRawat = $('input[name="jenisRawat"]:checked').val();
                const tanggalPulangInput = $('#tanggalPulang');
                const tanggalMasukInput = $('#tanggalMasuk');
                
                if (jenisRawat === '2') { // Rawat Jalan
                    tanggalPulangInput.prop('disabled', true);
                    // Set tanggal pulang sama dengan tanggal masuk
                    tanggalPulangInput.val(tanggalMasukInput.val());
                    tanggalPulangInput.addClass('bg-light');
                } else { // Rawat Inap
                    tanggalPulangInput.prop('disabled', false);
                    tanggalPulangInput.removeClass('bg-light');
                }
                
                // Recalculate LOS after changing tanggal pulang status
                calculateLOS();
            }
            
            // Initialize kelas rawat options based on current jenis rawat
            updateKelasRawatOptions();
            
            // Initialize tanggal pulang status based on current jenis rawat
            updateTanggalPulangStatus();
            
            // Add event listener for kelas rawat change to update display
            $('#kelasRawatSelect').on('change', function() {
                const jenisRawat = $('input[name="jenisRawat"]:checked').val();
                const kelasRawat = $(this).val();
                const kelasText = getKelasText(kelasRawat, jenisRawat);
                $('#kelasHak').text(kelasText);
            });
             
             // Function to calculate LOS - moved to global scope
             // calculateLOSFromDates function moved outside document.ready for global access
            
            function calculateLOS() {
                 const tglMasuk = $('#tanggalMasuk').val();
                 const tglPulang = $('#tanggalPulang').val();
                 const jenisRawat = $('input[name="jenisRawat"]:checked').val();
                 
                 if (jenisRawat === '2') { // Rawat Jalan
                     $('#losDisplay').text('0 hari');
                     return;
                 }
                 
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
                 } else {
                     $('#losDisplay').text('0 hari');
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
            const kelasText = getKelasText(patient.kelas_rawat, patient.jenis_rawat);
            $('#kelasHak').text(kelasText);
            
            // Update kelas rawat dropdown after setting jenis rawat
            setTimeout(function() {
                updateKelasRawatOptions();
                updateTanggalPulangStatus();
                $('#kelasRawatSelect').val(patient.kelas_rawat || '3');
                const updatedKelasText = getKelasText(patient.kelas_rawat || '3', patient.jenis_rawat || '2');
                $('#kelasHak').text(updatedKelasText);
            }, 100);
             
             // Update chronic
             $('#chronicDisplay').text('-');
             
             // Update clinical data if available
             if (patient.sistole) {
                 $('#sistole').val(patient.sistole);
             }
             if (patient.diastole) {
                 $('#diastole').val(patient.diastole);
             }
             
            // Set default covid status to '0' (checkbox removed)
             
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
             
             // Show IDRG section (diagnosa dan prosedur)
             $('#idrgSection').show();
             
             // Load saved diagnosis and procedure data
             loadSavedDiagnosisData(patient.id);
             loadSavedProcedureData(patient.id);
             
             // Check if grouping has been done before (method 07)
             checkPreviousGroupingResult(patient.nomor_sep);
         }
         
         // Helper function untuk mendeteksi error grouping
         function isGroupingError(mdcDescription, drgDescription, mdcNumber, drgCode) {
             // Deteksi error untuk MDC description yang lebih luas
             const mdcErrorKeywords = ['ungroupable', 'unrelated', 'invalid', 'error', 'not valid'];
             const isMdcError = mdcErrorKeywords.some(keyword => 
                 mdcDescription.toLowerCase().includes(keyword.toLowerCase())
             );
             
             // Deteksi error untuk DRG description yang lebih luas
             const drgErrorKeywords = ['not valid', 'unrelated', 'ungroupable', 'invalid', 'error'];
             const isDrgError = drgErrorKeywords.some(keyword => 
                 drgDescription.toLowerCase().includes(keyword.toLowerCase())
             );
             
            // Cek kondisi khusus untuk MDC 21/31 dan DRG yang dimulai dengan 21/31 (valid)
            const validMdcCodes = ['21', '31'];
            const isValidResult = validMdcCodes.includes(mdcNumber) && drgCode && validMdcCodes.some(code => drgCode.startsWith(code));
             
            // Return true jika ada error dan bukan kondisi valid MDC 21/31
            return (isMdcError || isDrgError) && !isValidResult;
         }
         
         // Helper function untuk apply error styling
         function applyErrorStyling(mdcElement, drgElement, mdcNumberElement, drgCodeElement, isError) {
             if (isError) {
                 mdcElement.addClass('text-danger fw-bold');
                 drgElement.addClass('text-danger fw-bold');
                 mdcNumberElement.addClass('text-danger');
                 drgCodeElement.addClass('text-danger');
             } else {
                 mdcElement.removeClass('text-danger fw-bold');
                 drgElement.removeClass('text-danger fw-bold');
                 mdcNumberElement.removeClass('text-danger');
                 drgCodeElement.removeClass('text-danger');
             }
         }
         
         // Helper function untuk show/hide sections
         function toggleGroupingSections(showErrorSection, showFinalDrgButton) {
             if (showFinalDrgButton) {
                 $('#finalDrgSection').show();
                 $('#groupingErrorSection').hide();
             } else if (showErrorSection) {
                 $('#finalDrgSection').hide();
                 $('#groupingErrorSection').show();
             } else {
                 $('#finalDrgSection').hide();
                 $('#groupingErrorSection').hide();
             }
         }
         
         // Function to check if grouping has been done before
         function checkPreviousGroupingResult(nomorSep) {
             if (!nomorSep) return;
             
             $.ajax({
                 url: 'api/check_grouping_status.php',
                 method: 'POST',
                 data: JSON.stringify({
                     nomor_sep: nomorSep,
                     method_code: '07'
                 }),
                 contentType: 'application/json',
                 dataType: 'json',
                 success: function(response) {
                     if (response.success && response.data) {
                         console.log('Previous grouping found:', response.data);
                         
                         // Parse response_data
                         let responseData = null;
                         try {
                             responseData = typeof response.data.response_data === 'string' 
                                 ? JSON.parse(response.data.response_data) 
                                 : response.data.response_data;
                         } catch (e) {
                             console.error('Error parsing response_data:', e);
                             return;
                         }
                         
                        // Display previous grouping result
                        // Check if responseData has the correct structure
                        if (responseData && responseData.data && responseData.data.response_idrg) {
                            // Data has wrapper structure, use the inner data
                            displayPreviousGroupingResult(responseData.data);
                        } else if (responseData && responseData.response_idrg) {
                            // Data is direct structure
                            displayPreviousGroupingResult(responseData);
                        } else {
                            console.error('Invalid response data structure:', responseData);
                        }
                     } else {
                         console.log('No previous grouping found for nomor_sep:', nomorSep);
                     }
                 },
                 error: function(xhr, status, error) {
                     console.log('Error checking grouping status:', error);
                 }
             });
         }
         
         // Function to display previous grouping result
         function displayPreviousGroupingResult(responseData) {
             // Show the results section
             $('#groupingResults').show();
             
             // Populate basic information
             $('#groupingInfo').text('INACBG @ 5 Sep 2025 22:28 - 1.0.24 / 0.2.1664.202505111134');
             
             // Set jenis rawat berdasarkan data form
             const jenisRawat = $('input[name="jenisRawat"]:checked').val();
             const tglMasuk = $('#tanggalMasuk').val();
             const tglPulang = $('#tanggalPulang').val();
             
             let jenisRawatText = '';
             if (jenisRawat === '1') {
                 // Rawat Inap - hitung LOS
                 if (tglMasuk && tglPulang) {
                     const los = calculateLOSFromDates(tglMasuk, tglPulang);
                     jenisRawatText = `Rawat Inap (${los} Hari)`;
                 } else {
                     jenisRawatText = 'Rawat Inap';
                 }
             } else if (jenisRawat === '2') {
                 jenisRawatText = 'Rawat Jalan';
             } else {
                 jenisRawatText = 'Tidak Diketahui';
             }
             
             $('#groupingJenisRawat').text(jenisRawatText);
             $('#groupingStatus').text('normal');
             
             // Populate MDC dan DRG dari response data
             const mdcDescription = responseData.response_idrg?.mdc_description || 'N/A';
             const mdcNumber = responseData.response_idrg?.mdc_number || 'N/A';
             const drgDescription = responseData.response_idrg?.drg_description || 'N/A';
             const drgCode = responseData.response_idrg?.drg_code || 'N/A';
             
             const mdcElement = $('#groupingMDC');
             const drgElement = $('#groupingDRG');
             const mdcNumberElement = $('#groupingMDCNumber');
             const drgCodeElement = $('#groupingDRGCode');
             
             mdcElement.text(mdcDescription);
             drgElement.text(drgDescription);
             mdcNumberElement.text(mdcNumber);
             drgCodeElement.text(drgCode);
             
             // Deteksi error menggunakan helper function
             const isError = isGroupingError(mdcDescription, drgDescription, mdcNumber, drgCode);
             
             // Apply error styling
             applyErrorStyling(mdcElement, drgElement, mdcNumberElement, drgCodeElement, isError);
             
             // Tentukan section yang akan ditampilkan
             const showErrorSection = isError;
             const showFinalDrgButton = !isError;
             
             // Show/hide sections menggunakan helper function
             toggleGroupingSections(showErrorSection, showFinalDrgButton);
             
             // Update final iDRG status
             updateFinalIdrgStatus();
             
             console.log('Previous grouping result displayed successfully');
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
        
        function loadInacbgImportData(nomorSep) {
            console.log('Loading INACBG import data for nomor_sep:', nomorSep);
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'get_inacbg_import_data',
                    nomor_sep: nomorSep
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('INACBG import data response:', response);
                    
                    if (response.success && response.data) {
                        console.log('INACBG import data found, populating tables...');
                        console.log('Diagnosis data structure:', response.data.diagnosis);
                        console.log('Procedure data structure:', response.data.procedure);
                        
                        // Populate INACBG diagnosis table
                        if (response.data.diagnosis && response.data.diagnosis.length > 0) {
                            populateInacbgDiagnosisFromImport(response.data.diagnosis);
                        }
                        
                        // Populate INACBG procedure table
                        if (response.data.procedure && response.data.procedure.length > 0) {
                            populateInacbgProcedureFromImport(response.data.procedure);
                        }
                        
                        console.log('INACBG import data loaded successfully');
                    } else {
                        console.log('No INACBG import data found for nomor_sep:', nomorSep);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error loading INACBG import data:', error);
                }
            });
        }
        
        function populateInacbgDiagnosisFromImport(diagnosisData) {
            console.log('Populating INACBG diagnosis from import data:', diagnosisData);
            
            // Clear existing data
            $('#inacbgDiagnosisTableBody').empty();
            
            if (diagnosisData.length === 0) {
                $('#inacbgDiagnosisTableBody').html('<tr class="empty-table"><td colspan="3" class="text-center text-muted">Tidak ada data diagnosa</td></tr>');
                return;
            }
            
            // Add each diagnosis
            diagnosisData.forEach((diagnosis, index) => {
                const jenisText = diagnosis.is_primary == 1 ? 'Primary' : 'Secondary';
                const jenisClass = diagnosis.is_primary == 1 ? 'bg-primary' : 'bg-secondary';
                
                const row = `
                    <tr>
                        <td>
                            <span class="badge ${jenisClass}">${jenisText}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary code-badge">${diagnosis.icd_code}</span>
                        </td>
                        <td>${diagnosis.icd_description}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTableRow(this, 'inacbgDiagnosisTableBody')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#inacbgDiagnosisTableBody').append(row);
            });
        }
        
        function populateInacbgProcedureFromImport(procedureData) {
            console.log('Populating INACBG procedure from import data:', procedureData);
            
            // Clear existing data
            $('#inacbgProcedureTableBody').empty();
            
            if (procedureData.length === 0) {
                $('#inacbgProcedureTableBody').html('<tr class="empty-table"><td colspan="4" class="text-center text-muted">Tidak ada data prosedur</td></tr>');
                return;
            }
            
            // Add each procedure
            procedureData.forEach((procedure, index) => {
                const jenisText = procedure.is_primary == 1 ? 'Primary' : 'Secondary';
                const jenisClass = procedure.is_primary == 1 ? 'bg-primary' : 'bg-secondary';
                
                const row = `
                    <tr>
                        <td>
                            <span class="badge ${jenisClass}">${jenisText}</span>
                        </td>
                        <td>
                            <span class="badge bg-success code-badge">${procedure.icd_code}</span>
                        </td>
                        <td>${procedure.icd_description}</td>
                        <td>${procedure.quantity}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTableRow(this, 'inacbgProcedureTableBody')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#inacbgProcedureTableBody').append(row);
            });
        }
        
        function getKelasText(kelas, jenisRawat = null) {
            // Jika jenis rawat tidak disediakan, coba ambil dari radio button
            if (!jenisRawat) {
                jenisRawat = $('input[name="jenisRawat"]:checked').val() || '2';
            }
            
            if (jenisRawat === '2') { // Rawat Jalan
                const kelasMap = {
                    '1': 'Kelas Eksekutif',
                    '3': 'Kelas Regular'
                };
                return kelasMap[kelas] || 'Kelas Regular';
            } else { // Rawat Inap
                const kelasMap = {
                    '1': 'Kelas 1',
                    '2': 'Kelas 2',
                    '3': 'Kelas 3'
                };
                return kelasMap[kelas] || 'Kelas 3';
            }
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
        
        function formatDateTime(dateTimeString) {
            if (!dateTimeString) return '';
            
            // Convert datetime-local format (YYYY-MM-DDTHH:MM) to standard format (YYYY-MM-DD HH:MM:SS)
            const date = new Date(dateTimeString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }
        
        function checkGroupingStatus(nomorSep) {
            console.log('Checking grouping status for nomor_sep:', nomorSep);
            
            // First check E-Klaim tracking status (prioritas utama) - SEQUENTIAL
            checkEklaimTrackingStatus(nomorSep).then(function() {
                // After E-Klaim tracking check is complete, then check grouping status from kunjungan_pasien table
                checkKunjunganPasienStatus(nomorSep);
            });
        }
        
        function checkKunjunganPasienStatus(nomorSep) {
            console.log('Checking kunjungan_pasien status for nomor_sep:', nomorSep);
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'checkGroupingStatus',
                    nomor_sep: nomorSep
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Grouping status from kunjungan_pasien:', response);
                    
                    // Only proceed if E-Klaim tracking doesn't show final_idrg
                    if (!isFinalIdrgCompleted && response.success && response.data) {
                        console.log('Processing grouping status from kunjungan_pasien because final_idrg not completed');
                        const status = response.data;
                        if (status.grouping_status === 'success' && status.grouping_result) {
                            // Parse grouping result
                            const result = JSON.parse(status.grouping_result);
                            console.log('Parsed grouping result from kunjungan_pasien:', result);
                            
                            // Check if grouping result is valid (MDC=31 and DRG starts with 31)
                            const mdcNumber = result.data?.response_idrg?.mdc_number;
                            const drgCode = result.data?.response_idrg?.drg_code;
                            
                            console.log('MDC Number:', mdcNumber, 'DRG Code:', drgCode);
                            
                            const validMdcCodes = ['21', '31'];
                            const isValidResult = validMdcCodes.includes(mdcNumber) && drgCode && validMdcCodes.some(code => drgCode.startsWith(code));
                            
                            if (isValidResult) {
                                console.log('Valid grouping result found, but final_idrg not completed - NOT showing Final iDRG button');
                                console.log('Form should remain editable since final_idrg is not completed');
                                $('#finalDrgSection').hide(); // Don't show Final iDRG button
                                ensureFormIsEditable(); // Ensure form is editable
                            } else {
                                console.log('Invalid grouping result - hiding Final iDRG button');
                                $('#finalDrgSection').hide();
                                ensureFormIsEditable(); // Ensure form is editable
                            }
                            
                            // Always display previous grouping result
                            // Adjust data structure to match displayGroupingResults expectations
                            const adjustedResult = {
                                response_idrg: result.data?.response_idrg || {}
                            };
                            console.log('Adjusted result for display:', adjustedResult);
                            displayGroupingResults(adjustedResult);
                        } else {
                            console.log('No previous grouping result found');
                            $('#groupingResults').hide();
                            ensureFormIsEditable(); // Ensure form is editable
                        }
                    } else if (isFinalIdrgCompleted) {
                        console.log('Final iDRG already completed - skipping grouping status check from kunjungan_pasien');
                    } else {
                        console.log('Failed to get grouping status AND final_idrg not completed:', response);
                        // Only call ensureFormIsEditable if we're sure final_idrg is not completed
                        if (!isFinalIdrgCompleted) {
                            ensureFormIsEditable();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error checking grouping status:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    // Only ensure form is editable on error if final_idrg is not completed
                    if (!isFinalIdrgCompleted) {
                        ensureFormIsEditable();
                    }
                }
            });
        }
        
        // Global flag to track if final_idrg is already completed
        let isFinalIdrgCompleted = false;
        
        function checkEklaimTrackingStatus(nomorSep) {
            console.log('Checking E-Klaim tracking status for nomor_sep:', nomorSep);
            
            return $.ajax({
                url: 'api/check_eklaim_tracking.php',
                method: 'POST',
                data: JSON.stringify({
                    nomor_sep: nomorSep
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('E-Klaim tracking response:', response);
                    
                    if (response.success && response.final_idrg_success) {
                        console.log('Final iDRG already completed - showing INACBG section and making form read-only');
                        
                        // Set global flag
                        isFinalIdrgCompleted = true;
                        
                        // Apply final iDRG status
                        applyFinalIdrgStatus(response.final_idrg_record);
                        
                    } else {
                        console.log('Final iDRG not completed yet - normal flow');
                        console.log('Tracking summary:', response.tracking_summary);
                        isFinalIdrgCompleted = false;
                        
                        // Check if there are any errors in the tracking
                        if (response.tracking_summary && response.tracking_summary.failed_methods > 0) {
                            console.log('There are failed methods in E-Klaim tracking');
                            // You might want to show a warning or handle this case
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error checking E-Klaim tracking status:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    // Continue with normal flow if tracking check fails
                    isFinalIdrgCompleted = false;
                }
            });
        }
        
        function ensureFormIsEditable() {
            console.log('Ensuring form is editable - final_idrg not completed');
            
            // Re-enable all form inputs
            $('#jaminanSelect').prop('disabled', false);
            $('input[name="jenisRawat"]').prop('disabled', false);
            $('#kelasRawatSelect').prop('disabled', false);
            $('#dpjpInput').prop('readonly', false);
            $('#noPeserta').prop('readonly', false);
            $('#noSEP').prop('readonly', false);
            $('#tanggalMasuk').prop('readonly', false);
            $('#tanggalPulang').prop('readonly', false);
            $('#caraMasukSelect').prop('disabled', false);
            $('#kodeTarifSelect').prop('disabled', false);
            $('#adlSubAcute').prop('readonly', false);
            $('#adlChronic').prop('readonly', false);
            $('#caraPulangSelect').prop('disabled', false);
            
            // Re-enable cost inputs
            $('.cost-input').prop('readonly', false);
            
            // Re-enable diagnosis and procedure selects
            $('#diagnosisSelect').prop('disabled', false);
            $('#procedureSelect').prop('disabled', false);
            
            // Remove visual indication that form is read-only
            $('.form-control-plaintext').addClass('form-control form-select');
            $('.form-control-plaintext').removeClass('form-control-plaintext');
            
            // Ensure Grouping button is in normal state
            const grouperBtn = $('#grouperBtn');
            grouperBtn.html('<i class="fas fa-cogs me-1"></i>Grouping');
            grouperBtn.removeClass('btn-warning').addClass('btn-primary');
            grouperBtn.off('click').on('click', processIDRG);
            
            // Hide INACBG section
            $('#inacbgSection').hide();
            
            // Hide status indicator
            $('#groupingStatusIndicator').hide();
            
        }
        
        function applyFinalIdrgStatus(finalIdrgRecord) {
            // Make form read-only
            makeFormReadOnly();
            
            // Change Grouping button to "Edit Ulang iDRG"
            changeGroupingButtonToEdit();
            
            // Show INACBG section
            showInacbgSection();
            
            // Show grouping results if available
            $('#groupingResults').show();
            
            // Update status indicator
            const statusIndicator = $('#groupingStatusIndicator');
            statusIndicator.removeClass('alert-warning alert-danger').addClass('alert-success');
            statusIndicator.html(`
                <i class="fas fa-check-circle me-2"></i>
                <strong>Final iDRG:</strong> Sudah diproses sebelumnya pada ${new Date(finalIdrgRecord.completed_at).toLocaleString('id-ID')}
            `);
            statusIndicator.show();
            
            // Hide Final iDRG button since it's already done
            $('#finalDrgSection').hide();
        }
        
        function performFinalIdrg() {
            // Prevent multiple clicks
            if ($('#finalDrgBtn').prop('disabled')) {
                console.log('Final iDRG sedang diproses, mohon tunggu...');
                return;
            }
            
            // Disable button to prevent multiple clicks
            $('#finalDrgBtn').prop('disabled', true);
            
            // Show processing message
            showProcessingMessage('Sedang memproses Final iDRG...');
            
            // Get nomor SEP from form
            const nomorSep = $('#noSEP').val();
            
            console.log('Nomor SEP untuk Final iDRG:', nomorSep);
            
            if (!nomorSep) {
                hideProcessingMessage();
                $('#finalDrgBtn').prop('disabled', false);
                showErrorMessage('Nomor SEP tidak ditemukan');
                return;
            }
            
            // Call Final iDRG API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'idrg_grouper_final',
                    nomor_sep: nomorSep
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Final iDRG response:', response);
                    
                    hideProcessingMessage();
                    
                    if (response.success === true || response.success === 'true') {
                        console.log('Final iDRG berhasil');
                        
                        if (response.already_final) {
                            showSuccessMessage('iDRG sudah dalam status final!');
                        } else {
                            showSuccessMessage('Final iDRG berhasil diproses!');
                        }
                        
                        // Update UI untuk menunjukkan status final
                        updateFinalIdrgStatus();
                        
                        // Refresh data jika diperlukan
                        if (response.data && response.data.response_idrg) {
                            displayFinalIdrgResult(response.data.response_idrg);
                        }
                    } else {
                        console.log('Final iDRG gagal:', response);
                        showErrorMessage('Error Final iDRG: ' + (response.error || response.message || 'Unknown error'));
                        // Re-enable button on error
                        $('#finalDrgBtn').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error Final iDRG:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error Final iDRG: ' + error);
                    // Re-enable button on error
                    $('#finalDrgBtn').prop('disabled', false);
                }
            });
        }
        
        function displayFinalIdrgResult(responseIdrg) {
            // Display hasil final iDRG jika ada
            if (responseIdrg) {
                console.log('Final iDRG Result:', responseIdrg);
                
                // Update display dengan hasil final
                if (responseIdrg.drg_code) {
                    $('#groupingDRGCode').text(responseIdrg.drg_code);
                }
                if (responseIdrg.drg_description) {
                    $('#groupingDRGDescription').text(responseIdrg.drg_description);
                }
                if (responseIdrg.mdc_number) {
                    $('#groupingMDCNumber').text(responseIdrg.mdc_number);
                }
                if (responseIdrg.mdc_description) {
                    $('#groupingMDCDescription').text(responseIdrg.mdc_description);
                }
            }
        }
        
        function showInacbgSection() {
            // Show INACBG section if it doesn't exist
            if ($('#inacbgSection').length === 0) {
                createInacbgSection();
            }
            $('#inacbgSection').show();
        }
        
        function makeFormReadOnly() {
            // Make all form inputs read-only
            $('#jaminanSelect').prop('disabled', true);
            $('input[name="jenisRawat"]').prop('disabled', true);
            $('#kelasRawatSelect').prop('disabled', true);
            $('#dpjpInput').prop('readonly', true);
            $('#noPeserta').prop('readonly', true);
            $('#noSEP').prop('readonly', true);
            $('#tanggalMasuk').prop('readonly', true);
            $('#tanggalPulang').prop('readonly', true);
            $('#caraMasukSelect').prop('disabled', true);
            $('#kodeTarifSelect').prop('disabled', true);
            $('#adlSubAcute').prop('readonly', true);
            $('#adlChronic').prop('readonly', true);
            $('#caraPulangSelect').prop('disabled', true);
            
            // Make all cost inputs read-only
            $('.cost-input').prop('readonly', true);
            
            // Disable diagnosis and procedure selects
            $('#diagnosisSelect').prop('disabled', true);
            $('#procedureSelect').prop('disabled', true);
            
            // Add visual indication that form is read-only
            $('.form-control, .form-select, .form-check-input').addClass('form-control-plaintext');
            $('.form-control, .form-select').removeClass('form-control form-select');
        }
        
        function changeGroupingButtonToEdit() {
            const grouperBtn = $('#grouperBtn');
            grouperBtn.html('<i class="fas fa-edit me-1"></i>Edit Ulang iDRG');
            grouperBtn.removeClass('btn-primary').addClass('btn-warning');
            grouperBtn.off('click').on('click', function() {
                // Call re-edit API first
                performIdrgReedit();
            });
        }
        
        function performIdrgReedit() {
            const nomorSep = $('#noSEP').val();
            if (!nomorSep) {
                showErrorMessage('Nomor SEP tidak ditemukan!');
                return;
            }
            
            showProcessingMessage('Sedang melakukan re-edit iDRG...');
            $('#grouperBtn').prop('disabled', true);
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    action: 'idrg_grouper_reedit',
                    nomor_sep: nomorSep
                }),
                success: function(response) {
                    hideProcessingMessage();
                    $('#grouperBtn').prop('disabled', false);
                    
                    if (response.success) {
                        showSuccessMessage('Re-edit iDRG berhasil! Form sekarang dapat diedit.');
                        
                        // Re-enable form for editing
                        enableFormForEditing();
                        
                        // Change button back to Grouping
                        const grouperBtn = $('#grouperBtn');
                        grouperBtn.html('<i class="fas fa-cogs me-1"></i>Grouping');
                        grouperBtn.removeClass('btn-warning').addClass('btn-primary');
                        grouperBtn.off('click').on('click', processIDRG);
                        
                        // Hide INACBG section
                        $('#inacbgSection').hide();
                        
                        // Hide grouping results
                        $('#groupingResults').hide();
                        
                        // Hide final iDRG section
                        $('#finalDrgSection').hide();
                        
                    } else {
                        showErrorMessage('Re-edit iDRG gagal: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    hideProcessingMessage();
                    $('#grouperBtn').prop('disabled', false);
                    showErrorMessage('Error saat melakukan re-edit iDRG: ' + error);
                }
            });
        }
        
        function enableFormForEditing() {
            // Re-enable all form inputs
            $('#jaminanSelect').prop('disabled', false);
            $('input[name="jenisRawat"]').prop('disabled', false);
            $('#kelasRawatSelect').prop('disabled', false);
            $('#dpjpInput').prop('readonly', false);
            $('#noPeserta').prop('readonly', false);
            $('#noSEP').prop('readonly', false);
            $('#tanggalMasuk').prop('readonly', false);
            $('#tanggalPulang').prop('readonly', false);
            $('#caraMasukSelect').prop('disabled', false);
            $('#kodeTarifSelect').prop('disabled', false);
            $('#adlSubAcute').prop('readonly', false);
            $('#adlChronic').prop('readonly', false);
            $('#caraPulangSelect').prop('disabled', false);
            
            // Re-enable cost inputs
            $('.cost-input').prop('readonly', false);
            
            // Re-enable diagnosis and procedure selects
            $('#diagnosisSelect').prop('disabled', false);
            $('#procedureSelect').prop('disabled', false);
            
            // Remove visual indication that form is read-only
            $('.form-control-plaintext').addClass('form-control form-select');
            $('.form-control-plaintext').removeClass('form-control-plaintext');
        }
        
        function saveDataToLocalDatabase(nomorSep, callback = null) {
            console.log('Saving data to local database for nomor_sep:', nomorSep);
            
            // Collect diagnosis data with improved selectors
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
                        validcode: parseInt(row.attr('data-validcode')) || 1,
                        accpdx: row.attr('data-accpdx') || 'Y',
                        asterisk: parseInt(row.attr('data-asterisk')) || 0,
                        im: 0
                    });
                }
            });
            
            // Collect procedure data with improved selectors
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
                        validcode: parseInt(row.attr('data-validcode')) || 1,
                        accpdx: row.attr('data-accpdx') || 'Y',
                        asterisk: parseInt(row.attr('data-asterisk')) || 0,
                        im: 0
                    });
                }
            });
            
            // Collect clinical data (sistole, diastole)
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
            
            // Get patient ID from URL
            const urlParams = new URLSearchParams(window.location.search);
            const patientId = urlParams.get('patient_id');
            
            if (!patientId) {
                console.error('Patient ID not found in URL');
                hideProcessingMessage();
                showErrorMessage('ID Pasien tidak ditemukan');
                return;
            }
            
            // Prepare data for API
            const saveData = {
                patient_id: parseInt(patientId),
                jaminan_cara_bayar: $('#jaminanSelect').val(),
                jenis_rawat: $('input[name="jenisRawat"]:checked').val(),
                nama_dokter: $('#dpjpInput').val(),
                nomor_kartu: $('#noPeserta').val(),
                nomor_sep: $('#noSEP').val(),
                tgl_masuk: $('#tanggalMasuk').val(),
                tgl_pulang: $('input[name="jenisRawat"]:checked').val() === '2' ? $('#tanggalMasuk').val() : $('#tanggalPulang').val(),
                cara_masuk: $('#caraMasukSelect').val(),
                kode_tarif: $('#kodeTarifSelect').val(),
                adl_sub_acute: parseInt($('#adlSubAcute').val()) || 0,
                adl_chronic: parseInt($('#adlChronic').val()) || 0,
                discharge_status: String($('#caraPulangSelect').val()),
                kelas_rawat: $('#kelasRawatSelect').val(),
                covid19_status_cd: '0',
                tb_status: $('#pasienTB').val() || '0',
                diagnosis: diagnosisData.map(d => ({
                    diagnosis_type: d.diagnosis_type,
                    icd_code: d.icd_code,
                    icd_description: d.icd_description,
                    icd_code_id: d.icd_code_id,
                    diagnosis_order: d.diagnosis_order,
                    validcode: d.validcode,
                    accpdx: d.accpdx,
                    asterisk: d.asterisk,
                    im: d.im
                })),
                procedures: procedureData.map(p => ({
                    procedure_type: p.procedure_type,
                    icd_code: p.icd_code,
                    icd_description: p.icd_description,
                    icd_code_id: p.icd_code_id,
                    procedure_order: p.procedure_order,
                    quantity: p.quantity,
                    validcode: p.validcode,
                    accpdx: p.accpdx,
                    asterisk: p.asterisk,
                    im: p.im
                })),
                detail_tarif: detailTarif,
                clinical_data: clinicalData
            };
            
            console.log('Data yang akan disimpan:', saveData);
            
            // Save data to local database
            $.ajax({
                url: 'api/save_all_coding_data.php',
                method: 'POST',
                data: JSON.stringify(saveData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Save response:', response);
                    
                    if (response.success) {
                        console.log('Data saved successfully');
                        
                        // Use callback if provided, otherwise use default E-Klaim flow
                        if (callback && typeof callback === 'function') {
                            callback(nomorSep);
                        } else {
                            console.log('Proceeding with E-Klaim...');
                            performEklaimProcess(nomorSep);
                        }
                    } else {
                        hideProcessingMessage();
                        showErrorMessage('Gagal menyimpan data: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error saving data:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error menyimpan data: ' + error);
                }
            });
        }
        
        function performEklaimProcess(nomorSep) {
            console.log('Step 1: Performing E-Klaim process for nomor_sep:', nomorSep);
            
            // Step 1: Set Claim Data
            console.log('Step 1: Setting claim data...');
            setClaimData(nomorSep);
        }
        
        function setClaimData(nomorSep) {
            console.log('Step 2: Setting claim data for nomor_sep:', nomorSep);
            
            // Collect claim data
            const claimData = {
                nomor_kartu: $('#noPeserta').val(),
                tgl_masuk: $('#tanggalMasuk').val(),
                tgl_pulang: $('input[name="jenisRawat"]:checked').val() === '2' ? $('#tanggalMasuk').val() : $('#tanggalPulang').val(),
                cara_masuk: $('#caraMasukSelect').val(),
                jenis_rawat: $('input[name="jenisRawat"]:checked').val(),
                kelas_rawat: $('#kelasRawatSelect').val(),
                discharge_status: String($('#caraPulangSelect').val()),
                adl_sub_acute: parseInt($('#adlSubAcute').val()) || 0,
                adl_chronic: parseInt($('#adlChronic').val()) || 0,
                nama_dokter: $('#dpjpInput').val(),
                kode_tarif: $('#kodeTarifSelect').val(),
                tarif_rs: {
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
                    sewa_alat: parseCurrencyValue($('#sewaAlat').val())
                }
            };
            
            console.log('Claim data:', claimData);
            
            // Call setClaimData API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'setClaimData',
                    nomor_sep: nomorSep,
                    claim_data: claimData
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Set claim data response:', response);
                    
                    if (response.success) {
                        console.log('Claim data set successfully, proceeding with IDRG methods...');
                        // Proceed with IDRG methods
                        setIdrgMethods(nomorSep);
                    } else {
                        hideProcessingMessage();
                        showErrorMessage('Gagal set claim data: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error setting claim data:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error set claim data: ' + error);
                }
            });
        }
        
        function setIdrgMethods(nomorSep) {
            console.log('Step 3: Setting IDRG methods for nomor_sep:', nomorSep);
            
            // Collect diagnosis data
            const diagnosisData = [];
            $('#diagnosisTableBody tr').not('.empty-table').each(function() {
                const row = $(this);
                diagnosisData.push({
                    diagnosis_type: row.find('select').val(),
                    icd_code: row.find('td:eq(1)').text(),
                    icd_description: row.find('td:eq(2)').text(),
                    icd_code_id: row.find('td:eq(1)').data('id') || 1,
                    diagnosis_order: row.index() + 1,
                    validcode: 1,
                    accpdx: 'Y',
                    asterisk: 0,
                    im: 0
                });
            });
            
            // Collect procedure data
            const procedureData = [];
            $('#procedureTableBody tr').not('.empty-table').each(function() {
                const row = $(this);
                procedureData.push({
                    procedure_type: row.find('select').val(),
                    icd_code: row.find('td:eq(1)').text(),
                    icd_description: row.find('td:eq(2)').text(),
                    quantity: parseInt(row.find('input[type="number"]').val()) || 1,
                    icd_code_id: row.find('td:eq(1)').data('id') || 1,
                    procedure_order: row.index() + 1,
                    validcode: 1,
                    accpdx: 'Y',
                    asterisk: 0,
                    im: 0
                });
            });
            
            console.log('IDRG methods data:', { diagnosisData, procedureData });
            
            // Call setIdrgMethods API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'setIdrgMethods',
                    nomor_sep: nomorSep,
                    diagnosis: diagnosisData,
                    procedures: procedureData
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Set IDRG methods response:', response);
                    
                    if (response.success) {
                        console.log('IDRG methods set successfully, proceeding with IDRG grouper...');
                        // Proceed with IDRG grouper
                        performIdrgGrouper(nomorSep);
                    } else {
                        hideProcessingMessage();
                        showErrorMessage('Gagal set IDRG methods: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error setting IDRG methods:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error set IDRG methods: ' + error);
                }
            });
        }
        
        function performIdrgGrouper(nomorSep) {
            console.log('Step 4: Performing IDRG grouper for nomor_sep:', nomorSep);
            
            // Call IDRG grouper API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'idrg_grouper',
                    nomor_sep: nomorSep,
                    force_api_call: true  // Force API call, skip cache
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('IDRG grouper response:', response);
                    
                    if (response.success) {
                        console.log('IDRG grouper successful');
                        
                        // Display grouping results
                        if (response.data && response.data.response_idrg) {
                            displayGroupingResults(response.data.response_idrg);
                        }
                        
                        // Show Final iDRG button if result is valid
                        const mdcNumber = response.data?.response_idrg?.mdc_number;
                        const drgCode = response.data?.response_idrg?.drg_code;
                        const validMdcCodes = ['21', '31'];
                        const isValidResult = validMdcCodes.includes(mdcNumber) && drgCode && validMdcCodes.some(code => drgCode.startsWith(code));
                        
                        if (isValidResult) {
                            $('#finalDrgSection').show();
                        } else {
                            $('#finalDrgSection').hide();
                        }
                        
                        hideProcessingMessage();
                        showSuccessMessage('Grouping iDRG berhasil!');
                        
                    } else {
                        hideProcessingMessage();
                        showErrorMessage('Error IDRG grouper: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error IDRG grouper:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error IDRG grouper: ' + error);
                }
            });
        }
        
        function updateFinalIdrgStatus() {
            // Create a mock finalIdrgRecord for current completion
            const finalIdrgRecord = {
                completed_at: new Date().toISOString()
            };
            
            // Apply final iDRG status using the optimized function
            applyFinalIdrgStatus(finalIdrgRecord);
            
            // Update status indicator untuk menunjukkan final iDRG
            const statusIndicator = $('#groupingStatusIndicator');
            statusIndicator.removeClass('alert-warning alert-danger').addClass('alert-success');
            statusIndicator.html(`
                <i class="fas fa-check-circle me-2"></i>
                <strong>Final iDRG:</strong> Berhasil diproses pada ${new Date().toLocaleString('id-ID')}
            `);
        }
        
        function processIDRG() {
            // Validasi: Pastikan minimal ada 1 diagnosa
            const diagnosisCount = $('#diagnosisTableBody tr').not('.empty-table').length;
            if (diagnosisCount === 0) {
                hideProcessingMessage();
                showErrorMessage('Proses grouping tidak dapat dilakukan. Minimal harus ada 1 record diagnosa pada grid Diagnosa (ICD-10-IM).');
                return;
            }
            
            console.log('Validasi diagnosa berhasil:', diagnosisCount, 'diagnosa ditemukan');
            
            // Show processing message
            showProcessingMessage('Sedang memproses Grouping IDRG...');
            
            // Get nomor SEP from form
            const nomorSep = $('#noSEP').val();
            
            console.log('Nomor SEP yang diambil:', nomorSep);
            
            if (!nomorSep) {
                hideProcessingMessage();
                showErrorMessage('Nomor SEP tidak ditemukan');
                return;
            }
            
            // Step 0: Save data to local database first
            console.log('Step 0: Saving data to local database...');
            saveDataToLocalDatabase(nomorSep, setClaimDataForGrouping);
        }
        
        
        function setClaimDataForGrouping(nomorSep) {
            console.log('Step 1: Setting claim data...');
            
            // Collect data untuk E-Klaim setClaimData
            const claimData = {
                nomor_kartu: $('#noPeserta').val(),
                tgl_masuk: formatDateTime($('#tanggalMasuk').val()),
                tgl_pulang: $('input[name="jenisRawat"]:checked').val() === '2' ? formatDateTime($('#tanggalMasuk').val()) : formatDateTime($('#tanggalPulang').val()),
                cara_masuk: $('#caraMasukSelect').val(),
                jenis_rawat: $('input[name="jenisRawat"]:checked').val(),
                kelas_rawat: $('#kelasRawatSelect').val(),
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
                    prosedur_non_bedah: parseCurrencyValue($('#prosedurNonBedah').val()) || '0',
                    prosedur_bedah: parseCurrencyValue($('#prosedurBedah').val()) || '0',
                    konsultasi: parseCurrencyValue($('#konsultasi').val()) || '0',
                    tenaga_ahli: parseCurrencyValue($('#tenagaAhli').val()) || '0',
                    keperawatan: parseCurrencyValue($('#keperawatan').val()) || '0',
                    penunjang: parseCurrencyValue($('#penunjang').val()) || '0',
                    radiologi: parseCurrencyValue($('#radiologi').val()) || '0',
                    laboratorium: parseCurrencyValue($('#laboratorium').val()) || '0',
                    pelayanan_darah: parseCurrencyValue($('#pelayananDarah').val()) || '0',
                    rehabilitasi: parseCurrencyValue($('#rehabilitasi').val()) || '0',
                    kamar: parseCurrencyValue($('#kamarAkomodasi').val()) || '0',
                    rawat_intensif: parseCurrencyValue($('#rawatIntensif').val()) || '0',
                    obat: parseCurrencyValue($('#obat').val()) || '0',
                    obat_kronis: parseCurrencyValue($('#obatKronis').val()) || '0',
                    obat_kemoterapi: parseCurrencyValue($('#obatKemoterapi').val()) || '0',
                    alkes: parseCurrencyValue($('#alkes').val()) || '0',
                    bmhp: parseCurrencyValue($('#bmhp').val()) || '0',
                    sewa_alat: parseCurrencyValue($('#sewaAlat').val()) || '0'
                },
                pemulasaraan_jenazah: '0',
                kantong_jenazah: '0',
                peti_jenazah: '0',
                plastik_erat: '0',
                desinfektan_jenazah: '0',
                mobil_jenazah: '0',
                desinfektan_mobil_jenazah: '0',
                covid19_status_cd: '0',
                tb_status: $('#pasienTB').val() || '0',
                nomor_kartu_t: 'nik',
                episodes: '1;12#2;3#6;5',
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
                cob_cd: $('#cobSelect').val() === 'COB' ? 1 : '#',
                coder_nik: '<?php echo EKLAIM_CODER_NIK; ?>' // Menggunakan konstanta dari eklaim_config.php
            };
            
            console.log('Claim data yang akan dikirim:', claimData);
            
            // Use existing eklaim_config.php function via API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'setClaimData',
                    nomor_sep: nomorSep,
                    claim_data: claimData
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Set Claim Data response:', response);
                    
                    if (response.success === true || response.success === 'true') {
                        console.log('Set Claim Data berhasil, melanjutkan ke Step 2...');
                        // Step 2: Set IDRG Diagnosa
                        setIdrgDiagnosaForGrouping(nomorSep);
                    } else {
                        console.log('Set Claim Data gagal:', response);
                        hideProcessingMessage();
                        showErrorMessage('Error setting claim data: ' + (response.error || response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error setting claim data:', {xhr, status, error});
                    hideProcessingMessage();
                    showErrorMessage('Error setting claim data: ' + error);
                }
            });
        }
        
        function setIdrgDiagnosaForGrouping(nomorSep) {
            console.log('Step 2: Setting IDRG diagnosa...');
            
            // Collect diagnosis data
            const diagnosisData = [];
            $('#diagnosisTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const icdCode = row.find('.code-badge').text().trim();
                    
                    if (icdCode && icdCode !== '-') {
                        diagnosisData.push({
                            code: icdCode
                        });
                    }
                }
            });
            
            // Format diagnosa string (ICD10#ICD10#ICD10)
            const diagnosaString = diagnosisData.map(d => d.code).join('#');
            
            console.log('Diagnosa string:', diagnosaString);
            
            if (!diagnosaString) {
                hideProcessingMessage();
                showErrorMessage('Tidak ada diagnosa yang ditemukan');
                return;
            }
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'setIdrgDiagnosa',
                    nomor_sep: nomorSep,
                    diagnosa: diagnosaString
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Set IDRG Diagnosa response:', response);
                    console.log('Response success status:', response.success);
                    
                    if (response.success === true || response.success === 'true') {
                        console.log('Set IDRG Diagnosa berhasil, melanjutkan ke Step 3...');
                        // Step 3: Set IDRG Procedure
                        setIdrgProcedureForGrouping(nomorSep);
                    } else {
                        console.log('Set IDRG Diagnosa gagal:', response);
                        hideProcessingMessage();
                        showErrorMessage('Error setting IDRG diagnosa: ' + (response.error || response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error setting IDRG diagnosa:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error setting IDRG diagnosa: ' + error);
                }
            });
        }
        
        function setIdrgProcedureForGrouping(nomorSep) {
            console.log('Step 3: Setting IDRG procedure...');
            
            // Collect procedure data
            const procedureData = [];
            $('#procedureTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const icdCode = row.find('.code-badge').text().trim();
                    const multiplier = row.find('.quantity-input').val() || '1';
                    
                    if (icdCode && icdCode !== '-') {
                        procedureData.push({
                            code: icdCode,
                            multiplier: multiplier
                        });
                    }
                }
            });
            
            console.log('Procedure data collected:', procedureData.length, 'procedures found');
            
            // Format procedure string (ICD9#ICD9+multiplier#ICD9)
            let procedureString = '';
            if (procedureData.length === 0) {
                // Jika tidak ada prosedur, kirim "#" sesuai format E-Klaim
                procedureString = '#';
                console.log('Tidak ada prosedur ditemukan, kirim procedure "#"');
            } else {
                procedureString = procedureData.map(p => {
                    if (p.multiplier && p.multiplier !== '1') {
                        return `${p.code}+${p.multiplier}`;
                    }
                    return p.code;
                }).join('#');
                console.log('Procedure string:', procedureString);
            }
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'setIdrgProcedure',
                    nomor_sep: nomorSep,
                    procedure: procedureString
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Set IDRG Procedure response:', response);
                    console.log('Response success status:', response.success);
                    
                    if (response.success === true || response.success === 'true') {
                        console.log('Set IDRG Procedure berhasil, melanjutkan ke Step 4...');
                        // Step 4: Perform Grouping
                        performGrouping(nomorSep);
                    } else {
                        console.log('Set IDRG Procedure gagal:', response);
                        hideProcessingMessage();
                        showErrorMessage('Error setting IDRG procedure: ' + (response.error || response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error setting IDRG procedure:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error setting IDRG procedure: ' + error);
                }
            });
        }
        
        function processINACBG() {
            // Validasi: Pastikan minimal ada 1 diagnosa
            const diagnosisCount = $('#diagnosisTableBody tr').not('.empty-table').length;
            if (diagnosisCount === 0) {
                hideProcessingMessage();
                showErrorMessage('Proses grouping INACBG tidak dapat dilakukan. Minimal harus ada 1 record diagnosa pada grid Diagnosa (ICD-10-IM).');
                return;
            }
            
            console.log('Validasi diagnosa berhasil:', diagnosisCount, 'diagnosa ditemukan');
            
            // Show processing message
            showProcessingMessage('Sedang memproses Grouping INACBG...');
            
            // Get nomor SEP from form
            const nomorSep = $('#noSEP').val();
            
            console.log('Nomor SEP yang diambil:', nomorSep);
            
            if (!nomorSep) {
                hideProcessingMessage();
                showErrorMessage('Nomor SEP tidak ditemukan');
                return;
            }
            
            // Step 0: Save data to local database first
            console.log('Step 0: Saving data to local database...');
            saveDataToLocalDatabase(nomorSep, setInacbgDiagnosaForGrouping);
        }
        
        function saveInacbgDataToImportTables(nomorSep) {
            console.log('Saving INACBG data to import tables...');
            
            // Collect diagnosis data from INACBG table
            const diagnosisData = [];
            $('#inacbgDiagnosisTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const icdCode = row.find('.code-badge').text().trim();
                    const description = row.find('td:eq(1)').text().trim();
                    
                    if (icdCode && icdCode !== '-') {
                        diagnosisData.push({
                            diagnosis_type: '1', // Default primer
                            icd_code: icdCode,
                            icd_description: description || icdCode
                        });
                    }
                }
            });
            
            // Collect procedure data from INACBG table
            const procedureData = [];
            $('#inacbgProcedureTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const icdCode = row.find('.code-badge').text().trim();
                    const description = row.find('td:eq(1)').text().trim();
                    const quantity = parseInt(row.find('td:eq(2)').text().trim()) || 1;
                    
                    if (icdCode && icdCode !== '-') {
                        procedureData.push({
                            procedure_type: '1', // Default primer
                            icd_code: icdCode,
                            icd_description: description || icdCode,
                            quantity: quantity
                        });
                    }
                }
            });
            
            console.log('Collected diagnosis data:', diagnosisData.length);
            console.log('Collected procedure data:', procedureData.length);
            
            // Call API to save to import tables
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'save_inacbg_to_import',
                    nomor_sep: nomorSep,
                    diagnosis: diagnosisData,
                    procedure: procedureData
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Save INACBG to import response:', response);
                    
                    if (response.success) {
                        console.log('INACBG data saved to import tables successfully');
                    } else {
                        console.log('Failed to save INACBG data to import tables:', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error saving INACBG data to import tables:', error);
                }
            });
        }
        
        function setInacbgDiagnosaForGrouping(nomorSep) {
            console.log('Step 1: Setting INACBG diagnosa...');
            
            // Collect diagnosis data from INACBG table
            const diagnosisData = [];
            $('#inacbgDiagnosisTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const icdCode = row.find('.code-badge').text().trim();
                    
                    if (icdCode && icdCode !== '-') {
                        diagnosisData.push({
                            code: icdCode
                        });
                    }
                }
            });
            
            // Format diagnosa string (ICD10#ICD10#ICD10)
            const diagnosaString = diagnosisData.map(d => d.code).join('#');
            
            console.log('INACBG Diagnosa string:', diagnosaString);
            
            if (!diagnosaString) {
                hideProcessingMessage();
                showErrorMessage('Tidak ada diagnosa yang ditemukan');
                return;
            }
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'setInacbgDiagnosa',
                    nomor_sep: nomorSep,
                    diagnosa: diagnosaString
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Set INACBG Diagnosa response:', response);
                    console.log('Response success status:', response.success);
                    
                    if (response.success === true || response.success === 'true') {
                        console.log('Set INACBG Diagnosa berhasil, melanjutkan ke Step 2...');
                        // Save INACBG data to import tables
                        saveInacbgDataToImportTables(nomorSep);
                        // Step 2: Set INACBG Procedure
                        setInacbgProcedureForGrouping(nomorSep);
                    } else {
                        console.log('Set INACBG Diagnosa gagal:', response);
                        hideProcessingMessage();
                        showErrorMessage('Error setting INACBG diagnosa: ' + (response.error || response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error setting INACBG diagnosa:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error setting INACBG diagnosa: ' + error);
                }
            });
        }
        
        function setInacbgProcedureForGrouping(nomorSep) {
            console.log('Step 2: Setting INACBG procedure...');
            
            // Collect procedure data from INACBG table
            const procedureData = [];
            $('#inacbgProcedureTableBody tr').each(function() {
                const row = $(this);
                if (!row.hasClass('empty-table')) {
                    const icdCode = row.find('.code-badge').text().trim();
                    
                    if (icdCode && icdCode !== '-') {
                        procedureData.push({
                            code: icdCode
                        });
                    }
                }
            });
            
            console.log('INACBG Procedure data collected:', procedureData.length, 'procedures found');
            
            // Format procedure string (ICD9#ICD9#ICD9) - INACBG tidak menggunakan multiplier
            let procedureString = '';
            if (procedureData.length === 0) {
                // Jika tidak ada prosedur, kirim "#" sesuai format E-Klaim
                procedureString = '#';
                console.log('Tidak ada prosedur ditemukan, kirim procedure "#"');
            } else {
                procedureString = procedureData.map(p => p.code).join('#');
                console.log('INACBG Procedure string:', procedureString);
            }
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'setInacbgProcedure',
                    nomor_sep: nomorSep,
                    procedure: procedureString
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Set INACBG Procedure response:', response);
                    console.log('Response success status:', response.success);
                    
                    if (response.success === true || response.success === 'true') {
                        console.log('Set INACBG Procedure berhasil, melanjutkan ke Step 3...');
                        // Save INACBG data to import tables (update with procedure data)
                        saveInacbgDataToImportTables(nomorSep);
                        // Step 3: Perform INACBG Grouping
                        performInacbgGrouping(nomorSep);
                    } else {
                        console.log('Set INACBG Procedure gagal:', response);
                        hideProcessingMessage();
                        showErrorMessage('Error setting INACBG procedure: ' + (response.error || response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error setting INACBG procedure:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error setting INACBG procedure: ' + error);
                }
            });
        }
        
        function performInacbgGrouping(nomorSep) {
            console.log('Step 3: Performing INACBG grouping...');
            
            // Prepare request data
            const requestData = {
                action: 'grouper',
                nomor_sep: nomorSep,
                stage: '1',
                grouper: 'inacbg'
            };
            
            console.log('INACBG Grouping request data:', requestData);
            
            // Call API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify(requestData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('INACBG Grouping response:', response);
                    console.log('Response success status:', response.success);
                    hideProcessingMessage();
                    
                    if (response.success === true || response.success === 'true') {
                        console.log('INACBG Grouping berhasil, menampilkan hasil...');
                        // Display INACBG grouping results
                        displayInacbgGroupingResults(response.data);
                    } else {
                        console.log('INACBG Grouping gagal:', response);
                        showErrorMessage('Error performing INACBG grouping: ' + (response.error || response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error performing INACBG grouping:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error performing INACBG grouping: ' + error);
                }
            });
        }
        
        function performGrouping(nomorSep) {
            console.log('Step 4: Performing grouping...');
            
            // Prepare request data
            const requestData = {
                    action: 'grouper',
                nomor_sep: nomorSep,
                stage: '1',
                grouper: 'idrg',
                force_api_call: true  // Force API call, skip cache
            };
            
            console.log('Grouping request data:', requestData);
            
            // Call API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify(requestData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Grouping response:', response);
                    console.log('Response success status:', response.success);
                    hideProcessingMessage();
                    
                    if (response.success === true || response.success === 'true') {
                        console.log('Grouping berhasil, menampilkan hasil...');
                        
                        // Display grouping results
                        // Handle different response data structures
                        let responseIdrgData = null;
                        
                        if (response.data && response.data.response_idrg) {
                            // Standard structure: {data: {response_idrg: {...}}}
                            responseIdrgData = response.data.response_idrg;
                            console.log('Using standard structure: response.data.response_idrg');
                        } else if (response.data && typeof response.data === 'object') {
                            // Check if response.data itself contains response_idrg properties
                            if (response.data.mdc_description || response.data.mdc_number || 
                                response.data.drg_description || response.data.drg_code) {
                                responseIdrgData = response.data;
                                console.log('Using direct structure: response.data contains response_idrg properties');
                            } else {
                                console.error('Invalid response data structure:', response.data);
                                console.error('Available keys in response.data:', Object.keys(response.data || {}));
                                showErrorMessage('Invalid response data structure - no response_idrg found');
                                return;
                            }
                        } else {
                            console.error('No response.data found:', response);
                            showErrorMessage('No response data found');
                            return;
                        }
                        
                        // Display grouping results
                        displayGroupingResults(responseIdrgData);
                    } else {
                        console.log('Grouping gagal:', response);
                        showErrorMessage('Error grouping: ' + (response.error || response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error grouping:', {xhr, status, error});
                    console.log('Response text:', xhr.responseText);
                    hideProcessingMessage();
                    showErrorMessage('Error grouping: ' + error);
                }
            });
        }
        
        function displayInacbgGroupingResults(data) {
            // Show the results section
            $('#groupingResults').show();
            
            // Populate basic information untuk INACBG
            $('#groupingInfo').text('INACBG @ ' + new Date().toLocaleString('id-ID') + ' - 1.0.24 / 0.2.1664.202505111134');
            
            // Set jenis rawat berdasarkan data form
            const jenisRawat = $('input[name="jenisRawat"]:checked').val();
            const tglMasuk = $('#tanggalMasuk').val();
            const tglPulang = $('#tanggalPulang').val();
            
            let jenisRawatText = '';
            if (jenisRawat === '1') {
                // Rawat Inap - hitung LOS
                if (tglMasuk && tglPulang) {
                    const los = calculateLOSFromDates(tglMasuk, tglPulang);
                    jenisRawatText = `Rawat Inap (${los} Hari)`;
                } else {
                    jenisRawatText = 'Rawat Inap';
                }
            } else if (jenisRawat === '2') {
                jenisRawatText = 'Rawat Jalan';
            } else {
                jenisRawatText = 'Tidak Diketahui';
            }
            
            $('#groupingJenisRawat').text(jenisRawatText);
            $('#groupingStatus').text('normal');
            
            // Populate CMG dan CMG Code dari response_inacbg
            const cmgDescription = data.response_inacbg?.cmg_description || 'N/A';
            const cmgCode = data.response_inacbg?.cmg_code || 'N/A';
            const cmgNumber = data.response_inacbg?.cmg_number || 'N/A';
            const cmgWeight = data.response_inacbg?.cmg_weight || 'N/A';
            
            const mdcElement = $('#groupingMDC');
            const drgElement = $('#groupingDRG');
            const mdcNumberElement = $('#groupingMDCNumber');
            const drgCodeElement = $('#groupingDRGCode');
            
            // Update labels untuk INACBG
            $('#groupingMDCLabel').text('CMG Description:');
            $('#groupingDRGLabel').text('CMG Code:');
            $('#groupingMDCNumberLabel').text('CMG Number:');
            $('#groupingDRGCodeLabel').text('CMG Weight:');
            
            mdcElement.text(cmgDescription);
            drgElement.text(cmgCode);
            mdcNumberElement.text(cmgNumber);
            drgCodeElement.text(cmgWeight);
            
            // Untuk INACBG, tidak ada error detection seperti iDRG
            // Semua hasil dianggap valid
            mdcElement.removeClass('text-danger fw-bold');
            drgElement.removeClass('text-danger fw-bold');
            mdcNumberElement.removeClass('text-danger');
            drgCodeElement.removeClass('text-danger');
            
            // Hide error section dan final DRG button untuk INACBG
            $('#groupingErrorSection').hide();
            $('#finalDrgSection').hide();
            
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#groupingResults').offset().top - 100
            }, 500);
        }
        
        function displayGroupingResults(data) {
            // Show the results section
            $('#groupingResults').show();
            
            // Populate basic information sesuai lampiran
            $('#groupingInfo').text('INACBG @ 5 Sep 2025 22:28 - 1.0.24 / 0.2.1664.202505111134');
            
            // Set jenis rawat berdasarkan data form
            const jenisRawat = $('input[name="jenisRawat"]:checked').val();
            const tglMasuk = $('#tanggalMasuk').val();
            const tglPulang = $('#tanggalPulang').val();
            
            let jenisRawatText = '';
            if (jenisRawat === '1') {
                // Rawat Inap - hitung LOS
                if (tglMasuk && tglPulang) {
                    const los = calculateLOSFromDates(tglMasuk, tglPulang);
                    jenisRawatText = `Rawat Inap (${los} Hari)`;
                } else {
                    jenisRawatText = 'Rawat Inap';
                }
            } else if (jenisRawat === '2') {
                jenisRawatText = 'Rawat Jalan';
            } else {
                jenisRawatText = 'Tidak Diketahui';
            }
            
            $('#groupingJenisRawat').text(jenisRawatText);
            $('#groupingStatus').text('normal');
            
            // Populate MDC dan DRG dari response_idrg (data sekarang langsung response_idrg object)
            const mdcDescription = data?.mdc_description || 'N/A';
            const mdcNumber = data?.mdc_number || 'N/A';
            const drgDescription = data?.drg_description || 'N/A';
            const drgCode = data?.drg_code || 'N/A';
            
            const mdcElement = $('#groupingMDC');
            const drgElement = $('#groupingDRG');
            const mdcNumberElement = $('#groupingMDCNumber');
            const drgCodeElement = $('#groupingDRGCode');
            
            // Reset labels untuk iDRG
            $('#groupingMDCLabel').text('MDC Description:');
            $('#groupingDRGLabel').text('DRG Description:');
            $('#groupingMDCNumberLabel').text('MDC Number:');
            $('#groupingDRGCodeLabel').text('DRG Code:');
            
            mdcElement.text(mdcDescription);
            drgElement.text(drgDescription);
            mdcNumberElement.text(mdcNumber);
            drgCodeElement.text(drgCode);
            
            // Deteksi error menggunakan helper function
            const isError = isGroupingError(mdcDescription, drgDescription, mdcNumber, drgCode);
            
            // Apply error styling
            applyErrorStyling(mdcElement, drgElement, mdcNumberElement, drgCodeElement, isError);
            
            // Tentukan section yang akan ditampilkan berdasarkan validitas hasil
            const validMdcCodes = ['21', '31'];
            const isValidResult = validMdcCodes.includes(mdcNumber) && drgCode && validMdcCodes.some(code => drgCode.startsWith(code));
            const showErrorSection = !isValidResult;
            const showFinalDrgButton = isValidResult;
            
            // Show/hide sections menggunakan helper function
            toggleGroupingSections(showErrorSection, showFinalDrgButton);
            
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#groupingResults').offset().top - 100
            }, 500);
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
        
        function hideProcessingMessage() {
            $('.alert-info').fadeOut(300, function() {
                $(this).remove();
            });
        }
        
        function showErrorMessage(message) {
            const notification = `
                <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(notification);
            
            setTimeout(function() {
                $('.alert-danger').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
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
                 procedureStatus.show().addClass('valid');
                 procedureStatus.find('i').removeClass('fa-exclamation-triangle text-warning').addClass('fa-check-circle text-success');
                 procedureStatus.find('small').text(`${procedureCount} record(s)`).removeClass('text-warning').addClass('text-success');
         }
         
         // Event listeners for table changes
         // $(document).on('DOMNodeInserted DOMNodeRemoved', '#diagnosisTableBody, #procedureTableBody', function() {
         //     updateValidationStatus();
         // });
         
         // Initial validation status update
         $(document).ready(function() {
             updateValidationStatus();
         });
         
         function createInacbgSection() {
             // Create INACBG section HTML
             const inacbgHtml = `
                 <div id="inacbgSection" class="row mt-4" style="display: none;">
                     <div class="col-12">
                         <div class="card">
                             <div class="card-header bg-info text-white">
                                 <h5 class="card-title mb-0">
                                     <i class="fas fa-hospital me-2"></i>
                                     INACBG Coding
                                 </h5>
                             </div>
                             <div class="card-body">
                                 <div class="row">
                                     <!-- INACBG Diagnosa Section -->
                                     <div class="col-md-6">
                                         <div class="section-container">
                                             <h6 class="section-title">
                                                 <i class="fas fa-stethoscope me-2"></i>
                                                 Diagnosa INACBG (ICD-10CM)
                                             </h6>
                                             
                                             <div class="search-row">
                                                 <label class="search-label">Diagnosa:</label>
                                                 <div class="search-input">
                                                     <select class="form-control" id="inacbgDiagnosisSelect" style="width: 100%;">
                                                         <option></option>
                                                     </select>
                                                 </div>
                                             </div>
                                             
                                             <div class="table-responsive">
                                                 <table class="table table-sm table-bordered">
                                                     <thead>
                                                         <tr>
                                                             <th width="20%">Jenis</th>
                                                             <th width="25%">Kode</th>
                                                             <th width="45%">Deskripsi</th>
                                                             <th width="10%">Aksi</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody id="inacbgDiagnosisTableBody">
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

                                     <!-- INACBG Prosedur Section -->
                                     <div class="col-md-6">
                                         <div class="section-container">
                                             <h6 class="section-title">
                                                 <i class="fas fa-procedures me-2"></i>
                                                 Prosedur INACBG (ICD-9CM)
                                             </h6>
                                             
                                             <div class="search-row">
                                                 <label class="search-label">Prosedur:</label>
                                                 <div class="search-input">
                                                     <select class="form-control" id="inacbgProcedureSelect" style="width: 100%;">
                                                         <option></option>
                                                     </select>
                                                 </div>
                                             </div>
                                             
                                             <div class="table-responsive">
                                                 <table class="table table-sm table-bordered">
                                                     <thead>
                                                         <tr>
                                                             <th width="25%">Jenis</th>
                                                             <th width="25%">Kode</th>
                                                             <th width="45%">Deskripsi</th>
                                                             <th width="5%">Aksi</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody id="inacbgProcedureTableBody">
                                                         <tr>
                                                             <td colspan="4" class="empty-table">
                                                                 <i class="fas fa-clipboard-list"></i>
                                                                 <p>Belum ada prosedur yang dipilih</p>
                                                             </td>
                                                         </tr>
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <!-- INACBG Action Buttons -->
                                 <div class="row mt-3">
                                     <div class="col-12 text-center">
                                         <button class="btn btn-warning me-2" id="importCodingBtn">
                                             <i class="fas fa-download me-1"></i>
                                             Import Coding
                                         </button>
                                         <button class="btn btn-success me-2" id="inacbgGrouperBtn">
                                             <i class="fas fa-hospital me-1"></i>
                                             Grouping INACBG
                                         </button>
                                         <button class="btn btn-success" id="inacbgFinalBtn" style="display: none;">
                                             <i class="fas fa-check-circle me-1"></i>
                                             Final INACBG
                                         </button>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             `;
             
             // Insert INACBG section after the grouping results
             $('#groupingResults').after(inacbgHtml);
             
             // Initialize INACBG functionality
             initializeInacbgFunctionality();
         }
         
         function initializeInacbgFunctionality() {
             // Initialize INACBG diagnosis select
             $('#inacbgDiagnosisSelect').select2({
                 placeholder: 'Pilih diagnosa INACBG...',
                 allowClear: true,
                 ajax: {
                     url: 'api/get_inacbg_codes.php',
                     dataType: 'json',
                     delay: 250,
                     data: function (params) {
                         return {
                             q: params.term,
                             type: 'diagnosis'
                         };
                     },
                     processResults: function (data) {
                         return {
                             results: data.results
                         };
                     },
                     cache: true
                 }
             });
             
             // Initialize INACBG procedure select
             $('#inacbgProcedureSelect').select2({
                 placeholder: 'Pilih prosedur INACBG...',
                 allowClear: true,
                 ajax: {
                     url: 'api/get_inacbg_codes.php',
                     dataType: 'json',
                     delay: 250,
                     data: function (params) {
                         return {
                             q: params.term,
                             type: 'procedure'
                         };
                     },
                     processResults: function (data) {
                         return {
                             results: data.results
                         };
                     },
                     cache: true
                 }
             });
             
             // Handle INACBG diagnosis selection
             $('#inacbgDiagnosisSelect').on('select2:select', function (e) {
                 const data = e.params.data;
                 addInacbgDiagnosis(data);
                 $(this).val(null).trigger('change');
             });
             
             // Handle INACBG procedure selection
             $('#inacbgProcedureSelect').on('select2:select', function (e) {
                 const data = e.params.data;
                 addInacbgProcedure(data);
                 $(this).val(null).trigger('change');
             });
             
            // Handle import coding button
            $('#importCodingBtn').on('click', function() {
                performImportCoding();
            });
            
            // Handle INACBG grouping button
            $('#inacbgGrouperBtn').on('click', function() {
                processINACBG();
            });
             
         }
         
        function addInacbgDiagnosis(data) {
            // Generate unique ID if not provided
            const uniqueId = data.id || `inacbg_diag_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            addDiagnosisToTable({
                id: uniqueId,
                code: data.code,
                description: data.description
            }, 'inacbgDiagnosisTableBody', true);
        }
        
        function addInacbgProcedure(data) {
            // Generate unique ID if not provided
            const uniqueId = data.id || `inacbg_proc_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            addProcedureToTable({
                id: uniqueId,
                code: data.code,
                description: data.description
            }, 'inacbgProcedureTableBody', true);
        }
         
        function removeInacbgDiagnosis(button) {
            removeTableRow(button, 'inacbgDiagnosisTableBody');
        }
        
        function removeInacbgProcedure(button) {
            removeTableRow(button, 'inacbgProcedureTableBody');
        }
        
        // Fungsi untuk membuat diagnosa INACBG menjadi primary
        function makePrimaryInacbg(id, tableId) {
            const tbody = $(`#${tableId}`);
            const targetRow = tbody.find(`tr[data-id="${id}"]`);
            
            if (targetRow.length > 0) {
                // Move target row to top
                targetRow.prependTo(tbody);
                
                // Update badge labels
                tbody.find('tr').each(function(index) {
                    const badge = $(this).find('.badge');
                    const jenis = index === 0 ? 'Primary' : 'Secondary';
                    const badgeClass = index === 0 ? 'bg-primary' : 'bg-secondary';
                    badge.removeClass('bg-primary bg-secondary').addClass(badgeClass).text(jenis);
                    
                    // Update primary button visibility
                    const primaryBtn = $(this).find('button[onclick*="makePrimaryInacbg"]');
                    if (index === 0) {
                        primaryBtn.hide();
                    } else {
                        primaryBtn.show();
                    }
                });
            }
        }
        
        // Fungsi untuk membuat prosedur INACBG menjadi primary
        function makePrimaryInacbgProcedure(id, tableId) {
            const tbody = $(`#${tableId}`);
            const targetRow = tbody.find(`tr[data-id="${id}"]`);
            
            if (targetRow.length > 0) {
                // Move target row to top
                targetRow.prependTo(tbody);
                
                // Update badge labels
                tbody.find('tr').each(function(index) {
                    const badge = $(this).find('.badge');
                    const jenis = index === 0 ? 'Primary' : 'Secondary';
                    const badgeClass = index === 0 ? 'bg-primary' : 'bg-secondary';
                    badge.removeClass('bg-primary bg-secondary').addClass(badgeClass).text(jenis);
                    
                    // Update primary button visibility
                    const primaryBtn = $(this).find('button[onclick*="makePrimaryInacbgProcedure"]');
                    if (index === 0) {
                        primaryBtn.hide();
                    } else {
                        primaryBtn.show();
                    }
                });
            }
        }
        
        function performImportCoding() {
            // Get nomor SEP
            const nomorSep = $('#noSEP').val();
            
            if (!nomorSep) {
                showErrorMessage('Nomor SEP tidak ditemukan!');
                return;
            }
            
            // Show processing message
            showProcessingMessage('Sedang mengimport coding dari IDRG ke INACBG...');
            
            // Disable button during processing
            $('#importCodingBtn').prop('disabled', true);
            
            // Call API to import coding
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    action: 'idrg_to_inacbg_import',
                    nomor_sep: nomorSep
                }),
                success: function(response) {
                    hideProcessingMessage();
                    $('#importCodingBtn').prop('disabled', false);
                    
                    if (response.success) {
                        // Populate INACBG diagnosis grid
                        if (response.data.diagnosis && response.data.diagnosis.length > 0) {
                            populateInacbgDiagnosis(response.data.diagnosis);
                        }
                        
                        // Populate INACBG procedure grid
                        if (response.data.procedure && response.data.procedure.length > 0) {
                            populateInacbgProcedure(response.data.procedure);
                        }
                        
                        // Tampilkan informasi tentang delete operation
                        let deleteInfo = '';
                        if (response.delete_info) {
                            const deletedLogs = response.delete_info.deleted_log_count || 0;
                            const deletedDiagnosis = response.delete_info.deleted_diagnosis_count || 0;
                            const deletedProcedure = response.delete_info.deleted_procedure_count || 0;
                            
                            if (deletedLogs > 0) {
                                deleteInfo = ` Data lama telah dihapus: ${deletedLogs} log, ${deletedDiagnosis} diagnosa, ${deletedProcedure} prosedur.`;
                            }
                        }
                        
                        showSuccessMessage('Import coding berhasil! Data diagnosa dan prosedur telah dimuat ke grid INACBG.' + deleteInfo);
                    } else {
                        showErrorMessage('Import coding gagal: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    hideProcessingMessage();
                    $('#importCodingBtn').prop('disabled', false);
                    showErrorMessage('Error saat mengimport coding: ' + error);
                }
            });
        }
        
        function populateInacbgDiagnosis(diagnosisData) {
            const tableBody = $('#inacbgDiagnosisTableBody');
            
            // Clear existing data
            tableBody.empty();
            
            // Extract codes for validation
            const codes = diagnosisData.map(d => d.icd_code);
            
            // Check codes in database
            $.ajax({
                url: 'api/check_inacbg_codes.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ codes: codes }),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Add diagnosis data with validation
                        diagnosisData.forEach(function(diagnosis, index) {
                            const isValid = response.data[diagnosis.icd_code] || false;
                            addDiagnosisToTable({
                                id: `inacbg_diag_${index}`,
                                code: diagnosis.icd_code,
                                description: diagnosis.icd_description,
                                isValid: isValid
                            }, 'inacbgDiagnosisTableBody', true);
                        });
                        
                        // Add empty row if no data
                        if (diagnosisData.length === 0) {
                            tableBody.append(`
                                <tr class="empty-table">
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Belum ada diagnosa INACBG
                                    </td>
                                </tr>
                            `);
                        }
                    } else {
                        console.error('Error checking INACBG codes:', response.error);
                        // Fallback: add data without validation
                        diagnosisData.forEach(function(diagnosis, index) {
                            addDiagnosisToTable({
                                id: `inacbg_diag_${index}`,
                                code: diagnosis.icd_code,
                                description: diagnosis.icd_description,
                                isValid: true // Assume valid if check fails
                            }, 'inacbgDiagnosisTableBody', true);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking INACBG codes:', error);
                    // Fallback: add data without validation
                    diagnosisData.forEach(function(diagnosis, index) {
                        addDiagnosisToTable({
                            id: `inacbg_diag_${index}`,
                            code: diagnosis.icd_code,
                            description: diagnosis.icd_description,
                            isValid: true // Assume valid if check fails
                        }, 'inacbgDiagnosisTableBody', true);
                    });
                }
            });
        }
        
        function populateInacbgProcedure(procedureData) {
            const tableBody = $('#inacbgProcedureTableBody');
            
            // Clear existing data
            tableBody.empty();
            
            // Extract codes for validation
            const codes = procedureData.map(p => p.icd_code);
            
            // Check codes in database
            $.ajax({
                url: 'api/check_inacbg_codes.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ codes: codes }),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Add procedure data with validation
                        procedureData.forEach(function(procedure, index) {
                            const isValid = response.data[procedure.icd_code] || false;
                            addProcedureToTable({
                                id: `inacbg_proc_${index}`,
                                code: procedure.icd_code,
                                description: procedure.icd_description,
                                isValid: isValid
                            }, 'inacbgProcedureTableBody', true);
                        });
                        
                        // Add empty row if no data
                        if (procedureData.length === 0) {
                            tableBody.append(`
                                <tr class="empty-table">
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Belum ada prosedur INACBG
                                    </td>
                                </tr>
                            `);
                        }
                    } else {
                        console.error('Error checking INACBG codes:', response.error);
                        // Fallback: add data without validation
                        procedureData.forEach(function(procedure, index) {
                            addProcedureToTable({
                                id: `inacbg_proc_${index}`,
                                code: procedure.icd_code,
                                description: procedure.icd_description,
                                isValid: true // Assume valid if check fails
                            }, 'inacbgProcedureTableBody', true);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking INACBG codes:', error);
                    // Fallback: add data without validation
                    procedureData.forEach(function(procedure, index) {
                        addProcedureToTable({
                            id: `inacbg_proc_${index}`,
                            code: procedure.icd_code,
                            description: procedure.icd_description,
                            isValid: true // Assume valid if check fails
                        }, 'inacbgProcedureTableBody', true);
                    });
                }
            });
        }
        
        function displayInacbgGroupingResults(data) {
            // Show the results section
            $('#inacbgGroupingResults').show();
            
            // Populate basic information untuk INACBG
            const currentDate = new Date().toLocaleString('id-ID');
            $('#inacbgGroupingInfo').text(`INACBG @ ${currentDate} •• Kelas D •• Tarif : TARIF RS KELAS A PEMERINTAH`);
            
            // Set jenis rawat berdasarkan data form
            const jenisRawat = $('input[name="jenisRawat"]:checked').val();
            const tglMasuk = $('#tanggalMasuk').val();
            const tglPulang = $('#tanggalPulang').val();
            
            let jenisRawatText = '';
            if (jenisRawat === '1') {
                // Rawat Inap - hitung LOS
                if (tglMasuk && tglPulang) {
                    const los = calculateLOSFromDates(tglMasuk, tglPulang);
                    jenisRawatText = `Rawat Inap Kelas 3 (${los} Hari)`;
                } else {
                    jenisRawatText = 'Rawat Inap Kelas 3';
                }
            } else if (jenisRawat === '2') {
                jenisRawatText = 'Rawat Jalan';
            } else {
                jenisRawatText = 'Tidak Diketahui';
            }
            
            $('#inacbgGroupingJenisRawat').text(jenisRawatText);
            $('#inacbgGroupingStatus').text('normal');
            
            // Populate Group information dari response_inacbg
            const cbgDescription = data.response_inacbg?.cbg?.description || 'N/A';
            const cbgCode = data.response_inacbg?.cbg?.code || 'N/A';
            const baseTariff = data.response_inacbg?.base_tariff || data.response_inacbg?.tariff || '0';
            
            $('#inacbgGroupingGroupDesc').text(cbgDescription);
            $('#inacbgGroupingGroupCode').text(cbgCode);
            $('#inacbgGroupingGroupAmount').text(formatCurrency(baseTariff));
            
            // Set default values untuk Sub Acute dan Chronic
            $('#inacbgGroupingSubAcute').text('-');
            $('#inacbgGroupingChronic').text('-');
            
            // Populate Special CMG Options
            const specialOptions = data.special_cmg_option || [];
            populateSpecialOptions(specialOptions);
            
            // Set Total Klaim
            $('#inacbgTotalKlaim').text(formatCurrency(baseTariff));
            
            // Store base tariff for reset functionality
            $('#inacbgGroupingResults').data('baseTariff', baseTariff);
            
            
            // Show Final INACBG button
            $('#inacbgFinalDrgSection').show();
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#inacbgGroupingResults').offset().top - 100
            }, 500);
        }
        
        function populateSpecialOptions(specialOptions) {
            
            // Clear existing options and add "None" option to each dropdown
            $('#inacbgSpecialProcedure').html('<option value="">None</option>');
            $('#inacbgSpecialProsthesis').html('<option value="">None</option>');
            $('#inacbgSpecialInvestigation').html('<option value="">None</option>');
            $('#inacbgSpecialDrug').html('<option value="">None</option>');
            
            // Group options by type
            if (specialOptions && Array.isArray(specialOptions)) {
                specialOptions.forEach(option => {
                    const optionHtml = `<option value="${option.code}">${option.description}</option>`;
                    
                    switch(option.type) {
                        case 'Special Procedure':
                            $('#inacbgSpecialProcedure').append(optionHtml);
                            break;
                        case 'Special Prosthesis':
                            $('#inacbgSpecialProsthesis').append(optionHtml);
                            break;
                        case 'Special Investigation':
                            $('#inacbgSpecialInvestigation').append(optionHtml);
                            break;
                        case 'Special Drug':
                            $('#inacbgSpecialDrug').append(optionHtml);
                            break;
                    }
                });
            }
        }
        
        function formatCurrency(amount) {
            if (!amount || amount === '0') return 'Rp 0';
            const numAmount = parseInt(amount);
            return 'Rp ' + numAmount.toLocaleString('id-ID');
        }
        
        function performInacbgStage2() {
            // Get nomor SEP
            const nomorSep = $('#noSEP').val();
            if (!nomorSep) {
                return;
            }
            
            // Collect selected special CMG codes
            const selectedCodes = [];
            
            // Get selected values from each dropdown
            const specialProcedure = $('#inacbgSpecialProcedure').val();
            const specialProsthesis = $('#inacbgSpecialProsthesis').val();
            const specialInvestigation = $('#inacbgSpecialInvestigation').val();
            const specialDrug = $('#inacbgSpecialDrug').val();
            
            // Add non-empty values to array
            if (specialProcedure) selectedCodes.push(specialProcedure);
            if (specialProsthesis) selectedCodes.push(specialProsthesis);
            if (specialInvestigation) selectedCodes.push(specialInvestigation);
            if (specialDrug) selectedCodes.push(specialDrug);
            
            console.log('Selected special CMG codes:', selectedCodes);
            
            // If no special CMG selected, reset to base tariff
            if (selectedCodes.length === 0) {
                resetToBaseTariff();
                return;
            }
            
            // Show processing message
            showProcessingMessage('Sedang memproses INACBG Stage 2...');
            
            // Prepare request data
            const requestData = {
                action: 'grouper',
                nomor_sep: nomorSep,
                stage: '2',
                grouper: 'inacbg',
                special_cmg: selectedCodes.join('#')
            };
            
            // Call API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                method: 'POST',
                data: JSON.stringify(requestData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    hideProcessingMessage();
                    
                    if (response.success === true || response.success === 'true') {
                        updateInacbgTariff(response.data);
                    } else {
                        showErrorMessage('Error INACBG Stage 2: ' + (response.error || response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    hideProcessingMessage();
                    showErrorMessage('Error INACBG Stage 2: ' + error);
                }
            });
        }
        
        function updateInacbgTariff(data) {
            // Update base tariff and total claim
            const newTariff = data.response_inacbg?.tariff || data.response_inacbg?.base_tariff || '0';
            $('#inacbgGroupingGroupAmount').text(formatCurrency(newTariff));
            $('#inacbgTotalKlaim').text(formatCurrency(newTariff));
            
            // Update special CMG amounts
            const specialCmg = data.response_inacbg?.special_cmg || [];
            
            // Reset all special amounts to 0
            $('#inacbgSpecialProcedureAmount').text('Rp 0');
            $('#inacbgSpecialProsthesisAmount').text('Rp 0');
            $('#inacbgSpecialInvestigationAmount').text('Rp 0');
            $('#inacbgSpecialDrugAmount').text('Rp 0');
            
            // Update amounts based on selected special CMG
            specialCmg.forEach(item => {
                const amount = formatCurrency(item.tariff || '0');
                
                switch(item.type) {
                    case 'Special Procedure':
                        $('#inacbgSpecialProcedureAmount').text(amount);
                        break;
                    case 'Special Prosthesis':
                        $('#inacbgSpecialProsthesisAmount').text(amount);
                        break;
                    case 'Special Investigation':
                        $('#inacbgSpecialInvestigationAmount').text(amount);
                        break;
                    case 'Special Drug':
                        $('#inacbgSpecialDrugAmount').text(amount);
                        break;
                }
            });
        }
        
        function resetToBaseTariff() {
            // Reset all special amounts to 0
            $('#inacbgSpecialProcedureAmount').text('Rp 0');
            $('#inacbgSpecialProsthesisAmount').text('Rp 0');
            $('#inacbgSpecialInvestigationAmount').text('Rp 0');
            $('#inacbgSpecialDrugAmount').text('Rp 0');
            
            // Get base tariff from stored data
            const baseTariff = $('#inacbgGroupingResults').data('baseTariff');
            if (baseTariff) {
                $('#inacbgGroupingGroupAmount').text(formatCurrency(baseTariff));
                $('#inacbgTotalKlaim').text(formatCurrency(baseTariff));
            }
        }
        
        function performFinalInacbg() {
            // Get nomor SEP
            const nomorSep = $('#noSEP').val();
            if (!nomorSep) {
                alert('Nomor SEP tidak ditemukan');
                return;
            }
            
            // Show confirmation dialog
            if (!confirm('Apakah Anda yakin ingin melakukan Final INACBG? Tindakan ini akan mengunci hasil grouping.')) {
                return;
            }
            
            // Show processing message
            showProcessingMessage('Sedang memproses Final INACBG...');
            
            // Prepare request data sesuai dengan koleksi Postman
            const requestData = {
                action: 'inacbg_grouper_final',
                nomor_sep: nomorSep
            };
            
            // Call API
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
                dataType: 'json',
                success: function(response) {
                    hideProcessingMessage();
                    
                    if (response.success) {
                        // Show success message
                        showSuccessMessage('Final INACBG berhasil dilakukan!');
                        
                        // Set INACBG grouping results to read-only
                        setInacbgGroupingReadOnly();
                        
                        // Hide Final INACBG button
                        $('#inacbgFinalDrgSection').hide();
                        
                        // Show new buttons after successful finalization
                        showInacbgFinalButtons();
                        
                        // Update status
                        $('#inacbgGroupingStatus').html('<span class="badge bg-success">Final</span>');
                        
                        // Scroll to results
                        $('html, body').animate({
                            scrollTop: $('#inacbgGroupingResults').offset().top - 100
                        }, 500);
                        
                    } else {
                        showErrorMessage('Final INACBG gagal: ' + (response.message || 'Terjadi kesalahan'));
                    }
                },
                error: function(xhr, status, error) {
                    hideProcessingMessage();
                    showErrorMessage('Error: ' + error);
                }
            });
        }
        
        function setInacbgGroupingReadOnly() {
            // Disable all dropdowns in INACBG grouping results
            $('#inacbgSpecialProcedure').prop('disabled', true).addClass('bg-light');
            $('#inacbgSpecialProsthesis').prop('disabled', true).addClass('bg-light');
            $('#inacbgSpecialInvestigation').prop('disabled', true).addClass('bg-light');
            $('#inacbgSpecialDrug').prop('disabled', true).addClass('bg-light');
            
            // Add visual indicator that the section is read-only
            $('#inacbgGroupingResults .card-header').addClass('bg-success').removeClass('bg-info');
            $('#inacbgGroupingResults .card-title').html('<i class="fas fa-lock me-2"></i>Hasil Grouping INACBG (Final)');
            
            // Add read-only indicator
            if (!$('#inacbgReadOnlyIndicator').length) {
                $('#inacbgGroupingResults .card-body').prepend(
                    '<div id="inacbgReadOnlyIndicator" class="alert alert-info mb-3">' +
                    '<i class="fas fa-info-circle me-2"></i>' +
                    'Hasil grouping telah difinalisasi dan tidak dapat diubah lagi.' +
                    '</div>'
                );
            }
        }
        
        function showInacbgFinalButtons() {
            // Create buttons section if it doesn't exist
            if (!$('#inacbgFinalButtonsSection').length) {
                const buttonsHtml = `
                    <div id="inacbgFinalButtonsSection" class="card mt-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-check-circle me-2"></i>Aksi Setelah Final INACBG
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-warning btn-lg w-100" id="inacbgReeditBtn">
                                        <i class="fas fa-edit me-2"></i>Edit Ulang INACBG
                                    </button>
                                    <small class="text-muted d-block mt-2">Mengizinkan pengeditan ulang hasil grouping INACBG</small>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-success btn-lg w-100" id="finalClaimBtn">
                                        <i class="fas fa-check-double me-2"></i>Final Klaim
                                    </button>
                                    <small class="text-muted d-block mt-2">Menyelesaikan proses klaim secara keseluruhan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Insert after INACBG grouping results
                $('#inacbgGroupingResults').after(buttonsHtml);
                
                // Bind click events
                $('#inacbgReeditBtn').on('click', performInacbgReedit);
                $('#finalClaimBtn').on('click', performFinalClaim);
            } else {
                // Show existing buttons section
                $('#inacbgFinalButtonsSection').show();
            }
        }
        
        function performInacbgReedit() {
            const nomorSep = $('#noSEP').val();
            if (!nomorSep) {
                alert('Nomor SEP tidak ditemukan');
                return;
            }
            
            if (!confirm('Apakah Anda yakin ingin melakukan Edit Ulang INACBG? Ini akan membuka kembali form untuk diedit.')) {
                return;
            }
            
            showProcessingMessage('Sedang memproses Edit Ulang INACBG...');
            
            const requestData = {
                action: 'inacbg_grouper_reedit',
                nomor_sep: nomorSep
            };
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
                dataType: 'json',
                success: function(response) {
                    hideProcessingMessage();
                    
                    if (response.success) {
                        showSuccessMessage('Edit Ulang INACBG berhasil! Form sekarang dapat diedit.');
                        
                        // Re-enable INACBG grouping section
                        setInacbgGroupingEditable();
                        
                        // Hide final buttons section
                        $('#inacbgFinalButtonsSection').hide();
                        
                        // Show Final INACBG button again
                        $('#inacbgFinalDrgSection').show();
                        
                        // Update status
                        $('#inacbgGroupingStatus').html('<span class="badge bg-warning">Dapat Diedit</span>');
                        
                    } else {
                        showErrorMessage('Edit Ulang INACBG gagal: ' + (response.message || 'Terjadi kesalahan'));
                    }
                },
                error: function(xhr, status, error) {
                    hideProcessingMessage();
                    showErrorMessage('Error: ' + error);
                }
            });
        }
        
        function performFinalClaim() {
            const nomorSep = $('#noSEP').val();
            if (!nomorSep) {
                alert('Nomor SEP tidak ditemukan');
                return;
            }
            
            if (!confirm('Apakah Anda yakin ingin melakukan Final Klaim? Ini akan menyelesaikan seluruh proses klaim.')) {
                return;
            }
            
            showProcessingMessage('Sedang memproses Final Klaim...');
            
            const requestData = {
                action: 'final_claim',
                nomor_sep: nomorSep,
                coder_nik: '<?php echo EKLAIM_CODER_NIK; ?>'
            };
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
                dataType: 'json',
                success: function(response) {
                    hideProcessingMessage();
                    
                    if (response.success) {
                        showSuccessMessage('Final Klaim berhasil diproses!');
                        
                        // Set entire form to read-only
                        setEntireFormReadOnly();
                        
                        // Show Status Klaim layout
                        showStatusKlaimLayout();
                        
                        // Update status
                        $('#inacbgGroupingStatus').html('<span class="badge bg-success">Final Klaim</span>');
                        
                        // Hide final buttons section
                        $('#inacbgFinalButtonsSection').hide();
                        
                    } else {
                        showErrorMessage('Final Klaim gagal: ' + (response.message || 'Terjadi kesalahan'));
                    }
                },
                error: function(xhr, status, error) {
                    hideProcessingMessage();
                    showErrorMessage('Error: ' + error);
                }
            });
        }
        
        function setInacbgGroupingEditable() {
            // Re-enable all dropdowns in INACBG grouping results
            $('#inacbgSpecialProcedure').prop('disabled', false).removeClass('bg-light');
            $('#inacbgSpecialProsthesis').prop('disabled', false).removeClass('bg-light');
            $('#inacbgSpecialInvestigation').prop('disabled', false).removeClass('bg-light');
            $('#inacbgSpecialDrug').prop('disabled', false).removeClass('bg-light');
            
            // Remove visual indicator that the section is read-only
            $('#inacbgGroupingResults .card-header').removeClass('bg-success').addClass('bg-info');
            $('#inacbgGroupingResults .card-title').html('<i class="fas fa-calculator me-2"></i>Hasil Grouping INACBG');
            
            // Remove read-only indicator
            $('#inacbgReadOnlyIndicator').remove();
        }
        
        function setEntireFormReadOnly() {
            // Disable all form inputs
            $('#jaminanSelect').prop('disabled', true);
            $('input[name="jenisRawat"]').prop('disabled', true);
            $('#kelasRawatSelect').prop('disabled', true);
            $('#dpjpInput').prop('readonly', true);
            $('#noPeserta').prop('readonly', true);
            $('#noSEP').prop('readonly', true);
            $('#tanggalMasuk').prop('readonly', true);
            $('#tanggalPulang').prop('readonly', true);
            
            // Disable all buttons
            $('#grouperBtn').prop('disabled', true);
            $('#finalDrgBtn').prop('disabled', true);
            $('#reeditBtn').prop('disabled', true);
            $('#inacbgGrouperBtn').prop('disabled', true);
            $('#inacbgFinalDrgBtn').prop('disabled', true);
            $('#inacbgReeditBtn').prop('disabled', true);
            $('#finalClaimBtn').prop('disabled', true);
            
            // Add visual indicator
            $('.card-header').addClass('bg-secondary').removeClass('bg-primary bg-info bg-success');
            $('.card-title').prepend('<i class="fas fa-lock me-2"></i>');
        }
        
        function showStatusKlaimLayout() {
            // Create Status Klaim layout if it doesn't exist
            if (!$('#statusKlaimSection').length) {
                const statusKlaimHtml = `
                    <div id="statusKlaimSection" class="card mt-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-check-circle me-2"></i>Status Klaim
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="alert alert-success mb-3">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-check-circle me-2"></i>Klaim Berhasil Difinalisasi
                                        </h6>
                                        <p class="mb-0">Klaim telah berhasil diproses dan siap untuk dikirim ke BPJS Kesehatan.</p>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success">
                                                        <i class="fas fa-file-medical me-2"></i>Detail Klaim
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Nomor SEP:</small><br>
                                                            <strong id="statusKlaimSep">-</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Status:</small><br>
                                                            <span class="badge bg-success">Final</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-6">
                                                            <small class="text-muted">Tanggal Final:</small><br>
                                                            <strong id="statusKlaimDate">-</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Coder:</small><br>
                                                            <strong id="statusKlaimCoder">-</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info">
                                                        <i class="fas fa-chart-bar me-2"></i>Ringkasan
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Total Diagnosa:</small><br>
                                                            <strong id="statusKlaimDiagnosis">-</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Total Prosedur:</small><br>
                                                            <strong id="statusKlaimProcedure">-</strong>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-12">
                                                            <small class="text-muted">Total Klaim:</small><br>
                                                            <strong class="text-success" id="statusKlaimTotal">-</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">
                                                <i class="fas fa-tools me-2"></i>Aksi Klaim
                                            </h6>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-outline-primary" id="cetakKlaimBtn">
                                                    <i class="fas fa-print me-2"></i>Cetak Klaim
                                                </button>
                                                <button type="button" class="btn btn-outline-success" id="kirimKlaimOnlineBtn">
                                                    <i class="fas fa-paper-plane me-2"></i>Kirim Klaim Online
                                                </button>
                                                <button type="button" class="btn btn-outline-warning" id="editUlangKlaimBtn">
                                                    <i class="fas fa-edit me-2"></i>Edit Ulang Klaim
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Insert after INACBG grouping results
                $('#inacbgGroupingResults').after(statusKlaimHtml);
                
                // Populate status data
                populateStatusKlaimData();
                
                // Bind click events
                $('#cetakKlaimBtn').on('click', performCetakKlaim);
                $('#kirimKlaimOnlineBtn').on('click', performKirimKlaimOnline);
                $('#editUlangKlaimBtn').on('click', performEditUlangKlaim);
                
            } else {
                // Show existing status klaim section
                $('#statusKlaimSection').show();
            }
        }
        
        function populateStatusKlaimData() {
            // Populate basic data
            $('#statusKlaimSep').text($('#noSEP').val());
            $('#statusKlaimDate').text(new Date().toLocaleDateString('id-ID'));
            $('#statusKlaimCoder').text('<?php echo EKLAIM_CODER_NIK; ?>');
            
            // Count diagnosis and procedure from INACBG tables
            const diagnosisCount = $('#inacbgDiagnosisTableBody tr:not(.empty-table)').length;
            const procedureCount = $('#inacbgProcedureTableBody tr:not(.empty-table)').length;
            
            $('#statusKlaimDiagnosis').text(diagnosisCount);
            $('#statusKlaimProcedure').text(procedureCount);
            
            // Get total claim amount
            const totalKlaim = $('#inacbgTotalKlaim').text();
            $('#statusKlaimTotal').text(totalKlaim || 'Rp 0');
        }
        
        function performCetakKlaim() {
            const nomorSep = $('#noSEP').val();
            if (!nomorSep) {
                alert('Nomor SEP tidak ditemukan');
                return;
            }
            
            showProcessingMessage('Sedang mempersiapkan dokumen untuk dicetak...');
            
            // Simulate print process
            setTimeout(function() {
                hideProcessingMessage();
                showSuccessMessage('Dokumen klaim siap untuk dicetak!');
                
                // In real implementation, this would open print dialog or download PDF
                console.log('Printing claim for SEP:', nomorSep);
                
                // For demo purposes, show alert
                alert('Fitur cetak klaim akan membuka dokumen PDF untuk dicetak.\n\nNomor SEP: ' + nomorSep);
                
            }, 2000);
        }
        
        function performKirimKlaimOnline() {
            const nomorSep = $('#noSEP').val();
            if (!nomorSep) {
                alert('Nomor SEP tidak ditemukan');
                return;
            }
            
            if (!confirm('Apakah Anda yakin ingin mengirim klaim online ke BPJS Kesehatan?')) {
                return;
            }
            
            showProcessingMessage('Sedang mengirim klaim online...');
            
            const requestData = {
                action: 'send_claim_online',
                nomor_sep: nomorSep
            };
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
                dataType: 'json',
                success: function(response) {
                    hideProcessingMessage();
                    
                    if (response.success) {
                        showSuccessMessage('Klaim berhasil dikirim online ke BPJS Kesehatan!');
                        
                        // Update status to sent
                        $('#statusKlaimSection .alert-success .alert-heading').html(
                            '<i class="fas fa-paper-plane me-2"></i>Klaim Berhasil Dikirim Online'
                        );
                        $('#statusKlaimSection .alert-success p').text(
                            'Klaim telah berhasil dikirim ke BPJS Kesehatan dan sedang dalam proses verifikasi.'
                        );
                        
                        // Disable send button
                        $('#kirimKlaimOnlineBtn').prop('disabled', true).text('Sudah Dikirim');
                        
                    } else {
                        showErrorMessage('Gagal mengirim klaim online: ' + (response.message || 'Terjadi kesalahan'));
                    }
                },
                error: function(xhr, status, error) {
                    hideProcessingMessage();
                    showErrorMessage('Error: ' + error);
                }
            });
        }
        
        function performEditUlangKlaim() {
            const nomorSep = $('#noSEP').val();
            if (!nomorSep) {
                alert('Nomor SEP tidak ditemukan');
                return;
            }
            
            if (!confirm('Apakah Anda yakin ingin melakukan Edit Ulang Klaim? Ini akan membuka kembali form untuk diedit.')) {
                return;
            }
            
            showProcessingMessage('Sedang memproses Edit Ulang Klaim...');
            
            const requestData = {
                action: 'reedit_claim',
                nomor_sep: nomorSep
            };
            
            $.ajax({
                url: 'api/eklaim_new_claim.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
                dataType: 'json',
                success: function(response) {
                    hideProcessingMessage();
                    
                    if (response.success) {
                        showSuccessMessage('Edit Ulang Klaim berhasil! Form sekarang dapat diedit.');
                        
                        // Re-enable entire form
                        setEntireFormEditable();
                        
                        // Hide status klaim section
                        $('#statusKlaimSection').hide();
                        
                        // Show Final INACBG button again
                        $('#inacbgFinalDrgSection').show();
                        
                        // Update status
                        $('#inacbgGroupingStatus').html('<span class="badge bg-warning">Dapat Diedit</span>');
                        
                    } else {
                        showErrorMessage('Edit Ulang Klaim gagal: ' + (response.message || 'Terjadi kesalahan'));
                    }
                },
                error: function(xhr, status, error) {
                    hideProcessingMessage();
                    showErrorMessage('Error: ' + error);
                }
            });
        }
        
        function setEntireFormEditable() {
            // Re-enable all form inputs
            $('#jaminanSelect').prop('disabled', false);
            $('input[name="jenisRawat"]').prop('disabled', false);
            $('#kelasRawatSelect').prop('disabled', false);
            $('#dpjpInput').prop('readonly', false);
            $('#noPeserta').prop('readonly', false);
            $('#noSEP').prop('readonly', false);
            $('#tanggalMasuk').prop('readonly', false);
            $('#tanggalPulang').prop('readonly', false);
            
            // Re-enable all buttons
            $('#grouperBtn').prop('disabled', false);
            $('#finalDrgBtn').prop('disabled', false);
            $('#reeditBtn').prop('disabled', false);
            $('#inacbgGrouperBtn').prop('disabled', false);
            $('#inacbgFinalDrgBtn').prop('disabled', false);
            
            // Re-enable INACBG grouping section
            setInacbgGroupingEditable();
            
            // Remove visual indicator
            $('.card-header').removeClass('bg-secondary').addClass('bg-primary bg-info bg-success');
            $('.card-title').each(function() {
                const text = $(this).text();
                if (text.includes('🔒')) {
                    $(this).text(text.replace('🔒 ', ''));
                }
            });
        }
        
    </script>
</body>
</html>
