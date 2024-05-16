@extends('test.template')
@section('content')
<div class="card card-primary">
<div class="card-header">
  <h3 class="card-title">Add Family</h3>
</div>
<!-- /.card-header -->
<!-- form start -->
<form  action="/bansos/update/{{$data->id}}" method="POST">
  {{ csrf_field() }}
  <div class="card-body">
    <div class="form-group">
      <label>Peminta</label>
      <select class="form-control col-sm-5" name="request_by"
      @if ($data->status == 'Approved')
                disabled
            @endif
            @if ($data->status == 'Rejected')
                disabled
            @endif>
          @foreach ($users as $user)
          <option value={{$user->id}}
            @if ($data->request_by == $user->id)
                selected
            @endif
            >
            {{$user->username}}
          </option>
          @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Tanggungan</label>
      <textarea class="form-control" rows="3" placeholder="Enter ..." name="tanggungan"
      @if ($data->status == 'Approved')
                disabled
            @endif
            @if ($data->status == 'Rejected')
                disabled
            @endif>{{ $data->tanggungan }}</textarea>
    </div>
    <div class="form-group">
      <label>Alasan</label>
      <textarea class="form-control" rows="3" placeholder="Enter ..." name="alasan"
      @if ($data->status == 'Approved')
                disabled
            @endif
            @if ($data->status == 'Rejected')
                disabled
            @endif>{{ $data->alasan }}</textarea>
    </div>
    <div class="form-group">
      <label for="customFile">Attachment</label>
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="customFile"
        @if ($data->status == 'Approved')
                disabled
            @endif
            @if ($data->status == 'Rejected')
                disabled
            @endif>
        <label class="custom-file-label" for="customFile">Choose file</label>
      </div>
    </div>
    <div class="form-group">
        <label for="residencySelect">Status</label>
        <div class="form-group">
            <div class="form-check form-check-inline">
              <input class="form-check-input" id="residency1" type="radio" name="statusRadio" checked value="Open"
                @if ($data->status == 'Open')
                checked
                @endif
                @if ($data->status == 'Approved')
                disabled
            @endif
            @if ($data->status == 'Rejected')
                disabled
            @endif
              >
              <label class="form-check-label">Open</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" id="residency2" type="radio" name="statusRadio" value="Approved"
              @if ($data->status == 'Approved')
                checked disabled
            @endif
            @if ($data->status == 'Rejected')
                disabled
            @endif
              >
              <label class="form-check-label">Approved</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" id="residency2" type="radio" name="statusRadio" value="Rejected"
              @if ($data->status == 'Rejected')
                checked disabled
            @endif
            @if ($data->status == 'Approved')
                disabled
            @endif
              >
              <label class="form-check-label">Rejected</label>
            </div>
          </div>
      </div>
    </div>
  </div>
  <!-- /.card-body -->

  <div class="card-footer">
    <button type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>
</div>
@endsection
