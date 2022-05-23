@extends('layouts.app', ['activePage' => 'transaction', 'titlePage' => __('TRansaction Management')])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">TRansaction</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-right mb-3">
                        <!-- <a href="{{URL::to('/artist/add')}}" class="btn btn-sm btn-primary">Add Artist</a> -->
                    </div>
                    <div class="table-responsive">
                        <table class="table  " id="tablesorter">
                            <thead class=" text-primary">
                                <tr>
                                    <th>Artist Name</th>
                                    <th>User Name</th>
                                    <th>Transaction ID</th>
                                    <th>Toatl Tip</th>
                                    <th>Artist Fund</th>
                                    <th>Admin Fund</th>
                                    <th>is Redeemed</th>
                                    <th> Created Date </th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($resultArray as $ul)
                                    <tr>
                                        <td>{{$ul['name']}}</td>
                                        <td>{{$ul['user_name']}}</td>
                                        <td>{{$ul['trn_id']}}</td>
                                        <td>{{$ul['tip_count']}}</td>
                                        <td>{{ $ul['art_count']}}</td>
                                        <td>{{$ul['admin_count']}}</td>
                                        <td>{{$ul['is_redemeeded']? 'Yes': 'No'}}</td>
                                        <td>{{$ul['created_at']}}</td>
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
