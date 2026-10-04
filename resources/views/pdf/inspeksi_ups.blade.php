@include('pdf.partials.inspection-device', [
    'inspection' => $ups,
    'deviceName' => 'UPS',
    'inspectionItems' => [
        'casing' => ['Kondisi casing UPS', 'tindakan_casing'],
        'kebersihan' => ['Kebersihan UPS', 'tindakan_kebersihan'],
        'kabel_adaptor' => ['Kondisi kabel/adaptor', 'tindakan_kabel_adaptor'],
        'tombol_switch' => ['Kondisi tombol dan switch', 'tindakan_tombol_switch'],
        'indikator_status' => ['Lampu Indikator', 'tindakan_indikator_status'],
        'fungsi_alarm' => ['Alarm', 'tindakan_fungsi_alarm'],
        'respon_kehilangan_daya' => ['Kondisi Baterai', 'tindakan_respon_kehilangan_daya'],
        'fuse' => ['Fuse', 'tindakan_fuse'],
    ],

    'documentInfo' => [
        'no_dokumen' => 'PPA-ADRO-F-ICTMD-49',
        'revisi' => '0',
        'tgl_efektif' => '01-Juli-2024',
        'Halaman' => '1'
    ],
])
