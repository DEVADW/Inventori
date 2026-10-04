@include('pdf.partials.inspection-device', [
    'inspection' => $proyektor,
    'deviceName' => 'Proyektor',
    'inspectionItems' => [
        'kondisi_casing' => ['Kondisi casing', 'tindakan_kondisi_casing'],
        'kebersihan' => ['Kebersihan perangkat', 'tindakan_kebersihan'],
        'kabel_adaptor' => ['Kabel adaptor', 'tindakan_kabel_adaptor'],
        'lensa_proyektor' => ['Lensa proyektor', 'tindakan_lensa_proyektor'],
        'indikator_lampu' => ['Indikator lampu', 'tindakan_indikator_lampu'],
        'fokus_zoom' => ['Fokus dan zoom', 'tindakan_fokus_zoom'],
        'kecerahan_kontras' => ['Kecerahan dan kontras', 'tindakan_kecerahan_kontras'],
        'koneksi_input_hdmi' => ['Koneksi input HDMI', null],
        'koneksi_input_vga' => ['Koneksi input VGA', null],
        'koneksi_input_usb' => ['Koneksi input USB', null],
    ],

    'documentInfo' => [
        'no_dokumen' => 'PPA-ADRO-F-ICTMD-51',
        'revisi' => '0',
        'tgl_efektif' => '01-Juli-2024',
        'Halaman' => '1'
    ],
])

