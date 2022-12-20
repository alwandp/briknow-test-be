@php
    $pointer = 0;
    //$first = !empty($data)?$data->from:null;
    //$last = !empty($data)?$data->to:null;
    $first = 1;
    $last = 10;
@endphp
@forelse ($data as $user)
    @php
        $pointer++;
    @endphp
    @if($pointer == $first)
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1);">
                @php $tempava = $user->avatar_id; @endphp
                <img src="{{config('app.FE_url')."assets/img/gamification/avatar/avatar $tempava.png"}}"  class="rounded-circle border-0" width="44px">
            </td>
            <td><div>{{$user->name??"-"}}</div></td>
            <td>
                <div>
                    @if($user->role == 0)
                        Maker
                    @elseif($user->role == 1)
                        Checker
                    @elseif($user->role == 2)
                        Signer
                    @elseif($user->role == 3)
                        Super Admin
                    @else
                        User
                    @endif
                </div>
            </td>
            <td>
                <div>
                    @if($user->role <> 3)
                        User
                    @elseif($user->role == 3)
                        Admin
                    @else
                        User
                    @endif
                </div>
            </td>
            <td><div>{{$user->email}}</div></td>
            <td><div>{{$user->divisis->shortname}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1);">
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$user->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @elseif ($pointer == $last)
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;">
                @php $tempava = $user->avatar_id; @endphp
                <img src="{{config('app.FE_url')."assets/img/gamification/avatar/avatar $tempava.png"}}"  class="rounded-circle border-0" width="44px">
            </td>
            <td><div>{{$user->name??"-"}}</div></td>
            <td>
                <div>
                    @if($user->role == 0)
                        Maker
                    @elseif($user->role == 1)
                        Checker
                    @elseif($user->role == 2)
                        Signer
                    @elseif($user->role == 3)
                        Super Admin
                    @else
                        User
                    @endif
                </div>
            </td>
            <td>
                <div>
                    @if($user->role <> 3)
                        User
                    @elseif($user->role == 3)
                        Admin
                    @else
                        User
                    @endif
                </div>
            </td>
            <td><div>{{$user->email}}</div></td>
            <td><div>{{$user->divisis->shortname}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;">
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$user->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @else
        <tr class="py-2 content-list">
            <td style="border-left: 1px solid rgba(214, 214, 214, 1);">
                @php $tempava = $user->avatar_id; @endphp
                <img src="{{config('app.FE_url')."assets/img/gamification/avatar/avatar $tempava.png"}}"  class="rounded-circle border-0" width="44px">
            </td>
            <td><div>{{$user->name??"-"}}</div></td>
            <td>
                <div>
                    @if($user->role == 0)
                        Maker
                    @elseif($user->role == 1)
                        Checker
                    @elseif($user->role == 2)
                        Signer
                    @elseif($user->role == 3)
                        Super Admin
                    @else
                        User
                    @endif
                </div>
            </td>
            <td>
                <div>
                    @if($user->role <> 3)
                        User
                    @elseif($user->role == 3)
                        Admin
                    @else
                        User
                    @endif
                </div>
            </td>
            <td><div>{{$user->email}}</div></td>
            <td><div>{{$user->divisis->shortname}}</div></td>
            <td style="border-right: 1px solid rgba(214, 214, 214, 1);">
                <button type="button" class="btn m-1 btn-danger" onClick='hapus({{$user->id}})'><i class="far fa-trash-alt"></i></button>
            </td>
        </tr>
    @endif
@empty
    <tr class="py-2 content-list">
        <td style="text-align: center; border-left: 1px solid rgba(214, 214, 214, 1); border-end-start-radius: 12px;border-right: 1px solid rgba(214, 214, 214, 1); border-end-end-radius: 12px;" colspan="7">
            <i>Tidak ada data.</i>
        </td>
    </tr>
@endforelse
{{-- <button type="button" class="btn m-1 btn-primary" data-toggle="modal" data-target="#staticBackdrop" data-keyboard="true" onClick='getData({{$user->id}})'><i class="fas fa-pencil-alt"></i></button> --}}