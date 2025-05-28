<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectUser;
use App\Models\User;
use App\Utility\ImageHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Library\func;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // dd(func::projectList());
        Session::forget('previousUrl');
        
        $s_name = $request->input('s_name', '');

        $projectQuery = Project::query();

        // 如果有搜尋
        if($s_name){ 
            $projectQuery->where('title', 'LIKE', '%' . $s_name . '%');
        }

        $projects = $projectQuery->with('schedule')->orderBy('project_status', 'asc')->paginate(25);

        $project_types = config('const.project_type');

        $schedules = config('const.project_schedule');

        $exception_project_id = [30, 32, 40]; 

        $levels = config('const.project_level');

        return view('backstage.project.index', compact('projects', 'project_types', 'schedules', 's_name', 'exception_project_id', 'levels'));
    }

    public function create()
    {
        $project_types = config('const.project_type');

        $schedules = config('const.project_schedule');

        $levels = config('const.project_level');

        // $dept1 = Department::where('name', '專案部')->first();
        $dept1 = Department::where('id', 3)->first();
        $employees1 = optional($dept1)->users; // 專案 人員

        // $dept2 = Department::where('name', '設計部')->first();
        $dept2 = Department::where('id', 5)->first();
        $employees2 = optional($dept2)->users; // 版面 人員

        // $dept3 = Department::where('name', '工程部')->first();
        $dept3 = Department::where('id', 4)->first();
        $employees3 = optional($dept3)->users; // 前端、後端 人員

        // $employees4 = User::whereNotIn('id', [1, 2, 3])->orderBy('id', 'asc')->get();
        $employees4 = func::userList();

        return view('backstage.project.create', compact('project_types', 'schedules', 'employees1', 'employees2', 'employees3', 'employees4', 'levels'));
    }

    public function store(Request $request)
    {
        $attributes = [
            'name' => '專案名稱',
            'type_id' => '專案類型',
            'color_code' => '專案標準色',
            'description' => '專案描述',
            'project_status' => '專案進度',

        ];
        $validatedData = $request->validate([
            'name' => 'required',
            'type_id' => 'required',
            'color_code' => 'required',
            'description' => 'required',
            'project_status' => 'required',

        ], [], $attributes);

        // dd($request);

        try {
            DB::beginTransaction();

            $c_json = json_encode($request->type_id);

            $new_project = Project::create([
                'title' => $request->name,
                'category' => $c_json,
                'color_code' => $request->color_code,
                'description' => $request->description,
                'project_status' => $request->project_status,
                'project_level' => $request->project_level,
            ]);

            if (!$new_project) {
                Log::info('新增專案失敗');
                return redirect()->back()->with('wrong', '新增失敗');
            }
            $new_project->schedule()->create([
                'estimated_draft_delivery' => $request->e_d_d,
                'actual_draft_delivery' => $request->a_d_d,
                'final_version_date' => $request->f_v_d,
                'estimated_test_delivery' => $request->e_t_d,
                'actual_test_delivery' => $request->a_t_d,
                'client_acceptance_start' => $request->c_a_s,
                'client_acceptance_end' => $request->c_a_e,
                'official_launch_date' => $request->o_l_d,
                'warranty_start_date' => $request->w_s_d,
                'warranty_expiration_date' => $request->w_e_d,
            ]);

            $employeesArray = array();
            // 工程人員
            $roles = [
                'project_employees' => 1,
                'layout_employees' => 2,
                'front_employees' => 3,
                'back_employees' => 4,
            ];

            foreach ($roles as $role => $job_role) {
                if ($request->has($role)) {
                    foreach ($request->$role as $employee) {
                        array_push($employeesArray, [
                            'user_id' => $employee,
                            'job_role' => $job_role,
                        ]);
                    }
                }
            }

            $new_project->project_users()->createMany($employeesArray);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return redirect()->back()->with('wrong', '新增失敗');
        }

        try {
            DB::beginTransaction();
            $files = $request->file('file');
            if ($files) {
                foreach ($files as $index => $file) {

                    $path = ImageHandler::store(
                        $file,
                        'project/' . $new_project->id,
                        date('His') . $index . (string) rand(10, 99),
                        $file->extension()
                    );

                    $new_project->files()->create([
                        'path' => $path,
                        'fname' => $file->getClientOriginalName(),
                        'format' => $file->extension(),
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return redirect()->back()->with('wrong', '新增檔案失敗');
        }
        return redirect()->route('project.show', ['project' => $new_project->id])->with('success', '新增成功');
    }

    public function edit($project)
    {
        $project = Project::find($project);

        $project_types = config('const.project_type');

        $schedules = config('const.project_schedule');

        $levels = config('const.project_level');

        // $dept1 = Department::where('name', '專案部')->first();
        $dept1 = Department::where('id', 3)->first();
        $employees1 = optional($dept1)->users; // 專案 人員
        
        // $dept2 = Department::where('name', '設計部')->first();
        $dept2 = Department::where('id', 5)->first();
        $employees2 = optional($dept2)->users; // 版面 人員

        // $dept3 = Department::where('name', '工程部')->first();
        $dept3 = Department::where('id', 4)->first();
        $employees3 = optional($dept3)->users; // 前端、後端 人員

        // $employees4 = User::whereNotIn('id', [1, 2, 3])->orderBy('id', 'asc')->get();
        $employees4 = func::userList();

        $p_employees = array(); // 專案
        $l_employees = array(); // 版面
        $f_employees = array(); // 前端
        $b_employees = array(); // 後端

        foreach (ProjectUser::where('project_id', $project->id)->get() as $p_u) {
            if ($p_u->job_role == 1) {
                array_push($p_employees, $p_u->user_id);
            } elseif ($p_u->job_role == 2) {
                array_push($l_employees, $p_u->user_id);
            } elseif ($p_u->job_role == 3) {
                array_push($f_employees, $p_u->user_id);
            } elseif ($p_u->job_role == 4) {
                array_push($b_employees, $p_u->user_id);
            }
        }

        $cat_json = json_decode($project->category);

        return view('backstage.project.edit', compact('project', 'project_types', 'schedules', 'levels', 'p_employees', 'l_employees', 'f_employees', 'b_employees', 'cat_json', 'employees1', 'employees2', 'employees3', 'employees4'));
    }

    public function update(Request $request, $project)
    {
        $project = Project::find($project);

        if (!$project) {
            return redirect()->route('project.index')->with('wrong', '編輯失敗(資料不存在)');
        }

        $attributes = [
            'name' => '專案名稱',
            'type_id' => '專案類型',
            'color_code' => '專案標準色',
            'description' => '專案描述',
            'project_status' => '專案進度',
        ];

        $validatedData = $request->validate([
            'name' => 'required',
            'type_id' => 'required',
            'color_code' => 'required',
            'description' => 'required',
            'project_status' => 'required',

        ], [], $attributes);

        $c_json = json_encode($request->type_id);

        try {
            DB::beginTransaction();

            $project->update([
                'title' => $request->name,
                'category' => $c_json,
                'color_code' => $request->color_code,
                'description' => $request->description,
                'project_status' => $request->project_status,
                'project_level' => $request->project_level,
            ]);

            $project->schedule()->update([
                'estimated_draft_delivery' => $request->e_d_d,
                'actual_draft_delivery' => $request->a_d_d,
                'final_version_date' => $request->f_v_d,
                'estimated_test_delivery' => $request->e_t_d,
                'actual_test_delivery' => $request->a_t_d,
                'client_acceptance_start' => $request->c_a_s,
                'client_acceptance_end' => $request->c_a_e,
                'official_launch_date' => $request->o_l_d,
                'warranty_start_date' => $request->w_s_d,
                'warranty_expiration_date' => $request->w_e_d,
            ]);

            // 工程人員
            $project->project_users()->delete(); // 先刪除原本的

            $employeesArray = array();
            $roles = [
                'project_employees' => 1,
                'layout_employees' => 2,
                'front_employees' => 3,
                'back_employees' => 4,
            ];

            foreach ($roles as $role => $job_role) {
                if ($request->has($role)) {
                    foreach ($request->$role as $employee) {
                        array_push($employeesArray, [
                            'user_id' => $employee,
                            'job_role' => $job_role,
                        ]);
                    }
                }
            }

            $project->project_users()->createMany($employeesArray);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return redirect()->back()->with('wrong', '編輯失敗');
        }

        try {
            // 1. 刪除舊檔table
            DB::beginTransaction();
            $willRemovePath = [];
            if ($request->filled('remove_files')) {

                $queryWillRemoveImgs = $project->files()->whereIn('id', $request->remove_files);
                // 取得要刪除的圖片路徑
                $willRemovePath = (clone $queryWillRemoveImgs)->get()->pluck('path')->toArray();
                // 真的執行刪除
                $queryWillRemoveImgs->delete();
            }

            // 2. 新增檔案
            if ($request->hasFile('file')) {
                $files = $request->file('file');

                foreach ($files as $index => $file) {
                    $path = ImageHandler::store(
                        $file,
                        'project/' . $project->id,
                        date('His') . $index . (string) rand(10, 99),
                        $file->extension()
                    );

                    $project->files()->create([
                        'path' => $path,
                        'fname' => $file->getClientOriginalName(),
                        'format' => $file->extension(),
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return redirect()->back()->with('wrong', '編輯檔案失敗');
        }

        // 刪除實體舊檔
        if (count($willRemovePath) > 0) {
            foreach ($willRemovePath as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        return redirect()->route('project.show', ['project' => $project])->with('success', '編輯成功');
        // return redirect()->back()->with('success', '編輯成功');
    }

    public function destroy(string $id)
    {

        $project = Project::findOrFail($id);

        if(!$project){
            return redirect()->back()->with('wrong', '刪除失敗(專案不存在)');
        }

        try {
            DB::beginTransaction();


            $diaries = $project->diaries()->get(); // 獲取這個 project 下的所有 diaries
            foreach ($diaries as $diary) { $diary->detail()->delete(); } // 刪除這個 diary 下的所有 detail
            $project->diaries()->delete();

            // 刪除次功能
            $subFuncsToDelete = $project->main_funcs()->with('sub_funcs')->get()->pluck('sub_funcs')->flatten();
            foreach ($subFuncsToDelete as $subFunc) { 
                $subFunc->sub_func_description()->delete();
                $subFunc->delete(); 
            }

            $project->main_funcs()->delete();

            // 刪除溝通參與人員
            $project->inside_meetings()->each(function ($inside_meeting) {
                $inside_meeting->files()->delete();
                $inside_meeting->participants()->delete();
            });
            $project->inside_meetings()->delete();
            // 刪除會議參與人員
            $project->outside_meetings()->each(function ($outside_meeting) {
                $outside_meeting->files()->delete();
                $outside_meeting->participants()->delete();
            });
            $project->outside_meetings()->delete();


            $project->project_users()->delete();
            
            $project->files()->delete();

            $project->schedule()->delete();

            $project->delete();

            DB::commit();
            return redirect()->back()->with('success', '刪除成功');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('專案刪除失敗 : ');
            Log::info($e->getMessage());

            return redirect()->back()->with('wrong', '刪除失敗');
        }
    }

    public function show(Project $project)
    {
        $project_types = config('const.project_type');

        $schedules = config('const.project_schedule');

        $levels = config('const.project_level');

        // $dept1 = Department::where('name', '專案部')->first();
        // $employees1 = $dept1->users; // 專案 人員

        // $dept2 = Department::where('name', '設計部')->first();
        // $employees2 = $dept2->users; // 版面 人員

        // $dept3 = Department::where('name', '工程部')->first();
        // $employees3 = $dept3->users; // 前端、後端 人員

        // $employees4 = User::all(); // 所有人員
        $employees4 = func::userList();

        $p_employees = array(); // 專案
        $l_employees = array(); // 版面
        $f_employees = array(); // 前端
        $b_employees = array(); // 後端

        foreach (ProjectUser::where('project_id', $project->id)->get() as $p_u) {
            if ($p_u->job_role == 1) {
                array_push($p_employees, $p_u->user_id);
            } elseif ($p_u->job_role == 2) {
                array_push($l_employees, $p_u->user_id);
            } elseif ($p_u->job_role == 3) {
                array_push($f_employees, $p_u->user_id);
            } elseif ($p_u->job_role == 4) {
                array_push($b_employees, $p_u->user_id);
            }
        }

        $cat_json = json_decode($project->category);
        // dd($p_employees, $l_employees, $f_employees, $b_employees);

        if (!$project) {
            return redirect()->route('project.index')->with('wrong', '找不到資料');
        }

        // if (env('APP_ENV') != 'local') { 
            $exception_project_id = [30, 32, 40]; 
        // }

        return view('backstage.project.show', compact('project', 'project_types', 'schedules', 'levels', 'employees4', 'p_employees', 'l_employees', 'f_employees', 'b_employees', 'cat_json', 'exception_project_id'));
    }

    public function showPdf($id)
    {

        $fileResult = ProjectFile::find($id);
        if (!$fileResult || !Storage::disk('public')->exists($fileResult->path)) {
            abort(404);
        }

        $project = $fileResult->project;

        $hasPermission = self::hasPermission(Auth::user(), $project);
        // if (!$hasPermission) {
        //     abort(403);
        // }

        $file = Storage::disk('public')->get($fileResult->path);
        $type = Storage::mimeType($fileResult->path);
        return response($file, 200)->header('Content-Type', $type);
    }

    public function downloadFile($id)
    {
        $fileResult = ProjectFile::find($id);
        if (!$fileResult || !Storage::disk('public')->exists($fileResult->path)) {
            abort(404);
        }
        $project = $fileResult->project;
        $hasPermission = self::hasPermission(Auth::user(), $project);
        // if (!$hasPermission) {
        //     abort(403);
        // }
        return response()->download(storage_path('app/public/' . $fileResult->path), $fileResult->fname);
    }

    public static function hasPermission($user, $project)
    {
        if ($user->level == 9) {
            return true;
        }

        // 判斷會有問題
        // if ($project->project_users->contains($user)) {
        //     return true;
        // }
        $project_users = $project->project_users->pluck('user_id')->toArray();
        if(in_array($user->id, $project_users)){
            return true;
        }
    }

    
    // 取得project
    public function get_project(Request $request)
    {
        $p_id = $request->get("p_id");

        $project = Project::find($p_id);

        if ($project) {

            return response()->json([
                'status' => 'success',
                'project' => $project,

            ]);
        } else {
            return response()->json([
                'status' => 'data_not_found',

            ]);
        }
    }
}
