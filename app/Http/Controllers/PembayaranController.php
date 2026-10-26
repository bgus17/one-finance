<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\Dc;
use App\Models\Dkh;
use App\Models\DkhDetail;
use App\Models\KasMasuk;
use App\Models\Modal;
use App\Models\Pembayaran;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    /**
     * DAFTAR PEMBAYARAN (INDEX)
     * PERIODE MINGGUAN = SELASA - SENIN
     */
    public function index(Request $request)
    {
        $periode = $request->periode ?? 'harian';
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $minggu = $request->minggu ?? date('W');
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $dcId = $request->dc_id ?? null;
        $metode = $request->metode ?? 'semua';

        $range = $this->getRangeTanggal($periode, $tahun, $bulan, $minggu, $tanggal);
        $dari = $range['dari'];
        $sampai = $range['sampai'];
        $label = $range['label'];

        $dcList = Dc::where('status', 'aktif')->orderBy('nama_dc')->get();

        $query = Pembayaran::with([
            'angsuran.pinjaman.nasabah',
            'angsuran.pinjaman.paketPinjaman',
            'dkhDetail.dkh',
            'dkhDetail.dkh.dc',
        ]);

        if ($dari && $sampai) {
            $query->whereBetween('tanggal_bayar', [$dari, $sampai]);
        }

        if ($dcId) {
            $query->whereHas('dkhDetail.dkh', function ($q) use ($dcId) {
                $q->where('dc_id', $dcId);
            });
        }

        if ($metode && $metode != 'semua') {
            $query->where('metode_bayar', $metode);
        }

        $pembayaran = $query->orderByDesc('tanggal_bayar')->paginate(20);

        $totalBayar = $query->sum('jumlah_bayar');
        $totalTransaksi = $query->count();
        $totalNasabah = $query->distinct('dkh_detail_id')->count('dkh_detail_id');

        $totalKas = Pembayaran::where('metode_bayar', 'kas');
        $totalTransfer = Pembayaran::where('metode_bayar', 'transfer');

        if ($dari && $sampai) {
            $totalKas->whereBetween('tanggal_bayar', [$dari, $sampai]);
            $totalTransfer->whereBetween('tanggal_bayar', [$dari, $sampai]);
        }
        if ($dcId) {
            $totalKas->whereHas('dkhDetail.dkh', function ($q) use ($dcId) {
                $q->where('dc_id', $dcId);
            });
            $totalTransfer->whereHas('dkhDetail.dkh', function ($q) use ($dcId) {
                $q->where('dc_id', $dcId);
            });
        }

        $totalKas = $totalKas->sum('jumlah_bayar');
        $totalTransfer = $totalTransfer->sum('jumlah_bayar');

        $dataPerDc = [];

        if ($dcId) {
            $dc = Dc::find($dcId);
            if ($dc) {
                $dcQuery = Pembayaran::with([
                    'angsuran.pinjaman.nasabah',
                    'angsuran.pinjaman.paketPinjaman',
                    'dkhDetail.dkh',
                ])->whereHas('dkhDetail.dkh', function ($q) use ($dcId) {
                    $q->where('dc_id', $dcId);
                });

                if ($dari && $sampai) {
                    $dcQuery->whereBetween('tanggal_bayar', [$dari, $sampai]);
                }

                if ($metode && $metode != 'semua') {
                    $dcQuery->where('metode_bayar', $metode);
                }

                $dcPembayaran = $dcQuery->orderByDesc('tanggal_bayar')->get();

                $dcKas = $dcQuery->where('metode_bayar', 'kas')->sum('jumlah_bayar');
                $dcTransfer = $dcQuery->where('metode_bayar', 'transfer')->sum('jumlah_bayar');

                $dataPerDc[] = [
                    'dc' => $dc,
                    'pembayaran' => $dcPembayaran,
                    'total_bayar' => $dcPembayaran->sum('jumlah_bayar'),
                    'total_nasabah' => $dcPembayaran->groupBy('dkh_detail_id')->count(),
                    'total_transaksi' => $dcPembayaran->count(),
                    'total_kas' => $dcKas,
                    'total_transfer' => $dcTransfer,
                ];
            }
        } else {
            foreach ($dcList as $dc) {
                $dcQuery = Pembayaran::with([
                    'angsuran.pinjaman.nasabah',
                    'angsuran.pinjaman.paketPinjaman',
                    'dkhDetail.dkh',
                ])->whereHas('dkhDetail.dkh', function ($q) use ($dc) {
                    $q->where('dc_id', $dc->id);
                });

                if ($dari && $sampai) {
                    $dcQuery->whereBetween('tanggal_bayar', [$dari, $sampai]);
                }

                if ($metode && $metode != 'semua') {
                    $dcQuery->where('metode_bayar', $metode);
                }

                $dcPembayaran = $dcQuery->orderByDesc('tanggal_bayar')->get();
                $total = $dcPembayaran->sum('jumlah_bayar');

                if ($total > 0) {
                    $dcKas = $dcQuery->where('metode_bayar', 'kas')->sum('jumlah_bayar');
                    $dcTransfer = $dcQuery->where('metode_bayar', 'transfer')->sum('jumlah_bayar');

                    $dataPerDc[] = [
                        'dc' => $dc,
                        'pembayaran' => $dcPembayaran,
                        'total_bayar' => $total,
                        'total_nasabah' => $dcPembayaran->groupBy('dkh_detail_id')->count(),
                        'total_transaksi' => $dcPembayaran->count(),
                        'total_kas' => $dcKas,
                        'total_transfer' => $dcTransfer,
                    ];
                }
            }
        }

        if (empty($dataPerDc) && ! $dcId) {
            $allPembayaran = Pembayaran::with([
                'angsuran.pinjaman.nasabah',
                'angsuran.pinjaman.paketPinjaman',
                'dkhDetail.dkh',
            ]);

            if ($dari && $sampai) {
                $allPembayaran->whereBetween('tanggal_bayar', [$dari, $sampai]);
            }

            if ($metode && $metode != 'semua') {
                $allPembayaran->where('metode_bayar', $metode);
            }

            $allPembayaran = $allPembayaran->orderByDesc('tanggal_bayar')->get();

            if ($allPembayaran->isNotEmpty()) {
                $dataPerDc[] = [
                    'dc' => (object) ['nama_dc' => 'Semua DC', 'kode_dc' => 'ALL'],
                    'pembayaran' => $allPembayaran,
                    'total_bayar' => $allPembayaran->sum('jumlah_bayar'),
                    'total_nasabah' => $allPembayaran->groupBy('dkh_detail_id')->count(),
                    'total_transaksi' => $allPembayaran->count(),
                    'total_kas' => $allPembayaran->where('metode_bayar', 'kas')->sum('jumlah_bayar'),
                    'total_transfer' => $allPembayaran->where('metode_bayar', 'transfer')->sum('jumlah_bayar'),
                ];
            }
        }

        $listTahun = Pembayaran::selectRaw('YEAR(tanggal_bayar) as tahun')
            ->distinct()
            ->pluck('tahun');
        if ($listTahun->isEmpty()) {
            $listTahun = collect([date('Y')]);
        }

        $listBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];
        $listMinggu = range(1, 52);

        $global = [
            'total_bayar' => $totalBayar,
            'total_transaksi' => $totalTransaksi,
            'total_nasabah' => $totalNasabah,
            'total_dc' => $dcList->count(),
            'total_kas' => $totalKas,
            'total_transfer' => $totalTransfer,
        ];

        return view('pembayaran.index', compact(
            'dataPerDc',
            'global',
            'dcList',
            'label',
            'periode',
            'tahun',
            'bulan',
            'minggu',
            'tanggal',
            'listTahun',
            'listBulan',
            'listMinggu',
            'dari',
            'sampai',
            'dcId',
            'pembayaran',
            'metode'
        ));
    }

    /**
     * RIWAYAT PEMBAYARAN
     */
    public function riwayat(Request $request)
    {
        $query = Pembayaran::with([
            'angsuran.pinjaman.nasabah',
            'angsuran.pinjaman.paketPinjaman',
            'dkhDetail.dkh',
            'dkhDetail.dkh.dc',
        ]);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('angsuran.pinjaman.nasabah', function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal_bayar', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal_bayar', '<=', $request->end_date);
        }

        if ($request->has('dc_id') && $request->dc_id) {
            $query->whereHas('dkhDetail.dkh', function ($q) use ($request) {
                $q->where('dc_id', $request->dc_id);
            });
        }

        if ($request->has('metode') && $request->metode && $request->metode != 'semua') {
            $query->where('metode_bayar', $request->metode);
        }

        $pembayaran = $query->orderByDesc('tanggal_bayar')->paginate(20);
        $dcList = Dc::where('status', 'aktif')->orderBy('nama_dc')->get();

        return view('pembayaran.riwayat', compact('pembayaran', 'dcList'));
    }

    /**
     * GET RANGE TANGGAL BERDASARKAN PERIODE
     * PERIODE MINGGUAN = SELASA - SENIN
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
                    $label = 'Harian: '.$dari->format('d/m/Y');
                    break;

                case 'mingguan':
                    $dari = Carbon::now()->setISODate((int) $tahun, (int) $minggu, 2)->startOfDay();
                    $sampai = (clone $dari)->addDays(6)->endOfDay();
                    $label = 'Mingguan: '.$dari->format('d/m/Y').' - '.$sampai->format('d/m/Y');
                    break;

                case 'bulanan':
                    $dari = Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->startOfMonth();
                    $sampai = Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->endOfMonth()->endOfDay();
                    $label = 'Bulanan: '.$dari->format('F Y');
                    break;

                case 'tahunan':
                    $dari = Carbon::createFromDate((int) $tahun, 1, 1)->startOfYear();
                    $sampai = Carbon::createFromDate((int) $tahun, 12, 31)->endOfYear();
                    $label = 'Tahunan: '.$tahun;
                    break;

                default:
                    $dari = Carbon::now()->startOfDay();
                    $sampai = Carbon::now()->endOfDay();
                    $label = 'Harian: '.$dari->format('d/m/Y');
            }
        } catch (\Exception $e) {
            $dari = Carbon::now()->startOfDay();
            $sampai = Carbon::now()->endOfDay();
            $label = 'Harian: '.$dari->format('d/m/Y');
        }

        return [
            'dari' => $dari->format('Y-m-d H:i:s'),
            'sampai' => $sampai->format('Y-m-d H:i:s'),
            'label' => $label,
        ];
    }

    /**
     * FORM PEMBAYARAN (dari DKH Detail)
     */
    public function createForm($dkhDetailId)
    {
        $dkhDetail = DkhDetail::with([
            'nasabah',
            'pinjaman',
            'angsuran',
            'dkh',
        ])->findOrFail($dkhDetailId);

        if ($dkhDetail->status == 'lunas') {
            return redirect()
                ->route('dkh.show', $dkhDetail->dkh_id)
                ->with('error', 'Tagihan ini sudah lunas!');
        }

        $angsuranBelumLunas = Angsuran::where('pinjaman_id', $dkhDetail->pinjaman_id)
            ->where('status', '!=', 'lunas')
            ->orderBy('minggu_ke')
            ->get();

        $modal = Modal::where('status', 'aktif')->first();
        $saldoModal = $modal ? $modal->saldo : 0;

        return view('pembayaran.create', compact('dkhDetail', 'angsuranBelumLunas', 'saldoModal'));
    }

    /**
     * 🔥 HITUNG POKOK DAN MARGIN (FINAL HYBRID FALLBACK)
     * Menangani pinjaman dengan total_pinjaman maupun relasi paketPinjaman
     */
    private function hitungPokokMargin($pinjaman, $jumlahBayar)
    {
        if (! $pinjaman) {
            return ['pokok' => $jumlahBayar, 'margin' => 0];
        }

        $totalTagihan = $pinjaman->total_pinjaman ?? 0;
        $pokokPinjaman = $pinjaman->jumlah_pinjam ?? 0;

        // Fallback: Jika total_pinjaman di DB kosong/0, hitung dari paketPinjaman
        if ($totalTagihan <= 0) {
            if (! $pinjaman->relationLoaded('paketPinjaman')) {
                $pinjaman->load('paketPinjaman');
            }
            $paket = $pinjaman->paketPinjaman;

            if ($paket && $paket->bunga > 0) {
                $totalBunga = ($pokokPinjaman * $paket->bunga / 100);
                $totalTagihan = $pokokPinjaman + $totalBunga;
            }
        }

        // Hitung proporsi pokok & margin dari selisih tagihan vs pokok pinjaman
        if ($totalTagihan > 0 && $pokokPinjaman > 0) {
            $totalBunga = $totalTagihan - $pokokPinjaman;
            if ($totalBunga > 0) {
                $proporsiMargin = $totalBunga / $totalTagihan;
                $margin = round($jumlahBayar * $proporsiMargin, 2);
                $pokok = round($jumlahBayar - $margin, 2);

                return [
                    'pokok' => $pokok,
                    'margin' => $margin,
                ];
            }
        }

        return ['pokok' => $jumlahBayar, 'margin' => 0];
    }

    /**
     * PROSES PEMBAYARAN (SINGLE DARI DKH FORM)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dkh_detail_id' => 'required|exists:dkh_detail,id',
            'angsuran_ids' => 'required|array|min:1',
            'angsuran_ids.*' => 'exists:angsurans,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_bayar' => 'required|in:kas,transfer',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $dkhDetail = DkhDetail::with([
                'nasabah',
                'pinjaman.paketPinjaman',
                'angsuran',
                'dkh',
            ])->findOrFail($request->dkh_detail_id);

            $pinjaman = $dkhDetail->pinjaman;
            $angsuranIds = $request->angsuran_ids;
            $totalBayar = $request->jumlah_bayar;

            if ($totalBayar > $pinjaman->sisa_tagihan) {
                throw new \Exception('Jumlah bayar melebihi sisa tagihan! Sisa: Rp '.number_format($pinjaman->sisa_tagihan, 0, ',', '.'));
            }

            $angsuranList = Angsuran::whereIn('id', $angsuranIds)
                ->where('status', '!=', 'lunas')
                ->orderBy('minggu_ke')
                ->get();

            if ($angsuranList->isEmpty()) {
                throw new \Exception('Tidak ada angsuran yang bisa dibayar!');
            }

            $totalTagihanAngsuran = $angsuranList->sum('sisa_tagihan');
            $sisaBayar = $totalBayar;

            foreach ($angsuranList as $angsuran) {
                $proporsi = $angsuran->sisa_tagihan / $totalTagihanAngsuran;
                $bayarAngsuran = round($totalBayar * $proporsi, 0);

                if ($bayarAngsuran > $angsuran->sisa_tagihan) {
                    $bayarAngsuran = $angsuran->sisa_tagihan;
                }

                $result = $this->hitungPokokMargin($pinjaman, $bayarAngsuran);
                $pokok = $result['pokok'];
                $margin = $result['margin'];

                $angsuran->jumlah_bayar += $bayarAngsuran;
                $angsuran->sisa_tagihan -= $bayarAngsuran;
                if ($angsuran->sisa_tagihan <= 0) {
                    $angsuran->status = 'lunas';
                }
                $angsuran->tanggal_bayar = $request->tanggal_bayar;
                $angsuran->metode_bayar = $request->metode_bayar;
                $angsuran->save();

                if ($angsuran->id == $dkhDetail->angsuran_id) {
                    $dkhDetail->jumlah_bayar += $bayarAngsuran;
                    $dkhDetail->sisa_bayar -= $bayarAngsuran;
                    if ($dkhDetail->sisa_bayar <= 0) {
                        $dkhDetail->status = 'lunas';
                    }
                    $dkhDetail->save();
                } else {
                    $dkhDetailLain = DkhDetail::where('angsuran_id', $angsuran->id)
                        ->where('status', '!=', 'lunas')
                        ->first();

                    if ($dkhDetailLain) {
                        $dkhDetailLain->jumlah_bayar += $bayarAngsuran;
                        $dkhDetailLain->sisa_bayar -= $bayarAngsuran;
                        if ($dkhDetailLain->sisa_bayar <= 0) {
                            $dkhDetailLain->status = 'lunas';
                        }
                        $dkhDetailLain->save();
                    }
                }

                Pembayaran::create([
                    'angsuran_id' => $angsuran->id,
                    'dkh_detail_id' => $dkhDetail->id,
                    'jumlah_tagihan' => $angsuran->jumlah_tagihan,
                    'jumlah_bayar' => $bayarAngsuran,
                    'tanggal_bayar' => Carbon::parse($request->tanggal_bayar)->format('Y-m-d H:i:s'),
                    'metode_bayar' => $request->metode_bayar,
                    'pokok' => $pokok,
                    'margin' => $margin,
                    'keterangan' => $request->keterangan ?? 'Pembayaran angsuran ke-'.$angsuran->minggu_ke,
                ]);

                $sisaBayar -= $bayarAngsuran;
            }

            $pinjaman->sisa_tagihan -= $totalBayar;
            if ($pinjaman->sisa_tagihan <= 0) {
                $pinjaman->status = 'lunas';
                $pinjaman->tanggal_lunas = $request->tanggal_bayar;
            }
            $pinjaman->save();

            $dkh = $dkhDetail->dkh;
            if ($dkh) {
                $totalTagihanDkh = DkhDetail::where('dkh_id', $dkh->id)->sum('total_tagihan');
                $totalBayarDkh = DkhDetail::where('dkh_id', $dkh->id)->sum('jumlah_bayar');
                $totalSisa = $totalTagihanDkh - $totalBayarDkh;

                $dkh->total_tagihan = $totalTagihanDkh;
                $dkh->total_jb = $totalSisa;
                if ($totalSisa <= 0 && $totalTagihanDkh > 0) {
                    $dkh->status = 'selesai';
                } else {
                    $dkh->status = ($dkh->status == 'dicetak') ? 'dicetak' : 'draft';
                }
                $dkh->save();
            }

            $modal = Modal::where('status', 'aktif')->first();
            if ($modal) {
                $modal->saldo += $totalBayar;
                $modal->save();

                KasMasuk::create([
                    'modal_id' => $modal->id,
                    'tanggal' => $request->tanggal_bayar,
                    'jenis' => 'Pembayaran Angsuran',
                    'nominal' => $totalBayar,
                    'keterangan' => 'Pembayaran '.$angsuranList->count().' angsuran - '.$dkhDetail->nasabah->nama,
                    'referensi_id' => $dkhDetail->id,
                    'referensi_type' => DkhDetail::class,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('dkh.show', $dkh->id)
                ->with('success', '✅ Pembayaran berhasil! Total Rp '.number_format($totalBayar, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', '❌ Gagal: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * ONE CLICK BAYAR (LANGSUNG DARI DKH)
     */
    public function bayarLangsung(Request $request, $dkhDetailId)
    {
        $validator = Validator::make($request->all(), [
            'metode_bayar' => 'required|in:kas,transfer',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $tagihan = DkhDetail::with(['pinjaman', 'pinjaman.paketPinjaman'])->findOrFail($dkhDetailId);

            if ($tagihan->status == 'lunas') {
                throw new \Exception('Tagihan ini sudah lunas!');
            }

            $jumlahBayar = (int) $tagihan->sisa_bayar;

            if ($jumlahBayar <= 0) {
                throw new \Exception('Sisa bayar sudah 0!');
            }

            $pinjaman = $tagihan->pinjaman;
            $result = $this->hitungPokokMargin($pinjaman, $jumlahBayar);
            $pokok = $result['pokok'];
            $margin = $result['margin'];

            $tagihan->jumlah_bayar = (int) $tagihan->jumlah_bayar + $jumlahBayar;
            $tagihan->sisa_bayar = 0;
            $tagihan->status = 'lunas';
            $tagihan->save();

            $angsuran = Angsuran::where('id', $tagihan->angsuran_id)->first();
            if ($angsuran) {
                $angsuran->jumlah_bayar = (int) $angsuran->jumlah_bayar + $jumlahBayar;
                $angsuran->sisa_tagihan = 0;
                $angsuran->status = 'lunas';
                $angsuran->save();
            }

            if ($pinjaman) {
                $pinjaman->sisa_tagihan = (int) $pinjaman->sisa_tagihan - $jumlahBayar;
                if ($pinjaman->sisa_tagihan <= 0) {
                    $pinjaman->status = 'lunas';
                    $pinjaman->tanggal_lunas = now();
                }
                $pinjaman->save();
            }

            $modal = Modal::where('status', 'aktif')->first();
            if ($modal) {
                $modal->saldo = (int) $modal->saldo + $jumlahBayar;
                $modal->save();

                KasMasuk::create([
                    'modal_id' => $modal->id,
                    'tanggal' => now(),
                    'jenis' => 'Pembayaran Angsuran',
                    'nominal' => $jumlahBayar,
                    'keterangan' => $request->keterangan ?? 'Pembayaran langsung',
                    'referensi_id' => $tagihan->id,
                    'referensi_type' => DkhDetail::class,
                ]);
            }

            $dkh = Dkh::where('id', $tagihan->dkh_id)->first();
            if ($dkh) {
                $totalTagihan = DkhDetail::where('dkh_id', $dkh->id)->sum('total_tagihan');
                $totalBayar = DkhDetail::where('dkh_id', $dkh->id)->sum('jumlah_bayar');
                $totalSisa = $totalTagihan - $totalBayar;

                $dkh->total_tagihan = $totalTagihan;
                $dkh->total_jb = $totalSisa;
                $dkh->total_angsuran = DkhDetail::where('dkh_id', $dkh->id)->sum('angsuran');
                $dkh->total_denda = DkhDetail::where('dkh_id', $dkh->id)->sum('denda');
                if ($totalSisa <= 0 && $totalTagihan > 0) {
                    $dkh->status = 'selesai';
                } else {
                    $dkh->status = ($dkh->status == 'dicetak') ? 'dicetak' : 'draft';
                }
                $dkh->save();
            }

            Pembayaran::create([
                'angsuran_id' => $tagihan->angsuran_id,
                'dkh_detail_id' => $tagihan->id,
                'jumlah_tagihan' => $tagihan->total_tagihan,
                'jumlah_bayar' => $jumlahBayar,
                'sisa_tagihan' => 0,
                'tanggal_bayar' => now(),
                'metode_bayar' => $request->metode_bayar,
                'keterangan' => $request->keterangan ?? 'Pembayaran langsung',
                'pokok' => $pokok,
                'margin' => $margin,
            ]);

            DB::commit();

            return redirect()
                ->route('dkh.show', $tagihan->dkh_id)
                ->with('success', '✅ Pembayaran berhasil! Rp '.number_format($jumlahBayar, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', '❌ Gagal: '.$e->getMessage());
        }
    }

    /**
     * FORM BAYAR MULTIPLE
     */
    public function bayarMultipleForm($pinjamanId)
    {
        $pinjaman = Pinjaman::with([
            'nasabah',
            'angsuran' => function ($query) {
                $query->where('status', '!=', 'lunas')
                    ->orderBy('minggu_ke');
            },
        ])->findOrFail($pinjamanId);

        $angsuranBelumLunas = $pinjaman->angsuran;

        if ($angsuranBelumLunas->isEmpty()) {
            return redirect()
                ->route('pinjaman.show', $pinjamanId)
                ->with('info', 'Semua angsuran sudah lunas!');
        }

        $modal = Modal::where('status', 'aktif')->first();
        $saldoModal = $modal ? $modal->saldo : 0;

        return view('pembayaran.bayar-multiple', compact('pinjaman', 'angsuranBelumLunas', 'saldoModal'));
    }

    /**
     * PROSES BAYAR MULTIPLE (FINAL REVISION)
     * 🔥 MENGATASI TABLE ERROR, UPDATE DKH DC & SINKRONISASI PENDAPATAN BUNGA MARGIN
     */
    public function bayarMultiple(Request $request)
    {
        $tabelPinjaman = (new Pinjaman)->getTable();
        $tabelAngsuran = (new Angsuran)->getTable();

        $validator = Validator::make($request->all(), [
            'pinjaman_id' => "required|exists:{$tabelPinjaman},id",
            'angsuran_ids' => 'required|array|min:1',
            'angsuran_ids.*' => "exists:{$tabelAngsuran},id",
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_bayar' => 'required|in:kas,transfer',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $pinjaman = Pinjaman::with(['paketPinjaman', 'nasabah'])->findOrFail($request->pinjaman_id);
            $angsuranIds = $request->angsuran_ids;
            $totalBayar = $request->jumlah_bayar;

            if ($totalBayar > $pinjaman->sisa_tagihan) {
                throw new \Exception('Jumlah bayar melebihi sisa tagihan! Sisa: Rp '.number_format($pinjaman->sisa_tagihan, 0, ',', '.'));
            }

            $angsuranList = Angsuran::whereIn('id', $angsuranIds)
                ->where('status', '!=', 'lunas')
                ->orderBy('minggu_ke')
                ->get();

            if ($angsuranList->isEmpty()) {
                throw new \Exception('Tidak ada angsuran yang bisa dibayar!');
            }

            $totalTagihanAngsuran = $angsuranList->sum('sisa_tagihan');
            $sisaBayar = $totalBayar;
            $totalMarginTerkumpul = 0; // Penampung akumulasi pendapatan bunga

            foreach ($angsuranList as $angsuran) {
                $proporsi = $angsuran->sisa_tagihan / $totalTagihanAngsuran;
                $bayarAngsuran = round($totalBayar * $proporsi, 0);

                if ($bayarAngsuran > $angsuran->sisa_tagihan) {
                    $bayarAngsuran = $angsuran->sisa_tagihan;
                }

                // 1. HITUNG POKOK DAN MARGIN BUNGA
                $result = $this->hitungPokokMargin($pinjaman, $bayarAngsuran);
                $pokok = $result['pokok'];
                $margin = $result['margin'];
                $totalMarginTerkumpul += $margin;

                // 2. UPDATE KARTU ANGSURAN (Kolom tanggal_bayar & metode_bayar dihapus karena disimpan di tabel pembayarans)
                $angsuran->jumlah_bayar += $bayarAngsuran;
                $angsuran->sisa_tagihan -= $bayarAngsuran;
                if ($angsuran->sisa_tagihan <= 0) {
                    $angsuran->status = 'lunas';
                }
                $angsuran->save();

                // 3. SINKRONISASI UPDATE DKH DETAIL & DKH MASTER
                $dkhDetailList = DkhDetail::where('angsuran_id', $angsuran->id)
                    ->where('status', '!=', 'lunas')
                    ->get();

                $lastDkhDetailId = null;

                foreach ($dkhDetailList as $dkhDetail) {
                    $bayarDkh = min($bayarAngsuran, $dkhDetail->sisa_bayar);
                    $dkhDetail->jumlah_bayar += $bayarDkh;
                    $dkhDetail->sisa_bayar -= $bayarDkh;

                    if ($dkhDetail->sisa_bayar <= 0) {
                        $dkhDetail->status = 'lunas';
                    }
                    $dkhDetail->save();
                    $lastDkhDetailId = $dkhDetail->id;

                    // Update DKH Header Master
                    $dkh = $dkhDetail->dkh;
                    if ($dkh) {
                        $totalTagihanDkh = DkhDetail::where('dkh_id', $dkh->id)->sum('total_tagihan');
                        $totalBayarDkh = DkhDetail::where('dkh_id', $dkh->id)->sum('jumlah_bayar');
                        $totalSisa = max(0, $totalTagihanDkh - $totalBayarDkh);

                        $dkh->total_tagihan = $totalTagihanDkh;
                        $dkh->total_jb = $totalSisa;

                        if ($totalSisa <= 0 && $totalTagihanDkh > 0) {
                            $dkh->status = 'selesai';
                        } else {
                            $dkh->status = ($dkh->status == 'dicetak') ? 'dicetak' : 'draft';
                        }
                        $dkh->save();
                    }
                }

                // 4. SIMPAN RIWAYAT PEMBAYARAN TERMASUK POKOK & MARGIN BUNGA
                Pembayaran::create([
                    'angsuran_id' => $angsuran->id,
                    'dkh_detail_id' => $lastDkhDetailId,
                    'jumlah_tagihan' => $angsuran->jumlah_tagihan,
                    'jumlah_bayar' => $bayarAngsuran,
                    'sisa_tagihan' => $angsuran->sisa_tagihan,
                    'tanggal_bayar' => Carbon::parse($request->tanggal_bayar)->format('Y-m-d H:i:s'),
                    'metode_bayar' => $request->metode_bayar,
                    'pokok' => $pokok,
                    'margin' => $margin,
                    'keterangan' => $request->keterangan ?? 'Pembayaran multiple angsuran ke-'.$angsuran->minggu_ke,
                ]);

                $sisaBayar -= $bayarAngsuran;
            }

            // 5. UPDATE PORTOFOLIO PINJAMAN
            $pinjaman->sisa_tagihan -= $totalBayar;
            if ($pinjaman->sisa_tagihan <= 0) {
                $pinjaman->status = 'lunas';
                $pinjaman->tanggal_lunas = $request->tanggal_bayar;
            }
            $pinjaman->save();

            // 6. UPDATE SALDO MODAL & KAS MASUK
            $modal = Modal::where('status', 'aktif')->first();
            if ($modal) {
                $modal->saldo += $totalBayar;
                $modal->save();

                KasMasuk::create([
                    'modal_id' => $modal->id,
                    'tanggal' => $request->tanggal_bayar,
                    'jenis' => 'Pembayaran Angsuran Multiple',
                    'nominal' => $totalBayar,
                    'keterangan' => 'Bayar '.$angsuranList->count().' angsuran (Pokok + Bunga: Rp '.number_format($totalMarginTerkumpul, 0, ',', '.').') - '.$pinjaman->nasabah->nama,
                    'referensi_id' => $pinjaman->id,
                    'referensi_type' => Pinjaman::class,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pinjaman.show', $pinjaman->id)
                ->with('success', '✅ Pembayaran multiple berhasil! Total: Rp '.number_format($totalBayar, 0, ',', '.').' (Margin Bunga: Rp '.number_format($totalMarginTerkumpul, 0, ',', '.').')');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', '❌ Gagal: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * DETAIL PEMBAYARAN
     */
    public function detail($id) //
    {
        $pembayaran = Pembayaran::with([
            'angsuran.pinjaman.nasabah',
            'angsuran.pinjaman.paketPinjaman',
            'dkhDetail.dkh.dc',
        ])->findOrFail($id);
        $pinjaman = $pembayaran->angsuran->pinjaman;
        $nasabah = $pinjaman->nasabah;

        return view('pembayaran.detail', compact('pembayaran', 'pinjaman', 'nasabah'));
    }
}
