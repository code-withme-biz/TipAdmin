@extends('layouts.app', ['activePage' => 'product', 'titlePage' => __('Product Management')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title ">Product Management</h4>
            <p class="card-category"> </p>
          </div>
          <div class="card-body">
            <div class="row">
                <div class="col-12 text-right">
                <a href="{{URL::to('/product/add')}}" class="btn btn-sm btn-danger"><span class="material-icons">add</span>Add Product</a>
                </div>
            </div>
            <div class="table-responsive">
              <table class="table" id="dataTable">
                <thead class=" text-danger">
                  <th>Name</th>
                  <th>SKU</th>
                  <th>Cateory</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  @foreach($products as $p)
                  <tr>
                    <td>{{$p->prod_name}}</td>
                    <td>{{$p->prod_sku}}</td>
                    <td>{{$p->name}}</td>
                    <td class="text-danger">
                        <a href="{{URL::to('/product-image').'/'.$p->prod_id}}"><span class="material-icons">collections</span></a>&nbsp;&nbsp;&nbsp;
                        <a href="{{URL::to('/product/edit').'/'.$p->prod_id}}"><span class="material-icons">create</span></a>&nbsp;&nbsp;&nbsp;
                        <a href="{{URL::to('/product/delete').'/'.$p->prod_id}}"><span class="material-icons">delete</span></a>
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
