@extends('layouts.app', ['activePage' => 'content', 'titlePage' => __('Content Management')])

@section('content')
<div class="row">

        <div class="col-md-12">
          <form method="post" enctype="multipart/form-data" action="{{URL::to('/content/insert')}}" autocomplete="off">
          @csrf();
            <div class="card ">
              <div class="card-header">
                <h4 class="card-title">Add Content</h4>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-12 text-right">
                      <a href="{{URL::to('/content')}}" class="btn btn-sm btn-primary">Back to list</a>
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
                  <label class="col-sm-2 col-form-label">Title</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input class="form-control" name="title" id="input-title" type="text" placeholder="Title" value="" required="true" aria-required="true">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">Description</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                    <textarea name="descrip" placeholder="" id="editorCopy" required="required"></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" class="btn">Add Content</button>
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
