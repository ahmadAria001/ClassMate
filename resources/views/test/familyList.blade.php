@extends('test.template')
@section('content')
<div class="container">
<table class="table table-bordered">
    <thead>
      <tr>
        <th scope="col">NKK</th>
        <th scope="col">RT</th>
        <th scope="col">Status Penduduk</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $key)
      <tr>
        <th scope="row"><a href="/family/detail/{{ $key->id }}">{{$key->nkk}}</a></th>
        <td>{{ $key->rt_id }}</td>
        <td>
        @switch($key->residentstatus)
            @case('PermanentResident')
                Permanen
                @break
            @case('ContractResident')
                Kontrak
                @break
        @endswitch
        </td>
        <td>
            <a class="btn btn-primary" href="/family/detail/{{ $key->id }}">Detail</a>
            <a class="btn btn-warning" href="/family/edit/{{ $key->id}}">Edit</a>
            <a class="btn btn-danger" href="/family/delete/{{ $key->id}}">Delete</a>
        </td>
      </tr>   
      @endforeach
    </tbody>
  </table>
  <div class="container">
    <a class="btn btn-primary" href="/family/create">Create</a>
  </div>
</div>
@endsection