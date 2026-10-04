@include('pdf.partials.inspection-device', [
    'inspection' => $monitor,
    'deviceName' => 'Monitor / TV',
    'inspectionItems' => [
        'tampilan_layer' => ['Tampilan layar', 'tindakan_tampilan_layer'],
        'kabel_power' => ['Kabel power', 'tindakan_kabel_power'],
        'bracket_dudukan' => ['Bracket / dudukan', 'tindakan_bracket_dudukan'],
        'kebersihan' => ['Kebersihan perangkat', 'tindakan_kebersihan'],
        'stop_kontak' => ['Stop kontak', 'tindakan_stop_kontak'],
    ],

    'documentInfo' => [
        'no_dokumen' => 'PPA-ADRO-F-ICTMD-27',
        'revisi' => '0',
        'tgl_efektif' => '01-Maret-2023',
        'Halaman' => '2'
    ],
])
