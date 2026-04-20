@extends($theme.'layouts.user')
@section('page_title',trans('Create Ticket'))
@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang('Dashboard')</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('Create Ticket')</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang('Create Ticket')</h1>
            </div>
        </div>
    </div>


    <div class="container-fluid" id="create_ticket">
        <div class="main row d-flex align-items-center justify-content-center">
            <div class="col-lg-8 col-sm-10">
                <div class="card">

                    <div class="card-body">
                        <form class="form-row" action="{{route('user.ticket.store')}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <div class=" mb-2">
                                    <label class="form-label">@lang('Subject')</label>
                                    <input class="form-control" type="text" name="subject"
                                           value="{{old('subject')}}" placeholder="@lang('Enter Subject')">
                                    @error('subject')
                                    <div class="error text-danger">@lang($message) </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class=" mb-2">
                                    <label class="form-label">@lang('Message')</label>
                                    <textarea class="form-control ticket-box" name="message" rows="5"
                                              id="textarea1"
                                                  placeholder="@lang('Enter Message')">{{old('message')}}</textarea>
                                    @error('message')
                                    <div class="error text-danger">@lang($message) </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="input-field">
                                    <div class="input-images-1" ></div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('Submit')<span></span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


    @push('style')
        <style>
            .image-uploader {
                height: 15rem;
                border: .125rem dashed rgba(231,234,243,.7);
                border-radius: 10px;
                position: relative;
                overflow: auto;
                cursor: pointer;
            }

            .input-images-1{
                padding-top: .5rem !important;
            }
        </style>
    @endpush



@endsection
@push('js-lib')
    <script src="{{ asset('assets/admin/js/image-uploader.js') }}"></script>
@endpush
@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/image-uploader.css') }}">
@endpush

@push('script')
    <script>
        $(document).ready(function (){
            $('.input-images-1').imageUploader();
        })
    </script>
@endpush
