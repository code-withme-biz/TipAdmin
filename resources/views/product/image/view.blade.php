@extends('layouts.app', ['activePage' => 'product', 'titlePage' => __('Product Management')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title "> Product Image</h4>
            <p class="card-category">Product Management</p>
          </div>
          <div class="card-body">
            <div class="row">
            <label class="col-sm-2 col-form-label"></label>
            <div class="col-sm-7">
                <form action="{{ URL::to('/product-image/upload')}}" class="dropzone" id="my-awesome-dropzone" method="post" enctype="multipart/form-data">
                @csrf
                    <input type="hidden" value={{$id}} name="prod_id" />
                </form>
            </div>
            @if(count($getImages) > 0)
            <div class="row">
                <div class="col-sm-3">
                    @foreach($getImages as $img)
                    <img src="{{asset('public/product_images').'/'.$img->image}}" />
                    <a href="{{URL::to('/product-image/remove').'/'.$img->prod_image_id}}">Remove</a>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn btn-danger">{{ __('Save') }}</button>
                <a href="{{ url()->previous() }}" role="button" class="btn btn-danger">{{ __('Back') }}</a>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('js')
<script>
 var uploadedDocumentMap = {};

 $("div#my-awesome-dropzone").dropzone({
    addRemoveLinks: true,
    parallelUploads:10,
    uploadMultiple:true,
    success: function (file, response) {
        console.log(response);
    }
});
</script>

@endpush
