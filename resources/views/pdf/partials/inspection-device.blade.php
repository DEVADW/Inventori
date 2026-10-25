<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 18px 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #000;
        }

        /* ===== KOP DOKUMEN ===== */
        table.kop {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.kop td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
        }

        .kop-logo {
            width: 14%;
            text-align: center;
        }

        .kop-logo img {
            max-width: 60px;
            max-height: 60px;
        }

        .kop-title {
            width: 56%;
            text-align: center;
        }

        .kop-title h1 {
            font-size: 14px;
            margin: 0 0 2px;
        }

        .kop-title .dept {
            font-size: 10px;
            font-weight: bold;
            margin: 0;
        }

        .kop-title .dept-full {
            font-size: 9px;
            margin: 0;
        }

        .kop-info {
            width: 30%;
            padding: 0 !important;
        }

        .kop-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-info table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 8px;
        }

        .kop-info table td:first-child {
            width: 55%;
        }

        .kop-info table td:nth-child(2) {
            width: 6%;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        th {
            background: #d9d9d9;
            text-align: center;
        }

        .section-header th {
            background: #bfbfbf;
            text-align: left;
        }

        .identity td.label {
            width: 17%;
            font-weight: bold;
        }

        .identity td.colon {
            width: 2%;
            text-align: center;
        }

        .identity td.value {
            width: 33%;
        }

        .identity td.empty {
            background: #fff;
        }

        .inspection {
            margin-top: 0;
        }

        .inspection .item {
            width: 38%;
        }

        .inspection .check {
            width: 8%;
            text-align: center;
        }

        .notes {
            min-height: 52px;
            height: 10%;
            white-space: pre-line;
        }

        .signature {
            width: 70%;
            margin: 18px auto 0;
            table-layout: fixed;
        }

        .signature-header {
            background: #bfbfbf;
            text-align: center;
            font-weight: bold;
        }

        .signature-space {
            height: 66px;
            text-align: center;
            vertical-align: middle;
        }

        .signature-name,
        .signature-position {
            border-top: 0;
            text-align: center;
        }

        .signature-name {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
        // --- Data inspektor & approver (dinamis dari relasi user) ---
        $inspectorName = $inspection->inspektorUser?->nama ?? ($inspection->inspektor ?? '');
        $inspectorQr = $inspection->inspektorUser?->qr_code ?? $inspection->inspektor;

        // --- Info dokumen (No. Dokumen / Revisi / Tgl Efektif / Halaman) ---
        // Departemen diambil dari $documentInfo supaya bisa beda-beda tergantung
        // jenis inspeksi (UPS, Genset, AC, dll) tanpa mengubah struktur template.
        $noDokumen = $documentInfo['no_dokumen'] ?? '-';
        $revisi = $documentInfo['revisi'] ?? '-';
        $tglEfektif = $documentInfo['tgl_efektif'] ?? '-';
        $halaman = $documentInfo['halaman'] ?? '1';
        $deptLabel = $documentInfo['dept_label'] ?? 'ICT';
        $deptFull = $documentInfo['dept_full'] ?? 'Information Communication & Technology';
        $logoPath = $documentInfo['logo_path'] ?? public_path('images/logo-ppa.png');

        // --- Field IDENTITAS PERANGKAT ---
        // Tetap dinamis: controller mengirim $identityFields sebagai array
        // asosiatif berurutan sesuai urutan tampil yang diinginkan
        // (kiri-kanan, kiri-kanan, ...), persis seperti urutan di form PPA.
        // Template hanya membaginya 2 per baris. Fallback di bawah dipakai
        // kalau controller belum mengirim variabel ini.
        $identityFields = $identityFields ?? [
            'Nomor Aset' => $inspection->nomor_aset,
            'Departemen' => $inspection->departemen,
            'Merek' => $inspection->merek,
            'Lokasi' => $inspection->lokasi,
            'Tipe / Model' => $inspection->type,
            'Tanggal Inspeksi' => $inspection->tanggal_inspeksi?->format('d-m-Y'),
            'Serial Number' => $inspection->sn,
        ];

        // Pecah jadi baris berisi maksimal 2 pasang label:value, sama seperti
        // grid 4 kolom (Label | Value | Label | Value) pada dokumen PPA.
        $identityRows = collect($identityFields)->map(fn($value, $label) => [$label, $value])->values()->chunk(2);

        // Helper tampilan nilai: pakai '-' hanya untuk null/string kosong,
        // supaya nilai legit seperti "0" tidak ikut ditimpa jadi '-'.
        $displayValue = fn($value) => $value !== null && $value !== '' ? $value : '-';

        // Satu instance QRCode dipakai ulang untuk inspektor & approver,
        // tidak perlu di-instantiate dua kali.
        $qr = new \chillerlan\QRCode\QRCode();
    @endphp

    {{-- ===== KOP DOKUMEN ===== --}}
    <table class="kop">
        <tr>
            <td class="kop-logo">
                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo">
                @endif
            </td>
            <td class="kop-title">
                <h1>INSPEKSI {{ strtoupper($deviceName) }}</h1>
                <p class="dept">{{ $deptLabel }}</p>
                <p class="dept-full">({{ $deptFull }})</p>
            </td>
            <td class="kop-info">
                <table>
                    <tr>
                        <td>No. Dokumen</td>
                        <td>:</td>
                        <td>{{ $noDokumen }}</td>
                    </tr>
                    <tr>
                        <td>Revisi</td>
                        <td>:</td>
                        <td>{{ $revisi }}</td>
                    </tr>
                    <tr>
                        <td>Tgl Efektif</td>
                        <td>:</td>
                        <td>{{ $tglEfektif }}</td>
                    </tr>
                    <tr>
                        <td>Halaman</td>
                        <td>:</td>
                        <td>{{ $halaman }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== IDENTITAS PERANGKAT ===== --}}
    <table class="identity">
        <colgroup>
            <col style="width: 17%">
            <col style="width: 33%">
            <col style="width: 17%">
            <col style="width: 33%">
        </colgroup>
        <thead class="section-header">
            <tr>
                <th colspan="4">IDENTITAS PERANGKAT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($identityRows as $row)
                <tr>
                    @foreach ($row as [$label, $value])
                        <td class="label">{{ $label }}</td>
                        <td class="value">{{ $displayValue($value) }}</td>
                    @endforeach
                    {{-- Kalau baris cuma punya 1 pasang (jumlah field ganjil),
                         genapkan lebar grid dengan sel kosong seperti baris
                         terakhir "S/N" pada dokumen PPA. --}}
                    @if ($row->count() === 1)
                        <td class="label empty">&nbsp;</td>
                        <td class="value empty">&nbsp;</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ===== KONDISI PEMERIKSAAN (field dinamis sesuai jenis inspeksi) ===== --}}
    <table class="inspection">
        <thead>
            <tr class="section-header">
                <th colspan="4">KONDISI PEMERIKSAAN</th>
            </tr>
            <tr>
                <th class="item">Media yang diperiksa</th>
                <th class="check">Baik</th>
                <th class="check">Tidak</th>
                <th>Tindakan perbaikan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inspectionItems as $field => [$label, $actionField])
                <tr>
                    <td>{{ $label }}</td>
                    <td class="check">✓</td>
                    <td class="check">{{ $inspection->$field === 'tidak' ? '✓' : '' }}</td>
                    <td>{{ $actionField ? $inspection->$actionField ?? '' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ===== KETERANGAN ===== --}}
    <table>
        <thead class="section-header">
            <tr>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="notes">{{ $inspection->keterangan ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ===== TANDA TANGAN ===== --}}
    <table class="signature">
        <tr>
            <td class="signature-header">Inspektor ({{ $deptLabel }})</td>
            <td class="signature-header">Diketahui oleh</td>
        </tr>
        <tr>
            <td class="signature-space">
                @if ($inspectorQr)
                    <img src="{{ $qr->render($inspectorQr) }}" width="60" height="60" alt="QR Inspektor">
                @endif
            </td>
            <td class="signature-space">
                @if ($inspection->approved_at && $inspection->qr_code_persetujuan)
                    <img src="{{ $qr->render($inspection->qr_code_persetujuan) }}" width="60" height="60"
                        alt="QR Persetujuan">
                @endif
            </td>
        </tr>
        <tr>
            <td class="signature-name">{{ $inspectorName }}</td>
            <td class="signature-name">{{ $inspection->approved_by ?? '' }}</td>
        </tr>
        <tr>
            <td class="signature-position">{{ $inspection->jabatan_inspektor ?? $deptLabel }}</td>
            <td class="signature-position">{{ $documentInfo['jabatan_approver'] ?? 'GL ' . $deptLabel }}</td>
        </tr>
    </table>
</body>

</html>
