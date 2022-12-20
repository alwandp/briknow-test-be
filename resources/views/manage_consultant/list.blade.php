@php
    $pointer = 0;
    //$first = !empty($data)?$data->from:null;
    //$last = !empty($data)?$data->to:null;
    $first = 1;
    $last = 10;
@endphp
@forelse ($data as $item)
    @php
        $pointer++;
    @endphp
    @if($pointer == $first)
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1);">
                <div><a href="{{config('app.APP_URL_FE')}}consultant/{{$item->id}}"  class='text-decoration-none text-dark' target='_blank'>{{$item->nama??"-"}}</a></div>
            </td>
            <td>
                <div>
                    {{$item->bidang}}
                </div>
            </td>
            <td>
                <div>
                    {{$item->website}}
                </div>
            </td>
            <td><div>{{$item->telepon <> '-' ? '+62'.$item->telepon : '-'}}</div></td>
            <td><div>{{$item->lokasi}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1);">
                <button type="button" class="btn m-1 btn-primary" data-toggle="modal" data-target="#modal-update-uker" data-keyboard="true" onClick='getData({{$item->id}})'><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$item->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @elseif ($pointer == $last)
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;">
                <div><a href="{{config('app.APP_URL_FE')}}consultant/{{$item->id}}"  class='text-decoration-none text-dark' target='_blank'>{{$item->nama??"-"}}</a></div>
            </td>
            <td>
                <div>
                    {{$item->bidang}}
                </div>
            </td>
            <td>
                <div>
                    {{$item->website}}
                </div>
            </td>
            <td><div>{{$item->telepon <> '-' ? '+62'.$item->telepon : '-'}}</div></td>
            <td><div>{{$item->lokasi}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;">
                <button type="button" class="btn m-1 btn-primary" data-toggle="modal" data-target="#modal-update-uker" data-keyboard="true" onClick='getData({{$item->id}})'><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$item->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @else
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1);">
                <div><a href="{{config('app.APP_URL_FE')}}consultant/{{$item->id}}"  class='text-decoration-none text-dark' target='_blank'>{{$item->nama??"-"}}</a></div>
            </td>
            <td>
                <div>
                    {{$item->bidang}}
                </div>
            </td>
            <td>
                <div>
                    {{$item->website}}
                </div>
            </td>
            <td><div>{{$item->telepon <> '-' ? '+62'.$item->telepon : '-'}}</div></td>
            <td><div>{{$item->lokasi}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1);">
                <button type="button" class="btn m-1 btn-primary" data-toggle="modal" data-target="#modal-update-uker" data-keyboard="true" onClick='getData({{$item->id}})'><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$item->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @endif
@empty
    <tr class="py-2">
        <td style="text-align: center; border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;" colspan="6">
            <i>Tidak ada data.</i>
        </td>
    </tr>
@endforelse