@extends('layouts.back-end.app')

@section('title', translate('sub_Category'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{asset('/public/assets/back-end/img/brand-setup.png')}}" alt="">
                {{translate('sub_Category_Setup')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <form action="{{route('admin.sub-category.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @php($language=\App\Model\BusinessSetting::where('type','pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach(json_decode($language) as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#"
                                           id="{{$lang}}-link">{{ucfirst(\App\CPU\Helpers::get_language_name($lang)).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div class=" {{ theme_root_path() == 'theme_aster'?'col-lg-6':'col-lg-12 d-flex gap-3' }}">
                                    <div class="w-100">
                                    @foreach(json_decode($language) as $lang)
                                        <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                             id="{{$lang}}-form">
                                            <label class="title-color"
                                                   for="exampleFormControlInput1">{{translate('sub_category_name')}}<span class="text-danger">*</span>
                                                ({{strtoupper($lang)}})</label>
                                            <input type="text" name="name[]" class="form-control"
                                                   placeholder="{{translate('new_Sub_Category')}}" {{$lang == $default_lang? 'required':''}}>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @endforeach
                                    <input name="position" value="1" class="d-none">
                                    </div>
                                    <div class="form-group w-100">
                                        <label class="title-color"
                                               for="exampleFormControlSelect1">{{translate('main_Category')}}
                                            <span class="text-danger">*</span></label>
                                        <select id="exampleFormControlSelect1" name="parent_id"
                                                class="form-control" required>
                                            <option value="" selected disabled>{{translate('select_main_category')}}</option>
                                            @foreach(\App\Model\Category::where(['position'=>0])->get() as $category)
                                                <option value="{{$category['id']}}">{{$category['defaultname']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group w-100">
                                        <label class="title-color" for="priority">{{translate('priority')}}
                                            <span>
                                                <i class="tio-info-outined" title="{{translate('the_lowest_number_will_get_the_highest_priority')}}"></i>
                                            </span>
                                        </label>
                                        <select class="form-control" name="priority" id="" required>
                                            <option disabled selected>{{translate('set_Priority')}}</option>
                                            @for ($i = 0; $i <= 10; $i++)
                                                <option
                                                    value="{{$i}}" >{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    @if (theme_root_path() == 'theme_aster')
                                    <div class="from_part_2">
                                        <label class="title-color">{{translate('sub_category_Logo')}}</label>
                                        <span class="text-info">{{ THEME_RATIO[theme_root_path()]['Category Image'] }}</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileEg1"
                                            class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                            for="customFileEg1">{{translate('choose_File')}}</label>
                                        </div>
                                    </div>
                                    @endif

                                </div>

                                @if (theme_root_path() == 'theme_aster')
                                <div class="col-lg-6 mt-4 mt-lg-0 from_part_2">
                                    <div class="form-group">
                                        <center>
                                            <img
                                                class="upload-img-view"
                                                id="viewer"
                                                src="{{asset('public/assets/back-end/img/900x400/img1.jpg')}}"
                                                alt="image"/>
                                        </center>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-20" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="text-capitalize d-flex gap-2">
                                    {{ translate('sub_category_list')}}
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $categories->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{translate('search_by_Sub_Category')}}" aria-label="Search orders" value="{{ $search }}" required>
                                        <button type="submit" class="btn btn--primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('ID')}}</th>
                                    @if (theme_root_path() == 'theme_aster')
                                        <th class="text-center">{{ translate('sub_category_Image')}}</th>
                                    @endif
                                    <th>{{ translate('name')}}</th>
                                    <th>{{translate('priority')}}</th>
                                    <th class="text-center">{{ translate('action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $key=>$category)
                                <tr>
                                    <td>{{$category['id']}}</td>
                                    @if (theme_root_path() == 'theme_aster')
                                        <td class="text-center">
                                            <img class="rounded" width="64"
                                                 onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                 src="{{asset('storage/app/public/category')}}/{{$category['icon']}}">
                                        </td>
                                    @endif
                                    <td>{{($category['defaultname'])}}</td>
                                    <td>{{$category['priority']}}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                title="{{ translate('edit')}}"
                                                href="{{route('admin.category.edit',[$category['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                title="{{ translate('delete')}}"
                                                id="{{$category['id']}}">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{$categories->links()}}
                        </div>
                    </div>

                    @if(count($categories)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Descripti">
                            <p class="mb-0">{{translate('no_data_to_show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            // console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{$default_lang}}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>

    <script>
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: "{{translate('are_you_sure_to_delete_this_sub_category')}}?",
                text: "{{translate('you_will_not_be_able_to_revert_this')}}!",
                showCancelButton: true,
                type: 'warning',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{translate('yes_delete_it')}}!",
                cancelButtonText: '{{ translate("cancel") }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.sub-category.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success("{{translate('sub_Category_deleted_Successfully.')}}");
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>
@endpush
