@php
    $pointer = 0;
    //$first = !empty($data)?$data->from:null;
    //$last = !empty($data)?$data->to:null;
    $first = 1;
    $last = 10;
@endphp
@forelse ($data as $divisi)
    @php
        $pointer++;
    @endphp
    @if($pointer == $first)
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1);">
                <div>{{$divisi->cost_center??"-"}}</div>
            </td>
            <td>
                <div>
                    @if($divisi->direktorat <> NULL)
                        {{$divisi->direktorat}}
                    @else
                        Lainnya
                    @endif
                </div>
            </td>
            <td>
                <div>
                    {{$divisi->divisi}}
                </div>
            </td>
            <td><div>{{$divisi->shortname}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1);">
                <button type="button" class="btn m-1 btn-primary" data-toggle="modal" data-target="#modal-update-uker" data-keyboard="true" onClick='getData({{$divisi->id}})'><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$divisi->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @elseif ($pointer == $last)
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;">
                <div>{{$divisi->cost_center??"-"}}</div>
            </td>
            <td>
                <div>
                    @if($divisi->direktorat <> NULL)
                        {{$divisi->direktorat}}
                    @else
                        Lainnya
                    @endif
                </div>
            </td>
            <td>
                <div>
                    {{$divisi->divisi}}
                </div>
            </td>
            <td><div>{{$divisi->shortname}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;">
                <button type="button" class="btn m-1 btn-primary" data-toggle="modal" data-target="#modal-update-uker" data-keyboard="true" onClick='getData({{$divisi->id}})'><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$divisi->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @else
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1);">
                <div>{{$divisi->cost_center??"-"}}</div>
            </td>
            <td>
                <div>
                    @if($divisi->direktorat <> NULL)
                        {{$divisi->direktorat}}
                    @else
                        Lainnya
                    @endif
                </div>
            </td>
            <td>
                <div>
                    {{$divisi->divisi}}
                </div>
            </td>
            <td><div>{{$divisi->shortname}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1);">
                <button type="button" class="btn m-1 btn-primary" data-toggle="modal" data-target="#modal-update-uker" data-keyboard="true" onClick='getData({{$divisi->id}})'><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$divisi->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @endif
@empty
    <tr class="py-2">
        <td style="text-align: center; border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;" colspan="5">
            <i>Tidak ada data.</i>
        </td>
    </tr>
@endforelse