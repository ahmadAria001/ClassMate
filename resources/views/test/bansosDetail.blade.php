@extends('test.template')
@section('content')
<div class="container">
<div class="container">
    <table class="table table-sm">
        <tr>
            <th>ID: </th>
            <td>{{ $data->id }}</td>
        </tr>
        <tr>
            <th>Peminta: </th>
            <td>{{ $data->request_by }}</td>
        </tr>
        <tr>
            <th>Tanggungan: </th>
            <td>{{ $data->tanggungan }}</td>
        </tr>
        <tr>
            <th>Alasan: </th>
            <td>{{ $data->alasan }}</td>
        </tr>
        <tr>
            <th>Status: </th>
            <td>{{ $data->status }}</td>
        </tr>
        <tr>
            <th>Attachment: </th>
            <td></td>
        </tr>
    </table>
</div>
<a class="btn btn-primary" href="/bansos">Kembali</a>
</div>
@endsection
