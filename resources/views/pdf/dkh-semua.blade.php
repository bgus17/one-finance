<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DKH - {{ $tanggal }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            margin: 20px 30px;
            color: #1a1a1a;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
            gap: 15px;
        }

        .header .logo {
            width: 60px;
            height: 60px;
        }

        .header .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header .title {
            text-align: center;
        }

        .header .title .nama-perusahaan {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .title .sub-perusahaan {
            font-size: 10px;
            font-weight: bold;
        }

        .header .title .alamat {
            font-size: 9px;
            color: #555;
        }

        .judul {
            text-align: center;
            margin: 10px 0;
        }

        .judul h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .judul p {
            margin: 2px 0;
            font-size: 10px;
        }

        .info-dkh {
            margin: 10px 0;
            padding: 8px 12px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }

        table th {
            background: #0d6efd;
            color: white;
            padding: 6px;
            text-align: center;
            font-weight: bold;
        }

        table td {
            padding: 5px 6px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background: #e8f4fd !important;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #000;
            padding: 6px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 9px;
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
            padding: 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="logo">
            @php
                $logoPath = public_path('assets/img/one-finance.png');
                $logoBase64 = '';
                if (file_exists($logoPath)) {
                    $logoData = file_get_contents($logoPath);
                    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                }
            @endphp
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo">
            @else
                <div style="width:60px;height:60px;border:2px solid #0d6efd;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:bold;color:#0d6efd;background:#f0f7ff;">
                    OF
                </div>
            @endif
        </div>
        <div class="title">
            <div class="nama-perusahaan">CV. ONE FINANCE</div>
            <div class="sub-perusahaan">LEMBAGA KEUANGAN MIKRO</div>
            <div class="alamat">Desa Nusa Bakti, Kec. Belitang III, Kab. OKU Timur</div>
        </div>
    </div>

    {{-- JUDUL --}}
    <div class="judul">
        <h2>DAFTAR KUNJUNGAN HARIAN (DKH)</h2>
        <p>Periode: {{ $tanggal }}</p>
        <p style="font-size: 9px; color: #666;">
            Total {{ $dkh->count() }} DKH
        </p>
    </div>

    @forelse($dkh as $index => $item)
        {{-- Info DKH --}}
        <div class="info-dkh">
            <table>
                <tr>
                    <td class="label" style="width:100px;">Nomor DKH</td>
                    <td>: <strong>{{ $item->nomor_dkh }}</strong></td>
                    <td class="label" style="width:100px;">DC</td>
                    <td>: {{ $item->dc->nama_dc ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Total Nasabah</td>
                    <td>: {{ $item->total_nasabah }} Orang</td>
                    <td class="label">Total Tagihan</td>
                    <td>: <strong>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td>: 
                        @if($item->status == 'draft')
                            <span class="badge badge-warning">Draft</span>
                        @elseif($item->status == 'dicetak')
                            <span class="badge badge-success">Dicetak</span>
                        @else
                            <span class="badge badge-success">Selesai</span>
                        @endif
                    </td>
                    <td class="label">Minggu Ke</td>
                    <td>: {{ $item->minggu_ke }}</td>
                </tr>
            </table>
        </div>

        {{-- TABEL DETAIL --}}
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nama Nasabah</th>
                    <th width="10%">Bayar Ke</th>
                    <th width="8%">Tenor</th>
                    <th width="12%">JB</th>
                    <th width="12%">Angsuran</th>
                    <th width="10%">Denda</th>
                    <th width="13%">Total</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($item->detail as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">
                        <strong>{{ $detail->nasabah->nama ?? '-' }}</strong>
                    </td>
                    <td>{{ $detail->bayar_ke }}</td>
                    <td>{{ $detail->jumlah_minggu }}</td>
                    <td class="text-right">Rp {{ number_format($detail->jb, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($detail->angsuran, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($detail->denda, 0, ',', '.') }}</td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($detail->total_tagihan, 0, ',', '.') }}</strong>
                    </td>
                    <td>
                        @if($detail->status == 'belum_bayar')
                            <span class="badge badge-danger">Belum</span>
                        @elseif($detail->status == 'kurang_bayar')
                            <span class="badge badge-warning">Kurang</span>
                        @else
                            <span class="badge badge-success">Lunas</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="no-data">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($item->total_jb, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($item->total_angsuran, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($item->total_denda, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</strong></td>
                    <td><strong>{{ $item->total_nasabah }}</strong></td>
                </tr>
            </tfoot>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p class="no-data" style="text-align:center;padding:40px;">Tidak ada DKH pada tanggal ini.</p>
    @endforelse

    {{-- FOOTER --}}
    <div class="footer">
        <p>
            Dokumen ini dicetak secara otomatis oleh sistem.<br>
            Dicetak pada: {{ $tanggal_cetak ?? now()->format('d F Y H:i:s') }}
        </p>
    </div>

</body>
</html>