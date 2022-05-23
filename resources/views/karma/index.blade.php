@extends('layouts.app', ['activePage' => 'karma-points', 'titlePage' => __('Karma Points')])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Karma Points</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-right mb-3">
                        <a href="{{URL::to('/karma-points/add')}}" class="btn btn-sm btn-primary">Add Points</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                                <tr>
                                    <th>Price Min </th>
                                    <th>Price Max</th>
                                    <th>Points</th>
                                    <th class="text-center">Action</th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($karmaPoint as $ul)
                                    <tr>
                                        <td>{{$ul->min}}</td>
                                        <td>{{$ul->max}}</td>
                                        <td>{{$ul->points}}</td>
                                        <td class="text-center">
                                            <a href="{{URL::to('/karma-points/edit').'/'.$ul->id}}"><i class="tim-icons icon-pencil"></i>Edit</a>&nbsp;&nbsp;&nbsp;
                                            <a href="{{URL::to('/karma-points/delete').'/'.$ul->id}}"><i class="tim-icons icon-trash-simple"></i>Delete</a>&nbsp;&nbsp;&nbsp;
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
