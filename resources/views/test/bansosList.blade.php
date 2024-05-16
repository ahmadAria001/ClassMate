@extends('test.template')
@section('content')
<div class="container">
<table class="table table-bordered">
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Requester</th>
        <th scope="col">Tanggungan</th>
        <th scope="col">Alasan</th>
        <th scope="col">Attachment</th>
        <th scope="col">Status</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $key)
          <tr>
            <th scope="row"><a href="/bansos/detail/{{ $key->id }}">{{$key->id}}</a></th>
            <td>{{ $key->request_by }}</td>
            <td>{{ Str::limit($key->tanggungan,20, '...') }}</td>
            <td>{{ Str::limit($key->alasan,20, '...') }}</td>
            <td></td>
            <td>{{ $key->status }}</td>
            <td>
              <a class="btn btn-primary" href="/bansos/detail/{{ $key->id }}">Detail</a>
              <a class="btn btn-warning" href="/bansos/edit/{{ $key->id}}">Edit</a>
              <a class="btn btn-danger" href="/bansos/delete/{{ $key->id}}">Delete</a>
          </td>
          </tr>
      @endforeach
    </tbody>
  </table>
  <div class="container">
    <a class="btn btn-primary" href="/bansos/create">Create</a>
  </div>
</div>
@endsection