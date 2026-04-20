@extends($theme.'layouts.app')
@section('title',$page_title)

@section('content')
    <section class="sign-in">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-12">
                    <div class="sign-in-container">
                        <div class="sign-in-container-inner">
                            <div class="sign-in-title">
                                <h4>@lang($page_title)</h4>
                            </div>
                            <div class="sign-in-form">
                                <form action="{{route('user.twoFA-Verify')}}" method="post">
                                    @csrf
                                    <div class="sign-in-form-group">
                                        <input type="text" name="code" value="{{old('code')}}" class="sign-in-input"
                                               placeholder="@lang('Code')" autocomplete="off" required>
                                        @error('code')<span class="text-danger  mt-1">{{ $message }}</span>@enderror
                                        @error('error')<span class="text-danger  mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="sign-in-btn">
                                        <button type="submit" class="btn-1">@lang('Submit')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection
