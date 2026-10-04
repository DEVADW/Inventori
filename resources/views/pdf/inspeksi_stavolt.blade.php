@include('pdf.partials.inspection-device', [
    'inspection' => $stavolt,
    'deviceName' => 'Stavolt',
    'inspectionItems' => [
        'casing' => ['Kondisi casing Stavolt', 'tindakan_casing'],
        'kebersihan' => ['Kebersihan Stavolt', 'tindakan_kebersihan'],
        'kabel_adaptor' => ['Kondisi kabel/adaptor', 'tindakan_kabel_adaptor'],
        'tombol_switch' => ['Kondisi tombol dan switch', 'tindakan_tombol_switch'],
        'indikator_voltase' => ['Indikator voltase', 'tindakan_indikator_voltase'],
        'respon_perubahan_beban' => ['Respons terhadap perubahan beban', 'tindakan_respon_perubahan_beban'],
    ],

    'documentInfo' => [
        'no_dokumen' => 'PPA-ADRO-F-ICTMD-50',
        'revisi' => '0',
        'tgl_efektif' => '01-Juli-2024',
        'Halaman' => '1'
    ],
])
