@extends('layouts.app', ['activePage' => 'content', 'titlePage' => __('Content Management')])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Content</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-right mb-3">
                        <a href="{{URL::to('/content/add')}}" class="btn btn-sm btn-primary">Add content</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                                <tr>
                                    <th>Title</th>
                                    <th>description</th>
                                    <th>Creation Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($contentList as $ul)
                                    <tr>
                                        <td>{{$ul->title}}</td>
                                        <td>{{substr(($ul->description), 0, 100)}}</td>
                                        <td>{{ date('M Y d', strtotime($ul->created_at))}}</td>
                                        <td class="text-center">
                                            <a href="{{URL::to('/content/edit').'/'.$ul->id}}"><i class="tim-icons icon-pencil"></i>Edit</a>&nbsp;&nbsp;&nbsp;
                                            <a href="{{URL::to('/content/delete').'/'.$ul->id}}"><i class="tim-icons icon-trash-simple"></i>Delete</a>&nbsp;&nbsp;&nbsp;
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
