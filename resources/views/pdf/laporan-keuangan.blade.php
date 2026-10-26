<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 11px; margin: 30px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; text-transform: uppercase; margin: 0; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        .filter-info { background: #f8f9fa; padding: 8px 12px; margin-bottom: 15px; border-radius: 4px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 6px 10px; border: 1px solid #000; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .total-row { background: #e8f4fd; font-weight: bold; }
        .footer { text-align: center; font-size: 9px; margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; color: #666; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Keuangan</h1>
        <p>Koperasi ONE FINANCE</p>
        <p>Periode: {{ $label ?? date('F Y') }}</p>
        <p>Dicetak: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="filter-info">
        <strong>Filter DC:</strong> {{ $filterLabel ?? 'Semua DC' }}
        <span style="float:right;"><strong>Periode:</strong> {{ $dari ?? '' }} - {{ $sampai ?? '' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="60%">Keterangan</th>
                <th width="40%" class="text-end">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Total Saldo Pinjaman</td><td class="text-end">{{ number_format($totalSaldoPinjaman, 0, ',', '.') }}</td></tr>
            <tr><td>Total Angsuran/Beban Per Minggu</td><td class="text-end">{{ number_format($totalAngsuranPerMinggu, 0, ',', '.') }}</td></tr>
            <tr><td>Sisa Bunga</td><td class="text-end">{{ number_format($sisaBunga, 0, ',', '.') }}</td></tr>
            <tr><td>Sisa Pokok</td><td class="text-end">{{ number_format($sisaPokok, 0, ',', '.') }}</td></tr>
            <tr><td>Sisa Bunga + Pokok</td><td class="text-end">{{ number_format($sisaBungaPokok, 0, ',', '.') }}</td></tr>
            <tr><td>Bunga/Margin Per Minggu</td><td class="text-end">{{ number_format($bungaPerMinggu, 0, ',', '.') }}</td></tr>
            <tr><td>Pokok Macet</td><td class="text-end">{{ number_format($pokokMacet, 0, ',', '.') }}</td></tr>
            <tr><td>Margin Macet</td><td class="text-end">{{ number_format($marginMacet, 0, ',', '.') }}</td></tr>
            <tr><td>Pokok & Margin Macet</td><td class="text-end">{{ number_format($totalMacet, 0, ',', '.') }}</td></tr>
            <tr><td>Real Margin</td><td class="text-end">{{ number_format($realMargin, 0, ',', '.') }}</td></tr>
            <tr><td>Real Sisa Pokok</td><td class="text-end">{{ number_format($realSisaPokok, 0, ',', '.') }}</td></tr>
            <tr><td>Sisa Pokok & Margin</td><td class="text-end">{{ number_format($sisaPokokMargin, 0, ',', '.') }}</td></tr>
            <tr><td>Kas Masuk</td><td class="text-end">{{ number_format($kasMasuk ?? 0, 0, ',', '.') }}</td></tr>
            <tr><td>Kas Keluar</td><td class="text-end">{{ number_format($kasKeluar ?? 0, 0, ',', '.') }}</td></tr>
            <tr><td>Saldo Kas</td><td class="text-end">{{ number_format($saldoKas ?? 0, 0, ',', '.') }}</td></tr>
            <tr class="total-row"><td>Real SPP</td><td class="text-end">{{ number_format($realSPP, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    {{-- TABEL RINCIAN PER DC (jika Semua DC) --}}
    @if(isset($rekapPerDc) && count($rekapPerDc) > 0)
    <div style="margin-top: 20px;">
        <h4 style="text-align:center; font-size:12px;">RINCIAN PER DC</h4>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama DC</th>
                    <th class="text-end">Total Tagihan</th>
                    <th class="text-end">Total Bayar</th>
                    <th class="text-end">Sisa Tagihan</th>
                    <th class="text-center">Nasabah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapPerDc as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['dc']->nama_dc }}</td>
                    <td class="text-end">{{ number_format($item['total_tagihan'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item['total_bayar'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item['total_sisa'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item['jumlah_nasabah'] }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format(collect($rekapPerDc)->sum('total_tagihan'), 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format(collect($rekapPerDc)->sum('total_bayar'), 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format(collect($rekapPerDc)->sum('total_sisa'), 0, ',', '.') }}</td>
                    <td class="text-center">{{ collect($rekapPerDc)->sum('jumlah_nasabah') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem.</p>
        <p>Koperasi ONE FINANCE - {{ date('Y') }}</p>
    </div>

</body>
</html>