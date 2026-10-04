<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Form Inspeksi Perangkat Monitor SS6</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 10mm 10mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        .no-border {
            border: none !important;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .header-table td {
            border: 1px solid #000;
        }

        .logo-area {
            width: 18%;
            height: 70px;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            font-size: 12px;
        }

        .title-area {
            width: 57%;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }

        .title-main {
            font-size: 14px;
            line-height: 18px;
        }

        .title-sub {
            font-size: 12px;
            line-height: 16px;
        }

        .document-info {
            width: 25%;
            font-size: 8px;
        }

        .document-info td {
            padding: 3px 4px;
        }

        .document-info .label {
            width: 45%;
        }

        .section-title {
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            background: #eaeaea;
        }

        .asset-table {
            margin-top: 3px;
            margin-bottom: 6px;
        }

        .asset-table td {
            height: 25px;
        }

        .asset-label {
            width: 22%;
            font-weight: bold;
        }

        .asset-value {
            width: 28%;
        }

        .condition-table {
            margin-top: 3px;
        }

        .condition-table th {
            font-weight: bold;
            text-align: center;
            background: #eaeaea;
            height: 25px;
        }

        .condition-name {
            width: 43%;
            font-weight: bold;
        }

        .condition-option {
            width: 14.25%;
            text-align: center;
        }

        .condition-table td {
            height: 24px;
        }

        .check {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            font-family: "DejaVu Sans", sans-serif;
            line-height: 1;
        }

        .checkbox-mark {
            display: inline-block;
            width: 16px;
            height: 16px;
            line-height: 15px;
            font-size: 13px;
            font-weight: bold;
            font-family: "DejaVu Sans", sans-serif;
            text-align: center;
            vertical-align: middle;
        }

        /* .checkbox-mark.checked {
            border: 1px solid #000;
         } */

        .checkbox-mark.checked::after {
            content: "✓";
        }

        .instruction {
            font-weight: bold;
            font-size: 8px;
            margin: 4px 0;
        }

        .charger-table {
            margin-top: 6px;
        }

        .charger-label {
            width: 43%;
            font-weight: bold;
        }

        .charger-value {
            width: 57%;
        }

        .keterangan-title {
            font-weight: bold;
            margin-top: 6px;
            margin-bottom: 2px;
        }

        .keterangan-box {
            height: 65px;
            vertical-align: top;
            padding: 6px;
        }

        .bottom-table {
            margin-top: 6px;
        }

        .bottom-table td {
            height: 28px;
        }

        .serial-label {
            width: 22%;
            font-weight: bold;
        }

        .serial-value {
            width: 28%;
        }

        .inspection-title {
            font-weight: bold;
            text-align: center;
        }

        .signature-table {
            margin-top: 6px;
        }

        .signature-table td {
            height: 85px;
            vertical-align: top;
        }

        .signature-label {
            font-weight: bold;
            text-align: left;
        }

        .signature-space {
            height: 58px;
            display: flex;
            align-content: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 15px;
        }

        .signature-name {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-role {
            text-align: center;
            font-size: 8px;
        }

        .footer-table {
            margin-top: 5px;
            font-size: 7px;
        }

        .footer-table td {
            padding: 3px;
        }

        .footer-left {
            width: 33%;
        }

        .footer-center {
            width: 34%;
            text-align: center;
        }

        .footer-right {
            width: 33%;
            text-align: right;
        }
    </style>
</head>

<body>

    @php
        /*
         * Data kondisi disimpan sebagai JSON, contoh:
         * {"ketersediaan":"Ada","kondisi":"Baik"}
         *
         * Fungsi di bawah menangani dua kondisi:
         * 1. Field sudah di-cast Laravel menjadi array
         * 2. Field masih berupa JSON string dari database
         */
        $conditionData = function ($value) {
            if (is_array($value)) {
                return [
                    'ketersediaan' => strtolower(trim((string) ($value['ketersediaan'] ?? ''))),
                    'kondisi' => strtolower(trim((string) ($value['kondisi'] ?? ''))),
                ];
            }

            if (is_string($value)) {
                $decoded = json_decode($value, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return [
                        'ketersediaan' => strtolower(trim((string) ($decoded['ketersediaan'] ?? ''))),
                        'kondisi' => strtolower(trim((string) ($decoded['kondisi'] ?? ''))),
                    ];
                }
            }

            return [
                'ketersediaan' => '',
                'kondisi' => '',
            ];
        };

        /*
         * ADA / TIDAK dibaca dari ketersediaan.
         * BAIK / RUSAK dibaca dari kondisi.
         */
        $isChecked = function ($value, $option) use ($conditionData) {
            $data = $conditionData($value);
            $option = strtolower(trim($option));

            if ($option === 'ada') {
                return $data['ketersediaan'] === 'ada';
            }

            if ($option === 'tidak') {
                return in_array($data['ketersediaan'], ['tidak', 'tidak ada', 'tidak tersedia'], true);
            }

            if ($option === 'baik') {
                return $data['kondisi'] === 'baik';
            }

            if ($option === 'rusak') {
                return $data['kondisi'] === 'rusak';
            }

            return false;
        };
    @endphp


    {{-- ============================================================
        HEADER
    ============================================================= --}}
    <table class="header-table">
        <tr>

            {{-- Logo / identitas perusahaan --}}
            <td class="logo-area">
                <img src="{{ public_path('asset/images/logos/logo_ppa.jpeg') }}" height="70px" width="70px"
                    alt="logo_ppa">
            </td>

            {{-- Judul --}}
            <td class="title-area">

                <div class="title-main">
                    FORM INSPEKSI PERANGKAT
                </div>

                <div class="title-sub">
                    MONITOR SS6
                </div>

            </td>

            {{-- Informasi dokumen --}}
            <td class="document-info">

                <table>

                    <tr>
                        <td class="label">
                            No. Dokumen
                        </td>

                        <td>
                            PPA-ADRO-F-ICTMD-012
                        </td>
                    </tr>

                    <tr>
                        <td class="label">
                            Revisi
                        </td>

                        <td>
                            2
                        </td>
                    </tr>

                    <tr>
                        <td class="label">
                            Tgl. Efektif
                        </td>

                        <td>
                            01 September 2024
                        </td>
                    </tr>

                    <tr>
                        <td class="label">
                            Halaman
                        </td>

                        <td>
                            1 dari 1
                        </td>
                    </tr>

                </table>

            </td>

        </tr>
    </table>


    {{-- ============================================================
        DETAIL ASET
    ============================================================= --}}
    <table class="asset-table">

        <tr>
            <td colspan="4" class="section-title">
                DETAIL ASET
            </td>
        </tr>

        <tr>

            <td class="asset-label">
                No Asset
            </td>

            <td class="asset-value">
                {{ $inspeksi->no_asset ?? '-' }}
            </td>

            <td class="asset-label">
                No Lambung Unit
            </td>

            <td class="asset-value">
                {{ $inspeksi->no_lambung ?? '-' }}
            </td>

        </tr>

        <tr>

            <td class="asset-label">
                Tanggal Inspeksi
            </td>

            <td class="asset-value">
                @if (!empty($inspeksi->tanggal_inspeksi))
                    {{ \Carbon\Carbon::parse($inspeksi->tanggal_inspeksi)->format('d/m/Y') }}
                @else
                    -
                @endif
            </td>

            <td class="asset-label">
                Serial Number
            </td>

            <td class="asset-value">
                {{ $inspeksi->serial_number ?? '-' }}
            </td>

        </tr>

    </table>


    {{-- ============================================================
        INSTRUKSI
    ============================================================= --}}
    <div class="instruction">
        (BERI TANDA ✓ ATAU X UNTUK PILIHAN)
    </div>


    {{-- ============================================================
        KONDISI PERANGKAT
    ============================================================= --}}
    <table class="condition-table">

        <thead>

            <tr>

                <th class="condition-name">
                    KONDISI PERANGKAT
                </th>

                <th class="condition-option">
                    ADA
                </th>

                <th class="condition-option">
                    TIDAK
                </th>

                <th class="condition-option">
                    BAIK
                </th>

                <th class="condition-option">
                    RUSAK
                </th>

            </tr>

        </thead>

        <tbody>

            {{-- Monitor --}}
            <tr>

                <td class="condition-name">
                    Kondisi Monitor
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_monitor ?? null, 'ada') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_monitor ?? null, 'tidak') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_monitor ?? null, 'baik') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_monitor ?? null, 'rusak') ? 'checked' : '' }}"></span>
                </td>

            </tr>


            {{-- Bracket --}}
            <tr>

                <td class="condition-name">
                    Kondisi Bracket
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_bracket ?? null, 'ada') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_bracket ?? null, 'tidak') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_bracket ?? null, 'baik') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_bracket ?? null, 'rusak') ? 'checked' : '' }}"></span>
                </td>

            </tr>


            {{-- Car Charger --}}
            <tr>

                <td class="condition-name">
                    Kondisi Car Charger
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_car_charger ?? null, 'ada') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_car_charger ?? null, 'tidak') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_car_charger ?? null, 'baik') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_car_charger ?? null, 'rusak') ? 'checked' : '' }}"></span>
                </td>

            </tr>


            {{-- Kabel Power --}}
            <tr>

                <td class="condition-name">
                    Kondisi Kabel Power
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_kabel_power ?? null, 'ada') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_kabel_power ?? null, 'tidak') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_kabel_power ?? null, 'baik') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_kabel_power ?? null, 'rusak') ? 'checked' : '' }}"></span>
                </td>

            </tr>


            {{-- APP Lock --}}
            <tr>

                <td class="condition-name">
                    Software APP Lock
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_app_lock ?? null, 'ada') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_app_lock ?? null, 'tidak') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_app_lock ?? null, 'baik') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_app_lock ?? null, 'rusak') ? 'checked' : '' }}"></span>
                </td>

            </tr>


            {{-- PPA Teams --}}
            <tr>

                <td class="condition-name">
                    Software PPA Teams
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->software_ppa_teams ?? null, 'ada') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->software_ppa_teams ?? null, 'tidak') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->software_ppa_teams ?? null, 'baik') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->software_ppa_teams ?? null, 'rusak') ? 'checked' : '' }}"></span>
                </td>

            </tr>


            {{-- Baterai --}}
            <tr>

                <td class="condition-name">
                    Kondisi Baterai Monitor
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_baterai ?? null, 'ada') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_baterai ?? null, 'tidak') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_baterai ?? null, 'baik') ? 'checked' : '' }}"></span>
                </td>

                <td class="condition-option check">
                    <span
                        class="checkbox-mark {{ $isChecked($inspeksi->kondisi_baterai ?? null, 'rusak') ? 'checked' : '' }}"></span>
                </td>

            </tr>

        </tbody>

    </table>


    {{-- ============================================================
        OUTPUT POWER CHARGER
    ============================================================= --}}
    <table class="charger-table">

        <tr>

            <td class="charger-label">
                Output Power Charger
            </td>

            <td class="charger-value">

                {{ $inspeksi->output_powercharge ?? '-' }}

                Volt DC

            </td>

        </tr>

    </table>


    {{-- ============================================================
        KETERANGAN
    ============================================================= --}}
    <div class="keterangan-title">
        KETERANGAN :
    </div>

    <table>

        <tr>

            <td class="keterangan-box">

                {!! nl2br(e($inspeksi->keterangan ?? '')) !!}

            </td>

        </tr>

    </table>


    {{-- ============================================================
        DATA INSPEKSI
    ============================================================= --}}
    <table class="bottom-table">

        <tr>

            <td class="serial-label">
                Serial Number
            </td>

            <td class="serial-value">
                {{ $inspeksi->serial_number ?? '-' }}
            </td>

            <td class="inspection-title">
                Di Inspeksi Oleh
            </td>

        </tr>

        <tr>

            <td class="serial-label">
                Di Inspeksi Oleh
            </td>

            <td class="serial-value">

                {{ $inspeksi->QrCodeKaryawan->nama ?? '-' }}

            </td>

            <td class="text-center">

                {{ $inspeksi->jabatan_inspektor ?? 'ICT' }}

            </td>

        </tr>

    </table>


    {{-- ============================================================
        TANDA TANGAN
    ============================================================= --}}
    <table class="signature-table">

        <tr>

            <td style="width: 50%;">

                <div class="signature-label">
                    Di Inspeksi Oleh;
                </div>

                <div class="signature-space">
                  <div class="signature-space" style="text-align: center;">
                        @php
                            $qr = new \chillerlan\QRCode\QRCode();
                            $qrCode = $qr->render($inspeksi->QrCodeKaryawan->qr_code);
                        @endphp
                        <div style="margin-top: 4px; text-align: center;">
                            <img src="{{ $qrCode }}" width="70" height="70" alt="QR Code"
                                style="display: block; margin: 0 auto; margin-bottom: 30px;">
                        </div>
                </div>

                </div>

                <div class="signature-name">

                    {{ $inspeksi->QrCodeKaryawan->nama ?? '.................................' }}

                </div>

                <div class="signature-role">

                    ICT

                </div>

            </td>


            <td style="width: 50%;">

                <div class="signature-label">
                    Diperiksa Oleh;
                </div>

                <div class="signature-space" style="text-align: center;">
                    @if ($inspeksi->approved_at && $inspeksi->qr_code_persetujuan)
                        @php
                            $qr = new \chillerlan\QRCode\QRCode();
                            $qrCode = $qr->render($inspeksi->qr_code_persetujuan);
                        @endphp
                        <div style="margin-top: 4px; text-align: center;">
                            <img src="{{ $qrCode }}" width="70" height="70" alt="QR Code"
                                style="display: block; margin: 0 auto;">
                        </div>
                    @endif
                </div>

                <div class="signature-name">

                    {{ $inspeksi->diperiksa_oleh ?? '.................................' }}

                </div>

                <div class="signature-role">

                  GL ICT

                </div>

            </td>

        </tr>

    </table>


    {{-- ============================================================
        FOOTER
    ============================================================= --}}
    <table class="footer-table">

        <tr>

            <td class="footer-left">
                PPA-ADRO-F-ICTMD-012
            </td>

            <td class="footer-center">
                FORM INSPEKSI PERANGKAT MONITOR SS6
            </td>

            <td class="footer-right">
                Revisi 2
            </td>

        </tr>

    </table>

</body>

</html>
