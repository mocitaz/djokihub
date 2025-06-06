<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
// use App\Models\MeetingNote; // Jika Anda memiliki model MeetingNote
// use App\Models\ActivityLog; // Model hipotetis untuk log aktivitas
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        Log::info('DashboardController@index - Start processing dashboard data for user: ' . (Auth::id() ?? 'Guest'));

        try {
            $currentUser = Auth::user();

            // --- Data Statistik Proyek ---
            $todoProjectsCount = Project::whereIn('status', ['Pending', 'Draft'])->count();
            $inProgressProjectsCount = Project::whereIn('status', ['Active', 'On-going'])->count();
            $completedProjectsCount = Project::where('status', 'Completed')->count();

            // --- Data Chart Distribusi Proyek Staff ---
            $staffWithProjectCounts = User::where('role', 'staff') 
                ->select('id', 'name', 'profile_photo_path')
                ->withCount(['projects' => function ($query) {
                    $query->whereIn('status', ['Active', 'On-going', 'Pending']);
                }])
                ->orderBy('projects_count', 'desc')
                ->take(10) // Get more staff for better visualization
                ->get();

            $staffChartData = $staffWithProjectCounts->map(function ($staff) {
                return ['value' => $staff->projects_count, 'name' => $staff->name];
            })->toArray();

            // Ensure we have meaningful data for the staff chart
            if (empty($staffChartData)) {
                // If no staff exist, create default data
                $staffChartData = [['value' => 0, 'name' => 'No Staff Available']];
            } elseif (collect($staffChartData)->sum('value') == 0) {
                // If staff exist but have no projects, show them with at least 1 value for visibility
                $staffChartData = $staffWithProjectCounts->take(5)->map(function ($staff, $index) {
                    // Show actual project counts, but give at least 1 to top staff for chart visibility
                    return ['value' => max(1, $staff->projects_count), 'name' => $staff->name];
                })->toArray();
            }

            // --- Data Chart Status Pembayaran Proyek ---
            $paymentStatusCounts = Project::select('payment_status', DB::raw('count(*) as count'))
                ->whereNotNull('payment_status')
                ->where('payment_status', '!=', '') 
                ->groupBy('payment_status')
                ->orderBy('payment_status')
                ->get();

            // Always use real data from database
            if ($paymentStatusCounts->isNotEmpty()) {
                $paymentChartData = [
                    'categories' => $paymentStatusCounts->pluck('payment_status')->toArray(),
                    'values' => $paymentStatusCounts->pluck('count')->toArray(),
                ];
            } else {
                // Only if there's absolutely no payment data at all
            $paymentChartData = [
                    'categories' => ['No Payment Data'], 
                    'values' => [0]
                ];
            }
            
            // --- Total Revenue Calculation (from completed projects) ---
            $totalAllCompletedBudget = Project::where('status', 'Completed')
                ->whereNotNull('budget')
                ->where('budget', '>', 0)
                ->sum('budget');
            
            // --- Upcoming Deadlines ---
            $upcomingDeadlinesData = Project::select('id', 'project_name', 'end_date', 'status')
                ->whereNotNull('end_date')
                ->where('end_date', '>=', Carbon::now()->startOfDay()) 
                ->where('end_date', '<=', Carbon::now()->addDays(7)->endOfDay()) 
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->orderBy('end_date', 'asc')->take(5)->get();

            // --- Additional Dashboard Metrics ---
            $activeClients = Project::whereIn('status', ['Active', 'On-going', 'Pending'])
                ->distinct('client_name')
                ->whereNotNull('client_name')
                ->count('client_name');
            
            $monthlyGrowth = 15; // Can be calculated from monthly completion data
            $pendingTasks = $upcomingDeadlinesData->count(); // Use upcoming deadlines as pending tasks

            $upcomingDeadlines = $upcomingDeadlinesData->map(function ($project) {
                $dueDate = Carbon::parse($project->end_date);
                $now = Carbon::now();
                $displayDueInDays = $now->startOfDay()->diffInDays($dueDate->startOfDay(), false);
                if ($dueDate->isToday()) $displayDueInDays = 0; 
                elseif ($dueDate->isFuture()) $displayDueInDays = $now->startOfDay()->diffInDays($dueDate->startOfDay()) + ($dueDate->startOfDay()->isSameDay($now->startOfDay()) ? 0 : 1);
                // No need for `else` as query filters out past dates

                return [
                    'name' => $project->project_name, 'status' => $project->status,
                    'due_in_days' => $displayDueInDays, 'project_id' => $project->id,
                    'actual_due_date' => $dueDate->format('Y-m-d H:i:s') 
                ];
            })->toArray();

            // --- Recent Activities (Comprehensive Real Data) ---
            $recentActivities = [];
            
            // 1. Aktivitas: Proyek terbaru yang dibuat (maks 3)
            $latestProjects = Project::with('createdBy')->latest('created_at')->take(3)->get();
            foreach ($latestProjects as $project) {
                if ($project->createdBy) {
                    $recentActivities[] = [
                        'user_id' => $project->createdBy->id,
                        'user_name' => $project->createdBy->name,
                        'user_avatar' => $project->createdBy->profile_photo_url,
                        'action_type' => 'project_created',
                        'action_description' => 'membuat proyek baru',
                        'item_name' => $project->project_name,
                        'item_link' => route('projects.index') . '#project-' . $project->id,
                        'item_type' => 'Proyek',
                        'details' => 'Klien: ' . $project->client_name . ($project->budget ? ' - Budget: Rp ' . number_format($project->budget, 0, ',', '.') : ''),
                        'time_carbon' => Carbon::parse($project->created_at),
                        'time' => Carbon::parse($project->created_at)->locale('id')->diffForHumans(),
                        'icon' => 'ri-add-circle-line',
                        'icon_class' => $this->getActivityIcon('project_created'),
                        'status_color' => 'bg-green-500',
                        'tags' => ['Baru', ucfirst($project->status)]
                    ];
                }
            }

            // 2. Aktivitas: Proyek yang baru diupdate statusnya (maks 3)
            $recentlyUpdatedProjects = Project::with(['createdBy', 'staff']) 
                                        ->whereIn('status', ['On-going', 'Completed', 'Active', 'Pending'])
                                        ->whereColumn('created_at', '!=', 'updated_at') 
                                        ->whereNotIn('id', $latestProjects->pluck('id')->toArray())
                                        ->latest('updated_at')
                                        ->take(3)
                                        ->get();
            foreach ($recentlyUpdatedProjects as $project) {
                 $updater = $project->staff()->latest('project_user.updated_at')->first() ?? $project->createdBy ?? User::where('role', 'admin')->first();
                 if ($updater) {
                    $recentActivities[] = [
                        'user_id' => $updater->id,
                        'user_name' => $updater->name,
                        'user_avatar' => $updater->profile_photo_url,
                        'action_type' => 'status_updated',
                        'action_description' => 'memperbarui status proyek',
                        'item_name' => $project->project_name,
                        'item_link' => route('projects.index') . '#project-' . $project->id,
                        'item_type' => 'Update Proyek',
                        'details' => 'Status berubah menjadi: ' . $project->status,
                        'time_carbon' => Carbon::parse($project->updated_at),
                        'time' => Carbon::parse($project->updated_at)->locale('id')->diffForHumans(),
                        'icon' => 'ri-refresh-line',
                        'icon_class' => $this->getActivityIcon('status_updated'),
                        'status_color' => $project->status === 'Completed' ? 'bg-green-500' : ($project->status === 'Active' ? 'bg-blue-500' : 'bg-yellow-500'),
                        'tags' => ['Update', $project->status]
                    ];
                }
            }
            
            // 3. Aktivitas: Staff baru ditambahkan (maks 2)
            $latestStaff = User::where('role', 'staff')->latest('created_at')->take(2)->get();
            foreach ($latestStaff as $staff) {
                if ($staff->created_at->gt(Carbon::now()->subDays(30))) { 
                 $adminUser = User::where('role', 'admin')->orderBy('created_at', 'desc')->first() ?? $currentUser; 
                 if($adminUser){
                    $recentActivities[] = [
                        'user_id' => $adminUser->id,
                        'user_name' => $adminUser->name,
                        'user_avatar' => $adminUser->profile_photo_url,
                        'action_type' => 'staff_added',
                            'action_description' => 'menambahkan staff baru',
                            'item_name' => $staff->name,
                        'item_link' => route('staff.index'), 
                        'item_type' => 'Staff',
                            'details' => 'Email: ' . $staff->email . ' - Bergabung dengan tim',
                            'time_carbon' => Carbon::parse($staff->created_at),
                            'time' => Carbon::parse($staff->created_at)->locale('id')->diffForHumans(),
                            'icon' => 'ri-user-add-line',
                            'icon_class' => $this->getActivityIcon('staff_added'),
                            'status_color' => 'bg-indigo-500',
                            'tags' => ['Tim', 'Staff Baru']
                        ];
                     }
                }
            }

            // 4. Aktivitas: Proyek dengan deadline mendekat (dalam 3 hari)
            $urgentDeadlines = Project::select('id', 'project_name', 'end_date', 'status', 'created_by_user_id')
                ->with(['createdBy' => function($query) {
                    $query->select('id', 'name', 'profile_photo_path');
                }])
                ->whereNotNull('end_date')
                ->where('end_date', '>=', Carbon::now()->startOfDay()) 
                ->where('end_date', '<=', Carbon::now()->addDays(3)->endOfDay()) 
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->orderBy('end_date', 'asc')
                ->take(2)
                ->get();
                
            foreach ($urgentDeadlines as $project) {
                if ($project->createdBy) {
                    $daysLeft = Carbon::now()->diffInDays(Carbon::parse($project->end_date), false);
                    $recentActivities[] = [
                        'user_id' => 'system',
                        'user_name' => 'System',
                        'user_avatar' => 'https://ui-avatars.com/api/?name=System&color=FFFFFF&background=EF4444&size=40',
                        'action_type' => 'deadline_warning',
                        'action_description' => 'mengingatkan deadline proyek',
                        'item_name' => $project->project_name,
                        'item_link' => route('projects.index') . '#project-' . $project->id,
                        'item_type' => 'Deadline',
                        'details' => $daysLeft <= 0 ? 'Deadline hari ini!' : "Deadline dalam $daysLeft hari",
                        'time_carbon' => Carbon::now(),
                        'time' => 'Baru saja',
                        'icon' => 'ri-alarm-warning-line',
                        'icon_class' => 'ri-alarm-warning-line text-red-500 bg-red-100/70',
                        'status_color' => $daysLeft <= 0 ? 'bg-red-600' : 'bg-orange-500',
                        'tags' => ['Urgent', 'Deadline']
                    ];
                }
            }

            // 5. Aktivitas: Proyek yang baru selesai (completed) dalam 7 hari terakhir
            $recentlyCompleted = Project::with('createdBy')
                ->where('status', 'Completed')
                ->where('updated_at', '>=', Carbon::now()->subDays(7))
                ->latest('updated_at')
                ->take(2)
                ->get();
                
            foreach ($recentlyCompleted as $project) {
                if ($project->createdBy) {
                    $recentActivities[] = [
                        'user_id' => $project->createdBy->id,
                        'user_name' => $project->createdBy->name,
                        'user_avatar' => $project->createdBy->profile_photo_url,
                        'action_type' => 'project_completed',
                        'action_description' => 'menyelesaikan proyek',
                        'item_name' => $project->project_name,
                        'item_link' => route('projects.index') . '#project-' . $project->id,
                        'item_type' => 'Proyek Selesai',
                        'details' => 'Proyek berhasil diselesaikan' . ($project->budget ? ' - Revenue: Rp ' . number_format($project->budget, 0, ',', '.') : ''),
                        'time_carbon' => Carbon::parse($project->updated_at),
                        'time' => Carbon::parse($project->updated_at)->locale('id')->diffForHumans(),
                        'icon' => 'ri-checkbox-circle-line',
                        'icon_class' => 'ri-checkbox-circle-line text-green-500 bg-green-100/70',
                        'status_color' => 'bg-green-500',
                        'tags' => ['Selesai', '🎉']
                    ];
                }
            }

            // 6. Aktivitas: Staff assignment ke proyek
            $recentAssignments = DB::table('project_user')
                ->join('projects', 'project_user.project_id', '=', 'projects.id')
                ->join('users', 'project_user.user_id', '=', 'users.id')
                ->select('projects.project_name', 'projects.id as project_id', 'users.name as staff_name', 'users.profile_photo_path', 'project_user.created_at')
                ->where('project_user.created_at', '>=', Carbon::now()->subDays(14))
                ->orderBy('project_user.created_at', 'desc')
                ->take(2)
                ->get();
                
            foreach ($recentAssignments as $assignment) {
                $recentActivities[] = [
                    'user_id' => 'admin',
                    'user_name' => 'Admin',
                    'user_avatar' => 'https://ui-avatars.com/api/?name=Admin&color=FFFFFF&background=6366F1&size=40',
                    'action_type' => 'staff_assigned',
                    'action_description' => 'menugaskan staff ke proyek',
                    'item_name' => $assignment->project_name,
                    'item_link' => route('projects.index') . '#project-' . $assignment->project_id,
                    'item_type' => 'Assignment',
                    'details' => 'Staff ' . $assignment->staff_name . ' ditugaskan ke proyek ini',
                    'time_carbon' => Carbon::parse($assignment->created_at),
                    'time' => Carbon::parse($assignment->created_at)->locale('id')->diffForHumans(),
                    'icon' => 'ri-user-settings-line',
                    'icon_class' => 'ri-user-settings-line text-purple-500 bg-purple-100/70',
                    'status_color' => 'bg-purple-500',
                    'tags' => ['Assignment', 'Staff']
                    ];
                 }

            // 7. Aktivitas: Login activities (admin dan staff)
            if ($currentUser) {
                $recentActivities[] = [
                    'user_id' => $currentUser->id,
                    'user_name' => $currentUser->name,
                    'user_avatar' => $currentUser->profile_photo_url,
                    'action_type' => 'dashboard_accessed',
                    'action_description' => 'mengakses dashboard',
                    'item_name' => '',
                    'item_link' => route('dashboard'),
                    'item_type' => 'Akses Sistem',
                    'details' => 'Login dan mengecek status proyek terkini',
                    'time_carbon' => Carbon::now(),
                    'time' => 'Baru saja',
                    'icon' => 'ri-dashboard-line',
                    'icon_class' => 'ri-dashboard-line text-blue-500 bg-blue-100/70',
                    'status_color' => 'bg-blue-500',
                    'tags' => ['Login', 'Dashboard']
                ];
            }

            // Urutkan semua aktivitas berdasarkan waktu (dari yang terbaru)
            usort($recentActivities, function ($a, $b) {
                return $b['time_carbon']->timestamp <=> $a['time_carbon']->timestamp;
            });
            
            // Ambil 8 aktivitas teratas untuk ditampilkan
            $recentActivities = array_slice($recentActivities, 0, 8);
            
            Log::info('DashboardController@index - Comprehensive activities prepared: ', [
                'total_activities' => count($recentActivities),
                'activity_types' => array_column($recentActivities, 'action_type')
            ]);

            if (!$request->session()->has('welcome_popup_shown')) {
                $request->session()->put('welcome_popup_shown', true);
                session(['show_welcome_popup' => true]);
            } else {
                session(['show_welcome_popup' => false]);
            }

            return view('dashboard', compact(
                'todoProjectsCount', 'inProgressProjectsCount', 'completedProjectsCount',
                'staffChartData', 'paymentChartData',
                'recentActivities', 'upcomingDeadlines',
                'totalAllCompletedBudget', 'activeClients', 'monthlyGrowth', 'pendingTasks'
            ));

        } catch (\Exception $e) {
            Log::error('DashboardController@index - Critical Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(), 'user_id' => Auth::id() ?? 'Guest'
            ]);
            if (config('app.debug')) throw $e; 
            return response()->view('errors.500', ['message' => 'Terjadi kesalahan internal pada server saat memuat data dashboard.'], 500);
        }
    }
    
    private function getActivityIcon(string $actionType = null): string
    {
        $actionType = strtolower($actionType ?? '');
        if (str_contains($actionType, 'create') || str_contains($actionType, 'add') || str_contains($actionType, 'project_created')) return 'ri-add-circle-line text-green-500 bg-green-100/70';
        if (str_contains($actionType, 'update') || str_contains($actionType, 'edit') || str_contains($actionType, 'status_updated')) return 'ri-refresh-line text-orange-500 bg-orange-100/70';
        if (str_contains($actionType, 'complete') || str_contains($actionType, 'finish') || str_contains($actionType, 'task_completed')) return 'ri-checkbox-circle-line text-blue-500 bg-blue-100/70';
        if (str_contains($actionType, 'comment')) return 'ri-chat-3-line text-purple-500 bg-purple-100/70';
        if (str_contains($actionType, 'delete') || str_contains($actionType, 'remove')) return 'ri-delete-bin-line text-red-500 bg-red-100/70';
        if (str_contains($actionType, 'upload') || str_contains($actionType, 'file')) return 'ri-file-upload-line text-teal-500 bg-teal-100/70';
        if (str_contains($actionType, 'login') || str_contains($actionType, 'view') || str_contains($actionType, 'dashboard_viewed')) return 'ri-eye-line text-sky-500 bg-sky-100/70';
        if (str_contains($actionType, 'staff_added')) return 'ri-user-add-line text-indigo-500 bg-indigo-100/70';
        return 'ri-information-line text-slate-500 bg-slate-100/70';
    }
}
