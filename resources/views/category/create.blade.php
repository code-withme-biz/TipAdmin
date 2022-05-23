@extends('layouts.app', ['activePage' => 'category', 'titlePage' => __('Category Management')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{URL::to('/category/insert')}}" autocomplete="off" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            <div class="card ">
              <div class="card-header card-header-danger">
                <h4 class="card-title">{{ __('Category Add') }}</h4>
                <p class="card-category">{{ __('Category Management') }}</p>
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
                  <label class="col-sm-2 col-form-label">{{ __('Parent Category') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('parent_cat') ? ' has-danger' : '' }}">
                      <select class="form-control{{ $errors->has('parent_cat') ? ' is-invalid' : '' }}" name="parent_cat" id="input-parent_cat"  aria-required="true">
                      <option value="0">N/A</option>
                      @foreach($parentCategory as $pc)
                        <option value="{{$pc->cat_id}}">{{$pc->name}}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('parent_cat'))
                        <span id="title-error" class="error text-danger" for="input-parent_cat">{{ $errors->first('parent_cat') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Name') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="input-name" type="text" value="" required="true" aria-required="true"/>
                      @if ($errors->has('name'))
                        <span id="title-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('Category Image') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}" name="image" id="input-image" type="file" value="" style="opacity: 3;   height: 0px;    width: 0px;    z-index: 999;"/>
                      @if ($errors->has('image'))
                        <span id="title-error" class="error text-danger" for="input-image">{{ $errors->first('image') }}</span>
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
