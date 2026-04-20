@extends('admin.layouts.app')
@section('page_title',__('Material List'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0)">@lang("Dashboard")</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang("Materials")</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang("Materials")</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header card-header-content-md-between d-flex justify-content-end">
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#CreateModal"
                           class="btn btn-white">
                            <i class="fa-duotone fa-solid fa-plus me-1"></i>
                            @lang('Create')</a>
                    </div>

                    <div class=" table-responsive datatable-custom  ">
                        <table id="datatable"
                               class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        >
                            <thead class="thead-light">
                            <tr>
                                <th>@lang('SL')</th>
                                <th>@lang('Material')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @php
                                $count = 0;
                            @endphp
                            @forelse($materials as $material)
                                <tr>
                                    <td>{{++$count}} </td>
                                    <td>{{$material->material}}</td>
                                    <td>
                                        @if($material->status)
                                            <span class="badge bg-soft-info text-info">
                                            <span class="legend-indicator bg-info"></span>
                                            @lang('Active')
                                        </span>
                                        @else
                                            <span class="badge bg-soft-dark text-dark">
                                            <span class="legend-indicator bg-dark"></span>
                                            @lang('Inactive')
                                        </span>
                                        @endif

                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a class="btn btn-white btn-sm editMaterialButton"
                                               data-route="{{route('admin.material.update',$material->id)}}"
                                               data-material="{{$material->material}}"
                                               data-status="{{$material->status}}"
                                               data-bs-toggle="modal" data-bs-target="#EditModal"
                                               href="javascript:void(0)">
                                                <i class="bi-pencil-fill me-1"></i>@lang('Edit')
                                            </a>
                                            <div class="btn-group">
                                                <button type="button"
                                                        class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty"
                                                        id="productsEditDropdown1" data-bs-toggle="dropdown"
                                                        aria-expanded="false"></button>

                                                <div class="dropdown-menu dropdown-menu-end mt-1"
                                                     aria-labelledby="productsEditDropdown1">
                                                    <a class="dropdown-item deleteBtn text-danger"
                                                       href="javascript:void(0)"
                                                       data-route="{{route('admin.material.destroy',$material->id)}}"
                                                       data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                        <i class="bi-trash dropdown-item-icon text-danger"></i> @lang('Delete')
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="odd">
                                    <td valign="top" colspan="9" class="dataTables_empty">
                                        <div class="text-center p-4">
                                            <img class="dataTables-image mb-3"
                                                 src="{{asset('assets/admin/img/oc-error.svg')}}"
                                                 alt="Image Description" data-hs-theme-appearance="default">
                                            <img class="dataTables-image mb-3"
                                                 src="{{asset('assets/admin/img/oc-error-light.svg')}}"
                                                 alt="Image Description" data-hs-theme-appearance="dark">
                                            <p class="mb-0">@lang('No data to show')</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteModalLabel"><i
                            class="bi bi-check2-square"></i> @lang("Confirmation")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" class="setRoute">
                    @csrf
                    @method("delete")
                    <div class="modal-body">
                        <p>@lang("Do you want to delete this material") </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="EditModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">@lang('Edit Material')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="editRoute" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label"
                                           for="exampleFormControlInput1">@lang('Material Name')</label>
                                    <input type="text" name="material_name" id="editMaterialName"
                                           class="form-control"
                                           placeholder="e.g : gold">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="status" id="editStatus" class="form-check-input">
                                    <label class="form-check-label" for="editStatus">@lang('Material Status')</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Save changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="CreateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">@lang('Create Material')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('admin.material.store')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label"
                                           for="exampleFormControlInput1">@lang('Material Name')</label>
                                    <input type="text" name="material_name" id="exampleFormControlInput1"
                                           class="form-control"
                                           placeholder="e.g : gold">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="status" id="formCheck11" class="form-check-input">
                                    <label class="form-check-label" for="formCheck11">@lang('Material Status')</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Save changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $(document).on('click','.editMaterialButton',function (){
            let material = $(this).data('material');
            let status = $(this).data('status');
            $('#editMaterialName').val(material);
            if(status == 1){
                $('#editStatus').attr('checked',true)
            }
            $('#editRoute').attr('action',$(this).data('route'))
        })

        $(document).on('click', '.deleteBtn', function () {
            let route = $(this).data('route');
            $('.setRoute').attr('action', route);
        })
    </script>
@endpush


