<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid #000;
        }

        .center {
            text-align: center;
            vertical-align: middle;
        }

        .bold {
            font-weight: bold;
        }

        /* =====================================================
           KOP
        ===================================================== */

        .kop {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .kop td {
            border: 1px solid #000;
            padding: 0;
        }

        .kop-logo {
            width: 22%;
            height: 102px;
            text-align: center;
            vertical-align: middle;
        }

        .logo-ppa {
            width: 60px;
            height: 60px;
            object-fit: contain;
            display: block;
            margin: 0 auto 2px auto;
        }

        .ppa-text {
            font-size: 10px;
            font-weight: bold;
            line-height: 10px;
        }

        .kop-title {
            width: 48%;
            height: 20px;
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
            font-weight: bold;
        }

        .kop-title-main {
            height: 20px;
            font-size: 7px;
        }

        .kop-ofa {
            height: 20px;
            font-size: 7px;
        }

        .kop-project {
            height: 21px;
            padding: 0 5px !important;
            vertical-align: middle;
            font-size: 6px;
            white-space: nowrap;
        }

        .kop-label {
            font-size: 6px;
            font-weight: normal;
        }

        .kop-colon {
            margin-left: 3px;
            margin-right: 3px;
        }

        .kop-value {
            font-size: 6px;
        }

        .kop-version-label {
            margin-left: 35px;
            font-size: 6px;
        }

        .kop-department {
            height: 21px;
            padding: 0 5px !important;
            vertical-align: middle;
            font-size: 6px;
        }

        .kop-doc-label {
            width: 12%;
            height: 20px;
            padding: 0 4px !important;
            vertical-align: middle;
            font-size: 6px;
            white-space: nowrap;
        }

        .kop-doc-value {
            width: 18%;
            height: 20px;
            padding: 0 4px !important;
            vertical-align: middle;
            font-size: 6px;
            white-space: nowrap;
        }

        .kop-empty {
            height: 21px;
        }

        /* =====================================================
           IDENTITAS UNIT
        ===================================================== */

        .identity {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .identity td {
            border: 1px solid #000;
            height: 19px;
            padding: 0 4px;
            font-size: 6.5px;
            vertical-align: middle;
        }

        .identity-label {
            width: 17%;
            font-weight: normal;
            white-space: nowrap;
        }

        .identity-value {
            width: 33%;
        }

        /* =====================================================
           CHECKLIST
        ===================================================== */

        .checklist {
            margin-top: 8px;
        }

        .checklist th,
        .checklist td {
            padding: 2.5px 4px;
        }

        .checklist-header th {
            height: 15px;
            font-weight: normal;
            text-align: center;
            vertical-align: middle;
        }

        .no-col {
            width: 5%;
            text-align: center;
        }

        .item-col {
            width: 35%;
        }

        .condition-col {
            width: 6.5%;
            text-align: center;
        }

        .note-col {
            width: 20%;
        }

        .condition-title {
            text-align: center;
            vertical-align: middle;
        }

        .condition-sub th {
            height: 14px;
            font-size: 6.5px;
            text-align: center;
            padding: 1px;
        }

        .section-row td {
            background: #c7e8f5;
            font-weight: bold;
            height: 17px;
            padding: 2px 5px;
        }

        .item-row td {
            height: 16px;
            vertical-align: middle;
        }

        .item-row .no {
            text-align: center;
        }

        .item-row .status {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }

        /* =====================================================
           CATATAN
        ===================================================== */

        .notes {
            margin-top: 0;
        }

        .notes-title {
            height: 20px;
            border-bottom: 0 !important;
            padding: 5px !important;
            vertical-align: middle;
        }

        .notes-content {
            height: 30px;
            vertical-align: top;
            padding: 5px 8px !important;
        }

        .notes-line {
            border-bottom: 1px dashed #000;
            height: 15px;
            margin-bottom: 1px;
        }

        /* =====================================================
   TIM PELAKSANA - DIPERKECIL
===================================================== */

        .team {
            margin-top: 0;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .team-title {
            background: #c7e8f5;
            font-weight: bold;
            height: 14px;
            padding: 1px 4px;
            font-size: 6.5px;
        }

        .team th {
            height: 14px;
            font-size: 6px;
            text-align: center;
            padding: 1px 2px;
            line-height: 7px;
        }

        .team td {
            height: 13px;
            padding: 1px 2px;
            font-size: 6px;
            line-height: 7px;
            vertical-align: middle;
        }

        .team .no {
            width: 5%;
            text-align: center;
        }

        .team .nama {
            width: 19%;
        }

        .team .nrp {
            width: 18%;
        }

        .team .jabatan {
            width: 15%;
        }

        .team .departemen {
            width: 15%;
        }

        .team .perusahaan {
            width: 12%;
        }

        .team .ttd {
            width: 16%;
        }

        /* =====================================================
   TANDA TANGAN
   SESUAI PDF ASLI
===================================================== */

        .approval {
            width: 100%;
            border-collapse: collapse;
            border: 0;
            table-layout: fixed;
            margin: 0;
        }

        .approval td {
            border: 0 !important;
            padding: 0;
        }

        .signature-box {
            width: 50%;
            height: 125px;
            text-align: center;
            vertical-align: top;
            position: relative;
        }

        .signature-title {
            display: block;
            margin-top: 14px;
            font-size: 7px;
            text-align: center;
        }

        .signature-space {
            height: 55px;
        }

        .signature-line {
            width: 145px;
            margin: 0 auto;
            border-top: 1px solid #000;
            height: 1px;
        }

        .signature-name {
            margin-top: 3px;
            font-size: 7px;
            text-align: center;
        }

        /* =====================================================
           FOOTER
        ===================================================== */

        .footer {
            position: fixed;
            bottom: -2px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 6px;
        }
    </style>
</head>

<body>

    @php

        /*
    |--------------------------------------------------------------------------
    | DATA ITEM PEMERIKSAAN
    |--------------------------------------------------------------------------
    */

        $mainModul = [
            'Letak modul sesuai lokasi yang disepakati',
            'Bracket / baut pengunci',
            'Led indicator (merah, kuning)',
            'Port WLAN',
            'Konektor input',
            'Cooling perangkat',
            'Box / cover main modul',
        ];

        $layarDisplay = [
            'Pemasangan sesuai lokasi yang disepakati',
            'Bracket / baut pengunci',
            'Kondisi layar',
            'Kabel charger / input power',
            'Input power DC in',
        ];

        $other = [
            'Sambungan kabel',
            'Kabel komunikasi data (PLM/TELEMETRY)',
            'Antena LTE & GPS',
            'Bracket & baut antenna',
            'Push to MQTT server',
            'Koneksi WiFi konfigurasi',
            'Kabel terconduit / terproteksi',
        ];

        /*
    |--------------------------------------------------------------------------
    | ITEM PEMERIKSAAN
    |--------------------------------------------------------------------------
    */

        $items = $ofa->item_pemeriksaan ?? [];

        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }

        /*
    |--------------------------------------------------------------------------
    | CARI ITEM
    |--------------------------------------------------------------------------
    */

        $findItem = function ($nama) use ($items) {
            /*
        | Format:
        | [
        |   [
        |       'nama' => '...',
        |       'status' => 'baik',
        |       'keterangan' => '...'
        |   ]
        | ]
        */

            foreach ($items as $key => $item) {
                if (is_array($item)) {
                    if (isset($item['nama']) && strtolower(trim($item['nama'])) === strtolower(trim($nama))) {
                        return $item;
                    }

                    /*
                | Kalau JSON berbentuk:
                | "nama_item": {
                |     "status": "baik",
                |     "keterangan": "..."
                | }
                */

                    if (strtolower(trim($key)) === strtolower(trim($nama)) && is_array($item)) {
                        return $item;
                    }
                }
            }

            return null;
        };

        /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

        $getStatus = function ($item) {
            if (!$item) {
                return '';
            }

            return strtolower(trim($item['status'] ?? ''));
        };

        /*
    |--------------------------------------------------------------------------
    | NORMALISASI STATUS
    |--------------------------------------------------------------------------
    */

        $normalizeStatus = function ($status) {
            $status = strtolower(trim($status));

            if ($status === 'baik') {
                return 'baik';
            }

            if (in_array($status, ['rusak', 'tidak_baik', 'tidak baik'])) {
                return 'rusak';
            }

            if (in_array($status, ['na', 'n/a', 'tidak_tersedia', 'tidak tersedia'])) {
                return 'na';
            }

            return '';
        };

        /*
    |--------------------------------------------------------------------------
    | TIM PELAKSANA
    |--------------------------------------------------------------------------
    */

        $tim = $ofa->tim_pelaksana ?? [];

        if (is_string($tim)) {
            $tim = json_decode($tim, true) ?? [];
        }

    @endphp


    {{-- =========================================================
     KOP
========================================================= --}}

    <table class="kop">

        <tr>

            <td rowspan="5" class="kop-logo">

                <img src="{{ public_path('images/logo-ppa-3.png') }}" class="logo-ppa">



            </td>


            <td class="kop-title">
                FORM &amp; CHECKLIST
            </td>


            <td class="kop-doc-label">
                No. Dokumen
            </td>

            <td class="kop-doc-value">
                PPA-ADRO-F-ICTMD-032
            </td>

        </tr>


        <tr>

            <td class="kop-title kop-title-main">
                INSPEKSI PERANGKAT ONBOARD FLEETSAFE ASSIST
            </td>

            <td class="kop-doc-label">
                Revisi
            </td>

            <td class="kop-doc-value">
                {{ $ofa->revisi ?? '1' }}
            </td>

        </tr>


        <tr>

            <td class="kop-title kop-ofa">
                (OFA)
            </td>

            <td class="kop-doc-label">
                Tgl. Efektif
            </td>

            <td class="kop-doc-value">
                {{ $ofa->tgl_efektif ?? '01-Agu-25' }}
            </td>

        </tr>


        <tr>

            <td class="kop-project">

                <span class="kop-label">
                    PROJECT NAME
                </span>

                <span class="kop-colon">
                    :
                </span>

                <span class="kop-value">
                    {{ $ofa->project_name ?: 'OFA' }}
                </span>


                <span class="kop-version-label">
                    VERSION
                </span>

                <span class="kop-colon">
                    :
                </span>

                <span class="kop-value">
                    {{ $ofa->version ?: '' }}
                </span>

            </td>


            <td class="kop-doc-label">
                Halaman
            </td>

            <td class="kop-doc-value">
                1 dari 1
            </td>

        </tr>


        <tr>

            <td class="kop-department">

                <span class="kop-label">
                    DIVISI / DEPARTEMEN
                </span>

                <span class="kop-colon">
                    :
                </span>

                <span class="kop-value">
                    {{ $ofa->divisi_department ?: '' }}
                </span>

            </td>

            <td class="kop-empty"></td>

            <td class="kop-empty"></td>

        </tr>

    </table>



    {{-- =========================================================
     IDENTITAS UNIT
========================================================= --}}

    <table class="identity">

        <tr>

            <td class="identity-label">
                TYPE UNIT
            </td>

            <td class="identity-value">
                : {{ $ofa->type_unit ?: '' }}
            </td>

            <td class="identity-label">
                JOBSITE
            </td>

            <td class="identity-value">
                : {{ $ofa->jobsite ?: '' }}
            </td>

        </tr>


        <tr>

            <td class="identity-label">
                CODE NUMBER UNIT
            </td>

            <td class="identity-value">
                : {{ $ofa->code_number_unit ?: '' }}
            </td>

            <td class="identity-label">
                DATE
            </td>

            <td class="identity-value">

                :
                {{ $ofa->created_at?->format('d-m-Y') ?: '' }}

            </td>

        </tr>


        <tr>

            <td class="identity-label">
                SERIAL NUMBER MODUL
            </td>

            <td class="identity-value">
                : {{ $ofa->serial_number_modul ?: '' }}
            </td>

            <td class="identity-label">
                LOCATION
            </td>

            <td class="identity-value">
                : {{ $ofa->location ?: '' }}
            </td>

        </tr>

    </table>



    {{-- =========================================================
     CHECKLIST
========================================================= --}}

    <table class="checklist">

        <thead>

            <tr class="checklist-header">

                <th rowspan="2" class="no-col">
                    No.
                </th>

                <th rowspan="2" class="item-col">
                    Item pemeriksaan
                </th>

                <th colspan="3" class="condition-title">
                    Kondisi
                </th>

                <th rowspan="2" class="note-col">
                    Keterangan
                </th>

            </tr>


            <tr class="condition-sub">

                <th class="condition-col">
                    Baik
                </th>

                <th class="condition-col">
                    Rusak
                </th>

                <th class="condition-col">
                    N/A
                </th>

            </tr>

        </thead>


        <tbody>


            {{-- =================================================
             A. MAIN MODUL
        ================================================== --}}

            <tr class="section-row">

                <td colspan="6">
                    A. &nbsp; MAIN MODUL
                </td>

            </tr>


            @foreach ($mainModul as $index => $namaItem)
                @php

                    $item = $findItem($namaItem);

                    $status = $normalizeStatus($getStatus($item));

                @endphp


                <tr class="item-row">

                    <td class="no">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $namaItem }}
                    </td>

                    <td class="status">
                        {{ $status === 'baik' ? '✓' : '' }}
                    </td>

                    <td class="status">
                        {{ $status === 'rusak' ? '✓' : '' }}
                    </td>

                    <td class="status">
                        {{ $status === 'na' ? '✓' : '' }}
                    </td>

                    <td>
                        {{ $item['keterangan'] ?? '' }}
                    </td>

                </tr>
            @endforeach



            {{-- =================================================
             B. LAYAR DISPLAY
        ================================================== --}}

            <tr class="section-row">

                <td colspan="6">
                    B. &nbsp; LAYAR DISPLAY
                </td>

            </tr>


            @foreach ($layarDisplay as $index => $namaItem)
                @php

                    $item = $findItem($namaItem);

                    $status = $normalizeStatus($getStatus($item));

                @endphp


                <tr class="item-row">

                    <td class="no">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $namaItem }}
                    </td>

                    <td class="status">
                        {{ $status === 'baik' ? '✓' : '' }}
                    </td>

                    <td class="status">
                        {{ $status === 'rusak' ? '✓' : '' }}
                    </td>

                    <td class="status">
                        {{ $status === 'na' ? '✓' : '' }}
                    </td>

                    <td>
                        {{ $item['keterangan'] ?? '' }}
                    </td>

                </tr>
            @endforeach



            {{-- =================================================
             C. OTHER
        ================================================== --}}

            <tr class="section-row">

                <td colspan="6">
                    C. &nbsp; OTHER
                </td>

            </tr>


            @foreach ($other as $index => $namaItem)
                @php

                    $item = $findItem($namaItem);

                    $status = $normalizeStatus($getStatus($item));

                @endphp


                <tr class="item-row">

                    <td class="no">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $namaItem }}
                    </td>

                    <td class="status">
                        {{ $status === 'baik' ? '✓' : '' }}
                    </td>

                    <td class="status">
                        {{ $status === 'rusak' ? '✓' : '' }}
                    </td>

                    <td class="status">
                        {{ $status === 'na' ? '✓' : '' }}
                    </td>

                    <td>
                        {{ $item['keterangan'] ?? '' }}
                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>



    {{-- =========================================================
     CATATAN TAMBAHAN
========================================================= --}}

    <table class="notes">

        <tr>

            <td class="notes-title">
                Catatan tambahan :
            </td>

        </tr>


        <tr>

            <td class="notes-content">

                @if ($ofa->catatan_tambahan)
                    {!! nl2br(e($ofa->catatan_tambahan)) !!}
                @else
                    <div class="notes-line"></div>
                    <div class="notes-line"></div>
                    <div class="notes-line"></div>
                    <div class="notes-line"></div>
                @endif

            </td>

        </tr>

    </table>



    {{-- =========================================================
     D. TIM PELAKSANA
========================================================= --}}

    <table class="team">

        <tr>
            <td colspan="7" class="team-title">
                D. &nbsp; TIM PELAKSANA
            </td>
        </tr>

        <tr>
            <th class="no">No</th>
            <th class="nama">Nama</th>
            <th class="nrp">NRP</th>
            <th class="jabatan">Jabatan</th>
            <th class="departemen">Departemen</th>
            <th class="perusahaan">Perusahaan</th>
            <th class="ttd">Tanda tangan</th>
        </tr>

        @for ($i = 0; $i < 5; $i++)
            @php
                $anggota = $tim[$i] ?? [];
            @endphp

            <tr>

                <td class="no center">
                    {{ $i + 1 }}
                </td>

                <td>
                    {{ $anggota['nama'] ?? '' }}
                </td>

                <td>
                    {{ $anggota['nrp'] ?? '' }}
                </td>

                <td>
                    {{ $anggota['jabatan'] ?? '' }}
                </td>

                <td>
                    {{ $anggota['departemen'] ?? '' }}
                </td>

                <td>
                    {{ $anggota['perusahaan'] ?? '' }}
                </td>

                <td></td>

            </tr>
        @endfor

    </table>

    {{-- =========================================================
     TANDA TANGAN
========================================================= --}}

    <table class="approval">

        <tr>

            {{-- =========================
             DISETUJUI
        ========================== --}}
            <td class="signature-box">

                <div class="signature-title">
                    DISETUJUI
                </div>

                <div class="signature-space">
                   
                    @if ($ofa->approved_at && $ofa->qr_code_persetujuan)
                        @php
                            $qr = new \chillerlan\QRCode\QRCode();
                            $qrCode = $qr->render($ofa->qr_code_persetujuan);
                        @endphp
                        <div style="margin-top: 4px; text-align: center;">
                            <img src="{{ $qrCode }}" width="48" height="48" alt="QR Code"
                                style="display: block; margin: 0 auto;">
                        </div>
                    @endif
                </div>

                <div class="signature-line"></div>

                <div class="signature-name">
                    {{ $ofa->diperiksa_oleh ?: 'GROUP LEADER' }}
                </div>


            </td>


            {{-- =========================
             DIPERIKSA
        ========================== --}}
            <td class="signature-box">

                <div class="signature-title">
                    DIPERIKSA
                </div>

                <div class="signature-space">
                    @php
                            $qr = new \chillerlan\QRCode\QRCode();
                            $qrCode = $qr->render($ofa->karyawanId->qr_code);
                        @endphp
                        <div style="margin-top: 4px; text-align: center;">
                            <img src="{{ $qrCode }}" width="48" height="48" alt="QR Code"
                                style="display: block; margin: 0 auto;">
                        </div>
                </div>

                <div class="signature-line"></div>

                <div class="signature-name">
                    {{ $ofa->karyawanId->nama ?: 'ICT TECHNICIAN' }}
                </div>

            </td>

        </tr>

    </table>


    {{-- =========================================================
     FOOTER
========================================================= --}}

    <div class="footer">
        PPA-ADRO-F-ICTMD-032
    </div>

</body>

</html>
