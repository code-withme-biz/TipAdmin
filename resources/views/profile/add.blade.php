@extends('layouts.app', ['activePage' => 'profile', 'titlePage' => __('User Profile')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{URL::to('/user/insert')}}" autocomplete="off" class="form-horizontal">
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-danger">
                <h4 class="card-title">{{ __('User Add') }}</h4>
                <p class="card-category">{{ __('User Management') }}</p>
              </div>
              <div class="card-body ">
                @if (session('status'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Name') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="input-name" type="text" value="" required="true" aria-required="true"/>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Email') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="input-email" type="email" value="" required />
                      @if ($errors->has('email'))
                        <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Password') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="input-password" type="password" value="" required />
                      @if ($errors->has('password'))
                        <span id="password-error" class="error text-danger" for="input-password">{{ $errors->first('password') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Contact Number') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('contact_number') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('contact_number') ? ' is-invalid' : '' }}" placeholder="(999)999-9999" name="contact_number" id="input-contact_number" type="text"  value="" required />
                      @if ($errors->has('contact_number'))
                        <span id="contact_number-error" class="error text-danger" for="input-contact_number">{{ $errors->first('contact_number') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('User Role') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('role') ? ' has-danger' : '' }}">
                        <select class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}" name="role" id="input-role"  required>
                        <option value="1">Master Admin</option>
                        <option value="2">Product Admin</option>
                        </select>
                      @if ($errors->has('role'))
                        <span id="role-error" class="error text-danger" for="input-role">{{ $errors->first('role') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-danger">{{ __('Save') }}</button>
                <a href="{{ url()->previous() }}" role="button" class="btn btn-danger">{{ __('Back') }}</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#input-contact_number").keyup(function(){
                let parent = $(this);
                if(parent.val().length >= 13) {
                    const pattern = /^(\()?\d{3}(\))?\d{3}(-)\d{4}$/;

                    if(!pattern.test(parent.val())) {
                        alert('Contant Number format (999)999-9999');
                        parent.val('');
                    }

                } else if(parent.val().length < 13 && parent.val().length > 9) {
                    if(!pattern.test(parent.val())) {
                        alert('Contant Number format (999)999-9999');
                        parent.val('');
                    }
                }
            });
        })

    </script>
@endpush
