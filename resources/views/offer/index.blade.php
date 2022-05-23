@extends('layouts.app', ['activePage' => 'offer', 'titlePage' => __('Offer Management')])

@section('content')
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title ">Offer Management</h4>
            <p class="card-category"> </p>
          </div>
          <div class="card-body">
            <div class="row">
                <div class="col-12 text-right">
                <a href="{{URL::to('/offer/add')}}" class="btn btn-sm btn-danger"><span class="material-icons">add</span>Add Offer</a>
                </div>
            </div>
            <div class="table-responsive">
              <table class="table" id="dataTable">
                <thead class=" text-danger">
                  <th>Name</th>
                  <th>Image</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  @foreach($offers as $offer)
                  <tr>
                    <td>{{$offer->name}}</td>
                    <td><img src="{{asset('public/offer_images').'/'.$offer->image}}" style="width: 300px;"/></td>
                    <td class="text-danger">
                        <a href="{{URL::to('/offer/edit').'/'.$offer->offer_id}}"><span class="material-icons">create</span></a>&nbsp;&nbsp;&nbsp;
                        <a href="{{URL::to('/offer/delete').'/'.$offer->offer_id}}"><span class="material-icons">delete</span></a>
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
