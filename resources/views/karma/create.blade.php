@extends('layouts.app', ['activePage' => 'karma-points', 'titlePage' => __('Karma Points')])

@section('content')
<div class="row">

        <div class="col-md-12">
          <form method="post" enctype="multipart/form-data" action="{{URL::to('/karma-points/insert')}}" autocomplete="off">
          @csrf();
            <div class="card ">
              <div class="card-header">
                <h4 class="card-title">Add Karma Points</h4>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="{{URL::to('/karma-points')}}" class="btn btn-sm btn-primary">Back to list</a>
                  </div>
                </div>
                @if (count($errors) > 0)
                    <div class = "alert alert-danger">
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row">
                  <label class="col-sm-2 col-form-label">Price Minimum</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" name="min" id="input-min" type="text" placeholder="Price Minimum" value="" required="true" aria-required="true">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Price Maximum</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" name="max" id="input-max" type="text" placeholder="Price Maximum" value="" required="true" aria-required="true">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Points</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" name="points" id="input-point" type="text" placeholder="Points" value="" required="true" aria-required="true">
                    </div>
                  </div>
                </div>

              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn">Add Karma Points</button>
              </div>
            </div>
          </form>
        </div>
</div>
@endsection
@section('scripts')
<script>
    $(function() {
        CKEDITOR.replace('descrip');
    });
</script>
@endsection
