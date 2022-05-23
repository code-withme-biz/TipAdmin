@extends('layouts.app', ['activePage' => 'customer', 'titlePage' => __('Customer Management')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{URL::to('/customer/update')}}" autocomplete="off" class="form-horizontal" enctype="multipart/form-data">
            @csrf

            <input type="hidden" value="{{$contentData->cust_id}}" name="cust_id" />
            <div class="card ">
              <div class="card-header card-header-danger">
                <h4 class="card-title">{{ __('Customer Update') }}</h4>
                <p class="card-category">{{ __('Customer Management') }}</p>
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
                        <span id="title-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Email') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="input-email" type="email" value="{{$contentData->email}}" required="true" aria-required="true"/>
                      @if ($errors->has('email'))
                        <span id="title-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Password') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="input-password" type="password" value=""/>
                      @if ($errors->has('password'))
                        <span id="title-error" class="error text-danger" for="input-password">{{ $errors->first('password') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Contact Number') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('contact_number') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('contact_number') ? ' is-invalid' : '' }}" name="contact_number" id="input-contact_number" type="text" value="{{$contentData->contact_number}}" required="true" aria-required="true"/>
                      @if ($errors->has('contact_number'))
                        <span id="title-error" class="error text-danger" for="input-password">{{ $errors->first('contact_number') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Store Name') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('store_name') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('store_name') ? ' is-invalid' : '' }}" name="store_name" id="input-store_name" type="text" value="{{$contentData->store_name}}" required="true" aria-required="true"/>
                      @if ($errors->has('store_name'))
                        <span id="title-error" class="error text-danger" for="input-store_name">{{ $errors->first('store_name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Address') }}</label>
                  <div class="col-sm-7">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                    <label for="inputCity">Street</label>
                                    <input type="text" class="form-control" id="inputStreet" name="street" value="{{$contentData->street}}">
                                    @if ($errors->has('street'))
                                        <span id="title-error" class="error text-danger" for="input-street">{{ $errors->first('street') }}</span>
                                    @endif
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="inputCity">City</label>
                                    <input type="text" class="form-control" id="inputCity" name="city" value="{{$contentData->city}}">
                                    @if ($errors->has('city'))
                                        <span id="title-error" class="error text-danger" for="input-city">{{ $errors->first('city') }}</span>
                                    @endif
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="inputState">State</label>
                                    <input type="text" class="form-control" id="inputState" name="state" value="{{$contentData->state}}">
                                    @if ($errors->has('state'))
                                        <span id="title-error" class="error text-danger" for="input-state">{{ $errors->first('state') }}</span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="inputZip">Zip</label>
                                    <input type="text" class="form-control" id="inputZip" name="zip" value="{{$contentData->zip}}">
                                    @if ($errors->has('zip'))
                                        <span id="title-error" class="error text-danger" for="input-zip">{{ $errors->first('zip') }}</span>
                                    @endif
                                </div>
                            </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Sales Repo') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('sales_repo') ? ' has-danger' : '' }}">
                      <select class="form-control{{ $errors->has('sales_repo') ? ' is-invalid' : '' }}" name="sales_repo" id="input-sales_repo"  aria-required="true">
                      @foreach($salesRepo as $pc)
                        <option @if($contentData->sales_repo_id == $pc->id) selected @endif value="{{$pc->id}}">{{$pc->name}}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('sales_repo'))
                        <span id="title-error" class="error text-danger" for="input-sales_repo">{{ $errors->first('sales_repo') }}</span>
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
