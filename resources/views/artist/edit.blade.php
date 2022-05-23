@extends('layouts.app', ['activePage' => 'artist', 'titlePage' => __('Artist Management')])

@section('content')
<div class="row">

        <div class="col-md-12">
          <form method="post" enctype="multipart/form-data" action="{{URL::to('/artist/update')}}" autocomplete="off">
          @csrf();
            <div class="card ">
              <div class="card-header">
                <h4 class="card-title">Edit User</h4>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="{{URL::to('/artist')}}" class="btn btn-sm btn-primary">Back to list</a>
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
                <input type="hidden" name="user_id" value="{{$userData->id}}" />
                <div class="row">
                  <label class="col-sm-2 col-form-label">Name</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" name="name" id="input-name" type="text" placeholder="Name" value="{{$userData->name}}" required="true" aria-required="true">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" name="email" id="input-email" type="email" placeholder="Email" value="{{$userData->email}}" required="">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password"> Password</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" input="" type="password" name="password" id="input-password" placeholder="Password">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password-confirmation">Confirm Password</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" name="password_confirmation" id="input-password-confirmation" type="password" placeholder="Confirm Password">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn">Update Artist</button>
              </div>
            </div>
          </form>
        </div>
</div>
@endsection
