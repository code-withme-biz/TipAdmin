@extends('layouts.app', ['activePage' => 'table', 'titlePage' => __('User List')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title ">User Management</h4>
            <p class="card-category"> </p>
          </div>
          <div class="card-body">
            <div class="row">
                <div class="col-12 text-right">
                <a href="{{URL::to('/user/add')}}" class="btn btn-sm btn-danger"><span class="material-icons">add</span>Add user</a>
                </div>
            </div>
            <div class="table-responsive">
              <table class="table" id="dataTable">
                <thead class=" text-danger">
                  <th>Name</th>
                  <th>Email</th>
                  <th>Contact</th>
                  <th>Role</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  @foreach($allUsers as $user)
                  <tr>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->contact_number}}</td>
                    <td>
                        @if($user->role_id == 1)
                        Master Admin
                        @else if($user->role_id == 2)
                        Product Admin
                        @endif
                    </td>
                    <td class="text-danger">
                        <a href="{{URL::to('/user/edit').'/'.$user->id}}"><span class="material-icons">create</span></a>&nbsp;&nbsp;&nbsp;
                        <a href="{{URL::to('/user/delete').'/'.$user->id}}"><span class="material-icons">delete</span></a>&nbsp;&nbsp;&nbsp;
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
