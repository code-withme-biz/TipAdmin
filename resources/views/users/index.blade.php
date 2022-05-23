@extends('layouts.app', ['activePage' => 'user', 'titlePage' => __('User Management')])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Users</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-right mb-3">
                        <a href="{{URL::to('/user/add')}}" class="btn btn-sm btn-primary">Add user</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Karma Points</th>
                                    <th>Creation Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($userList as $ul)
                                    <tr>
                                        <td>{{$ul->name}}</td>
                                        <td>{{$ul->email}}</td>
                                        <td>{{$ul->kPoints}}</td>
                                        <td>{{ date('M Y d', strtotime($ul->created_at))}}</td>
                                        <td class="text-center">
                                            @if($ul->is_active)
                                                <a href="{{URL::to('/user/statusChange').'/'.$ul->id.'/0'}}"><i class="tim-icons icon-simple-remove"></i>Disable</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            @else
                                                <a href="{{URL::to('/user/statusChange').'/'.$ul->id.'/1'}}"><i class="tim-icons icon-check-2"></i>Enable</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                            <a href="{{URL::to('/user/edit').'/'.$ul->id}}"><i class="tim-icons icon-pencil"></i>Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="{{URL::to('/user/delete').'/'.$ul->id}}"><i class="tim-icons icon-trash-simple"></i>Delete</a>&nbsp;&nbsp;&nbsp;&nbsp;
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
@endsection
