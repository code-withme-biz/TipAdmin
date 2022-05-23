@extends('layouts.app', ['activePage' => 'category', 'titlePage' => __('Category List')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title ">Linked Products</h4>
            <p class="card-category"> Category Management</p>

          </div>
          <div class="card-body">
          <div class="row">
                <div class="col-12 text-right">
                    <a href="{{ url()->previous() }}" role="button" class="btn btn-danger">{{ __('Back') }}</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-right">
                <!-- <a href="{{URL::to('/category/add')}}" class="btn btn-sm btn-danger"><span class="material-icons">add</span>Add Category</a> -->
                </div>
            </div>
            <div class="table-responsive">
              <table class="table" id="dataTable">
                <thead class="text-danger">
                  <th style="width: 20%">S.No.</th>
                  <th style="width: 50%">Product Name</th>

                </thead>
                <tbody>
                  @foreach($contentData as $k => $list)
                  <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$list->prod_name}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
    <script type="text/javascript">
        $('#dataTable').DataTable();
    </script>
@endpush
