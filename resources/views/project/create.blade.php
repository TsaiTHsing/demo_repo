@extends('backstage.main')

@section('style')
    <link rel="stylesheet" href="/css/summernote/summernote-bs4.css">
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

        .btn:focus {
            box-shadow: none;
        }

        .ImageEdit_content {
            display: flex;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .ImageEdit_content .img_box {
            width: calc(100%/4 - 1rem);
            height: 0;
            background-size: cover !important;
            padding-bottom: 20%;
            margin-right: 1rem;
            margin-bottom: 1rem;
            overflow: hidden;
            display: block;
            cursor: zoom-in;
        }

        .ImageEdit_content .img_box img {
            opacity: 0;
        }

        @media screen and (max-width:600px) {
            .ImageEdit_content .img_box {
                width: calc(100% / 2 - 1rem);
                padding-bottom: 45%;
            }
        }
        @media (max-width: 991.98px) {
            .form-group.mb-4.col-12 {
                padding-right: 0px !important;
            }
        }
    </style>

    <!-- 放大圖片 -->
    <link rel="stylesheet" href="{{ asset('css/photoswipe.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Uploadfile.css') }}">
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

        // const lightbox = createLightbox('#gallery--getting-started');
        const uploadlightbox = createLightbox('#uploadgallery--getting-started');
        lightbox.init();
        uploadlightbox.init();

        //讀取評價圖片，調整放大尺寸
        window.addEventListener('load', () => {
            //抓取圖片區塊
            const star_bar_photos = document.querySelector('.ImageEdit_content');
            if (star_bar_photos) {
                //抓取圖片div
                const img_box = document.querySelectorAll('.img_box');
                if (img_box.length > 0) {
                    img_box.forEach(item => {
                        //取得圖片尺寸
                        const Imgs = item.querySelector('img');
                        if (Imgs) {
                            //將原本的資料做串改
                            item.setAttribute('data-pswp-width', Imgs.naturalWidth);
                            item.setAttribute('data-pswp-height', Imgs.naturalHeight);
                        }

                    });
                }
            }
        });
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
        <form action="{{ route('project.store') }}" method="post" enctype="multipart/form-data">
            @csrf
        <div class="breadcrumb-wrapper breadcrumb-contacts d-flex justify-content-between">
            <div>
                <h1 class="">新增專案</h1>
            </div>
            <div>
                <button type="submit" class="btn btn-gk-save">儲存</button>
                <a href="{{ route('project.index') }}" class="btn btn-gk-return">取消</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                    <div class="card card-default">
                        <div class="card-header card-header-border-bottom d-flex justify-content-between">
                            <div>
                                <h2 class="">專案資訊</h2>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="row col-12">
                                    <div class="form-group mb-4 col-lg-6">
                                        <label>專案名稱<span class="required">*</span></label>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                                            class="form-control" placeholder="請輸入專案名稱" maxlength="255" required>
                                        @if ($errors->has('name'))
                                            <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                                {{ $errors->first('name') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-4 col-lg-6">
                                        <label>專案類型<span class="required">*</span></label>
                                        <select id="type_id" name="type_id[]" class="" multiple required>
                                            @foreach ($project_types as $type_id => $type)
                                                <option value="{{ $type_id }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('type_id'))
                                            <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                                {{ $errors->first('type_id') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row col-12">
                                    <div class="form-group mb-4 col-lg-4">
                                        <label>專案標準色<span class="required">*</span></label>
                                        <input type="text" name="color_code" value="{{ old('color_code') }}"
                                            class="form-control" placeholder="請輸入色碼" maxlength="20" required>
                                        @if ($errors->has('color_code'))
                                            <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                                {{ $errors->first('color_code') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-4 col-lg-4">
                                        <label>專案進度<span class="required">*</span></label>
                                        <select id="project_status" name="project_status" class="form-control">
                                            @foreach ($schedules as $schedule_id => $schedule)
                                                <option value="{{ $schedule_id }}">{{ $schedule }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('project_status'))
                                            <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                                {{ $errors->first('project_status') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-4 col-lg-4">
                                        <label>專案等級</label>
                                        <select id="project_level" name="project_level" class="form-control">
                                            <option value="">請選擇等級</option>
                                            @foreach ($levels as $level_id => $level)
                                                <option value="{{ $level_id }}">{{ $level }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('project_level'))
                                            <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                                {{ $errors->first('project_level') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group mb-4">
                                        <label>專案描述</label>
                                        <textarea class="form-control" placeholder="請描述專案" id="description" name="description" rows="10"></textarea>
                                        @if ($errors->has('description'))
                                            <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                                {{ $errors->first('description') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group mb-4 col-lg-3">
                                    <div>
                                        <label>專案</label>
                                    </div>
                                    <select id="project_employees" name="project_employees[]" class="" multiple>
                                        @foreach ($employees1 as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('project_employees'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('project_employees') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-3">
                                    <div>
                                        <label>版面</label>
                                    </div>
                                    <select id="layout_employees" name="layout_employees[]" class="" multiple>
                                        @foreach ($employees2 as $employee2)
                                            <option value="{{ $employee2->id }}">{{ $employee2->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('layout_employees'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('layout_employees') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-3">
                                    <div>
                                        <label>前端</label>
                                    </div>
                                    <select id="front_employees" name="front_employees[]" class="" multiple>
                                        @foreach ($employees3 as $employee3)
                                            <option value="{{ $employee3->id }}">{{ $employee3->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('front_employees'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('front_employees') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-3">
                                    <div>
                                        <label>後端</label>
                                    </div>
                                    <select id="back_employees" name="back_employees[]" class="" multiple>
                                        @foreach ($employees3 as $employee4)
                                            <option value="{{ $employee4->id }}">{{ $employee4->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('back_employees'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('back_employees') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="Edit_content mt-2">

                                <!-- 上傳圖片 -->
                                <div class="memberForumUploadfile">
                                    <label class="upload_string">批次上傳圖片</label>
                                    <div class="border rounded p-1">
                                        <label for="filenew_1_file" class="w-100" data-id="uploadbtn">
                                            <div class="d-flex">
                                                <div class="py-1 px-3 font14x bg-opacity-10 text-nowrap border">
                                                    選擇檔案</div>
                                                <div class="w-100 py-1 px-2 font14x"> 請選擇檔案 </div>
                                            </div>
                                            <input type="file" id="filenew_1_file" name="" class="d-none"
                                                data-id="filesinputs" accept="" onchange="showMultiPreview(this)"
                                                multiple>
                                        </label>
                                    </div>

                                    <div class="fileUploadContent mt-3" data-id="fileUploadContent"
                                        id="uploadgallery--getting-started">

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
                                <div class="form-group mb-4 col-lg-4">
                                    <label>預計交付初稿時間<span class="required"></span></label>
                                    <input type="date" id="e_d_d" name="e_d_d" value="{{ old('e_d_d') }}"
                                        class="form-control">
                                    @if ($errors->has('e_d_d'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('e_d_d') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-4">
                                    <label>實際交付初稿時間<span class="required"></span></label>
                                    <input type="date" id="a_d_d" name="a_d_d" value="{{ old('a_d_d') }}"
                                        class="form-control">
                                    @if ($errors->has('a_d_d'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('a_d_d') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-4">
                                    <label>定稿時間<span class="required"></span></label>
                                    <input type="date" id="f_v_d" name="f_v_d" value="{{ old('f_v_d') }}"
                                        class="form-control">
                                    @if ($errors->has('f_v_d'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('f_v_d') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="form-group mb-4 col-lg-3">
                                    <label>預計交測試檔時間</span></label>
                                    <input type="date" id="e_t_d" name="e_t_d" value="{{ old('e_t_d') }}"
                                        class="form-control">
                                    @if ($errors->has('e_t_d'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('e_t_d') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-3">
                                    <label>實際交付測試檔時間<span class="required"></span></label>
                                    <input type="date" id="a_t_d" name="a_t_d" value="{{ old('a_t_d') }}"
                                        class="form-control">
                                    @if ($errors->has('a_t_d'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('a_t_d') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-3">
                                    <label>客戶開始驗收時間</label>
                                    <input type="date" id="c_a_s" name="c_a_s" value="{{ old('c_a_s') }}"
                                        class="form-control">
                                    @if ($errors->has('c_a_s'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('c_a_s') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-3">
                                    <label>客戶結束驗收時間</label>
                                    <input type="date" id="c_a_e" name="c_a_e" value="{{ old('c_a_e') }}" class="form-control">
                                    @if ($errors->has('c_a_e'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('c_a_e') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="form-group mb-4 col-lg-4">
                                    <label>正式上線時間</label>
                                    <input type="date" id="o_l_d" name="o_l_d" value="{{ old('o_l_d') }}"
                                        class="form-control">
                                    @if ($errors->has('o_l_d'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('o_l_d') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-4">
                                    <label>保固開始時間</label>
                                    <input type="date" id="w_s_d" name="w_s_d" value="{{ old('w_s_d') }}"
                                        class="form-control">
                                    @if ($errors->has('w_s_d'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('w_s_d') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-4 col-lg-4">
                                    <label>保固結束時間</label>
                                    <input type="date" id="w_e_d" name="w_e_d" value="{{ old('w_e_d') }}"
                                        class="form-control">
                                    @if ($errors->has('w_e_d'))
                                        <div class="d-block is-invalid invalid-feedback position-absolute mt-0">
                                            {{ $errors->first('w_e_d') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('plugins/selectize/selectize.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#type_id').selectize();
            $('#project_employees').selectize();
            $('#layout_employees').selectize();
            $('#front_employees').selectize();
            $('#back_employees').selectize();
        });
    </script>

    <script>
        var removeHouseImages = []; // 用來記錄要刪除既有的圖片
        var removeADImages = []; // 用來記錄要刪除既有的圖片
        var ratio = 0.75 // 預設的縮小比率
        if (window.innerWidth < 600 || window.innerHeight < 600) {
            ratio = 0.8; // 如果螢幕的寬度或高度小於600，則將縮小比率設為0.8
        }

        var maxWidth = window.innerWidth * ratio; // 瀏覽器寬度的70%或80%
        var maxHeight = window.innerHeight * ratio; // 瀏覽器高度的70%或80%

        function createImagePreview2($previewContainer, file2) {

            // 判斷副檔名 決定要放哪個icon
            let parts = (file2['name']).split("."); // 使用 "." 分割字符串
            let f_extension = parts.pop();
            let icon_url = "{{ asset('images/icon/media.jpg') }}";

            if (f_extension == "xlsx" || f_extension == "xls") {
                icon_url = "{{ asset('images/icon/excel.jpg') }}";
            } else if (f_extension == "pdf") {
                icon_url = "{{ asset('images/icon/pdf.jpg') }}";
            } else if (f_extension == "docx" || f_extension == "doc") {
                icon_url = "{{ asset('images/icon/word.jpg') }}";
            } else if (f_extension == "txt") {
                icon_url = "{{ asset('images/icon/text.jpg') }}";
            }

            var reader = new FileReader(); // 創建一個 FileReader
            // 當檔案讀取完成時
            reader.onload = function(e) {
                var $div2 = $('<div>');
                $div2.addClass('card');
                var $input2 = $('<input type="file" class="d-none" data-id="filesinputs" accept="" multiple>');
                $input2.attr('name', 'file[]');
                var dataTransfer2 = new DataTransfer();
                dataTransfer2.items.add(file2);
                $input2[0].files = dataTransfer2.files;

                var $btnGround = $('<div class="btn_ground">');
                $btnGround.html(
                    '<button type="button" class="delete-image-btn new"><img src="{{ asset('images/icon/close.png') }}" alt="移除"></button>'
                );

                var $imgBox = $('<div class="img_box border">');

                $imgBox.css({
                    'background': 'url(' + icon_url + ') no-repeat',
                    'background-size': 'contain',
                    'background-position': 'center'
                });

                var $p = $('<p></p>');
                // $p.attr('href', icon_url);
                // $p.attr('data-pswp-width', '500px');
                // $p.attr('data-pswp-height', '500px');
                $p.html(
                    '<div class="Uploadfile_status"><span class="badge rounded-pill bg-secondary">尚未上傳</span></div>'
                );

                $imgBox.append($p);
                $div2.append($input2);
                $div2.append($btnGround);
                $div2.append($imgBox);

                $previewContainer.append($div2);
            };

            reader.readAsDataURL(file2); // 讀取檔案
        }

        function createImagePreview(data, $previewContainer) {
            var file = data.file;
            var imageRatio = data.width / data.height; // 圖片的寬高比
            // 如果圖片的寬度大於最大寬度，則調整寬度和高度
            if (data.width > maxWidth) {
                data.width = maxWidth;
                data.height = data.width / imageRatio;
            }

            // 如果調整後的高度仍然大於最大高度，則再次調整寬度和高度
            if (data.height > maxHeight) {
                data.height = maxHeight;
                data.width = data.height * imageRatio;
            }

            var reader = new FileReader(); // 創建一個 FileReader
            // 當檔案讀取完成時
            reader.onload = function(e) {
                var $div = $('<div>');
                $div.addClass('card');
                var $input = $('<input type="file" class="d-none" data-id="filesinputs" accept="image/*" multiple>');
                $input.attr('name', 'file[]');
                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                $input[0].files = dataTransfer.files;

                var $btnGround = $('<div class="btn_ground">');
                $btnGround.html(
                    '<button type="button" class="delete-image-btn new"><img src="{{ asset('images/icon/close.png') }}" alt="移除"></button>'
                );

                var $imgBox = $('<div class="img_box border">');
                $imgBox.css({
                    'background': 'url(' + e.target.result + ') no-repeat',
                    'background-size': 'contain',
                    'background-position': 'center'
                });

                var $a = $('<a class="pre-lightbox" data-pswp-width="500" data-pswp-height="500" target="_blank">');
                $a.attr('href', e.target.result);
                $a.attr('data-pswp-width', data.width);
                $a.attr('data-pswp-height', data.height);
                $a.html(
                    '<div class="Uploadfile_status"><span class="badge rounded-pill bg-secondary">尚未上傳</span></div>'
                );

                $imgBox.append($a);
                $div.append($input);
                $div.append($btnGround);
                $div.append($imgBox);

                $previewContainer.append($div);
            };

            reader.readAsDataURL(file); // 讀取檔案
        }

        function getImageSize(file) {
            return new Promise(function(resolve, reject) {
                var img = new Image();
                img.onload = function() {
                    resolve({
                        file: file,
                        width: this.width,
                        height: this.height
                    });
                };
                img.onerror = function() {
                    reject(new Error('Could not load image'));
                };
                img.src = URL.createObjectURL(file);
            });
        }

        function showMultiPreview(input) {
            if (!window.FileReader) {
                alert('你的瀏覽器不支援 File API');
                return;
            }
            var $previewContainer = $('#uploadgallery--getting-started');
            var promises = Array.from(input.files).map(function(file, i) {
                // console.log(file['name'], file['type'], i);

                if (file.type.startsWith('image/')) {
                    console.log('檔案是圖片檔');
                    return getImageSize(file)
                        .then(function(data) {
                            console.log(data);
                            createImagePreview(data, $previewContainer);
                        })
                        .catch(function() {
                            return false;
                        });
                } else {
                    console.log('檔案不是圖片檔');
                    createImagePreview2($previewContainer, file);
                }
            });

            Promise.allSettled(promises).finally(function() {
                return;
            });

            // 清除此input的值
            input.value = '';
        }

        $(document).ready(function() {
            $('#uploadgallery--getting-started').on('click', '.delete-image-btn.new', function(e) {
                // 找到父元素，並remove
                $(this).parent().parent().remove();
            })
        });
    </script>

    <script src="/js/summernote/summernote-bs4.min.js"></script>
    <script src="/js/summernote/summernote-zh-TW.min.js"></script>
    <script>
        $('#description').summernote({
            lang: 'zh-TW',
            tabsize: 2,
            height: 500,
            toolbar: [
                // ['style', ['style']],
                // ['font', ['bold', 'italic', 'underline', 'clear']],
                // ['fontsize', ['fontsize']],
                // ['color', ['color']],
                // ['para', ['ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']], // link , picture , video
                // ['view', ['fullscreen', 'codeview']],
                // ['help', ['help']]
            ],
        });
    </script>
@endsection
