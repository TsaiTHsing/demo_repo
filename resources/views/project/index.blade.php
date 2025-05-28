@extends('backstage.main')
@section('meta')
@endsection
@section('style')
@endsection

@section('content')
    <link rel="stylesheet" href="{{ asset('plugins/project/project.css?v=2024') }}" />
    <?php
    use App\Models\ProjectSubFunc;
    use App\Models\DailyReports;
    ?>
    <div class="content">
        @include('backstage.notification.success')
        @include('backstage.notification.wrong')
        <div class="breadcrumb-wrapper breadcrumb-contacts">
            <div>
                <h1 class="">
                    專案發佈區
                </h1>
            </div>
            @if (Session::get('can.project.create'))
                <div>
                    <a href="{{ route('project.create') }}" class="btn btn-gk-add">新增</a>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header card-header-border-bottom d-flex justify-content-between">
                        <h2 class=""></h2>
                    </div>
                    <div class="card-body pt-0">

                        <form action="{{ route('project.index') }}" method="GET">
                            <div class="d-flex flex-wrap justify-content-between justify-content-md-start mt-3">
                                <div class="search-field">
                                    <input type="text" name="s_name" class="form-control" value="{{$s_name}}" placeholder="搜尋專案名稱">
                                </div>
                                <div class="search-btn-box d-flex">
                                    <button class="btn btn-gk-search mr-1">搜尋
                                    </button>
                                    <a href="{{ route('project.index') }}" class="btn btn-gk-clear">清除搜尋</a>
                                </div>
                            </div>
                        </form>

                        <div class="basic-data-table">
                            <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <table class="table nowrap dataTable no-footer" style="max-width: 1500px;" role="grid"
                                    aria-describedby="person-data-table_info">
                                    <thead>
                                        <tr role="row">
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style="width:50px;"><div>#</div></th>
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style=""><div>專案名稱</div></th>
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style=""><div>等級</div></th>
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style="width:63px;"><div>類型</div></th>
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style=""><div>進度</div></th>
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style=""><div>任務達成率</div></th>
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style="">
                                                <div>預計工時</div>
                                                <div>實際工時</div>
                                            </th>
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style="">
                                                <div>預估初稿</div>
                                                <div>實際初稿</div>
                                            </th>
                                            <th class="text-center text-nowrap h6 align-middle" rowspan="1" colspan="1"
                                                style="">
                                                <div>預估交件</div>
                                                <div>實際交件</div>
                                            </th>
                                            <th class="text-right text-nowrap" rowspan="1" colspan="1" style="">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projects as $p_index => $project)
                                            @php
                                                $tot_estimate_hours = ProjectSubFunc::whereHas('main_func', function (
                                                    $query,
                                                ) use ($project) {
                                                    $query->where('project_id', $project->id)->where('type', '3');
                                                })->sum('estimated_work_hour');

                                                $tot_worked_hours = DailyReports::whereHas('main_func', function (
                                                    $query,
                                                ) use ($project) {
                                                    $query->where('project_id', $project->id)->where('type', '3');
                                                })->sum('work_hour');

                                                $finish_worked_hours = ProjectSubFunc::where('is_completed', 1)
                                                    ->whereHas('main_func', function ($query) use ($project) {
                                                        $query->where('project_id', $project->id)->where('type', '3');
                                                    })
                                                    ->sum('estimated_work_hour');

                                                $completed_percent = 0;
                                                if ($tot_estimate_hours > 0) {
                                                    $completed_percent = number_format(
                                                        ($finish_worked_hours / $tot_estimate_hours) * 100,
                                                        2,
                                                    );
                                                    if ($completed_percent > 100) {
                                                        $completed_percent = 100;
                                                    } elseif ($completed_percent < 0) {
                                                        $completed_percent = 0;
                                                    }
                                                }
                                            @endphp
                                            <tr role="row" class="odd">
                                                <td class="text-center font14x">{{ $p_index + 1 }}</td>
                                                <td class="text-center font14x text-nowrap">{{ $project->title }}</td>
                                                <td class="text-center font14x text-nowrap">{{ array_key_exists($project->project_level, $levels) ? $levels[$project->project_level] : "" }}</td>
                                                <td class="text-center font14x">
                                                    @foreach (json_decode($project->category) as $key => $cat_key)
                                                        @if (array_key_exists($cat_key, $project_types))
                                                            <div class="mb-2 typeIcon_box typeIcon_{{ $cat_key }}">{{ $project_types[$cat_key] }}</div>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td class="text-center font14x text-nowrap">
                                                    {{ array_key_exists($project->project_status, $schedules) ? $schedules[$project->project_status] : '' }}
                                                </td>
                                                <td class="text-center font14x">{{ $completed_percent . '%' }}</td>
                                                <td class="text-center font14x">
                                                    <div class="">
                                                        <b>{{ $tot_estimate_hours }}</b>
                                                    </div>
                                                    <div class="">
                                                        {{ $tot_worked_hours }}
                                                    </div>
                                                </td>
                                                <td class="text-center font14x text-nowrap">
                                                    <div class="">
                                                        <b>{{ optional($project->schedule)->estimated_draft_delivery ?? '-' }}</b></div>
                                                    <div class="font-weight500">
                                                        {{ optional($project->schedule)->actual_draft_delivery ?? '-' }}</div>
                                                </td>
                                                <td class="text-center font14x text-nowrap">
                                                    <div class="">
                                                        <b>{{ optional($project->schedule)->estimated_test_delivery ?? '-' }}</b></div>
                                                    <div class="font-weight500">
                                                        {{ optional($project->schedule)->actual_test_delivery ?? '-' }}</div>
                                                </td>
                                                
                                                <td class="text-right text-nowrap">
                                                    @if (Session::get('can.project_info.read'))
                                                        <a href="{{ route('project.show', ['project' => $project->id]) }}"
                                                            class="btn btn-gk-edit">資料</a>
                                                    @endif
                                                    @if (Session::get('can.project_task.read'))
                                                        @if(in_array($project->id, $exception_project_id))
                                                            <a href="{{ route('project_task.index', ['project_id' => $project->id, 'tag_type' => '99']) }}" class="btn btn-gk-save">任務</a>
                                                        @else
                                                            <a href="{{ route('project_task.index', ['project_id' => $project->id, 'tag_type' => '1']) }}" class="btn btn-gk-save">任務</a>
                                                        @endif
                                                    @endif
                                                    
                                                    @if (Session::get('can.project.delete'))
                                                        <form
                                                            action="{{ route('project.destroy', ['project' => $project->id]) }}"
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
                                    {{ $projects->links('backstage.pagination.index') }}
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection
