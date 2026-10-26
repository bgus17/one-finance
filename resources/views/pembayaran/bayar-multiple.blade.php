@extends('layouts.app')

@section('title', 'Form Pembayaran Multiple')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-money-bill-wave me-2"></i>
                Form Pembayaran Multiple Angsuran
            </h5>
        </div>
        <div class="card-body">

            {{-- ALERT INFORMASI & ERROR --}}
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle me-1"></i> Terjadi Kesalahan Validasi:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- INFO NASABAH --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="fw-bold">Informasi Nasabah</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="120">Nama</td><td>: <strong>{{ $pinjaman->nasabah->nama }}</strong></td></tr>
                                <tr><td>Kode</td><td>: {{ $pinjaman->nasabah->kode_nasabah }}</td></tr>
                                <tr><td>Pinjaman</td><td>: Rp {{ number_format($pinjaman->jumlah_pinjam, 0, ',', '.') }}</td></tr>
                                <tr><td class="text-danger fw-bold">Sisa Tagihan</td><td class="text-danger fw-bold">: Rp {{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="fw-bold">Informasi Pembayaran</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="120">Angsuran Aktif</td><td>: {{ $angsuranBelumLunas->count() }} Minggu</td></tr>
                                <tr><td>Total Tagihan</td><td>: Rp {{ number_format($angsuranBelumLunas->sum('sisa_tagihan'), 0, ',', '.') }}</td></tr>
                                <tr><td>Saldo Modal</td><td>: Rp {{ number_format($saldoModal ?? 0, 0, ',', '.') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM PEMBAYARAN --}}
            <form action="{{ route('pembayaran.bayar-multiple') }}" method="POST" id="formBayar">
                @csrf
                <input type="hidden" name="pinjaman_id" value="{{ $pinjaman->id }}">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="50" class="text-center">
                                    <input type="checkbox" id="checkAll" checked class="form-check-input">
                                </th>
                                <th>Minggu Ke</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Tagihan</th>
                                <th class="text-end">Sisa Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($angsuranBelumLunas as $item)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" 
                                           name="angsuran_ids[]" 
                                           value="{{ $item->id }}"
                                           class="form-check-input angsuran-checkbox"
                                           data-tagihan="{{ $item->sisa_tagihan }}"
                                           checked>
                                </td>
                                <td>Ke-{{ $item->minggu_ke }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}</td>
                                <td class="text-end">Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-danger">Belum Bayar</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">TOTAL</td>
                                <td class="text-end text-primary" id="totalTagihan">Rp 0</td>
                                <td class="text-end text-danger" id="totalSisa">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah Bayar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="jumlah_bayar" 
                                   id="jumlahBayar"
                                   class="form-control @error('jumlah_bayar') is-invalid @enderror" 
                                   value="{{ old('jumlah_bayar') }}"
                                   required>
                        </div>
                        <small class="text-muted" id="sisaInfo">Sisa tagihan terpilih: Rp 0</small>
                        @error('jumlah_bayar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Bayar <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="tanggal_bayar" 
                               class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                               value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                               required>
                        @error('tanggal_bayar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="form-label fw-bold">Metode Bayar <span class="text-danger">*</span></label>
                        <select name="metode_bayar" class="form-select @error('metode_bayar') is-invalid @enderror" required>
                            <option value="kas" {{ old('metode_bayar') == 'kas' ? 'selected' : '' }}>Kas</option>
                            <option value="transfer" {{ old('metode_bayar') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('metode_bayar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="form-label fw-bold">Keterangan</label>
                        <input type="text" 
                               name="keterangan" 
                               class="form-control" 
                               placeholder="Opsional"
                               value="{{ old('keterangan') }}">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5" id="btnBayar">
                        <i class="fas fa-check-circle me-2"></i>
                        Proses Pembayaran
                    </button>
                    <a href="{{ route('pinjaman.show', $pinjaman->id) }}" class="btn btn-secondary btn-lg px-5">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.angsuran-checkbox');
        const totalTagihan = document.getElementById('totalTagihan');
        const totalSisa = document.getElementById('totalSisa');
        const jumlahBayar = document.getElementById('jumlahBayar');
        const sisaInfo = document.getElementById('sisaInfo');
        const btnBayar = document.getElementById('btnBayar');

        // Check all event
        checkAll?.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateTotal();
        });

        // Checkbox individual event
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                if (checkAll) checkAll.checked = allChecked;
                updateTotal();
            });
        });

        // Fungsi Kalkulasi Total & Auto-Fill Input Jumlah Bayar
        function updateTotal() {
            let total = 0;
            let sisa = 0;
            let checked = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const row = cb.closest('tr');
                    const tagihan = row.querySelector('td:nth-child(4)').textContent.replace(/[^0-9]/g, '');
                    const sisaText = row.querySelector('td:nth-child(5)').textContent.replace(/[^0-9]/g, '');
                    total += parseInt(tagihan) || 0;
                    sisa += parseInt(sisaText) || 0;
                    checked++;
                }
            });

            totalTagihan.textContent = 'Rp ' + total.toLocaleString('id-ID');
            totalSisa.textContent = 'Rp ' + sisa.toLocaleString('id-ID');

            if (jumlahBayar) {
                jumlahBayar.max = sisa;
                jumlahBayar.placeholder = 'Maks: Rp ' + sisa.toLocaleString('id-ID');
                sisaInfo.textContent = 'Sisa tagihan terpilih: Rp ' + sisa.toLocaleString('id-ID');
                
                // Otomatis isi nilai input jumlah bayar sesuai total sisa yang dicentang
                // Kecuali jika user sedang mengetik manual nilainya sendiri (bisa disesuaikan)
                if (checked > 0) {
                    jumlahBayar.value = sisa;
                } else {
                    jumlahBayar.value = '';
                }
            }

            if (btnBayar) {
                btnBayar.textContent = checked === 0 ? 
                    'Pilih Angsuran Dulu' : 
                    'Bayar ' + checked + ' Angsuran';
                
                btnBayar.disabled = checked === 0;
            }
        }

        // Jalankan perhitungan pertama kali saat halaman dimuat
        updateTotal();
    });
</script>
@endsection