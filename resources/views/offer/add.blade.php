@extends('layouts.app', ['activePage' => 'offer', 'titlePage' => __('Offer Management')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{URL::to('/offer/insert')}}" autocomplete="off" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-danger">
                <h4 class="card-title">{{ __('Offer Add') }}</h4>
                <p class="card-category">{{ __('Offer Management') }}</p>
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
                  <label class="col-sm-2 col-form-label">{{ __('Image') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}" name="image" id="input-image" type="file" value="" required style="opacity: 1;    width: 100%;  height: 100%;   z-index: 99999;"/>
                      @if ($errors->has('image'))
                        <span id="image-error" class="error text-danger" for="input-image">{{ $errors->first('image') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Product') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('product') ? ' has-danger' : '' }}">
                    <span class="prod_id"></span>
                    <input type="hidden" id="prod_id"  name="prod_id"/>
                      @if ($errors->has('product'))
                        <span id="product-error" class="error text-danger" for="input-product">{{ $errors->first('product') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Start Date') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('start_date') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}" name="start_date" id="input-start_date" type="date" value="" required="true" />
                      @if ($errors->has('start_date'))
                        <span id="start_date-error" class="error text-danger" for="input-start_date">{{ $errors->first('start_date') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('End Date') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('end_date') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}" name="end_date" id="input-end_date" type="date" value="" required="true"/>
                      @if ($errors->has('end_date'))
                        <span id="end_date-error" class="error text-danger" for="input-end_date">{{ $errors->first('end_date') }}</span>
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
        var category = "{{$cat}}";
        category = JSON.parse(category.replace(/&quot;/g,'"'));

        var cat_instance = new SelectPure(".prod_id", {
            options: category,
            autocomplete: true,
            icon: "fa fa-times",
            multiple: true ,
            onChange: value => {
                // set hidden value
                $('#prod_id').val(value);
                // console.log(value);
            }
        });


        // function setProductDropDown(optionList = []) {
        //     console.log(optionList);

        // }

    </script>
@endpush
