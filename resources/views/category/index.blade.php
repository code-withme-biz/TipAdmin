@extends('layouts.app', ['activePage' => 'category', 'titlePage' => __('Category List')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title ">Category Management</h4>
            <p class="card-category"> </p>
          </div>
          <div class="card-body">
            <div class="row">
                <div class="col-12 text-right">
                <a href="{{URL::to('/category/add')}}" class="btn btn-sm btn-danger"><span class="material-icons">add</span>Add Category</a>
                </div>
            </div>
            <div class="table-responsive">
              <table class="table" id="dataTable">
                <thead class="text-danger">
                  <th style="width: 20%">Name</th>
                  <th style="width: 50%">Parent Category</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  @foreach($resultArry as $list)
                  <tr>
                    <td>{{$list['name']}}</td>
                    <td>{{$list['parent_cat_name']}}</td>
                    <td class="text-danger">
                        <a href="{{URL::to('/category/edit').'/'.$list['cat_id']}}"><span class="material-icons">create</span></a>&nbsp;&nbsp;&nbsp;
                        <a href="{{URL::to('/category/product').'/'.$list['cat_id']}}"><span class="material-icons">description</span></a>&nbsp;&nbsp;&nbsp;
                        <a href="{{URL::to('/category/delete').'/'.$list['cat_id']}}"><span class="material-icons">delete</span></a>
                    </td>
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
