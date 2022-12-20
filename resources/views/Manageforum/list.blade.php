@forelse ($datas as $post)
            <div class="card my-1">
                <div class="card-body">
                    <h5>{{$post->judul}}</h5>
                    <div class="d-flex">
                        <div class="pr-4">
                            <i class="fa fa-user-alt"></i>
                            {{$post->username}}
                        </div>
                        <div class="pr-4">
                            <i class="fa fa-clock"></i>
                            {{$post->updated_at}}
                            {{-- 8 Jun 2021 at 15:28 --}}
                        </div>
                        <div class="pr-4">
                            <i class="fa fa-comment-alt"></i>
                            {{count($post->forumcomment)}}
                        </div>
                        <div>
                            <i class="fa fa-globe-asia"></i>
                            {{($post->restriction == '1' ) ? "Private" : "Public"}}
                        </div>
                        <div class="ml-auto">
                            <a href="#" id="dropdownMenuLink" style="text-decoration: none; color: black;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                •••
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a href="{{config('app.FE_url')."forum/edit/".$post->id}}" target="_blank" class="btn dropdown-item text-decoration-none text-dark px-3">
                                    <i class="fas fa-pencil-alt mr-2"></i>
                                    Edit
                                </a>
                                <hr class="m-1">
                                    @if($post->status == 2)
                                        <button class="btn dropdown-item px-3" onclick="restore({{$post->id}})">
                                            <i class="fas fa-undo mr-2"></i>
                                            Restore 
                                        </button>
                                    @else
                                        <button class="btn dropdown-item px-3" onclick="remove({{$post->id}})">
                                            <i class="fas fa-trash mr-2"></i>
                                            Remove
                                        </button>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="d-flex justify-content-center">
                <h5>Tidak ada data</h5>
            </div>
        @endforelse