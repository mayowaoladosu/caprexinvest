@extends('admin.layouts.app')
@section('page_title', __('Home Styles'))
@section('content')
    <div class="content container-fluid" id="homeStyles">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0)">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item active"
                                aria-current="page">@lang("Home Style")</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang("Home Styles")</h1>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-10">
                <div class="row d-flex justify-content-center">
                    <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5 ">
                        <div class="select-theme">
                            <label class="form-control" for="formControlRadioReverseEg1">
                      <span class="form-check home_style">
                        <input type="radio" class="form-check-input" name="homeStyle" value="home_one"
                               id="formControlRadioReverseEg1" @checked(basicControl()->home_style=='home_one')>
                         <img class="img-fluid w-100" src="{{asset($themeTrue.'images/home_one.png')}}"
                              alt="Image Description">
                      </span>
                            </label>
                        </div>
                        <div class="text-center">
                            <h5 class="mb-0 bg-warning p-3">@lang('Home Style 01')</h5>
                        </div>
                        <!-- End Card -->
                    </div>

                    <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5 ">
                        <label class="form-control" for="formControlRadioReverseEg2">
                      <span class="form-check home_style">
                        <input type="radio" class="form-check-input" name="homeStyle" value="home_two"
                               id="formControlRadioReverseEg2" @checked(basicControl()->home_style=='home_two')>
                         <img class="img-fluid w-100" src="{{asset($themeTrue.'images/home_two.png')}}"
                              alt="Image Description">
                      </span>
                        </label>
                        <div class="text-center">
                            <h5 class="mb-0 bg-warning p-3">@lang('Home Style 02')</h5>
                        </div>
                        <!-- End Card -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(document).ready(function () {
            // Assuming you want to trigger an event when the radio button is changed
            $('.form-check-input').on('change', function () {
                if ($(this).prop('checked')) {
                    Notiflix.Block.standard('#homeStyles');
                    var radioValue = $(this).val();
                    selectHomeStyle(radioValue)
                }
            });

            async function selectHomeStyle(val) {
                let url = "{{ route('admin.select.home.style', ['val' => ':val']) }}";
                url = url.replace(':val', val);
                await axios.get(url)
                    .then(function (res) {

                        console.log(res)

                        Notiflix.Block.remove('#homeStyles');
                    })
                    .catch(function (error) {

                    });
            }
        });
    </script>
@endpush
