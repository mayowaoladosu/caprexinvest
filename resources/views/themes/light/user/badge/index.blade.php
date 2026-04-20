@extends(template().'layouts.user')
@section('page_title', 'Badges')

@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang('Dashboard')</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('Badges')</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang('Badges')</h1>
            </div>
        </div>
    </div>


    <div class="mt-50">
        @if($allBadges)
            <div class="badge-box-wrapper">
                <div class="row g-4 mb-4">
                    @forelse($allBadges as $key => $badge)
                        <div class="col-xl-4 col-lg-4 col-md-6 box">
                            <div class="card badge-box">
                               <div class="card-body text-center">
                                   @if(auth()->user()->rank && auth()->user()->rank->id == $badge->id)
                                       <i class="fa-regular fa-square-check" data-bs-toggle="tooltip" data-bs-placement="top" title="Your Current Badge"></i>
                                   @endif
                                   <img src="{{ getFile($badge->driver,$badge->rank_icon) }}" alt="" />
                                   <h3>@lang(@$badge->rank_lavel)</h3>
                                   <p>@lang($badge->description)</p>
                                   <div class="text-start">
                                       <h5>@lang('Minimum Invest'): <span>{{ currencyPosition($badge->min_invest) }}</span></h5>
                                       <h5>@lang('Minimum Deposit'): <span>{{ currencyPosition($badge->min_deposit) }}</span></h5>
                                       <h5>@lang('Minimum Earning'): <span>{{ currencyPosition($badge->min_earning) }}</span></h5>
                                   </div>
                               </div>
                            </div>
                        </div>
                        @empty
                        <div class="row d-flex text-center justify-content-center mt-5">
                            <div class="col-12">
                                <img src="{{ asset('assets/admin/img/oc-error.svg') }}" id="empty-image" class="no-data-image" alt="" srcset="">
                                <p>@lang('No data to show')</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <div class="text-center p-4">
                <img class=" mb-3 w-25" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="Image Description" data-hs-theme-appearance="default">
                <p class="mb-0">@lang('No data to show')</p>
            </div>
        @endif
    </div>
@endsection
