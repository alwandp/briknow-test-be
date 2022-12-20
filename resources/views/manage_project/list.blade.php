@php
    $i = 0;
    $last = sizeof($data);
@endphp
@forelse($data as $proj)
    @php
        $i++;
        $created_at = strtotime($proj->created_at);
        $tahun = date('Y', $created_at);
        $tgl = date('Y-m-d', $created_at);
    @endphp
    {{-- FIRST --}}
    @if($data->total() > 1 && $i == 1)
        <tr class="py-2 content-table">
            <td style="width: 250px; border-left: 1px solid rgba(214, 214, 214, 1);" @if($proj->status_read <> 1)class="beep-before"@endif>
                <div>
                    @php
                        $nama = strip_tags($proj->nama);
                        if (strlen($nama) > 30) {
                        $stringCut = substr($nama, 0, 30);
                        $endPoint = strrpos($stringCut, ' ');
                        $nama = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                        $nama .= '...';
                        }
                    @endphp
                    {{ $nama }}
                </div>
            </td>
            <td style="width: 300px;">
                <div>
                    @php
                        $direktorat = strip_tags($proj->direktorat);
                        if (strlen($direktorat) > 40) {
                        $stringCut = substr($direktorat, 0, 40);
                        $endPoint = strrpos($stringCut, ' ');
                        $direktorat = $endPoint? substr($stringCut, 0, $endPoint) :
                        substr($stringCut, 0);
                        $direktorat .= '...';
                        }
                    @endphp
                    {{ $direktorat }}
                </div>
            </td>
            <td style="width: 300px;">
                <div>
                    @php
                        $divisi = strip_tags($proj->divisi);
                        if (strlen($divisi) > 40) {
                        $stringCut = substr($divisi, 0, 40);
                        $endPoint = strrpos($stringCut, ' ');
                        $divisi = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut,
                        0);
                        $divisi .= '...';
                        }
                    @endphp
                    {{ $divisi }}
                </div>
            </td>
            <td>
                <div>{{ $tahun }}</div>
            </td>
            <td>
                <div>
                    @if ($proj->flag_mcs == 3)
                        Pending Review
                    @elseif ($proj->flag_mcs == 4)
                        Reviewed
                    @elseif ($proj->flag_mcs == 5)
                        Published
                    @elseif ($proj->flag_mcs == 6)
                        Unpublished
                    @elseif ($proj->flag_mcs == 7)
                        Rejected
                    @else
                        {{$proj->flag_mcs}}
                    @endif
                </div>
            </td>
            <td>
                <div>{{ $tgl }}</div>
            </td>
            <td>
                <div>
                    @if ($proj->is_restricted == 0)
                        Tidak
                    @else
                        Ya
                    @endif
                </div>
            </td>
            <td style="width: 40px; padding-left: 0 !important; border-right: 1px solid rgba(214, 214, 214, 1);">
                <a href="#" id="dropdownMenuLink" style="text-decoration: none; color: black;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    •••
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <button class="btn dropdown-item" onclick="views('{{$proj->slug}}')">
                        <i class="fas fa-eye mr-2"></i>View
                    </button>
                    <a href="{{config('app.FE_url')."kontribusi/".$proj->slug}}" class="btn">
                        <i class="fas fa-pencil-alt mr-2"></i>Edit
                    </a>
                    @if ($proj->flag_mcs != 5)    
                    <button class="btn dropdown-item" onclick="publish({{$proj->id}})">
                        <i class="fas fa-upload mr-2"></i>Publish
                    </button>
                    @elseif ($proj->flag_mcs == 5)
                    <button class="btn dropdown-item" onclick="unpublish({{$proj->id}})">
                        <i class="fas fa-upload mr-2"></i>Unpublish
                    </button>
                    @endif
                    <button class="btn dropdown-item" data-toggle="modal" data-target="#modal-log-status{{$proj->id}}">
                        <i class="fas fa-info-circle mr-2"></i>Log Status
                    </button>
                    <hr class="m-1">
                    <button class="btn dropdown-item" onclick="hapus({{$proj->id}})">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </td>
        </tr>
    {{-- LAST --}}
    @elseif($i==$last)
        <tr class="py-2 content-table">
            <td style="width: 250px; border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;" @if($proj->status_read <> 1)class="beep-before"@endif>
                <div>
                    @php
                        $nama = strip_tags($proj->nama);
                        if (strlen($nama) > 30) {
                        $stringCut = substr($nama, 0, 30);
                        $endPoint = strrpos($stringCut, ' ');
                        $nama = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                        $nama .= '...';
                        }
                    @endphp
                    {{ $nama }}
                </div>
            </td>
            <td style="width: 300px;">
                <div>
                    @php
                        $direktorat = strip_tags($proj->direktorat);
                        if (strlen($direktorat) > 40) {
                        $stringCut = substr($direktorat, 0, 40);
                        $endPoint = strrpos($stringCut, ' ');
                        $direktorat = $endPoint? substr($stringCut, 0, $endPoint) :
                        substr($stringCut, 0);
                        $direktorat .= '...';
                        }
                    @endphp
                    {{ $direktorat }}
                </div>
            </td>
            <td style="width: 300px;">
                <div>
                    @php
                        $divisi = strip_tags($proj->divisi);
                        if (strlen($divisi) > 40) {
                        $stringCut = substr($divisi, 0, 40);
                        $endPoint = strrpos($stringCut, ' ');
                        $divisi = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut,
                        0);
                        $divisi .= '...';
                        }
                    @endphp
                    {{ $divisi }}
                </div>
            </td>
            <td>
                <div>{{ $tahun }}</div>
            </td>
            <td>
                <div>
                    @if ($proj->flag_mcs == 3)
                        Pending Review
                    @elseif ($proj->flag_mcs == 4)
                        Reviewed
                    @elseif ($proj->flag_mcs == 5)
                        Published
                    @elseif ($proj->flag_mcs == 6)
                        Unpublished
                    @elseif ($proj->flag_mcs == 7)
                        Rejected
                    @else
                        {{$proj->flag_mcs}}
                    @endif
                </div>
            </td>
            <td>
                <div>{{ $tgl }}</div>
            </td>
            <td>
                <div>
                    @if ($proj->is_restricted == 0)
                        Tidak
                    @else
                        Ya
                    @endif
                </div>
            </td>
            <td style="padding-left: 0 !important; border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;">
                <a href="#" id="dropdownMenuLink" style="text-decoration: none; color: black;"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    •••
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <button class="btn dropdown-item" onclick="views('{{$proj->slug}}')">
                        <i class="fas fa-eye mr-2"></i>View
                    </button>
                    <a href="{{config('app.FE_url')."kontribusi/".$proj->slug}}" class="btn">
                        <i class="fas fa-pencil-alt mr-2"></i>Edit
                    </a>
                    @if ($proj->flag_mcs != 5)    
                    <button class="btn dropdown-item" onclick="publish({{$proj->id}})">
                        <i class="fas fa-upload mr-2"></i>Publish
                    </button>
                    @elseif ($proj->flag_mcs == 5)
                    <button class="btn dropdown-item" onclick="unpublish({{$proj->id}})">
                        <i class="fas fa-upload mr-2"></i>Unpublish
                    </button>
                    @endif
                    <button class="btn dropdown-item" data-toggle="modal" data-target="#modal-log-status{{$proj->id}}">
                        <i class="fas fa-info-circle mr-2"></i>Log Status
                    </button>
                    <hr class="m-1">
                    <button class="btn dropdown-item" onclick="hapus({{$proj->id}})">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </td>
        </tr>
    {{-- ONLY 1 --}}
    @elseif($data->total() == 1 && $i == 1)
        <tr class="py-2 content-table">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;" @if($proj->status_read <> 1)class="beep-before"@endif>
                <div>
                    @php
                        $nama = strip_tags($proj->nama);
                        if (strlen($nama) > 30) {
                        $stringCut = substr($nama, 0, 30);
                        $endPoint = strrpos($stringCut, ' ');
                        $nama = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                        $nama .= '...';
                        }
                    @endphp
                    {{ $nama }} only 1
                </div>
            </td>
            <td style="width: 300px;">
                <div>
                    @php
                        $direktorat = strip_tags($proj->direktorat);
                        if (strlen($direktorat) > 40) {
                        $stringCut = substr($direktorat, 0, 40);
                        $endPoint = strrpos($stringCut, ' ');
                        $direktorat = $endPoint? substr($stringCut, 0, $endPoint) :
                        substr($stringCut, 0);
                        $direktorat .= '...';
                        }
                    @endphp
                    {{ $direktorat }}
                </div>
            </td>
            <td style="width: 300px;">
                <div>
                    @php
                        $divisi = strip_tags($proj->divisi);
                        if (strlen($divisi) > 40) {
                        $stringCut = substr($divisi, 0, 40);
                        $endPoint = strrpos($stringCut, ' ');
                        $divisi = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut,
                        0);
                        $divisi .= '...';
                        }
                    @endphp
                    {{ $divisi }}
                </div>
            </td>
            <td>
                <div>{{ $tahun }}</div>
            </td>
            <td>
                <div>
                    @if ($proj->flag_mcs == 3)
                        Pending Review
                    @elseif ($proj->flag_mcs == 4)
                        Reviewed
                    @elseif ($proj->flag_mcs == 5)
                        Published
                    @elseif ($proj->flag_mcs == 6)
                        Unpublished
                    @elseif ($proj->flag_mcs == 7)
                        Rejected
                    @else
                        {{$proj->flag_mcs}}
                    @endif
                </div>
            </td>
            <td>
                <div>{{ $tgl }}</div>
            </td>
            <td>
                <div>
                    @if ($proj->is_restricted == 0)
                        Tidak
                    @else
                        Ya
                    @endif
                </div>
            </td>
            <td style="padding-left: 0 !important; border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;">
                <a href="#" id="dropdownMenuLink" style="text-decoration: none; color: black;"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    •••
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <button class="btn dropdown-item" onclick="views('{{$proj->slug}}')">
                        <i class="fas fa-eye mr-2"></i>View
                    </button>
                    <a href="{{config('app.FE_url')."kontribusi/".$proj->slug}}" class="btn">
                        <i class="fas fa-pencil-alt mr-2"></i>Edit
                    </a>
                    @if ($proj->flag_mcs != 5)    
                    <button class="btn dropdown-item" onclick="publish({{$proj->id}})">
                        <i class="fas fa-upload mr-2"></i>Publish
                    </button>
                    @elseif ($proj->flag_mcs == 5)
                    <button class="btn dropdown-item" onclick="unpublish({{$proj->id}})">
                        <i class="fas fa-upload mr-2"></i>Unpublish
                    </button>
                    @endif
                    <button class="btn dropdown-item" data-toggle="modal" data-target="#modal-log-status{{$proj->id}}">
                        <i class="fas fa-info-circle mr-2"></i>Log Status
                    </button>
                    <hr class="m-1">
                    <button class="btn dropdown-item" onclick="hapus({{$proj->id}})">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </td>
        </tr>
    {{-- MID --}}
    @else
        <tr class="py-2 content-table">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1);" @if($proj->status_read <> 1)class="beep-before"@endif>
                <div>
                    @php
                        $nama = strip_tags($proj->nama);
                        if (strlen($nama) > 30) {
                        $stringCut = substr($nama, 0, 30);
                        $endPoint = strrpos($stringCut, ' ');
                        $nama = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                        $nama .= '...';
                        }
                    @endphp
                    {{ $nama }}
                </div>
            </td>
            <td style="width: 300px;">
                <div>
                    @php
                        $direktorat = strip_tags($proj->direktorat);
                        if (strlen($direktorat) > 40) {
                        $stringCut = substr($direktorat, 0, 40);
                        $endPoint = strrpos($stringCut, ' ');
                        $direktorat = $endPoint? substr($stringCut, 0, $endPoint) :
                        substr($stringCut, 0);
                        $direktorat .= '...';
                        }
                    @endphp
                    {{ $direktorat }}
                </div>
            </td>
            <td style="width: 300px;">
                <div>
                    @php
                        $divisi = strip_tags($proj->divisi);
                        if (strlen($divisi) > 40) {
                        $stringCut = substr($divisi, 0, 40);
                        $endPoint = strrpos($stringCut, ' ');
                        $divisi = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut,
                        0);
                        $divisi .= '...';
                        }
                    @endphp
                    {{ $divisi }}
                </div>
            </td>
            <td>
                <div>{{ $tahun }}</div>
            </td>
            <td>
                <div>
                    @if ($proj->flag_mcs == 3)
                        Pending Review
                    @elseif ($proj->flag_mcs == 4)
                        Reviewed
                    @elseif ($proj->flag_mcs == 5)
                        Published
                    @elseif ($proj->flag_mcs == 6)
                        Unpublished
                    @elseif ($proj->flag_mcs == 7)
                        Rejected
                    @else
                        {{$proj->flag_mcs}}
                    @endif
                </div>
            </td>
            <td>
                <div>{{ $tgl }}</div>
            </td>
            <td>
                <div>
                    @if ($proj->is_restricted == 0)
                        Tidak
                    @else
                        Ya
                    @endif
                </div>
            </td>
            <td style="padding-left: 0 !important; border-right: 1px solid rgba(214, 214, 214, 1);">
                <a href="#" id="dropdownMenuLink" style="text-decoration: none; color: black;"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    •••
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <button class="btn dropdown-item" onclick="views('{{$proj->slug}}')">
                        <i class="fas fa-eye mr-2"></i>View
                    </button>
                    <a href="{{config('app.FE_url')."kontribusi/".$proj->slug}}" class="btn">
                        <i class="fas fa-pencil-alt mr-2"></i>Edit
                    </a>
                    @if ($proj->flag_mcs != 5)    
                    <button class="btn dropdown-item" onclick="publish({{$proj->id}})">
                        <i class="fas fa-upload mr-2"></i>Publish
                    </button>
                    @elseif ($proj->flag_mcs == 5)
                    <button class="btn dropdown-item" onclick="unpublish({{$proj->id}})">
                        <i class="fas fa-upload mr-2"></i>Unpublish
                    </button>
                    @endif
                    <button class="btn dropdown-item" data-toggle="modal" data-target="#modal-log-status{{$proj->id}}">
                        <i class="fas fa-info-circle mr-2"></i>Log Status
                    </button>
                    <hr class="m-1">
                    <button class="btn dropdown-item" onclick="hapus({{$proj->id}})">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </td>
        </tr>
    @endif
{{-- EMPTY --}}
@empty
    <tr class="py-2">
        <td style="text-align: center; border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;" colspan="8">
            <i>Tidak ada data.</i>
        </td>
    </tr>
@endforelse