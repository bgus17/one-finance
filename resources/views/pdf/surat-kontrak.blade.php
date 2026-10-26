<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Penitipan Modal Usaha</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            margin: 30px 40px;
            color: #1a1a1a;
            line-height: 1.6;
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
            width: 70px;
            height: 70px;
            flex-shrink: 0;
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
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
        }

        .header .title .sub-perusahaan {
            font-size: 11px;
            font-weight: bold;
            margin: 0;
        }

        .header .title .no-perusahaan {
            font-size: 10px;
            margin: 2px 0 0 0;
        }

        .header .title .alamat {
            font-size: 9px;
            margin: 2px 0 0 0;
            color: #555;
        }

        .judul-surat {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .judul-surat h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
        }

        .judul-surat .no-surat {
            font-size: 11px;
            margin: 2px 0 0 0;
        }

        .content {
            margin-top: 10px;
        }

        .content p {
            margin: 4px 0;
            text-align: justify;
        }

        .data-nasabah {
            margin: 8px 0 8px 20px;
        }

        .data-nasabah td {
            padding: 1px 10px 1px 0;
            vertical-align: top;
        }

        .data-nasabah .label {
            width: 100px;
            font-weight: bold;
        }

        .rincian-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .rincian-table td {
            padding: 4px 8px;
            border: 1px solid #000;
        }

        .rincian-table .label-col {
            width: 35%;
            font-weight: bold;
            background: #f5f5f5;
        }

        .rincian-table .value-col {
            width: 25%;
            text-align: right;
        }

        .rincian-table .value-col2 {
            width: 15%;
            text-align: center;
        }

        .ttd-section {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
        }

        .ttd-box {
            text-align: center;
            width: 45%;
        }

        .ttd-box .garis {
            margin-top: 45px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .ttd-box .nama {
            font-weight: bold;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 25px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            color: #666;
        }

        .agunan {
            margin-top: 12px;
            padding: 8px 12px;
            border: 1px solid #000;
            background: #fafafa;
        }

        .agunan h4 {
            margin: 0 0 3px 0;
            font-size: 11px;
        }

        .poin-list {
            padding-left: 20px;
        }

        .poin-list li {
            margin-bottom: 4px;
            text-align: justify;
        }

        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    {{-- ============================================ --}}
    {{-- HEADER DENGAN LOGO (PAKAI ASSET) --}}
    {{-- ============================================ --}}
    <div class="header">
        {{-- LOGO KOPERASI --}}
        <div class="logo">
            <img src="{{ public_path('assets/img/one-finance.png') }}" alt="Logo Koperasi" style="width:100px;height:auto;">
        </div>

        {{-- JUDUL PERUSAHAAN --}}
        <div class="title">
            <div class="nama-perusahaan">CV. ONE FINANCE</div>
            <div class="sub-perusahaan">LEMBAGA KEUANGAN MIKRO</div>
            <div class="no-perusahaan">NO: AHU-0026800-AH.01.14 TAHUN 2025</div>
            <div class="alamat">Desa Nusa Bakti, Kecamatan Belitang III, Kabupaten OKU Timur, Kode Pos 3238</div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- JUDUL SURAT --}}
    {{-- ============================================ --}}
    <div class="judul-surat">
        <h2>SURAT PERJANJIAN PENITIPAN MODAL USAHA</h2>
        <div class="no-surat">NO: {{ $nomor_surat ?? 'AHU-0026800-AH.01.14 TAHUN 2025' }}</div>
    </div>

    {{-- ============================================ --}}
    {{-- ISI SURAT --}}
    {{-- ============================================ --}}
    <div class="content">
        <p>
            Pada hari ini, <strong>{{ $tanggal_surat ?? date('d F Y') }}</strong>,
            telah ditanda tangani Surat Perjanjian Penitipan Modal Usaha antara:
        </p>

        {{-- PIHAK PERTAMA --}}
        <table class="data-nasabah" style="margin-top: 8px;">
            <tr>
                <td class="label">NAMA</td>
                <td>: <strong>DIAN PRIADI</strong></td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td>: 1608080108990001</td>
            </tr>
            <tr>
                <td class="label">TTL</td>
                <td>: OKU TIMUR, 01-08-1999</td>
            </tr>
            <tr>
                <td class="label">PEKERJAAN</td>
                <td>: WIRASWASTA</td>
            </tr>
            <tr>
                <td class="label">ALAMAT</td>
                <td>: MARGOMULYO KEC. BELITANG II KAB. OKU TIMUR</td>
            </tr>
        </table>
        <p style="margin-top: 0;">
            Bertindak atas nama Koperasi ONE FINANCE dan selanjutnya disebut <strong>PIHAK PERTAMA</strong>
        </p>

        <br>

        {{-- PIHAK KEDUA --}}
        <table class="data-nasabah">
            <tr>
                <td class="label">NAMA</td>
                <td>: <strong>{{ $nasabah->nama ?? '____________________' }}</strong></td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td>: {{ $nasabah->nik ?? '____________________' }}</td>
            </tr>
            <tr>
                <td class="label">TTL</td>
                <td>: {{ $nasabah->tempat_lahir ?? '' }},
                    {{ $nasabah->tanggal_lahir ? \Carbon\Carbon::parse($nasabah->tanggal_lahir)->format('d-m-Y') : '____________________' }}
                </td>
            </tr>
            <tr>
                <td class="label">PEKERJAAN</td>
                <td>: {{ $nasabah->pekerjaan ?? '____________________' }}</td>
            </tr>
            <tr>
                <td class="label">ALAMAT</td>
                <td>: {{ $nasabah->alamat ?? '____________________' }}</td>
            </tr>
            <tr>
                <td class="label">NID</td>
                <td>: {{ $pinjaman->id ?? '____' }}</td>
            </tr>
        </table>
        <p style="margin-top: 0;">
            Bertindak atas nama pribadi dan selanjutnya disebut <strong>PIHAK KEDUA</strong>
        </p>

        <br>

        {{-- ISI PERJANJIAN --}}
        <p><strong>ISI PERJANJIAN :</strong></p>

        <p>
            Dalam hal ini disebutkan sebagai PIHAK KEDUA dititipkan dana atau modal usaha.
            Dengan ini menyatakan:
        </p>

        <ol class="poin-list">
            <li>
                Bahwa PIHAK PERTAMA telah menitipkan dana atau modal usaha sebesar
                <strong>Rp {{ number_format($pinjaman->nominal_dicairkan ?? 0, 0, ',', '.') }}</strong>
                ({{ $terbilang ?? '____________________' }}) kepada PIHAK KEDUA.
            </li>
            <li>
                PIHAK KEDUA meminjam modal usaha kepada PIHAK PERTAMA untuk keperluan usaha
                dalam bidang: {{ $nasabah->bidang_usaha ?? '.....................................................' }}
            </li>
            <li>
                PIHAK KEDUA akan mengembalikan penitipan modal usaha tersebut kepada PIHAK PERTAMA
                dengan cara bertahap/kredit perminggu dengan waktu
                <strong>{{ $pinjaman->tenor ?? '____' }} minggu</strong>
                sebesar <strong>Rp {{ number_format($pinjaman->angsuran_per_minggu ?? 0, 0, ',', '.') }}</strong>
                jatuh tempo setiap minggu pada hari: <strong>{{ $hari_jatuh_tempo ?? '________' }}</strong>
            </li>
            <li>
                Apabila PIHAK KEDUA tidak membayar selama 3 minggu berturut-turut maka PIHAK KEDUA
                bersedia memberikan barang kepada PIHAK PERTAMA dengan harga barang yang sesuai dengan
                penitipan modal usaha.
            </li>
            <li>
                Atas titipan modal usaha tersebut PIHAK KEDUA akan memberikan bunga kepada
                PIHAK PERTAMA sebesar <strong>{{ $paket->bunga ?? '2,5' }}%</strong> dalam satu minggu.
            </li>
            <li>
                Dan apabila PIHAK KEDUA tidak membayar dengan waktu yang telah ditentukan oleh PIHAK
                PERTAMA maka PIHAK KEDUA dikenakan denda sebesar
                <strong>Rp {{ number_format($paket->denda ?? 15000, 0, ',', '.') }}</strong>
            </li>
        </ol>

        <p style="margin-top: 12px;">
            Demikian surat perjanjian penitipan modal usaha ini saya buat, dan disetujui oleh kedua belah
            pihak tanpa ada unsur paksaan oleh pihak manapun dan dalam keadaan sehat jasmani dan rohani.
        </p>

        {{-- AGUNAN --}}
        <div class="agunan">
            <h4>AGUNAN/JAMINAN</h4>
            <ol style="padding-left: 20px; margin: 3px 0;">
                <li>{{ $agunan1 ?? '____________________' }}</li>
                <li>{{ $agunan2 ?? '____________________' }}</li>
                <li>{{ $agunan3 ?? '____________________' }}</li>
            </ol>
        </div>

        {{-- RINCIAN --}}
        <div class="mt-20">
            <p style="font-weight: bold; margin-bottom: 5px;">RINCIAN PENITIPAN MODAL USAHA :</p>

            <table class="rincian-table">
                <tr>
                    <td class="label-col">PENITIPAN MODAL USAHA</td>
                    <td class="value-col">Rp {{ number_format($pinjaman->jumlah_pinjam ?? 0, 0, ',', '.') }}</td>
                    <td class="value-col2"></td>
                </tr>
                <tr>
                    <td class="label-col">ADMINISTRASI</td>
                    <td class="value-col">
                    <td class="value-col2"></td>
                </tr>
                <tr>
                    <td class="label-col">POT ANGSURAN</td>
                    <td class="value-col">Rp {{ number_format($pinjaman->angsuran_per_minggu ?? 0, 0, ',', '.') }}</td>
                    <td class="value-col2">{{ $pinjaman->tenor ?? 0 }} x {{ $pinjaman->angsuran_per_minggu ?? 0 }}
                    </td>
                </tr>
                <tr>
                    <td class="label-col">POT DENDA</td>
                    <td class="value-col">Rp {{ number_format($denda ?? 0, 0, ',', '.') }}</td>
                    <td class="value-col2"></td>
                </tr>
                <tr>
                    <td class="label-col">KEKURANGAN ANGSURAN</td>
                    <td class="value-col">Rp {{ number_format($kekurangan_angsuran ?? 0, 0, ',', '.') }}</td>
                    <td class="value-col2"></td>
                </tr>
                <tr style="background: #e8f4fd; font-weight: bold;">
                    <td class="label-col">SISA PENITIPAN MODAL USAHA</td>
                    <td class="value-col">Rp {{ number_format($pinjaman->sisa_tagihan ?? 0, 0, ',', '.') }}</td>
                    <td class="value-col2"></td>
                </tr>
            </table>
        </div>

        {{-- TTD --}}
        <div class="ttd-section">
            <div class="ttd-box">
                <p>PIHAK PERTAMA</p>
                <p style="font-size: 10px;">(Koperasi ONE FINANCE)</p>
                <div class="garis">
                    <strong>DIAN PRIADI</strong>
                </div>
                <p style="font-size: 9px; margin-top: 2px;">Direktur</p>
            </div>
            <div class="ttd-box">
                <p>PIHAK KEDUA</p>
                <p style="font-size: 10px;">(Nasabah)</p>
                <div class="garis">
                    <strong>{{ $nasabah->nama ?? '____________________' }}</strong>
                
        </div>

        {{-- SAKSI --}}
        <div style="margin-top: 20px; display: flex; justify-content: center; gap: 60px;">
            <div style="text-align: center; width: 30%;">
                <p style="font-size: 10px;">Saksi 1</p>
                <div class="garis" style="margin-top: 30px;">
                    <strong>____________________</strong>
                </div>
            </div>
            <div style="text-align: center; width: 30%;">
                <p style="font-size: 10px;">Saksi 2</p>
                <div class="garis" style="margin-top: 30px;">
                    <strong>____________________</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>
            Dokumen ini dicetak secara otomatis oleh sistem.<br>
            Dicetak pada: {{ now()->format('d F Y H:i:s') }}
        </p>
    </div>

</body>

</html>
