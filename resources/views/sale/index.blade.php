@extends('layouts.app', ['activePage' => 'sales', 'titlePage' => __('Sales Rep')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-danger">
                <h4 class="card-title">{{ __('Sales Representative') }}</h4>
                <!-- <p class="card-category">{{ __('Sales Representative') }}</p> -->
          </div>
          <div class="card-body">
            <div class="row">
                <div class="col-12 text-right">
                <a href="{{URL::to('/sales/add')}}" class="btn btn-sm btn-danger"><span class="material-icons">add</span>Add Sale Rep</a>
                </div>
            </div>
            <div class="table-responsive">
              <table class="table" id="dataTable">
                <thead class=" text-danger">
                  <th>Name</th>
                  <th>Email</th>
                  <th>Contact</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  @foreach($allUsers as $sale)
                  <tr>
                    <td>{{$sale->name}}</td>
                    <td>{{$sale->email}}</td>
                    <td>{{$sale->contact}}</td>
                    <td class="text-danger">
                        <a href="{{URL::to('/sales/edit').'/'.$sale->sale_rep_id}}"><span class="material-icons">create</span></a>&nbsp;&nbsp;&nbsp;
                        <a href="{{URL::to('/sales/delete').'/'.$sale->sale_rep_id}}"><span class="material-icons">delete</span></a>
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
