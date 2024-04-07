@extends('test.template')
@section('content')
<div class="container">
<div class="container">
    <p><b>NKK: </b>{{ $header_data->nkk }}</p>
    <p><b>Status: </b>{{ $header_data->residentstatus }}</p>
    <p><b>RT: </b>{{ $header_data->rt_id }}</p>
</div>
<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">NIK</th>
      <th scope="col">Nama Lengkap</th>
      <th scope="col">Tempat Lahir</th>
      <th scope="col">Tanggal Lahir</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $key)
    <tr>
      <th scope="row">{{ $key->nik }}</th>
      <td> {{ $key->fullName }} </td>
      <td> {{ $key->birthplace }} </td>
      <td>
        {{ date("d-m-Y", $key->birthdate / 1000)}}
      </td>
    </tr>
    @endforeach   
  </tbody>
</table>
<a class="btn btn-primary" href="/family">Kembali</a>
</div>
@endsection
