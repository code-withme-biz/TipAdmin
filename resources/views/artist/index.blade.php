@extends('layouts.app', ['activePage' => 'artist', 'titlePage' => __('Artist Management')])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Artist</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-right mb-3">
                        <a href="{{URL::to('/artist/add')}}" class="btn btn-sm btn-primary">Add Artist</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table  " id="tablesorter">
                            <thead class=" text-primary">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Artist Code</th>
                                    <th>Creation Date</th>
                                    <th>Total Tips</th>
                                    <th>Tips Redeemed</th>
                                    <th>Tips Pending</th>
                                    <th class="text-center">Action</th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($resultArray as $ul)
                                    <tr>
                                        <td>{{$ul['name']}} - {{$ul['song']}}</td>
                                        <td>{{$ul['email']}}</td>
                                        <td>{{$ul['unique_id']}}</td>
                                        <td>{{ date('d M Y', strtotime($ul['created_at']))}}</td>
                                        <td>{{$ul['tip_count']}}</td>
                                        <td>{{$ul['red_count']}}</td>
                                        <td>{{$ul['pend_count']}}</td>
                                        <td class="text-center">
                                            @if($ul['is_active'])
                                                <a href="{{URL::to('/artist/statusChange').'/'.$ul['id'].'/0'}}"><i class="tim-icons icon-simple-remove"></i>Disable</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            @else
                                                <a href="{{URL::to('/artist/statusChange').'/'.$ul['id'].'/1'}}"><i class="tim-icons icon-check-2"></i>Enable</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                            <a href="{{URL::to('/artist/edit').'/'.$ul['id']}}"><i class="tim-icons icon-pencil"></i>Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="{{URL::to('/artist/delete').'/'.$ul['id']}}"><i class="tim-icons icon-trash-simple"></i>Delete</a>&nbsp;&nbsp;&nbsp;&nbsp;
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
@section('scripts')
<script>
$('#tablesorter').DataTable({
    "order": [[ 3, "desc" ]]
});
</script>

@endsection
