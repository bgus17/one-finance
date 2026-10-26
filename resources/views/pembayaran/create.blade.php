@extends('layouts.app')

@section('title', 'Form Pembayaran')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-money-bill-wave me-2"></i>
                Form Pembayaran
            </h5>
        </div>
        <div class="card-body">

            {{-- ALERT --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <small>{{ $error }}</small><br>
                    @endforeach
                </div>
            @endif

            {{-- INFO NASABAH --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="fw-bold">Informasi Nasabah</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td width="120">Nama</td><td>: <strong>{{ $dkhDetail->nasabah->nama }}</strong></td></tr>
                                <tr><td>Kode</td><td>: {{ $dkhDetail->nasabah->kode_nasabah }}</td></tr>
                                <tr><td>DKH</td><td>: {{ $dkhDetail->dkh->nomor_dkh }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="fw-bold">Informasi Tagihan</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td width="120">Total Tagihan</td><td>: <strong>Rp {{ number_format($dkhDetail->total_tagihan, 0, ',', '.') }}</strong></td></tr>
                                <tr><td>Sudah Dibayar</td><td>: Rp {{ number_format($dkhDetail->jumlah_bayar, 0, ',', '.') }}</td></tr>
                                <tr><td class="text-danger fw-bold">Sisa Bayar</td><td class="text-danger fw-bold">: Rp {{ number_format($dkhDetail->sisa_bayar, 0, ',', '.') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <form action="{{ route('pembayaran.store') }}" method="POST">
                @csrf
                <input type="hidden" name="dkh_detail_id" value="{{ $dkhDetail->id }}">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="checkAll" checked>
                                </th>
                                <th>Minggu Ke</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Tagihan</th>
                                <th class="text-end">Sisa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($angsuranBelumLunas as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" 
                                           name="angsuran_ids[]" 
                                           value="{{ $item->id }}"
                                           class="angsuran-checkbox"
                                           data-tagihan="{{ $item->sisa_tagihan }}"
                                           checked>
                                </td>
                                <td>Ke-{{ $item->minggu_ke }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}</td>
                                <td class="text-end">Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</td>
                                <td><span class="badge bg-danger">Belum</span></td>
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
                        <label class="form-label fw-bold">Jumlah Bayar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="jumlah_bayar" 
                                   id="jumlahBayar"
                                   class="form-control" 
                                   value="{{ old('jumlah_bayar', $dkhDetail->sisa_bayar) }}"
                                   required>
                        </div>
                        <small class="text-muted" id="sisaInfo">Maks: Rp {{ number_format($dkhDetail->sisa_bayar, 0, ',', '.') }}</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Bayar</label>
                        <input type="date" 
                               name="tanggal_bayar" 
                               class="form-control" 
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="form-label fw-bold">Metode Bayar</label>
                        <select name="metode_bayar" class="form-select" required>
                            <option value="kas">Kas</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="form-label fw-bold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Opsional">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-check-circle me-2"></i>
                        Proses Pembayaran
                    </button>
                    <a href="{{ route('dkh.show', $dkhDetail->dkh_id) }}" class="btn btn-secondary btn-lg px-5">
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

        checkAll?.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateTotal();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                checkAll.checked = allChecked;
                updateTotal();
            });
        });

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
                sisaInfo.textContent = 'Maks: Rp ' + sisa.toLocaleString('id-ID');
            }
        }

        updateTotal();
    });
</script>

@endsection