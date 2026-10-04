<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Inspeksi Baterai UPS - {{ $ups->nomor_aset ?? '-' }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Dejavu sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
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
            padding: 4px 6px;
            vertical-align: middle;
        }

        /* ===== HEADER ===== */
        .header-table {
            margin-bottom: 0;
        }

        .logo-cell {
            width: 90px;
            text-align: center;
            vertical-align: middle;
        }

        .logo-cell img {
            width: 60px;
            height: auto;
        }

        .title-cell {
            text-align: center;
            vertical-align: middle;
        }

        .title-cell h1 {
            font-size: 15px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }

        .title-cell .subtitle {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
        }

        .title-cell .subtitle2 {
            font-size: 10px;
            margin: 0;
        }

        .doc-label {
            width: 90px;
        }

        /* ===== SECTION TITLE ===== */
        .section-title {
            background-color: #d9d9d9;
            font-weight: bold;
            text-align: left;
        }

        /* ===== IDENTITAS PERANGKAT ===== */
        .identitas-table td.label {
            width: 22%;
        }

        .identitas-table td.value {
            width: 28%;
        }

        /* ===== KONDISI PEMERIKSAAN ===== */
        .kondisi-table th {
            background-color: #fff;
            text-align: center;
            font-weight: bold;
        }

        .kondisi-table td.media {
            width: 34%;
        }

        .kondisi-table td.chk {
            width: 8%;
            text-align: center;
            font-weight: bold;
        }

        .kondisi-table td.tindakan {
            width: 50%;
        }

        /* ===== KETERANGAN ===== */
        .keterangan-box {
            border: 1px solid #000;
            border-top: none;
            min-height: 110px;
            padding: 8px;
            white-space: pre-line;
        }

        /* ===== TANDA TANGAN ===== */
        .ttd-wrapper {
            width: 100%;
            margin-top: 40px;
        }

        .ttd-wrapper>table {
            border: none;
            table-layout: fixed;
        }

        .ttd-wrapper>table>tr>td {
            border: none;
            width: 50%;
            vertical-align: top;
            padding: 0 20px;
        }

        .ttd-wrapper>table>tr>td>table {
            table-layout: fixed;
            width: 100%;
        }

        .ttd-header {
            background-color: #d9d9d9;
            border: 1px solid #000 !important;
            text-align: center;
            font-weight: bold;
            padding: 6px;
        }

        .ttd-space {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            height: 70px;
            text-align: center;
        }

        .ttd-footer {
            border: 1px solid #000;
            border-top: none;
            padding: 6px;
        }

        .ttd-footer div {
            margin-bottom: 2px;
        }

        .ttd-footer .lbl {
            display: inline-block;
            width: 55px;
        }

        .no-print {
            text-align: center;
            margin: 20px 0;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
@php
        // --- Data inspektor & approver (tetap dinamis dari relasi karyawan) ---
        $inspectorName = $inspection->inspektorKaryawan?->nama ?? ($inspection->inspektor ?? '');
        $inspectorQr = $inspection->inspektorKaryawan?->qr_code ?? $inspection->inspektor;

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
    {{-- ===================== HEADER ===================== --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell" rowspan="4">
                <img src="{{ public_path('images/logo-ppa.png') }}" alt="Logo">
            </td>
            <td class="title-cell" rowspan="4" style="width: 55%;">
                <h1>Inspeksi Baterai UPS</h1>
                <p class="subtitle">ICT</p>
                <p class="subtitle2">(Information Communication &amp; Technology)</p>
            </td>
            <td class="doc-label">No. Dokumen</td>
            <td>: PPA-ADRO-F-ICTMD-49</td>
        </tr>
        <tr>
            <td class="doc-label">Revisi</td>
            <td>: 0</td>
        </tr>
        <tr>
            <td class="doc-label">Tgl Efektif</td>
            <td>: 01-Juli-2024</td>
        </tr>
        <tr>
            <td class="doc-label">Halaman</td>
            <td>: 1</td>
        </tr>
    </table>

    <div style="height:15px;"></div>

    {{-- ===================== IDENTITAS PERANGKAT ===================== --}}
    <table class="identitas-table">
        <tr>
            <td class="section-title" colspan="4">IDENTITAS PERANGKAT</td>
        </tr>
        <tr>
            <td class="label">Nomor Aset UPS</td>
            <td class="value">{{ $ups->nomor_aset ?? '' }}</td>
            <td class="label">Departemen</td>
            <td class="value">{{ $ups->departemen ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Merek</td>
            <td class="value">{{ $ups->merek ?? '' }}</td>
            <td class="label">Lokasi</td>
            <td class="value">{{ $ups->lokasi ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Type</td>
            <td class="value">{{ $ups->type ?? '' }}</td>
            <td class="label">Tanggal Inspeksi</td>
            <td class="value">
                {{ $ups->tanggal_inspeksi ? \Carbon\Carbon::parse($ups->tanggal_inspeksi)->translatedFormat('d F Y') : '' }}
            </td>
        </tr>
        <tr>
            <td class="label">S/N</td>
            <td class="value">{{ $ups->sn ?? '' }}</td>
            <td class="label"></td>
            <td class="value"></td>
        </tr>
    </table>

    {{-- ===================== KONDISI PEMERIKSAAN ===================== --}}
    <table class="kondisi-table">
        <tr>
            <td class="section-title" colspan="4">KONDISI PEMERIKSAAN</td>
        </tr>
        <tr>
            <th class="media">Media Yang diperiksa</th>
            <th class="chk">Baik</th>
            <th class="chk">Tidak</th>
            <th>Tindakan Perbaikan</th>
        </tr>

        @php
            $items = [
                [
                    'label' => 'Kondisi Casing UPS',
                    'value' => $ups->casing,
                    'tindakan' => $ups->tindakan_casing,
                ],
                [
                    'label' => 'Kebersihan UPS',
                    'value' => $ups->kebersihan,
                    'tindakan' => $ups->tindakan_kebersihan,
                ],
                [
                    'label' => 'Kondisi kabel adaptor',
                    'value' => $ups->kabel_adaptor,
                    'tindakan' => $ups->tindakan_kabel_adaptor,
                ],
                [
                    'label' => 'Kondisi tombol dan switch',
                    'value' => $ups->tombol_switch,
                    'tindakan' => $ups->tindakan_tombol_switch,
                ],
                [
                    'label' => 'Indikator status (power, battery, load)',
                    'value' => $ups->indikator_status,
                    'tindakan' => $ups->tindakan_indikator_status,
                ],
                [
                    'label' => 'Fungsi alarm',
                    'value' => $ups->fungsi_alarm,
                    'tindakan' => $ups->tindakan_fungsi_alarm,
                ],
                [
                    'label' => 'Respon terhadap kehilangan daya',
                    'value' => $ups->respon_kehilangan_daya,
                    'tindakan' => $ups->tindakan_respon_kehilangan_daya,
                ],
                ['label' => 'Fuse (sekering)', 'value' => $ups->fuse, 'tindakan' => $ups->tindakan_fuse],
            ];
        @endphp

        @foreach ($items as $item)
            <tr>
                <td class="media">{{ $item['label'] }}</td>
                <td class="chk">{{ $item['value'] === 'baik' ? '✓' : '' }}</td>
                <td class="chk">{{ $item['value'] === 'tidak' ? '✓' : '' }}</td>
                <td class="tindakan">{{ $item['tindakan'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>

    {{-- ===================== KETERANGAN ===================== --}}
    <table>
        <tr>
            <td class="section-title">KETERANGAN</td>
        </tr>
    </table>
    <div class="keterangan-box">{{ $ups->keterangan ?? '' }}</div>

    {{-- ===================== TANDA TANGAN ===================== --}}
    <div class="ttd-wrapper">
        <table>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td class="ttd-header">Inspektor (ICT)</td>
                        </tr>
                        <tr>
                            <td class="ttd-space">
				@if ($inspectorQr)
				    <img src="{{ $qr->render($inspectorQr) }}" width="60" height="60" alt="QR Inspektor">
				@endif
                               @php
                            </td>
                        </tr>
                        <tr>
                            <td class="ttd-footer">
                                <div><span class="lbl">Nama</span>: {{ $ups->inspektorKaryawan->nama ?? '' }}</div>
                                <div><span class="lbl">Jabatan</span>: ICT </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td class="ttd-header">Diketahui Oleh</td>
                        </tr>
                        <tr>
                            <td class="ttd-space">
		                @if ($inspection->approved_at && $inspection->qr_code_persetujuan)
				   <img src="{{ $qr->render($inspection->qr_code_persetujuan) }}" width="60" height="60" alt="QR Persetujuan">
				@endif
                            </td>
                        </tr>
                        <tr>
                            <td class="ttd-footer">

                                <div><span class="lbl">Nama</span>: {{ $ups->approved_by ?? '' }}</div>
                                <div><span class="lbl">Jabatan</span>: Group Leader ICT</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
