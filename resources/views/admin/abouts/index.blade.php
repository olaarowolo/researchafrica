@extends('layouts.admin')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Update About Us
            </div>
        
            <div class="card-body">
                <form action="{{ route('admin.abouts.update') }}" method="post">
                    @csrf
                    <div class="mb-3 form-group">
                      <label for="description" class="form-label font-weight-bold required">
                        {{ trans('global.description') }}
                      </label>
                      <textarea class="form-control textarea" name="description" id="description" rows="3">{{ $about->description }}</textarea>
                    </div>
                    <div class="mb-3 form-group">
                      <label for="mission" class="form-label font-weight-bold required">
                        {{ __('Mission') }}
                      </label>
                      <textarea class="form-control textarea" name="mission" style="height: 100px;">{{ $about->mission }}</textarea>
                    </div>
                    <div class="mb-3 form-group">
                      <label for="vision" class="form-label font-weight-bold required">
                        {{ __('Vision') }}
                      </label>
                      <textarea class="form-control textarea" name="vision" id="vision" style="height: 100px;">{{ $about->vision }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@endsection
