@extends('layouts.app', ['activePage' => 'customer', 'titlePage' => __('Customer List')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title ">Customer Management</h4>
            <p class="card-category"> </p>
          </div>
          <div class="card-body">
            <div class="row">
                <div class="col-12 text-right">
                <a href="{{URL::to('/customer/add')}}" class="btn btn-sm btn-danger"><span class="material-icons">add</span>Add Customer</a>
                </div>
            </div>
            <div class="table-responsive">
              <table class="table" id="dataTable">
                <thead class=" text-danger">
                  <th style="width: 20%">Name</th>
                  <th style="width: 20%">Email</th>
                  <th style="width: 25%">Conatct Info</th>
                  <th style="width: 25%">Sales Rep</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  @foreach($contentList as $list)
                  <tr>
                    <td>{{$list->name}}</td>
                    <td>{{$list->email}}</td>
                    <td>{{$list->contact_number}}</td>
                    <td>{{$list->sales_repo}}</td>
                    <td class="text-danger">
                        <a href="{{URL::to('/customer/edit').'/'.$list->cust_id}}"><span class="material-icons">create</span></a>&nbsp;&nbsp;&nbsp;
                        <a href="{{URL::to('/customer/delete').'/'.$list->cust_id}}"><span class="material-icons">delete</span></a>&nbsp;&nbsp;&nbsp;
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
