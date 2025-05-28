<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- FAVICON -->
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="bookmark" href="/favicon.ico" />

        <!-- GOOGLE FONTS -->
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.4.95/css/materialdesignicons.min.css" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500%7CPoppins:400,500,600,700%7CRoboto:400,500" rel="stylesheet"/>

        <!-- NPROGRESS CSS STYLE -->
        {{-- <link href="{{ asset('plugins/nprogress/nprogress.css') }}" rel="stylesheet" /> --}}
        <!-- SLEEK CSS -->
        <link id="sleek-css" rel="stylesheet" href="{{ asset('plugins/sleek/sleek.css?v=2024') }}" />
        <!-- 添加預設值 -->
        <link rel="stylesheet" href="{{ asset('plugins/project/default.css?v=2024') }}" />

        <title>專案管理系統</title>
        <style>

            .fnt_12 {
                font-size: 12px !important;
            }

            .tb_fnt, .tb_fnt p, .tb_fnt a, .tb_fnt span, .tb_fnt font, .tb_fnt table{
                font-size: 13px !important;
                font-weight: bold !important;
                color: #8a909d !important;
            }

            .tb_fnt2, .tb_fnt2 p, .tb_fnt2 a, .tb_fnt2 span, .tb_fnt2 font, .tb_fnt2 table{
                font-size: 14px !important;
                font-weight: bold !important;
                color: #8a909d !important;
            }

            .upload_string {
                color: #1b223c;
                font-size: 0.98rem;
            }
            
            /* 行事曆 */
            .navbar .navbar-right .navbar-nav .calendar-link {
                text-align: center;
                cursor: pointer;
                border-left: 1px solid #e5e9f2;
                border-right: 1px solid #e5e9f2;
                min-width: 60px;
            }

            .navbar .navbar-right .navbar-nav .calendar-link > a {
                line-height: 75px;
            }

            @media (min-width: 1200px) {
                .navbar .navbar-right .navbar-nav .calendar-link {
                    min-width: 70px;
                }
            }

            /* calendar-content */
            @media (max-width: 576px) {
                .calendar-content .breadcrumb-wrapper {
                    margin-bottom: 1rem;
                    padding-left: 0.94rem;
                    padding-right: 0.94rem;
                }

                .calendar-content {
                    padding: 0.5rem;
                    padding-left: 0;
                    padding-right: 0;
                }
            }

            .calendar-card {
                padding-top: 0.8rem;
                padding-left: 0;
                padding-right: 0;
                padding-bottom: 1.5rem;
            }

            @media (max-width: 576px) {
                .calendar-card {
                    margin-bottom: 10px;
                }

                .calendar-card .card-body {
                    padding-top: 0 !important;
                }
            }

            .calendar-card .card-body {
                padding: 0 0 10px 0;
            }

            @media (min-width: 1400px) {
                .calendar-card .card-body {
                    padding: 0.8rem 0 1.5rem 0;
                }
            }

        </style>
        @yield('style')
        {{-- main css --}}
        <link rel="stylesheet" href="/css/backstage.css?{{ date('YmdHis') }}">
        <link rel="stylesheet" href="/css/custom_btn.css?{{ date('YmdHis') }}">

    </head>

    <body class="header-fixed sidebar-fixed sidebar-dark header-light" id="body">

        {{-- <script>
            NProgress.configure({
                showSpinner: false
            });
            NProgress.start();
        </script> --}}

        <div class="wrapper">
            @include('backstage.toolbar')
            <div class="page-wrapper">
                <!-- Header -->
                <header class="main-header noPrint" id="header">
                    <nav class="navbar navbar-static-top navbar-expand-lg">
                        <!-- Sidebar toggle button -->
                        <button id="sidebar-toggler" class="sidebar-toggle">
                            <span class="sr-only">Toggle navigation</span>
                        </button>
                        <!-- search form -->
                        <div class="search-form d-none d-lg-inline-block">
                        </div>

                        <div class="navbar-right ">
                            <ul class="nav navbar-nav">
                                {{-- @if(auth()->user()->level >= 9 || auth()->user()->username=='kaya') --}}
                                <li class="calendar-link">
                                    <a href="{{ route('calendar.index') }}">
                                        <i><img src="/images/icon/calendar@3x.png" style="width: 25px; filter: invert(1);"></i>
                                    </a>
                                </li>
                                
                                {{-- @endif --}}
                                <!-- User Account -->
                                <li class="dropdown user-menu">
                                    <button href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                                        <span class="d-none d-lg-inline-block">
                                            {{ auth()->user()->name }}
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <!-- User image -->
                                        <li class="dropdown-footer">
                                            <a href="{{ route('password.reset') }}" class="d-flex align-items-center">
                                                <img src="/images/icon/lock.png" style="width:18px;" class="mr-3"
                                                    alt="">
                                                <span style="height:24px;font-size:16px;line-height:26px;">更改密碼</span>
                                            </a>
                                        </li>
                                        <li class="dropdown-footer">
                                            <a href="{{ route('backstage.logout') }}" class="d-flex align-items-center">
                                                <img src="/images/icon/logout.png" style="width:18px;" class="mr-3"
                                                    alt="">
                                                <span style="height:24px;font-size:16px;line-height:26px;">登出</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </header>

                <div class="content-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>

        {{-- sweetalert --}}
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.33/sweetalert2.min.css" />

        <link rel="stylesheet" href="{{ asset('plugins/jquery/jquery-ui.css') }}">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.33/sweetalert2.all.min.js"></script>

        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('plugins/slimscrollbar/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ asset('plugins/jekyll-search.min.js') }}"></script>
        <script src="{{ asset('plugins/nprogress/nprogress.js') }}"></script>
        <script src="{{ asset('plugins/sleek/sleek.bundle.min.js') }}"></script>
        {{-- datepicker --}}
        <script>
            const dd = (data) => {
                console.log(data);
            }

            const showAlert = (icon, message) => {
                Swal.fire({
                    title: message,
                    icon: icon,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: '確定'
                });
            }

            const showLoading = (message) => {
                Swal.fire({
                    title: message,
                    allowOutsideClick: false,
                    // onBeforeOpen: () => {
                    //     Swal.showLoading()
                    // },
                    didOpen: () => {
                        Swal.showLoading()
                    },
                });
            }

            $("a.sidenav-item-link.main-title").on('click', function() {
                $("a.main-title").map((index, data) => {
                    let img = $(data).data('img');
                    let url = `/images/toolbar/${img}.png`;
                    $(data).find('img').attr('src', url);
                });

                let img = $(this).data('img');

                if (img) {
                    if ($(this).parent().hasClass('expand')) {
                        var url = `/images/toolbar/${img}.png`;
                    } else {
                        var url = `/images/toolbar/hover/${img}.png`;
                    }

                    $(this).find('img').attr('src', url);
                }
            });

            $(document).ready(function() {
                if ($("a.links").length > 0) {
                    var target_top = $("a.links").offset().top;
                } else {
                    var target_top = 0;
                }

                var div_h = $("div.slimScrollDiv").height();
                var scrollbar_h = $("div.slimScrollBar")[0].offsetHeight;
                if (target_top - div_h > 0) {
                    $("div.sidebar-scrollbar").scrollTop(target_top - div_h + 300);
                    $("div.slimScrollBar").css('top', div_h - scrollbar_h);
                }

                // 防止連續儲存
                $('form').on('submit', function()
                {
                    console.log('submit form');
                    // $('button[type="submit"]').prop('disabled', true);
                    $(this).find('button[type="submit"]').prop('disabled', true);

                    var form_this_5sec = this;
                    setTimeout(function() {
                        // console.log('disabled false', $(form_this_5sec).find('button[type="submit"]'));
                        $(form_this_5sec).find('button[type="submit"]').prop('disabled', false);
                        
                    }, 5000); 
                });
            });

            $("input.deleteBtn").on("click", function() {
                Swal.fire({
                    title: '您確定要刪除嗎？',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: '取消',
                    confirmButtonText: '確定'
                }).then((result) => {
                    dd(result);
                    if (result.value == true) {
                        $(this).parent().submit();
                    } else {
                        return false;
                    }
                });
            });
        </script>
        <script>
            $(function() {
                // 啟用可輸入分頁頁碼功能
                $('#page-input').on('change', function() {
                    var page = Number($(this).val());
                    var paginator = $('#page-manual');
                    var href = paginator.attr('href');
                    var maxPage = paginator.data('counts');
                    if (page < 1) {
                        page = 1;
                    } else if (page > maxPage) {
                        page = maxPage;
                    }

                    if (href.includes("page=1")) {
                        href = href.replace("page=1", "page=" + page);
                        paginator.attr('href', href);
                        console.log(paginator.attr('href'));
                        location.replace(paginator.attr('href'));
                    }
                })
            })
        </script>
        <script src="/js/switch_btn.js?{{ date('YmdHis') }}"></script>
        @yield('script')
    </body>

</html>
