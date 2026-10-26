<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\KasMasuk;
use App\Models\KasKeluar;
use App\Models\Modal;
use App\Models\Dkh;
use App\Models\DkhDetail;
use App\Models\Pembayaran;
use App\Models\Dc;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKeuanganController extends Controller
{
    /**
     * LAPORAN KEUANGAN LENGKAP - VERSI FINAL
     * 🔥 KAS MASUK & KAS KELUAR ALL RELATIONS (TERMASUK PENCAIRAN PINJAMAN DC)
     */
    public function index(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $dcId = $request->dc_id;

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        // 🔥 AMBIL DATA DC
        $dcList = Dc::where('status', 'aktif')->orderBy('nama_dc')->get();

        // ============================================
        // 🔥 1. TOTAL ANGSURAN PER MINGGU (TAGIHAN)
        // ============================================
        $queryAngsuran = Angsuran::whereBetween('tanggal_jatuh_tempo', [$dari, $sampai]);

        if ($dcId) {
            $queryAngsuran->whereHas('pinjaman', function($q) use ($dcId) {
                $q->where('dc_id', $dcId)->where('status', 'aktif');
            });
        } else {
            $queryAngsuran->whereHas('pinjaman', function($q) {
                $q->where('status', 'aktif');
            });
        }

        $totalAngsuranPerMinggu = $queryAngsuran->sum('jumlah_tagihan');

        // ============================================
        // 🔥 2. TOTAL SALDO PINJAMAN (AKTIF)
        // ============================================
        $queryPinjaman = Pinjaman::with(['paketPinjaman', 'angsuran'])->where('status', 'aktif');

        if ($dcId) {
            $queryPinjaman->where('dc_id', $dcId);
        }

        $pinjamanAktif = $queryPinjaman->get();
        $totalSaldoPinjaman = $pinjamanAktif->sum('jumlah_pinjam');

        // ============================================
        // 🔥 3. SISA BUNGA & SISA POKOK (AKTIF)
        // ============================================
        $sisaBunga = 0;
        $sisaPokok = 0;

        foreach ($pinjamanAktif as $pinjaman) {
            $paket = $pinjaman->paketPinjaman;
            $persenBunga = $paket ? $paket->bunga : 0;
            $totalBunga = ($pinjaman->jumlah_pinjam * $persenBunga / 100);
            $totalTagihan = $pinjaman->jumlah_pinjam + $totalBunga;
            
            $totalBayar = $pinjaman->angsuran
                ->where('status', 'lunas')
                ->sum('jumlah_tagihan');

            if ($totalTagihan > 0 && $totalBayar > 0) {
                $proporsiPokok = $pinjaman->jumlah_pinjam / $totalTagihan;
                $proporsiBunga = $totalBunga / $totalTagihan;
                $pokokTerbayar = $totalBayar * $proporsiPokok;
                $bungaTerbayar = $totalBayar * $proporsiBunga;
            } else {
                $pokokTerbayar = 0;
                $bungaTerbayar = 0;
            }

            $sisaPokok += ($pinjaman->jumlah_pinjam - $pokokTerbayar);
            $sisaBunga += ($totalBunga - $bungaTerbayar);
        }

        $sisaBungaPokok = $sisaBunga + $sisaPokok;

        // ============================================
        // 🔥 4. BUNGA PER MINGGU (AKTIF)
        // ============================================
        $bungaPerMingguQuery = DB::table('pinjamans')
            ->join('paket_pinjaman', 'pinjamans.paket_pinjaman_id', '=', 'paket_pinjaman.id')
            ->where('pinjamans.status', 'aktif');

        if ($dcId) {
            $bungaPerMingguQuery->where('pinjamans.dc_id', $dcId);
        }

        $bungaPerMinggu = $bungaPerMingguQuery
            ->selectRaw('SUM(pinjamans.jumlah_pinjam * paket_pinjaman.bunga / 100 / pinjamans.tenor) as total_bunga_per_minggu')
            ->first();

        $bungaPerMinggu = $bungaPerMinggu->total_bunga_per_minggu ?? 0;

        // ============================================
        // 🔥 5. MACET
        // ============================================
        $queryMacet = Pinjaman::with(['paketPinjaman', 'angsuran'])
            ->where('status', 'macet');

        if ($dcId) {
            $queryMacet->where('dc_id', $dcId);
        }

        $pinjamanMacet = $queryMacet->get();

        $pokokMacet = 0;
        $marginMacet = 0;

        foreach ($pinjamanMacet as $pinjaman) {
            $paket = $pinjaman->paketPinjaman;
            $persenBunga = $paket ? $paket->bunga : 0;
            $totalBunga = ($pinjaman->jumlah_pinjam * $persenBunga / 100);
            $totalTagihan = $pinjaman->jumlah_pinjam + $totalBunga;
            $totalBayar = $pinjaman->angsuran->where('status', 'lunas')->sum('jumlah_tagihan');
            $sisaTagihan = $totalTagihan - $totalBayar;

            if ($totalTagihan > 0 && $sisaTagihan > 0) {
                $proporsiPokok = $pinjaman->jumlah_pinjam / $totalTagihan;
                $proporsiBunga = $totalBunga / $totalTagihan;
                $pokokMacet += ($sisaTagihan * $proporsiPokok);
                $marginMacet += ($sisaTagihan * $proporsiBunga);
            }
        }

        $totalMacet = $pokokMacet + $marginMacet;

        // ============================================
        // 🔥 6. REAL MARGIN & REAL SISA POKOK
        // ============================================
        $realMargin = $sisaBunga - $marginMacet;
        $realSisaPokok = $sisaPokok - $pokokMacet;
        $sisaPokokMargin = $realSisaPokok + $realMargin;
        $realSPP = $sisaPokokMargin;

        // ============================================
        // 🔥 7. KAS MASUK & KELUAR (FIXED ALL RELATIONS)
        // ============================================
        $kasMasukLain = KasMasuk::whereBetween('tanggal', [$dari, $sampai])
            ->where('jenis', '!=', 'Pembayaran Angsuran');

        if ($dcId) {
            $kasMasukLain->where(function($query) use ($dcId) {
                $query->where('dc_id', $dcId)
                ->orWhereHasMorph('referensi', [Pinjaman::class], function($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                });
            });
        }
        $totalKasMasukLain = $kasMasukLain->sum('nominal');

        $kasMasukPembayaran = Pembayaran::whereBetween('tanggal_bayar', [$dari, $sampai]);

        if ($dcId) {
            $kasMasukPembayaran->where(function($query) use ($dcId) {
                $query->whereHas('dkhDetail.dkh', function($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                })
                ->orWhereHas('angsuran.pinjaman', function($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                });
            });
        }
        $totalKasMasukPembayaran = $kasMasukPembayaran->sum('jumlah_bayar');

        $kasMasuk = $totalKasMasukLain + $totalKasMasukPembayaran;

        // Kas Keluar (Termasuk Operasional DC & Pencairan Pinjaman DC)
        $kasKeluarQuery = KasKeluar::whereBetween('tanggal', [$dari, $sampai]);

        if ($dcId) {
            $kasKeluarQuery->where(function($query) use ($dcId) {
                $query->where('dc_id', $dcId)
                ->orWhereHasMorph('referensi', [Pinjaman::class], function($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                });
            });
        }
        $kasKeluar = $kasKeluarQuery->sum('nominal');

        $saldoKas = $kasMasuk - $kasKeluar;

        // ============================================
        // 🔥 8. TOTAL NASABAH
        // ============================================
        $queryNasabah = Pinjaman::where('status', 'aktif');

        if ($dcId) {
            $queryNasabah->where('dc_id', $dcId);
        }

        $totalNasabah = $queryNasabah->distinct('nasabah_id')->count('nasabah_id');

        // ============================================
        // 🔥 9. REKAP PER DC
        // ============================================
        $rekapPerDc = [];
        if (!$dcId) {
            foreach ($dcList as $dc) {
                $tagihanDc = Angsuran::whereBetween('tanggal_jatuh_tempo', [$dari, $sampai])
                    ->whereHas('pinjaman', function($q) use ($dc) {
                        $q->where('dc_id', $dc->id)->where('status', 'aktif');
                    })
                    ->sum('jumlah_tagihan');

                $bayarDc = Pembayaran::whereBetween('tanggal_bayar', [$dari, $sampai])
                    ->where(function($query) use ($dc) {
                        $query->whereHas('dkhDetail.dkh', function($q) use ($dc) {
                            $q->where('dc_id', $dc->id);
                        })
                        ->orWhereHas('angsuran.pinjaman', function($q) use ($dc) {
                            $q->where('dc_id', $dc->id);
                        });
                    })
                    ->sum('jumlah_bayar');

                $pinjamanDc = Pinjaman::where('status', 'aktif')
                    ->where('dc_id', $dc->id)
                    ->get();

                $rekapPerDc[] = [
                    'dc' => $dc,
                    'total_tagihan' => $tagihanDc,
                    'total_bayar' => $bayarDc,
                    'total_sisa' => $pinjamanDc->sum('sisa_tagihan'),
                    'jumlah_nasabah' => $pinjamanDc->count(),
                ];
            }
        }

        // ============================================
        // 🔥 10. GRAFIK
        // ============================================
        $grafik = [
            'labels' => ['Sisa Pokok', 'Sisa Bunga', 'Pokok Macet', 'Margin Macet', 'Real Sisa Pokok', 'Real Margin'],
            'data' => [
                (int) $sisaPokok,
                (int) $sisaBunga,
                (int) $pokokMacet,
                (int) $marginMacet,
                (int) $realSisaPokok,
                (int) $realMargin,
            ],
            'colors' => ['#0d6efd', '#ffc107', '#dc3545', '#dc3545', '#198754', '#198754'],
        ];

        // ============================================
        // 🔥 11. DATA FILTER
        // ============================================
        $listTahun = Pinjaman::selectRaw('YEAR(tanggal_cair) as tahun')->distinct()->pluck('tahun');
        if ($listTahun->isEmpty()) {
            $listTahun = collect([date('Y')]);
        }

        $listBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $listMinggu = range(1, 52);

        $filterLabel = 'Semua DC';
        if ($dcId) {
            $dc = Dc::find($dcId);
            $filterLabel = $dc ? $dc->nama_dc : 'DC Tidak Diketahui';
        }

        return view('laporan-keuangan.index', compact(
            'totalSaldoPinjaman',
            'totalAngsuranPerMinggu',
            'sisaBunga',
            'sisaPokok',
            'sisaBungaPokok',
            'bungaPerMinggu',
            'pokokMacet',
            'marginMacet',
            'totalMacet',
            'realMargin',
            'realSisaPokok',
            'sisaPokokMargin',
            'realSPP',
            'kasMasuk',
            'kasKeluar',
            'saldoKas',
            'grafik',
            'label',
            'periode',
            'tahun',
            'bulan',
            'minggu',
            'tanggal',
            'dcId',
            'dcList',
            'rekapPerDc',
            'listTahun',
            'listBulan',
            'listMinggu',
            'filterLabel',
            'totalNasabah'
        ));
    }

    /**
     * CETAK PDF LAPORAN KEUANGAN
     */
    public function cetak(Request $request)
    {
        $periode = $request->periode ?? 'bulanan';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $dcId = $request->dc_id;

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        $queryAngsuran = Angsuran::whereBetween('tanggal_jatuh_tempo', [$dari, $sampai]);
        if ($dcId) {
            $queryAngsuran->whereHas('pinjaman', function($q) use ($dcId) {
                $q->where('dc_id', $dcId)->where('status', 'aktif');
            });
        } else {
            $queryAngsuran->whereHas('pinjaman', function($q) {
                $q->where('status', 'aktif');
            });
        }
        $totalAngsuranPerMinggu = $queryAngsuran->sum('jumlah_tagihan');

        $queryPinjaman = Pinjaman::with(['paketPinjaman', 'angsuran'])->where('status', 'aktif');
        if ($dcId) {
            $queryPinjaman->where('dc_id', $dcId);
        }
        $pinjamanAktif = $queryPinjaman->get();
        $totalSaldoPinjaman = $pinjamanAktif->sum('jumlah_pinjam');

        $sisaBunga = 0;
        $sisaPokok = 0;
        foreach ($pinjamanAktif as $pinjaman) {
            $paket = $pinjaman->paketPinjaman;
            $persenBunga = $paket ? $paket->bunga : 0;
            $totalBunga = ($pinjaman->jumlah_pinjam * $persenBunga / 100);
            $totalTagihan = $pinjaman->jumlah_pinjam + $totalBunga;
            $totalBayar = $pinjaman->angsuran->where('status', 'lunas')->sum('jumlah_tagihan');

            if ($totalTagihan > 0 && $totalBayar > 0) {
                $proporsiPokok = $pinjaman->jumlah_pinjam / $totalTagihan;
                $proporsiBunga = $totalBunga / $totalTagihan;
                $pokokTerbayar = $totalBayar * $proporsiPokok;
                $bungaTerbayar = $totalBayar * $proporsiBunga;
            } else {
                $pokokTerbayar = 0;
                $bungaTerbayar = 0;
            }

            $sisaPokok += ($pinjaman->jumlah_pinjam - $pokokTerbayar);
            $sisaBunga += ($totalBunga - $bungaTerbayar);
        }

        $sisaBungaPokok = $sisaBunga + $sisaPokok;

        $bungaPerMingguQuery = DB::table('pinjamans')
            ->join('paket_pinjaman', 'pinjamans.paket_pinjaman_id', '=', 'paket_pinjaman.id')
            ->where('pinjamans.status', 'aktif');
        if ($dcId) {
            $bungaPerMingguQuery->where('pinjamans.dc_id', $dcId);
        }
        $bungaPerMinggu = $bungaPerMingguQuery
            ->selectRaw('SUM(pinjamans.jumlah_pinjam * paket_pinjaman.bunga / 100 / pinjamans.tenor) as total_bunga_per_minggu')
            ->first();
        $bungaPerMinggu = $bungaPerMinggu->total_bunga_per_minggu ?? 0;

        $queryMacet = Pinjaman::with(['paketPinjaman', 'angsuran'])->where('status', 'macet');
        if ($dcId) {
            $queryMacet->where('dc_id', $dcId);
        }
        $pinjamanMacet = $queryMacet->get();

        $pokokMacet = 0;
        $marginMacet = 0;
        foreach ($pinjamanMacet as $pinjaman) {
            $paket = $pinjaman->paketPinjaman;
            $persenBunga = $paket ? $paket->bunga : 0;
            $totalBunga = ($pinjaman->jumlah_pinjam * $persenBunga / 100);
            $totalTagihan = $pinjaman->jumlah_pinjam + $totalBunga;
            $totalBayar = $pinjaman->angsuran->where('status', 'lunas')->sum('jumlah_tagihan');
            $sisaTagihan = $totalTagihan - $totalBayar;

            if ($totalTagihan > 0 && $sisaTagihan > 0) {
                $proporsiPokok = $pinjaman->jumlah_pinjam / $totalTagihan;
                $proporsiBunga = $totalBunga / $totalTagihan;
                $pokokMacet += ($sisaTagihan * $proporsiPokok);
                $marginMacet += ($sisaTagihan * $proporsiBunga);
            }
        }

        $totalMacet = $pokokMacet + $marginMacet;
        $realMargin = $sisaBunga - $marginMacet;
        $realSisaPokok = $sisaPokok - $pokokMacet;
        $sisaPokokMargin = $realSisaPokok + $realMargin;
        $realSPP = $sisaPokokMargin;

        $kasMasukLain = KasMasuk::whereBetween('tanggal', [$dari, $sampai])->where('jenis', '!=', 'Pembayaran Angsuran');
        if ($dcId) {
            $kasMasukLain->where(function($query) use ($dcId) {
                $query->where('dc_id', $dcId)
                ->orWhereHasMorph('referensi', [Pinjaman::class], function($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                });
            });
        }
        $totalKasMasukLain = $kasMasukLain->sum('nominal');

        $kasMasukPembayaran = Pembayaran::whereBetween('tanggal_bayar', [$dari, $sampai]);
        if ($dcId) {
            $kasMasukPembayaran->where(function($query) use ($dcId) {
                $query->whereHas('dkhDetail.dkh', function($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                })
                ->orWhereHas('angsuran.pinjaman', function($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                });
            });
        }
        $totalKasMasukPembayaran = $kasMasukPembayaran->sum('jumlah_bayar');

        $kasMasuk = $totalKasMasukLain + $totalKasMasukPembayaran;

        $kasKeluarQuery = KasKeluar::whereBetween('tanggal', [$dari, $sampai]);
        if ($dcId) {
            $kasKeluarQuery->where(function($query) use ($dcId) {
                $query->where('dc_id', $dcId)
                ->orWhereHasMorph('referensi', [Pinjaman::class], function($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                });
            });
        }
        $kasKeluar = $kasKeluarQuery->sum('nominal');
        $saldoKas = $kasMasuk - $kasKeluar;

        $queryNasabah = Pinjaman::where('status', 'aktif');
        if ($dcId) {
            $queryNasabah->where('dc_id', $dcId);
        }
        $totalNasabah = $queryNasabah->distinct('nasabah_id')->count('nasabah_id');

        $dcList = Dc::where('status', 'aktif')->orderBy('nama_dc')->get();
        $rekapPerDc = [];

        if (!$dcId) {
            foreach ($dcList as $dc) {
                $tagihanDc = Angsuran::whereBetween('tanggal_jatuh_tempo', [$dari, $sampai])
                    ->whereHas('pinjaman', function($q) use ($dc) {
                        $q->where('dc_id', $dc->id)->where('status', 'aktif');
                    })
                    ->sum('jumlah_tagihan');

                $bayarDc = Pembayaran::whereBetween('tanggal_bayar', [$dari, $sampai])
                    ->where(function($query) use ($dc) {
                        $query->whereHas('dkhDetail.dkh', function($q) use ($dc) {
                            $q->where('dc_id', $dc->id);
                        })
                        ->orWhereHas('angsuran.pinjaman', function($q) use ($dc) {
                            $q->where('dc_id', $dc->id);
                        });
                    })
                    ->sum('jumlah_bayar');

                $pinjamanDc = Pinjaman::where('status', 'aktif')
                    ->where('dc_id', $dc->id)
                    ->get();

                $rekapPerDc[] = [
                    'dc' => $dc,
                    'total_tagihan' => $tagihanDc,
                    'total_bayar' => $bayarDc,
                    'total_sisa' => $pinjamanDc->sum('sisa_tagihan'),
                    'jumlah_nasabah' => $pinjamanDc->count(),
                ];
            }
        }

        $filterLabel = 'Semua DC';
        if ($dcId) {
            $dc = Dc::find($dcId);
            $filterLabel = $dc ? $dc->nama_dc : 'DC Tidak Diketahui';
        }

        $data = compact(
            'totalSaldoPinjaman',
            'totalAngsuranPerMinggu',
            'sisaBunga',
            'sisaPokok',
            'sisaBungaPokok',
            'bungaPerMinggu',
            'pokokMacet',
            'marginMacet',
            'totalMacet',
            'realMargin',
            'realSisaPokok',
            'sisaPokokMargin',
            'realSPP',
            'kasMasuk',
            'kasKeluar',
            'saldoKas',
            'label',
            'filterLabel',
            'rekapPerDc',
            'dari',
            'sampai',
            'totalNasabah'
        );

        $pdf = Pdf::loadView('pdf.laporan-keuangan', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan_Keuangan_' . date('Ymd') . '.pdf');
    }

    /**
     * HITUNG RENTANG TANGGAL (FIXED PRECISI JAM)
     * 🔥 PERIODE MINGGUAN = SELASA - SENIN
     */
    private function getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal)
    {
        $dari = null;
        $sampai = null;
        $label = '';

        try {
            switch ($periode) {
                case 'harian':
                    $dari = Carbon::parse($tanggal)->startOfDay();
                    $sampai = Carbon::parse($tanggal)->endOfDay();
                    $label = 'Harian - ' . $dari->translatedFormat('d F Y');
                    break;
                case 'mingguan':
                    $dari = Carbon::now()->setISODate((int)$tahun, (int)$minggu, 2)->startOfDay();
                    $sampai = (clone $dari)->addDays(6)->endOfDay();
                    $label = 'Mingguan - Minggu ke-' . $minggu . ' (' . $dari->format('d M') . ' - ' . $sampai->format('d M Y') . ')';
                    break;
                case 'bulanan':
                    $dari = Carbon::createFromDate((int)$tahun, (int)$bulan, 1)->startOfMonth();
                    $sampai = Carbon::createFromDate((int)$tahun, (int)$bulan, 1)->endOfMonth()->endOfDay();
                    $label = 'Bulanan - ' . $dari->translatedFormat('F Y');
                    break;
                case 'tahunan':
                    $dari = Carbon::createFromDate((int)$tahun, 1, 1)->startOfYear();
                    $sampai = Carbon::createFromDate((int)$tahun, 12, 31)->endOfYear();
                    $label = 'Tahunan - ' . $tahun;
                    break;
                default:
                    $dari = Carbon::now()->startOfMonth();
                    $sampai = Carbon::now()->endOfDay();
                    $label = 'Bulan Ini';
            }
        } catch (\Exception $e) {
            $dari = Carbon::now()->startOfMonth();
            $sampai = Carbon::now()->endOfDay();
            $label = 'Bulan Ini';
        }

        return [
            'dari' => $dari->format('Y-m-d H:i:s'),
            'sampai' => $sampai->format('Y-m-d H:i:s'),
            'label' => $label,
        ];
    }
}