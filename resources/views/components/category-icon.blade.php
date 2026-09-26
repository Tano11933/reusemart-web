@props(['kategori'])

@php
    $key = \Illuminate\Support\Str::lower($kategori ?? '');

    // Pencocokan berbasis kata kunci supaya tetap cocok walau penamaan kategori di data sedikit berbeda.
    $rules = [
        ['elektronik', 'bi-laptop'],
        ['gadget', 'bi-phone'],
        ['pakaian', 'bi-bag'],
        ['aksesori', 'bi-bag'],
        ['fashion', 'bi-bag'],
        ['perabot', 'bi-house-door'],
        ['rumah', 'bi-house-door'],
        ['furnitur', 'bi-house-door'],
        ['bayi', 'bi-balloon'],
        ['anak', 'bi-balloon'],
        ['sekolah', 'bi-book'],
        ['buku', 'bi-book'],
        ['alat tulis', 'bi-pencil'],
        ['mainan', 'bi-controller'],
        ['hobi', 'bi-controller'],
        ['koleksi', 'bi-controller'],
        ['taman', 'bi-tree'],
        ['outdoor', 'bi-tree'],
        ['kantor', 'bi-briefcase'],
        ['industri', 'bi-briefcase'],
        ['kosmetik', 'bi-droplet'],
        ['perawatan', 'bi-droplet'],
        ['otomotif', 'bi-car-front'],
        ['makanan', 'bi-cup-hot'],
        ['olahraga', 'bi-bicycle'],
        ['musik', 'bi-music-note-beamed'],
    ];

    $icon = 'bi-box-seam';

    foreach ($rules as [$needle, $bi]) {
        if (str_contains($key, $needle)) {
            $icon = $bi;
            break;
        }
    }
@endphp

<i class="bi {{ $icon }}"></i>
