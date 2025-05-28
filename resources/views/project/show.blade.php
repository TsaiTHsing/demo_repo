@extends('backstage.main')

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/project/project.css?v=202407') }}" />
    <style>
        .slc_disabled {
            pointer-events: none;
            background: #dddddd;
        }

        a.pre-lightbox{
            display: block;
            width: 100%;
            height: 100%;
            padding: 50%;
        }
    </style>

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            background-color: #f8f8f8;
        }

        a {
            text-decoration: none;
            color: #6c757d;
        }

        /* 忘記密碼已使用過，可直接略過此設定 */
        .btn.submit_btn {
            background-color: #e9892c;
            color: #FFFFFF;
            width: 100%;
            padding: 10px;
            height: 45px;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.1);
            }

            60% {
                opacity: 1;
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .bounceIn {
            animation-name: bounceIn;
            animation-duration: 0.5s;
        }

        .modal-backdrop {
            display: none;
        }
    </style>
    <style>
        .font20x {
            font-size: 20px;
        }

        .font18x {
            font-size: 18px;
        }

        .font16x {
            font-size: 16px;
        }

        .font14x {
            font-size: 14px;
        }

        .font12x {
            font-size: 12px;
        }

        .textGreen {
            color: #6EBA44;
        }

        .textmuted {
            color: #6c757d;
        }

        .minw-100x {
            min-width: 100px;
        }

        .btn:focus {
            box-shadow: none;
        }
        
    </style>

    <!-- 放大圖片 -->
    <link rel="stylesheet" href="{{ asset('css/photoswipe.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Uploadfile.css') }}">

    <script>
        var ratio = 0.75 // 預設的縮小比率
        if (window.innerWidth < 600 || window.innerHeight < 600) {
            ratio = 0.8; // 如果螢幕的寬度或高度小於600，則將縮小比率設為0.8
        }

        var maxWidth = window.innerWidth * ratio; // 瀏覽器寬度的70%或80%
        var maxHeight = window.innerHeight * ratio; // 瀏覽器高度的70%或80%

        function handleImageLoad(event) {
            console.log('handleImageLoad');
            var img = event.target;
            var a = img.parentElement;
            var width = img.width;
            var height = img.height;

            var imageRatio = width / height;

            if (width > maxWidth) {
                width = maxWidth;
                height = width / imageRatio;
            }

            if (height > maxHeight) {
                height = maxHeight;
                width = height * imageRatio;
            }

            a.setAttribute('data-pswp-width', width);
            a.setAttribute('data-pswp-height', height);
        }
    </script>
    <style>
        /* 修改進用放大的滑鼠指標修正 */
        .pswp__img {
            cursor: pointer !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('plugins/selectize/selectize.bootstrap4.css') }}">
@endsection

