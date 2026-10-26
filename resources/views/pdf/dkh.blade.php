<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DKH - {{ $dkh->nomor_dkh }}</title>
    <style>
        @page {
            size: landscape;
            margin: 10px 15px;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
        }

        /* HEADER DENGAN TULISAN DI TENGAH */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2px double #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .header .title {
            text-align: center;
            line-height: 1.2;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .header .title .nama-perusahaan {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .title .sub-perusahaan {
            font-size: 9px;
            font-weight: bold;
        }

        .header .title .alamat {
            font-size: 7.5px;
            color: #555;
        }

        /* JUDUL */
        .judul {
            text-align: center;
            margin: 3px 0 2px 0;
        }

        .judul h2 {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .judul p {
            margin: 1px 0;
            font-size: 8px;
        }

        /* TABEL */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
            font-size: 8px;
        }

        table th {
            background: #0d6efd;
            color: white;
            padding: 3px 2px;
            text-align: center;
            font-weight: bold;
            font-size: 7.5px;
        }

        table td {
            padding: 2px 2px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background: #e8f4fd !important;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #000;
            padding: 3px 2px;
            font-size: 8px;
        }

        /* BADGE */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 6.5px;
            font-weight: bold;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .no-data {
            text-align: center;
            padding: 15px;
            color: #999;
            font-style: italic;
        }

        /* ALAMAT */
        .alamat-detail {
            font-size: 7.5px;
            line-height: 1.3;
        }

        /* NASABAH */
        .nasabah-info {
            font-size: 8px;
            line-height: 1.2;
        }

        .nasabah-info .nama-nasabah {
            font-weight: bold;
            font-size: 8px;
        }

        .nasabah-info .kode-nasabah {
            color: #666;
            font-size: 7px;
        }

        /* TANDA TANGAN */
        .ttd {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
        }

        .ttd-box {
            text-align: center;
            width: 45%;
        }

        .ttd-box .garis {
            margin-top: 25px;
            border-top: 1px solid #000;
            padding-top: 3px;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            font-size: 7px;
            margin-top: 8px;
            border-top: 1px solid #ccc;
            padding-top: 4px;
            color: #666;
        }

        /* FORCE TABLE TO FIT */
        table {
            table-layout: fixed;
        }

        /* KOLOM WIDTH */
        .col-no { width: 4%; }
        .col-nasabah { width: 16%; }
        .col-alamat { width: 18%; }
        .col-bayar-ke { width: 7%; }
        .col-tenor { width: 6%; }
        .col-jb { width: 11%; }
        .col-angsuran { width: 11%; }
        .col-denda { width: 9%; }
        .col-total { width: 13%; }
        .col-status { width: 9%; }
    </style>
</head>
<body>

    {{-- HEADER TANPA LOGO --}}
    <div class="header">
        <div class="title">
            <div class="nama-perusahaan">CV. ONE FINANCE</div>
            <div class="sub-perusahaan">LEMBAGA KEUANGAN MIKRO</div>
            <div class="alamat">Desa Nusa Bakti, Kec. Belitang III, Kab. OKU Timur</div>
        </div>
    </div>

    {{-- JUDUL --}}
    <div class="judul">
        <h2>DAFTAR KUNJUNGAN HARIAN (DKH)</h2>
        <p>{{ $dkh->nomor_dkh }} | {{ \Carbon\Carbon::parse($dkh->tanggal_dkh)->format('d F Y') }}</p>
    </div>

    {{-- TABEL DETAIL --}}
    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-nasabah">Nama Nasabah</th>
                <th class="col-alamat">Alamat</th>
                <th class="col-bayar-ke">Bayar Ke</th>
                <th class="col-tenor">Tenor</th>
                <th class="col-jb">JB</th>
                <th class="col-angsuran">Angsuran</th>
                <th class="col-denda">Denda</th>
                <th class="col-total">Total</th>
                <th class="col-status">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dkh->detail as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                
                {{-- KOLOM NASABAH --}}
                <td class="text-left nasabah-info">
                    <div class="nama-nasabah">{{ Str::limit($item->nasabah->nama ?? '-', 20) }}</div>
                    <div class="kode-nasabah">{{ $item->nasabah->kode_nasabah ?? '-' }}</div>
                </td>
                
                {{-- KOLOM ALAMAT --}}
                <td class="text-left alamat-detail">
                    @if($item->nasabah)
                        <div>{{ Str::limit($item->nasabah->alamat ?? '-', 25) }}</div>
                        @if(!empty($item->nasabah->kelurahan) || !empty($item->nasabah->kecamatan))
                            <div style="color:#555;font-size:7px;">
                                {{ $item->nasabah->kelurahan ?? '' }}{{ !empty($item->nasabah->kelurahan) && !empty($item->nasabah->kecamatan) ? ', ' : '' }}{{ $item->nasabah->kecamatan ?? '' }}
                            </div>
                        @endif
                    @else
                        <span style="color: #999;">-</span>
                    @endif
                </td>
                
                <td class="text-center">{{ $item->bayar_ke }}</td>
                <td class="text-center">{{ $item->jumlah_minggu }}</td>
                <td class="text-right">Rp {{ number_format($item->jb, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->angsuran, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</strong></td>
                <td class="text-center">
                    @if($item->status == 'belum_bayar')
                        <span class="badge badge-danger">Belum</span>
                    @elseif($item->status == 'kurang_bayar')
                        <span class="badge badge-warning">Kurang</span>
                    @else
                        <span class="badge badge-success">Lunas</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="no-data">Tidak ada data tagihan.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-right"><strong>Rp {{ number_format($dkh->total_jb, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($dkh->total_angsuran, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($dkh->total_denda, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($dkh->total_tagihan, 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ $dkh->total_nasabah }}</strong></td>
            </tr>
        </tfoot>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak: {{ $tanggal_cetak ?? now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>