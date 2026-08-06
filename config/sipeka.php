<?php

return [

    /*
    |--------------------------------------------------------------------
    | Batas hari pelaporan realisasi bulanan
    |--------------------------------------------------------------------
    | Realisasi bulan ke-N dianggap "tepat waktu" (dipakai kriteria C3 pada
    | metode SAW) jika diinput paling lambat N hari setelah bulan tsb berakhir.
    | Contoh default 10 -> laporan Januari dianggap tepat waktu jika diinput
    | paling lambat tanggal 10 Februari.
    */
    'batas_hari_lapor' => (int) env('SIPEKA_BATAS_HARI_LAPOR', 10),

    /*
    |--------------------------------------------------------------------
    | Bobot kriteria metode SAW (Simple Additive Weighting)
    |--------------------------------------------------------------------
    | Total bobot harus berjumlah 1.0 (100%).
    */
    'saw' => [
        'bobot_kinerja' => (float) env('SIPEKA_BOBOT_KINERJA', 0.35),
        'bobot_anggaran' => (float) env('SIPEKA_BOBOT_ANGGARAN', 0.35),
        'bobot_ketepatan_waktu' => (float) env('SIPEKA_BOBOT_KETEPATAN', 0.30),
    ],

];