@section('content')
    <div class="content">
        @include('backstage.notification.success')
        @include('backstage.notification.wrong')
        <div class="breadcrumb-wrapper breadcrumb-contacts">
            <div class="w-100 d-flex justify-content-between align-items-center" style="margin-bottom:30px;">
                <h1 class="">專案基本資料</h1>
                <div class="d-flex">
                <a href="{{ route('project.index') }}"
                        class="btn btn-gk-return ml-auto mr-1">返回</a>
                @if (Session::get('can.project_info.update'))
                    <a href="{{ route('project.edit', ['project' => $project->id]) }}"
                        class="btn btn-gk-edit ml-auto mr-1">編輯</a>
                @endif
                @if (Session::get('can.project_task.read'))
                    @if(in_array($project->id, $exception_project_id))
                        <a href="{{ route('project_task.index', ['project_id' => $project->id, 'tag_type' => '99']) }}" class="btn btn-gk-save">程式開發</a>
                    @else
                        <a href="{{ route('project_task.index', ['project_id' => $project->id, 'tag_type' => '1']) }}" class="btn btn-gk-save">程式開發</a>
                    @endif
                @endif
                </div>
            </div>
            <div class="w-100">
                <!-- <form action="{{ route('project.update', ['project' => $project->id]) }}" method="post" enctype="multipart/form-data"> -->
                <div class="card card-default">
                    <div class="card-header card-header-border-bottom">
                        <h2 class="">專案資訊</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- <div class="row col-12"> -->
                            <div class="form-group mb-4 col-12 col-lg-6">
                                <label>專案名稱<span class="required">*</span></label>
                                <input type="text" id="name" name="name" value="{{ $project->title }}"
                                    class="form-control" placeholder="請輸入專案名稱" maxlength="255" disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-6">
                                <label>專案類型<span class="required">*</span></label>
                                <select id="type_id" name="type_id[]" class="slc_disabled" multiple>
                                    @foreach ($project_types as $type_id => $type)
                                        <option value="{{ $type_id }}"
                                            {{ in_array($type_id, $cat_json) ? 'selected' : '' }}>{{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>專案標準色<span class="required">*</span></label>
                                <input type="text" name="color_code" value="{{ $project->color_code }}"
                                    class="form-control" placeholder="請輸入色碼" maxlength="20" disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>專案進度<span class="required">*</span></label>
                                <select id="project_status" name="project_status" class="form-control slc_disabled">
                                    @foreach ($schedules as $schedule_id => $schedule)
                                        <option value="{{ $schedule_id }}"
                                            {{ $schedule_id == $project->project_status ? 'selected' : '' }}>
                                            {{ $schedule }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>專案等級</label>
                                <select id="project_level" name="project_level" class="form-control slc_disabled">
                                    <option value="">請選擇等級</option>
                                    @foreach ($levels as $level_id => $level)
                                        <option value="{{ $level_id }}"
                                            {{ $level_id == $project->project_level ? 'selected' : '' }}>
                                            {{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- </div> -->
                            <div class="col-12">
                                <div class="form-group mb-4">
                                    <label>專案描述</label>
                                    <textarea class="form-control d-none" placeholder="請描述專案" id="textArea0" name="description" rows="10" disabled>{{ $project->description }}</textarea>
                                    <div id="textDisplay" class="form-control" style="overflow-y: auto; height: 250px; width: 100%; resize: none; border: 1px solid #ccc; padding: 8px; box-sizing: border-box; ">{!! $project->description !!}</div>
                                </div>
                            </div>

                            <div class="form-group mb-4 col-3">
                                <div>
                                    <label>專案</label>
                                </div>
                                <select id="project_employees" name="project_employees[]" class="slc_disabled" multiple>
                                    @foreach ($employees4 as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ in_array($employee->id, $p_employees) ? 'selected' : '' }}>
                                            {{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-4 col-3">
                                <div>
                                    <label>版面</label>
                                </div>
                                <select id="layout_employees" name="layout_employees[]" class="slc_disabled" multiple>
                                    @foreach ($employees4 as $employee2)
                                        <option value="{{ $employee2->id }}"
                                            {{ in_array($employee2->id, $l_employees) ? 'selected' : '' }}>
                                            {{ $employee2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-4 col-3">
                                <div>
                                    <label>前端</label>
                                </div>
                                <select id="front_employees" name="front_employees[]" class="slc_disabled" multiple>
                                    @foreach ($employees4 as $employee3)
                                        <option value="{{ $employee3->id }}"
                                            {{ in_array($employee3->id, $f_employees) ? 'selected' : '' }}>
                                            {{ $employee3->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-4 col-3">
                                <div>
                                    <label>後端</label>
                                </div>
                                <select id="back_employees" name="back_employees[]" class="slc_disabled" multiple>
                                    @foreach ($employees4 as $employee4)
                                        <option value="{{ $employee4->id }}"
                                            {{ in_array($employee4->id, $b_employees) ? 'selected' : '' }}>
                                            {{ $employee4->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="Edit_content mt-2">

                            <!-- 上傳圖片 -->
                            <div class="memberForumUploadfile">
                                <h3 class="upload_string">專案檔案</h3>
                                <div class="fileUploadContent mt-3" data-id="fileUploadContent"
                                    id="gallery--getting-started">
                                    @foreach ($project->files as $projectFile)
                                        @php
                                            $data = GetFileInfo::format($projectFile);
                                            [
                                                'img_url' => $img_url,
                                                'isImage' => $isImage,
                                                'isPreview' => $isPreview,
                                            ] = $data;
                                        @endphp
                                        <div class="card">
                                            @if ($isImage)
                                                <div class="img_box border"
                                                    style="background: url({{ $img_url }}) no-repeat; background-size: contain; background-position: center;">
                                                    <a href="{{ $img_url }}" class="img_box pre-lightbox"
                                                        data-pswp-width="" data-pswp-height="" target="_blank">
                                                        <img src="{{ $img_url }}" class="d-none"
                                                            onload="handleImageLoad(event)" />
                                                    </a>
                                                    <div class="d-flex position-absolute" style="top:0;right:0">
                                                        <div class="project-file" data-id="{{ $projectFile->id }}"
                                                            style="background-color: #808080;width:24px;height:24px">
                                                            <x-download_icon />
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="img_box border"
                                                    style="background: url({{ $img_url }}) no-repeat; background-size: contain; background-position: center;">
                                                    <div class="d-flex position-absolute" style="top:0;right:0">
                                                        @if ($isPreview)
                                                            <div class="preview-file" data-id="{{ $projectFile->id }}"
                                                                style="background-color: #808080;width:24px;height:24px;margin-right:4px">
                                                                <x-preview_icon />
                                                            </div>
                                                        @endif
                                                        <div class="project-file" data-id="{{ $projectFile->id }}"
                                                            style="background-color: #808080;width:24px;height:24px">
                                                            <x-download_icon />
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-center">
                                                <span style="width: 100%; font-size:12px;">{{ $projectFile->fname }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-default">
                    <div class="card-header card-header-border-bottom">
                        <h2 class="">時間排程</h2>
                    </div>
                    <div class="card-body">
                        <div class="row col-12">
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>預計交付初稿時間<span class="required"></span></label>
                                <input type="date" id="e_d_d" name="e_d_d"
                                    value="{{ optional($project->schedule)->estimated_draft_delivery }}"
                                    class="form-control" disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>實際交付初稿時間<span class="required"></span></label>
                                <input type="date" id="a_d_d" name="a_d_d"
                                    value="{{ optional($project->schedule)->actual_draft_delivery }}"
                                    class="form-control" disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>定稿時間<span class="required"></span></label>
                                <input type="date" id="f_v_d" name="f_v_d"
                                    value="{{ optional($project->schedule)->final_version_date }}" class="form-control"
                                    disabled>
                            </div>
                        </div>
                        <div class="row col-12">
                            <div class="form-group mb-4 col-12 col-lg-3">
                                <label>預計交測試檔時間</span></label>
                                <input type="date" id="e_t_d" name="e_t_d"
                                    value="{{ optional($project->schedule)->estimated_test_delivery }}"
                                    class="form-control" disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-3">
                                <label>實際交付測試檔時間<span class="required"></span></label>
                                <input type="date" id="a_t_d" name="a_t_d"
                                    value="{{ optional($project->schedule)->actual_test_delivery }}" class="form-control"
                                    disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-3">
                                <label>客戶開始驗收時間</label>
                                <input type="date" id="c_a_s" name="c_a_s"
                                    value="{{ optional($project->schedule)->client_acceptance_start }}"
                                    class="form-control" disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-3">
                                <label>客戶結束驗收時間</label>
                                <input type="date" id="c_a_e" name="c_a_e"
                                    value="{{ optional($project->schedule)->client_acceptance_end }}"
                                    class="form-control" disabled>
                            </div>
                        </div>
                        <div class="row col-12">
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>正式上線時間</label>
                                <input type="date" id="o_l_d" name="o_l_d"
                                    value="{{ optional($project->schedule)->official_launch_date }}" class="form-control"
                                    disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>保固開始時間</label>
                                <input type="date" id="w_s_d" name="w_s_d"
                                    value="{{ optional($project->schedule)->warranty_start_date }}" class="form-control"
                                    disabled>
                            </div>
                            <div class="form-group mb-4 col-12 col-lg-4">
                                <label>保固結束時間</label>
                                <input type="date" id="w_e_d" name="w_e_d"
                                    value="{{ optional($project->schedule)->warranty_expiration_date }}"
                                    class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                @if (Session::get('can.project_inter_meeting.read'))
                <div class="card card-default">

                    <div class="card-header card-header-border-bottom d-flex justify-content-between">
                        <h2 class="">溝通紀錄</h2>
                        @if (Session::get('can.project_inter_meeting.create'))
                            <a href="{{ route('project_inter_meeting.create', ['project_id' => $project->id, 'type' => 1]) }}"
                                class="btn btn-gk-add">新增</a>
                        @endif
                    </div>

                    <div class="card-body">

                        <div class="basic-data-table">
                            <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <table class="table nowrap dataTable no-footer" style="width: 100%;" role="grid"
                                    aria-describedby="person-data-table_info">
                                    <thead>
                                        <tr role="row">
                                            <th class="text-center text-nowrap h6" rowspan="1" colspan="1"
                                                style="min-width:150px !important;">會議日期</th>
                                            <th class="text-center text-nowrap h6" rowspan="1" colspan="1"
                                                style="min-width:350px !important;">紀錄</th>
                                            <th class="text-center text-nowrap h6" rowspan="1" colspan="1"
                                                style="min-width:150px !important;">建立人</th>
                                            <th class="text-center">檔案</th>
                                            <th class="text-right text-nowrap" rowspan="1" colspan="1"
                                                style="">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($project->inside_meetings as $i_meet_no => $inside_meeting)
                                            <tr role="row" class="odd">
                                                <td class="text-center tb_fnt">
                                                    {{ $inside_meeting->meet_date }}</td>
                                                <td class="text-left tb_fnt" id="i_meet_id_{{$i_meet_no}}" style="max-width: 350px;">
                                                    {!! nl2br(($inside_meeting->content)) !!}</td>
                                                <td class="text-center tb_fnt">
                                                    {{ optional($inside_meeting->editor)->name }}</td>
                                                <td>
                                                    <div class="fileUploadContent meeting-file-group d-flex justify-content-center"
                                                        id="gallery--getting-started" data-id="fileUploadContent">
                                                        @foreach ($inside_meeting->files as $projectFile)
                                                            @php
                                                                $data = GetFileInfo::format($projectFile);
                                                                [
                                                                    'img_url' => $img_url,
                                                                    'isImage' => $isImage,
                                                                    'isPreview' => $isPreview,
                                                                ] = $data;
                                                            @endphp
                                                            <div class="card meetingCard border-0 mr-2" style="max-width:100px;">
                                                                @if ($isImage)
                                                                    <div class="img_box border"
                                                                        style="background: url({{ $img_url }}) no-repeat; background-size: contain; background-position: center;">
                                                                        <a href="{{ $img_url }}"
                                                                            class="img_box pre-lightbox"
                                                                            data-pswp-width="" data-pswp-height=""
                                                                            target="_blank" style="position: absolute;">
                                                                            <img src="{{ $img_url }}" class="d-none"
                                                                                onload="handleImageLoad(event)" />
                                                                        </a>
                                                                        <div class="d-flex position-absolute"
                                                                            style="top:0;right:0">
                                                                            <!-- <div class="preview-meeting-file" data-path="{{ $projectFile->path }}" style="background-color: #808080;width:24px;height:24px; margin-right:4px">
                                                                                <x-preview_icon />
                                                                            </div> -->
                                                                            <div class="meeting-project-file"
                                                                                data-id="{{ $projectFile->id }}"
                                                                                style="background-color: #808080;width:24px;height:24px">
                                                                                <x-download_icon />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="img_box border"
                                                                        style="background: url({{ $img_url }}) no-repeat; background-size: contain; background-position: center;">
                                                                        <div class="d-flex position-absolute"
                                                                            style="top:0;right:0">
                                                                            @if ($isPreview)
                                                                                <div class="meeting-preview-file"
                                                                                    data-id="{{ $projectFile->id }}"
                                                                                    style="background-color: #808080;width:24px;height:24px;margin-right:4px">
                                                                                    <x-preview_icon />
                                                                                </div>
                                                                            @endif
                                                                            <div class="meeting-project-file"
                                                                                data-id="{{ $projectFile->id }}"
                                                                                style="background-color: #808080;width:24px;height:24px">
                                                                                <x-download_icon />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="d-flex justify-content-center">
                                                                    <span style="width: 100%; font-size:12px;">{{ $projectFile->fname }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </td>
                                                <td class="text-right text-nowrap">
                                                    @if (Session::get('can.project_inter_meeting.update'))
                                                        <a href="{{ route('project_inter_meeting.edit', ['inter_meeting' => $inside_meeting->id]) }}"
                                                            class="btn btn-gk-edit">編輯</a>
                                                    @endif
                                                    @if (Session::get('can.project_inter_meeting.delete'))
                                                        <form
                                                            action="{{ route('project_inter_meeting.destroy', ['inter_meeting' => $inside_meeting->id]) }}"
                                                            method="POST" class="d-inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="button" class="btn btn-gk-delete deleteBtn"
                                                                value="刪除">
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="row justify-content-between bottom-information">
                                    <div class="dataTables_info" id="person-data-table_info" role="status"
                                        aria-live="polite">
                                    </div>
                                    {{-- $projects->links('backstage.pagination.index') --}}
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>

                    </div>
                </div>
                @endif
                
                @if (Session::get('can.project_outer_meeting.read'))
                <div class="card card-default">

                    <div class="card-header card-header-border-bottom d-flex justify-content-between">
                        <h2 class="">會議紀錄</h2>
                        @if (Session::get('can.project_outer_meeting.create'))
                            <a href="{{ route('project_inter_meeting.create', ['project_id' => $project->id, 'type' => 2]) }}"
                                class="btn btn-gk-add">新增</a>
                        @endif
                    </div>

                    <div class="card-body">

                        <div class="basic-data-table">
                            <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <table class="table nowrap dataTable no-footer" style="width: 100%;" role="grid"
                                    aria-describedby="person-data-table_info">
                                    <thead>
                                        <tr role="row">
                                            <th class="text-center text-nowrap h6" rowspan="1" colspan="1"
                                                style="min-width:150px !important;">會議日期</th>
                                            <th class="text-center text-nowrap h6" rowspan="1" colspan="1"
                                                style="min-width:350px !important;">紀錄</th>
                                            <th class="text-center text-nowrap h6" rowspan="1" colspan="1"
                                                style="min-width:150px !important;">建立人</th>
                                            <th class="text-center">檔案</th>
                                            <th class="text-right text-nowrap" rowspan="1" colspan="1"
                                                style="">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($project->outside_meetings as $outside_meeting)
                                            <tr role="row" class="odd">
                                                <td class="text-center tb_fnt">
                                                    {{ $outside_meeting->meet_date }}</td>
                                                <td class="text-left tb_fnt" style="max-width: 350px;">
                                                    {!! nl2br(($outside_meeting->content)) !!}</td>
                                                <td class="text-center tb_fnt">
                                                    {{ optional($outside_meeting->editor)->name }}</td>
                                                <td>
                                                    <div class="fileUploadContent meeting-file-group d-flex"
                                                        data-id="fileUploadContent" id="gallery--getting-started">
                                                        @foreach ($outside_meeting->files as $projectFile)
                                                            @php
                                                                $data = GetFileInfo::format($projectFile);
                                                                [
                                                                    'img_url' => $img_url,
                                                                    'isImage' => $isImage,
                                                                    'isPreview' => $isPreview,
                                                                ] = $data;
                                                            @endphp
                                                            <div class="card meetingCard border-0 mr-2" style="max-width:100px;">
                                                                @if ($isImage)
                                                                <div class="img_box border"
                                                                        style="background: url({{ $img_url }}) no-repeat; background-size: contain; background-position: center;">
                                                                        <a href="{{ $img_url }}"
                                                                            class="img_box pre-lightbox"
                                                                            data-pswp-width="" data-pswp-height=""
                                                                            target="_blank" style="position: absolute;">
                                                                            <img src="{{ $img_url }}" class="d-none"
                                                                                onload="handleImageLoad(event)" />
                                                                        </a>
                                                                        <div class="d-flex position-absolute"
                                                                            style="top:0;right:0">
                                                                            <!-- <div class="preview-meeting-file" data-path="{{ $projectFile->path }}" style="background-color: #808080;width:24px;height:24px; margin-right:4px">
                                                                                <x-preview_icon />
                                                                            </div> -->
                                                                            <div class="meeting-project-file"
                                                                                data-id="{{ $projectFile->id }}"
                                                                                style="background-color: #808080;width:24px;height:24px">
                                                                                <x-download_icon />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="img_box border"
                                                                        style="background: url({{ $img_url }}) no-repeat; background-size: contain; background-position: center;">
                                                                        <div class="d-flex position-absolute"
                                                                            style="top:0;right:0">
                                                                            @if ($isPreview)
                                                                                <div class="meeting-preview-file"
                                                                                    data-id="{{ $projectFile->id }}"
                                                                                    style="background-color: #808080;width:24px;height:24px;margin-right:4px">
                                                                                    <x-preview_icon />
                                                                                </div>
                                                                            @endif
                                                                            <div class="meeting-project-file"
                                                                                data-id="{{ $projectFile->id }}"
                                                                                style="background-color: #808080;width:24px;height:24px">
                                                                                <x-download_icon />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="d-flex justify-content-center">
                                                                    <span style="width: 100%; font-size:12px;">{{ $projectFile->fname }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="text-right text-nowrap">
                                                    @if (Session::get('can.project_outer_meeting.update'))
                                                        <a href="{{ route('project_inter_meeting.edit', ['inter_meeting' => $outside_meeting->id]) }}"
                                                            class="btn btn-gk-edit">編輯</a>
                                                    @endif
                                                    @if (Session::get('can.project_outer_meeting.delete'))
                                                        <form
                                                            action="{{ route('project_inter_meeting.destroy', ['inter_meeting' => $outside_meeting->id]) }}"
                                                            method="POST" class="d-inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="button" class="btn btn-gk-delete deleteBtn"
                                                                value="刪除">
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="row justify-content-between bottom-information">
                                    <div class="dataTables_info" id="person-data-table_info" role="status"
                                        aria-live="polite">
                                    </div>
                                    {{-- $projects->links('backstage.pagination.index') --}}
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>

                    </div>
                </div>
                @endif

                <!-- </form> -->
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="module">
        import PhotoSwipeLightbox from "{{ asset('js/photoswipe-lightbox.esm.min.js') }}";

        function createLightbox(galleryId) {
            const lightbox = new PhotoSwipeLightbox({
                gallery: galleryId,
                children: 'a',
                pswpModule: () => import("{{ asset('js/photoswipe.esm.min.js') }}"),
                zoom: false,
                imageClickAction: 'close',
                tapAction: 'close',
                doubleTapAction: false,
            });
            lightbox.init();
            return lightbox;
        }

        const lightbox = createLightbox('#gallery--getting-started');
        const uploadlightbox = createLightbox('#uploadgallery--getting-started');
        // console.log(uploadlightbox);
        // const meetingbox = createLightbox('.meeting-file-group');
        // console.log(meetingbox);
        // lightbox.init();
        // uploadlightbox.init();
        // meetingbox.init();
    </script>
    <script src="{{ asset('plugins/selectize/selectize.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#type_id').selectize();
            $('#project_employees').selectize();
            $('#layout_employees').selectize();
            $('#front_employees').selectize();
            $('#back_employees').selectize();
            $('.preview-file').on('click', function() {
                var id = $(this).data('id');
                window.open('showPdf/' + id);
            });

            $('.preview-meeting-file').on('click', function() {
                let show_pic_url = "{{url('/storage')}}" + '/' + $(this).data('path')
                // console.log(show_pic_url);
                window.open(show_pic_url);
            });

            $('.project-file').on('click', function() {
                var id = $(this).data('id');
                window.open('downloadFile/' + id);
            });
            $('.meeting-preview-file').on('click', function() {
                var id = $(this).data('id');
                window.open('/admin/imanage/meeting/showPdf/' + id);
            });
            $('.meeting-project-file').on('click', function() {
                var id = $(this).data('id');
                // window.open('/admin/imanage/meeting/downloadMeetingFile/' + id);
                window.open('/admin/imanage/meeting/downloadFile/' + id);
            });
            
        });

        // window.onload = function() {
        //     var textArea = document.getElementById('textArea0').value;
        //     var convertedText = textArea.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" style="blue" target="_blank">$1</a>');

        //     convertedText = convertedText.replace(/\n/g, '<br>');

        //     var textDisplay = document.getElementById('textDisplay');
        //     textDisplay.innerHTML = convertedText;
        // };
    </script>
@endsection
