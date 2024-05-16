@extends('test.template')
@section('content')
<div class="card card-primary">
<div class="card-header">
  <h3 class="card-title">Add Family</h3>
</div>
<!-- /.card-header -->
<!-- form start -->
<form  action="/family/store" method="POST">
  {{ csrf_field() }}
  <div class="card-body">
    <div class="form-group">
        <label for="nkkForm">NKK</label>
        <input type="text" class="form-control" id="nkkForm" placeholder="Masukkan NKK.." name="nkk" required>
    </div>
    <div class="form-group">
        <label for="rtSelect">RT</label>
        <select class="form-control col-sm-5" name="rt_id">
            @foreach ($rt as $rts)
            <option value={{$rts->id}}>
              {{$rts->id}}
            </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="residencySelect">Residency Status</label>
        <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" id="residency1" type="radio" name="ResidencyRadio" checked value="PermanentResident">
              <label class="form-check-label">Permanen</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" id="residency2" type="radio" name="ResidencyRadio" value="ContractResident">
              <label class="form-check-label">Kontrak</label>
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
