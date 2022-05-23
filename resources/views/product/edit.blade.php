@extends('layouts.app', ['activePage' => 'product', 'titlePage' => __('Product Management')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{URL::to('/product/update')}}" autocomplete="off" class="form-horizontal">
            @csrf
            @method('post')

            <input type="hidden" name="prod_id" value="{{$contentData->prod_id}}"/>

            <div class="card ">
              <div class="card-header card-header-danger">`
                <h4 class="card-title">{{ __('Product Update') }}</h4>
                <p class="card-category">{{ __('Product Management') }}</p>
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
                  <label class="col-sm-2 col-form-label">{{ __('Category') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('cat_id') ? ' has-danger' : '' }}">
                        <select class="form-control{{ $errors->has('cat_id') ? ' is-invalid' : '' }}" name="cat_id" id="input-cat_id"  required>
                        @foreach($categoryList as $pc)
                        <option value="{{$pc->cat_id}}">{{$pc->name}}</option>
                        @endforeach
                      </select>
                        </select>
                      @if ($errors->has('role'))
                        <span id="role-cat_id" class="error text-danger" for="input-cat_id">{{ $errors->first('cat_id') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Name') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('prod_name') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('prod_name') ? ' is-invalid' : '' }}" name="prod_name" id="input-prod_name" type="text" value="{{$contentData->prod_name}}" required="true" aria-required="true"/>
                      @if ($errors->has('prod_name'))
                        <span id="prod_name-error" class="error text-danger" for="input-prod_name">{{ $errors->first('prod_name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('Description') }}</label>
                    <div class="col-sm-7">
                        <div class="form-group{{ $errors->has('descrip') ? ' has-danger' : '' }}">
                        <textarea class="form-control{{ $errors->has('descrip') ? ' is-invalid' : '' }}" name="descrip" id="input-descrip" required>{{$contentData->prod_detail}}</textarea>
                        @if ($errors->has('descrip'))
                            <span id="descrip-error" class="error text-danger" for="input-descrip">{{ $errors->first('descrip') }}</span>
                        @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('SKU') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('prod_sku') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('prod_sku') ? ' is-invalid' : '' }}" name="prod_sku" id="input-prod_sku" type="text" value="{{$contentData->prod_sku}}" required />
                      @if ($errors->has('prod_sku'))
                        <span id="prod_sku-error" class="error text-danger" for="input-prod_sku">{{ $errors->first('prod_sku') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Unit of Measurement') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('unit_of_measure') ? ' has-danger' : '' }}">
                        <select class="form-control{{ $errors->has('unit_of_measure') ? ' is-invalid' : '' }}" name="unit_of_measure" id="input-unit_of_measure"  required>
                        <option value="Each">Each</option>
                        <option value="Cases">Cases</option>
                        <option value="Lbs">Lbs</option>
                        </select>
                      @if ($errors->has('unit_of_measure'))
                        <span id="unit_of_measure-error" class="error text-danger" for="input-unit_of_measure">{{ $errors->first('unit_of_measure') }}</span>
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
<script  type="text/javascript">
    $(function() {
        CKEDITOR.replace('descrip');
    });
</script>
@endpush
