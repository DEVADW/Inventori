<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 portrait;
            margin: 6mm 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
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
            border: 1px solid #777;
            padding: 2px;
            vertical-align: middle;
        }

        /* =========================
           HEADER
        ========================= */

        .header {
            height: 75px;
            table-layout: fixed;
        }

        .logo-cell {
            width: 15%;
            height: 50px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
        }

        .logo {
            width: 50px;
            height: 50px;
        }

        .title-cell {
            width: 50%;
            text-align: center;
            vertical-align: middle;
        }

        .title {
            font-size: 12px;
            font-weight: bold;
            line-height: 1.25;
        }

        .subtitle {
            font-size: 8px;
            font-weight: bold;
            margin-top: 4px;
        }

        .document-cell {
            width: 35%;
            padding: 0;
        }

        .document-table {
            width: 100%;
        }

        .document-table td {
            height: 18px;
            padding: 1px 4px;
        }

        .document-label {
            width: 42%;
        }

        /* =========================
           IDENTITAS
        ========================= */

        .identity {
            width: 55%;
            margin-top: 6px;
        }

        .identity td {
            height: 17px;
            padding: 2px 5px;
        }

        .identity-label {
            width: 40%;
        }

        .identity-value {
            width: 60%;
        }

        /* =========================
           TABLE PEMERIKSAAN
        ========================= */

        .inspection {
            margin-top: 6px;
            table-layout: fixed;
        }

        .inspection th {
            background: #bfbfbf;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 2px;
        }

        .inspection td {
            height: 19px;
            padding: 2px 3px;
        }

        .col-no {
            width: 5%;
        }

        .col-item {
            width: 40%;
        }

        .col-status {
            width: 7%;
        }

        .col-catatan {
            width: 20%;
        }

        .col-tindakan {
            width: 21%;
        }

        .item-text {
            line-height: 1.2;
        }

        .center {
            text-align: center;
        }

        .check {
            font-size: 15px;
            text-align: center;
        }

        /* =========================
           NOTE
        ========================= */

        .note {
            margin-top: 6px;
        }

        .note-title {
            background: #bfbfbf;
            font-weight: bold;
            height: 17px;
            padding: 2px 5px;
        }

        .note-content {
            height: 35px;
            vertical-align: top;
        }

        /* =========================
           SIGNATURE
        ========================= */

        .signature {
            width: 70%;
            margin: 6px auto 0 auto;
            table-layout: fixed;
            height: 120px;
            page-break-inside: avoid;
        }

        .signature-header {
            background: #bfbfbf;
            text-align: center;
            height: 17px;
            padding: 2px;
        }

        .signature-space {
            height: 58px;
            vertical-align: middle;
            text-align: center;
        }

        .signature-name {
            height: 15px;
            text-align: center;
        }

        .signature-position {
            height: 15px;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- ============================================================
         HEADER
    ============================================================ --}}
    <table class="header">
        <tr>

            {{-- LOGO --}}
            <td class="logo-cell">
                <img src="{{ public_path('images/logo-ppa-3.png') }}" class="logo" width="60" height="60"
                    style="width:60px; height:60px;">
            </td>

            {{-- TITLE --}}
            <td class="title-cell">

                <div class="title">
                    PEMELIHARAAN PERANGKAT<br>
                    IN CAR CAMERA (ICC)
                </div>

                <div class="subtitle">
                    (Information Communication &amp; Technology)
                </div>

            </td>

            {{-- DOCUMENT --}}
            <td class="document-cell">

                <table class="document-table">

                    <tr>
                        <td class="document-label">No. Dokumen</td>
                        <td>: PPA-ADRO-F-ICTMD-035</td>
                    </tr>

                    <tr>
                        <td class="document-label">Revisi</td>
                        <td>: 0</td>
                    </tr>

                    <tr>
                        <td class="document-label">Tgl Efektif</td>
                        <td>: 15 Maret 2026</td>
                    </tr>

                    <tr>
                        <td class="document-label">Halaman</td>
                        <td>: 1 dari 1</td>
                    </tr>

                </table>

            </td>

        </tr>
    </table>


    {{-- ============================================================
         IDENTITAS
    ============================================================ --}}
    <table class="identity">

        <tr>
            <td class="identity-label">No. Lambung Unit&nbsp;&nbsp;:</td>
            <td class="identity-value">{{ $icc->no_lambung_unit ?? '' }}</td>
        </tr>

        <tr>
            <td class="identity-label">Tanggal Inspeksi&nbsp;&nbsp;&nbsp;&nbsp;:</td>
            <td class="identity-value">{{ $icc->tanggal_inspeksi?->format('d-m-Y') ?? '' }}</td>
        </tr>

        <tr>
            <td class="identity-label">Lokasi inspeksi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
            <td class="identity-value">{{ $icc->lokasi_inspeksi ?? '' }}</td>
        </tr>

    </table>


    {{-- ============================================================
         TABEL PEMERIKSAAN
    ============================================================ --}}
    <table class="inspection">

        <thead>

            <tr>
                <th rowspan="2" class="col-no">No</th>
                <th rowspan="2" class="col-item">Item Pemeriksaan</th>
                <th colspan="2" class="col-status" style="width: 14%;">Status</th>
                <th rowspan="2" class="col-catatan">Catatan</th>
                <th rowspan="2" class="col-tindakan">Tindakan perbaikan</th>
            </tr>

            <tr>
                <th class="col-status">Iya</th>
                <th class="col-status">Tidak</th>
            </tr>

        </thead>

        <tbody>

            @php
                $storedItems = $icc->item_pemeriksaan ?? [];
                $formItems = \App\Http\Controllers\inspeksi\IccController::CHECKLIST_ITEMS;
                $itemsByName = collect($storedItems)->keyBy('nama');
            @endphp

            @foreach ($formItems as $number => $nama)
                @php
                    $item = $itemsByName->get($nama, []);
                    $status = strtolower($item['status'] ?? '');
                    $keterangan = $item['keterangan'] ?? '';
                    $tindakan = $item['tindakan'] ?? '';

                    // Kalau nama item berformat "Kamera: Lensa bersih...",
                    // bagian sebelum ":" ("Kamera") ditampilkan bold.
                    [$kategori, $deskripsi] = str_contains($nama, ':')
                        ? array_map('trim', explode(':', $nama, 2))
                        : ['', $nama];

                    $statusIya = in_array($status, ['iya', 'ya', 'baik', 'yes']);
                    $statusTidak = in_array($status, ['tidak', 'no', 'rusak']);
                @endphp

                <tr>
                    <td class="center">{{ $number }}.</td>

                    <td class="item-text">
                        @if ($kategori)
                            <strong>{{ $kategori }}:</strong> {{ $deskripsi }}
                        @else
                            {{ $deskripsi }}
                        @endif
                    </td>

                    <td class="check">{{ $statusIya ? '✓' : '' }}</td>
                    <td class="check">{{ $statusTidak ? '✓' : '' }}</td>

                    <td>{{ $keterangan }}</td>
                    <td>{{ $tindakan }}</td>
                </tr>
            @endforeach

        </tbody>

    </table>


    {{-- ============================================================
         NOTE
    ============================================================ --}}
    <table class="note">

        <tr>
            <td class="note-title">Note</td>
        </tr>

        <tr>
            <td class="note-content">{!! nl2br(e($icc->note ?? '')) !!}</td>
        </tr>

    </table>


    {{-- ============================================================
         SIGNATURE
    ============================================================ --}}
    <table class="signature">

        <tr>
            <td class="signature-header">Inspektor</td>
            <td class="signature-header">Diketahui oleh,</td>
        </tr>

        <tr>
            <td class="signature-space">
                @php
                    $qr = new \chillerlan\QRCode\QRCode();
                    $qrCode = $qr->render($icc->IdKaryawan?->qr_code ?? $icc->inspektor);
                @endphp
                <div style="margin-top: 4px; text-align: center;">
                    <img src="{{ $qrCode }}" width="64" height="64" alt="QR Code"
                        style="display: block; margin: 0 auto;">
                </div>
            </td>

            <td class="signature-space">
                @if ($icc->approved_by)
                    @php
                        $qr = new \chillerlan\QRCode\QRCode();
                        $qrCode = $qr->render($icc->qr_code_persetujuan ?? $icc->approved_by);
                    @endphp
                    <div style="margin-top: 4px; text-align: center;">
                        <img src="{{ $qrCode }}" width="64" height="64" alt="QR Code"
                            style="display: block; margin: 0 auto;">
                    </div>
                @endif

            </td>
        </tr>

        <tr>
            <td class="signature-name">
                @if (!empty($icc->inspektor))
                    <strong>{{ $icc->IdKaryawan?->nama }}</strong>
                @endif
            </td>
            <td class="signature-name">
                @if (!empty($icc->approved_by))
                    <strong>{{ $icc->approved_by }}</strong>
                @endif
            </td>
        </tr>

        <tr>
            <td class="signature-position">ICT</td>
            <td class="signature-position">GL ICT</td>
        </tr>

    </table>

</body>

</html>
