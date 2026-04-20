@extends($theme.'layouts.user')
@section('title',trans('Wishlist'))
@section('content')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="{{route('user.dashboard')}}">@lang("Dashboard")</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang("Wishlist")</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang("Wishlist")</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive datatable-custom">
                <table class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">@lang('No')</th>
                        <th scope="col">@lang('Image')</th>
                        <th scope="col">@lang('Title')</th>
                        <th scope="col">@lang('Price')</th>
                        <th scope="col">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($wishlists as $key => $item)
                        <tr id="remove{{$item->id}}">
                            <td data-label="@lang('No')">{{$wishlists->firstItem()+$key}}</td>
                            <td data-label="@lang('Image')"><img
                                    src="{{getFile(optional($item->product)->driver,optional($item->product)->thumbnail_image)}}"
                                    class="img-thumbnail" alt="product image"></td>
                            <td data-label="@lang('Title')">{{optional($item->product->details)->title}}</td>
                            <td data-label="@lang('Price')"> {{currencyPosition(optional($item->product)->price)}} </td>
                            <td data-label="@lang('Action')">


                                <div class="dropdown">
                                    <button class="action-btn2" type="button" id="verificationAction"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="fa-regular fa-ellipsis-stroke-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{route('product.details',optional($item->product)->slug??'slug')}}"
                                               class="dropdown-item">
                                                @lang('View') <span></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href=""
                                               onclick="event.preventDefault()"
                                               data-id="{{$item->id}}"
                                               class="dropdown-item removeFromWishlist">
                                                @lang('Remove') <span></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            @if(count($wishlists??[]) == 0)
                <div class="row d-flex text-center justify-content-center">
                    <div class="col-4">
                        <img src="{{ asset('assets/admin/img/oc-error.svg') }}" id="no-data-image"
                             class="no-data-image" alt="" srcset="">
                        <p>@lang('No data to show')</p>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <div class="pagination-section">
                <nav aria-label="...">
                    {{ $wishlists->appends($_GET)->links($theme.'partials.user-pagination') }}
                </nav>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $(document).on('click', '.removeFromWishlist', function () {
            let id = $(this).data('id');
            let url = '{{route('user.removeFromWishlist')}}'
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response === 1) {
                        $(`#remove${id}`).remove()
                        Notiflix.Notify.success('Wishlist removed successfully');
                    }
                },
                error: function (err) {
                    console.log(err.responseText)
                }
            });
        })
    </script>
@endpush


@push('style')
    <style>
        .img-thumbnail {
            padding: .25rem;
            background-color: var(--bs-body-bg);
            border: var(--bs-border-width) solid var(--bs-border-color);
            border-radius: var(--bs-border-radius);
            height: 60px;
            width: 70px !important;
        }

        #verificationAction {
            padding: 5px 15px;
            border: .0625rem solid rgba(231, 234, 243, .9) !important;
            border-radius: 5px;
        }

        #verificationAction i {
            font-weight: 300;
        }

    </style>
@endpush
