@extends('layouts.app', ['activePage' => 'profile', 'titlePage' => __('User Profile')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{URL::to('/sales/update')}}" autocomplete="off" class="form-horizontal">
            @csrf
            @method('post')

            <input type="hidden" name="sale_rep_id" value="{{$contentData->sale_rep_id}}" />

            <div class="card ">
              <div class="card-header card-header-danger">
                <h4 class="card-title">{{ __('Sales Rep Update') }}</h4>
                <p class="card-category">{{ __('Sales Representative') }}</p>
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
                      <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="input-name" type="text" value="{{$contentData->name}}" required="true" aria-required="true"/>
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
                      <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="input-email" type="email" value="{{$contentData->email}}" required />
                      @if ($errors->has('email'))
                        <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Contact') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('contact') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('contact') ? ' is-invalid' : '' }}" name="contact" id="input-contact" type="text"  value="{{$contentData->contact}}"  required />
                      @if ($errors->has('contact'))
                        <span id="contact-error" class="error text-danger" for="input-contact">{{ $errors->first('contact') }}</span>
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
            $("#input-contact").keyup(function(){
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
