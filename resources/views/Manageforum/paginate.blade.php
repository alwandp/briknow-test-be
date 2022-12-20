@forelse($data as $key)
    {{$data->links()}}
    @break
@empty
@endforelse