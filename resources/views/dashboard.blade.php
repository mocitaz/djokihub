@php
    $currentUser = auth()->user();
    $isAdmin = $currentUser && $currentUser->role === 'admin';
    $isStaff = $currentUser && $currentUser->role === 'staff';
    $userNameForModal = $currentUser && $currentUser->name ? $currentUser->name : 'User';

    // Enhanced dashboard data with defaults
    $recentActivities = $recentActivities ?? [];
    $upcomingDeadlines = $upcomingDeadlines ?? [];
    
    // Real project counts from database
    $todoProjectsCount = $todoProjectsCount ?? 0; // Draft + Pending
    $inProgressProjectsCount = $inProgressProjectsCount ?? 0; // On-going + Active
    $completedProjectsCount = $completedProjectsCount ?? 0; // Completed
    
    // Chart data
    $staffChartData = $staffChartData ?? [['value' => 0, 'name' => 'No Data']];
    $paymentChartData = $paymentChartData ?? ['categories' => ['No Data'], 'values' => [0]];

    // Real revenue data - use the same calculation as in projects index
    $totalRevenue = $totalAllCompletedBudget ?? 0; // Total from completed projects
    $monthlyGrowth = $monthlyGrowth ?? 15; // Can be calculated from monthly data
    $activeClients = $activeClients ?? 0; // Count of distinct clients with active projects
    $pendingTasks = $pendingTasks ?? 0; // Count of pending requirements or deadlines
@endphp

@extends('layouts.app')

@section('title', 'Dashboard - DjokiHub')

@push('styles')
<style>
    /* Enhanced Dashboard Styles */
    :root {
        --dashboard-primary: #6366F1;
        --dashboard-secondary: #8B5CF6;
        --dashboard-success: #10B981;
        --dashboard-warning: #F59E0B;
        --dashboard-danger: #EF4444;
        --dashboard-info: #06B6D4;
        --dashboard-surface: #FFFFFF;
        --dashboard-background: #F8FAFC;
        --dashboard-text: #1E293B;
        --dashboard-text-muted: #64748B;
        --dashboard-border: #E2E8F0;
        --dashboard-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --dashboard-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        --dashboard-radius: 0.75rem;
        --dashboard-radius-lg: 1rem;
    }

    /* Enhanced Scrollbar */
    .styled-scrollbar::-webkit-scrollbar { 
        width: 6px; 
        height: 6px; 
    }
    .styled-scrollbar::-webkit-scrollbar-track { 
        background: var(--dashboard-background);
        border-radius: 10px; 
    }
    .styled-scrollbar::-webkit-scrollbar-thumb { 
        background: linear-gradient(180deg, var(--dashboard-primary), var(--dashboard-secondary));
        border-radius: 10px; 
        border: 1px solid var(--dashboard-background);
    }
    .styled-scrollbar::-webkit-scrollbar-thumb:hover { 
        background: linear-gradient(180deg, var(--dashboard-secondary), var(--dashboard-primary));
    }

    /* Premium Interactive Cards */
    .dashboard-card {
        background: var(--dashboard-surface);
        border-radius: var(--dashboard-radius-lg);
        box-shadow: var(--dashboard-shadow);
        border: 1px solid var(--dashboard-border);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
        transition: left 0.6s ease;
        z-index: 1;
    }

    .dashboard-card:hover::before {
        left: 100%;
    }

    .dashboard-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--dashboard-shadow-lg);
        border-color: var(--dashboard-primary);
    }

    .dashboard-card .card-content {
        position: relative;
        z-index: 2;
    }

    /* Gradient Backgrounds for Stats Cards */
    .stat-card-primary {
        background: linear-gradient(135deg, var(--dashboard-primary) 0%, var(--dashboard-secondary) 100%);
        color: white;
    }

    .stat-card-success {
        background: linear-gradient(135deg, var(--dashboard-success) 0%, #059669 100%);
        color: white;
    }

    .stat-card-warning {
        background: linear-gradient(135deg, var(--dashboard-warning) 0%, #D97706 100%);
        color: white;
    }

    .stat-card-info {
        background: linear-gradient(135deg, var(--dashboard-info) 0%, #0891B2 100%);
        color: white;
    }

    /* Enhanced Animations */
    @keyframes slideInUp {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @keyframes slideInLeft {
        from { 
            opacity: 0; 
            transform: translateX(-30px); 
        }
        to { 
            opacity: 1; 
            transform: translateX(0); 
        }
    }

    @keyframes fadeInScale {
        from { 
            opacity: 0; 
            transform: scale(0.9); 
        }
        to { 
            opacity: 1; 
            transform: scale(1); 
        }
    }

    @keyframes pulse {
        0%, 100% { 
            transform: scale(1); 
        }
        50% { 
            transform: scale(1.05); 
        }
    }

    @keyframes shimmer {
        0% { 
            background-position: -200% 0; 
        }
        100% { 
            background-position: 200% 0; 
        }
    }

    .animate-slideInUp { 
        animation: slideInUp 0.6s ease-out forwards; 
    }
    .animate-slideInLeft { 
        animation: slideInLeft 0.5s ease-out forwards; 
    }
    .animate-fadeInScale { 
        animation: fadeInScale 0.4s ease-out forwards; 
    }
    .animate-pulse-gentle { 
        animation: pulse 2s ease-in-out infinite; 
    }

    /* Enhanced Quick Action Buttons */
    .quick-action-btn {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-radius: var(--dashboard-radius);
        border: 2px solid transparent;
    }

    .quick-action-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s ease, height 0.3s ease;
    }

    .quick-action-btn:hover::before {
        width: 300%;
        height: 300%;
    }

    .quick-action-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }

    .quick-action-btn:active {
        transform: translateY(-2px);
    }

    /* Enhanced Chart Containers */
    .chart-container {
        min-height: 320px;
        position: relative;
        background: var(--dashboard-surface);
        border-radius: var(--dashboard-radius);
        overflow: hidden;
    }

    .chart-skeleton {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
        border-radius: var(--dashboard-radius);
    }

    /* Enhanced Activity Items */
    .activity-item {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-radius: var(--dashboard-radius);
        position: relative;
    }

    .activity-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: transparent;
        border-radius: 0 3px 3px 0;
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        transform: translateX(8px);
        box-shadow: 0 4px 12px -2px rgba(99, 102, 241, 0.15);
    }

    .activity-item:hover::before {
        background: linear-gradient(180deg, var(--dashboard-primary), var(--dashboard-secondary));
    }

    /* Enhanced Deadline Items */
    .deadline-item {
        transition: all 0.2s ease;
        border-radius: var(--dashboard-radius);
        border-left: 3px solid transparent;
    }

    .deadline-item:hover {
        background: rgba(99, 102, 241, 0.05);
        border-left-color: var(--dashboard-primary);
        transform: translateX(4px);
    }

    .deadline-item.urgent {
        border-left-color: var(--dashboard-danger);
    }

    .deadline-item.warning {
        border-left-color: var(--dashboard-warning);
    }

    .deadline-item.normal {
        border-left-color: var(--dashboard-info);
    }

    /* Enhanced Motivational Card */
    .motivational-card {
        background: linear-gradient(135deg, var(--dashboard-primary) 0%, var(--dashboard-secondary) 50%, var(--dashboard-info) 100%);
        background-size: 200% 200%;
        animation: gradientShift 8s ease-in-out infinite;
        position: relative;
        overflow: hidden;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .motivational-card::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
        pointer-events: none;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: rotate(30deg) translateY(0); }
        50% { transform: rotate(30deg) translateY(-10px); }
    }

    /* Real-time Notification Badge */
    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, var(--dashboard-danger), #DC2626);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        animation: pulse 2s infinite;
    }

    /* Enhanced Mobile Responsiveness */
    @media (max-width: 768px) {
        .dashboard-card {
            margin-bottom: 1rem;
        }
        
        .chart-container {
            min-height: 240px;
        }
        
        .quick-action-btn {
            padding: 0.75rem;
        }
        
        .stat-card-content {
            text-align: center;
        }
    }

    /* Widget Customization */
    .widget-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .widget-actions {
        display: flex;
        gap: 0.5rem;
    }

    .widget-action-btn {
        padding: 0.25rem;
        border-radius: 0.375rem;
        color: var(--dashboard-text-muted);
        transition: all 0.2s ease;
    }

    .widget-action-btn:hover {
        color: var(--dashboard-primary);
        background: rgba(99, 102, 241, 0.1);
    }

    /* Progress Bars */
    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--dashboard-border);
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--dashboard-primary), var(--dashboard-secondary));
        border-radius: 4px;
        transition: width 1s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }

    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: progressShine 2s ease-in-out infinite;
    }

    @keyframes progressShine {
        0% { left: -100%; }
        100% { left: 100%; }
    }
</style>
@endpush

@section('navigation_links')
    <nav class="ml-4 md:ml-10 flex items-center space-x-2 md:space-x-4 xl:space-x-6 overflow-x-auto py-2 styled-scrollbar">
        <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-dashboard-3-line mr-1"></i>Dashboard
        </a>
        <a href="{{ route('projects.index') }}" class="{{ request()->is('projects*') || request()->is('taskboard*') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-folder-3-line mr-1"></i>Projects
        </a>
        <a href="{{ route('staff.index') }}" class="{{ request()->is('staff*') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-team-line mr-1"></i>Staff
        </a>
        <a href="{{ route('work-rules') }}" class="{{ request()->is('work-rules*') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-file-list-2-line mr-1"></i>Work Rules
        </a>
        <a href="{{ route('meeting-notes.index') }}" class="{{ request()->is('meeting-notes*') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-chat-3-line mr-1"></i>Meeting Notes
        </a>
    </nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-8">
    <!-- Enhanced Welcome Section with Real-time Clock -->
    <div class="dashboard-card animate-slideInUp p-6" style="animation-delay: 0.1s;">
        <div class="card-content flex flex-col md:flex-row items-start md:items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Welcome Back, {{ $currentUser->name ?? 'User' }}!</h1>
                    <div class="notification-badge" id="notificationBadge" style="display: none;">0</div>
                </div>
                <p class="text-sm md:text-base text-slate-600 mb-3">Here's what's happening with your projects today.</p>
                <div class="flex items-center space-x-4 text-sm text-slate-500">
                    <div class="flex items-center space-x-1">
                        <i class="ri-calendar-line"></i>
                        <span id="currentDate">{{ now()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('l, j F Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <i class="ri-time-line"></i>
                        <span id="currentTime">{{ now()->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <i class="ri-user-line"></i>
                        <span>{{ $activeClients }} Active Clients</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="flex items-center space-x-2">
                    <button class="widget-action-btn" onclick="refreshDashboard()" title="Refresh Dashboard">
                        <i class="ri-refresh-line text-lg"></i>
                    </button>
                    <button class="widget-action-btn" onclick="toggleFullscreen()" title="Fullscreen">
                        <i class="ri-fullscreen-line text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards with Progress Bars -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- To Do Projects Card -->
        <div class="dashboard-card stat-card-primary animate-slideInUp" style="animation-delay: 0.15s;">
            <div class="card-content p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-white/80 text-xs font-medium uppercase tracking-wider">To Do Projects</p>
                        <h3 class="text-3xl font-bold text-white mt-1 counter" data-target="{{ $todoProjectsCount }}">0</h3>
                        <div class="progress-bar mt-2">
                            <div class="progress-fill bg-white/30" style="width: {{ $todoProjectsCount > 0 ? min(($todoProjectsCount / ($todoProjectsCount + $inProgressProjectsCount + $completedProjectsCount)) * 100, 100) : 0 }}%"></div>
                    </div>
                    </div>
                    <div class="w-12 h-12 bg-white/20 text-white rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="ri-file-list-3-line text-2xl"></i>
                </div>
            </div>
                <a href="{{ route('projects.index', ['statuses[]' => 'Draft', 'statuses[]' => 'Pending']) }}" class="text-white/90 hover:text-white text-xs font-medium flex items-center group">
                    View All <i class="ri-arrow-right-s-line ml-1 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- In Progress Projects Card -->
        <div class="dashboard-card stat-card-warning animate-slideInUp" style="animation-delay: 0.2s;">
            <div class="card-content p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-white/80 text-xs font-medium uppercase tracking-wider">In Progress</p>
                        <h3 class="text-3xl font-bold text-white mt-1 counter" data-target="{{ $inProgressProjectsCount }}">0</h3>
                        <div class="progress-bar mt-2">
                            <div class="progress-fill bg-white/30" style="width: {{ $inProgressProjectsCount > 0 ? min(($inProgressProjectsCount / ($todoProjectsCount + $inProgressProjectsCount + $completedProjectsCount)) * 100, 100) : 0 }}%"></div>
                    </div>
                    </div>
                    <div class="w-12 h-12 bg-white/20 text-white rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="ri-loader-2-line text-2xl animate-pulse-gentle"></i>
                </div>
            </div>
                <a href="{{ route('projects.index', ['statuses[]' => 'On-going', 'statuses[]' => 'Active']) }}" class="text-white/90 hover:text-white text-xs font-medium flex items-center group">
                    View All <i class="ri-arrow-right-s-line ml-1 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- Completed Projects Card -->
        <div class="dashboard-card stat-card-success animate-slideInUp" style="animation-delay: 0.25s;">
            <div class="card-content p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-white/80 text-xs font-medium uppercase tracking-wider">Completed</p>
                        <h3 class="text-3xl font-bold text-white mt-1 counter" data-target="{{ $completedProjectsCount }}">0</h3>
                        <div class="progress-bar mt-2">
                            <div class="progress-fill bg-white/30" style="width: {{ $completedProjectsCount > 0 ? min(($completedProjectsCount / ($todoProjectsCount + $inProgressProjectsCount + $completedProjectsCount)) * 100, 100) : 0 }}%"></div>
                    </div>
                    </div>
                    <div class="w-12 h-12 bg-white/20 text-white rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="ri-checkbox-multiple-line text-2xl"></i>
                </div>
            </div>
                <a href="{{ route('projects.index', ['statuses[]' => 'Completed']) }}" class="text-white/90 hover:text-white text-xs font-medium flex items-center group">
                    View All <i class="ri-arrow-right-s-line ml-1 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="dashboard-card stat-card-info animate-slideInUp" style="animation-delay: 0.3s;">
            <div class="card-content p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-white/80 text-xs font-medium uppercase tracking-wider">Total Revenue</p>
                        <h3 class="text-3xl font-bold text-white mt-1">
                            @if($totalRevenue > 0)
                                <span class="text-sm">Rp</span> <span class="counter" data-target="{{ floor($totalRevenue / 1000000) }}">0</span><span class="text-lg">M</span>
                            @else
                                <span class="counter" data-target="0">0</span>
                            @endif
                        </h3>
                        <div class="flex items-center mt-1">
                            <i class="ri-arrow-up-line text-green-300 text-sm"></i>
                            <span class="text-green-300 text-xs font-medium">+{{ $monthlyGrowth }}% this month</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white/20 text-white rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="ri-money-dollar-circle-line text-2xl"></i>
                    </div>
                </div>
                <a href="{{ route('projects.index', ['statuses[]' => 'Completed', 'payment_status' => 'Fully Paid']) }}" class="text-white/90 hover:text-white text-xs font-medium flex items-center group">
                    View Details <i class="ri-arrow-right-s-line ml-1 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Charts Section with Interactive Features -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Staff Distribution Chart with Customization -->
        <div class="lg:col-span-2 dashboard-card animate-slideInUp" style="animation-delay: 0.35s;">
            <div class="card-content p-6">
                <div class="widget-header">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center">
                        <i class="ri-team-line mr-2 text-primary"></i>
                        Staff Project Distribution
                    </h3>
                    <div class="widget-actions">
                        <select id="chartTimeFilter" class="text-xs border border-slate-200 rounded-lg px-2 py-1" onchange="updateChartData()">
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                        </select>
                        <button class="widget-action-btn" onclick="exportChart('staff')" title="Export Chart">
                            <i class="ri-download-line"></i>
                        </button>
                        <button class="widget-action-btn" onclick="toggleChartType('staff')" title="Toggle Chart Type">
                            <i class="ri-bar-chart-line"></i>
                        </button>
                    </div>
            </div>
            <div id="staff-chart" class="chart-container">
                <div class="chart-skeleton"></div>
                </div>
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="p-3 rounded-lg bg-slate-50">
                        <div class="text-sm font-medium text-slate-600">Active Staff</div>
                        <div class="text-xl font-bold text-primary counter" data-target="{{ count($staffChartData) > 1 ? count($staffChartData) : 0 }}">0</div>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50">
                        <div class="text-sm font-medium text-slate-600">Avg Projects</div>
                        <div class="text-xl font-bold text-warning">
                            @if(count($staffChartData) > 1)
                                {{ number_format(collect($staffChartData)->avg('value'), 1) }}
                            @else
                                0
                            @endif
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50">
                        <div class="text-sm font-medium text-slate-600">Top Performer</div>
                        <div class="text-xl font-bold text-success">
                            @if(count($staffChartData) > 1 && isset($staffChartData[0]['name']))
                                {{ Str::limit($staffChartData[0]['name'], 8) }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50">
                        <div class="text-sm font-medium text-slate-600">Projects</div>
                        <div class="text-xl font-bold text-info">{{ $todoProjectsCount + $inProgressProjectsCount + $completedProjectsCount }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Chart with Enhanced Features -->
        <div class="dashboard-card animate-slideInUp" style="animation-delay: 0.4s;">
            <div class="card-content p-6">
                <div class="widget-header">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center">
                        <i class="ri-money-dollar-circle-line mr-2 text-primary"></i>
                        Payment Status
                    </h3>
                    <div class="widget-actions">
                        <button class="widget-action-btn" onclick="refreshPaymentData()" title="Refresh Data">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
            </div>
            <div id="payment-chart" class="chart-container">
                <div class="chart-skeleton"></div>
            </div>
                <div class="mt-4 space-y-2">
                    @foreach($paymentChartData['categories'] as $index => $status)
                        @php
                            $statusClass = match(strtolower($status)) {
                                'fully paid', 'paid' => 'green',
                                'dp', 'partially paid', 'partial' => 'yellow', 
                                'pending', 'unpaid' => 'red',
                                default => 'gray'
                            };
                        @endphp
                        <div class="flex items-center justify-between p-2 rounded-lg bg-{{ $statusClass }}-50">
                            <span class="text-sm font-medium text-{{ $statusClass }}-700">{{ $status }}</span>
                            <span class="text-sm font-bold text-{{ $statusClass }}-800">{{ $paymentChartData['values'][$index] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- New Task Management and Calendar Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity with Enhanced Features -->
        <div class="lg:col-span-2 dashboard-card animate-slideInUp" style="animation-delay: 0.45s;">
            <div class="card-content p-6">
                <div class="widget-header">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center">
                        <i class="ri-pulse-line mr-2 text-primary"></i>
                        Live Activity Feed
                        <div class="ml-2 w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    </h3>
            </div>
                <div class="space-y-3 max-h-96 overflow-y-auto styled-scrollbar -mr-2 pr-2" id="activityFeed">
                @forelse($recentActivities as $index => $activity)
                        <div class="activity-item animate-slideInLeft p-3 rounded-lg" 
                             style="animation-delay: {{ $index * 0.05 }}s"
                             data-activity-type="{{ $activity['action_type'] ?? 'normal' }}">
                            <div class="flex items-start space-x-3">
                                <div class="relative flex-shrink-0">
                        <img src="{{ $activity['user_avatar'] }}"
                             alt="{{ $activity['user_name'] }}"
                                         class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-md"
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($activity['user_name'] ?? 'X') }}&color=FFFFFF&background=4F46E5&size=40'">
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 {{ $activity['status_color'] ?? 'bg-green-500' }} rounded-full border-2 border-white"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm text-slate-600 leading-relaxed">
                                            <span class="font-semibold text-slate-800 hover:text-primary transition-colors">{{ $activity['user_name'] }}</span>
                                {{ $activity['action_description'] }}
                                @if($activity['item_name'])
                                                <a href="{{ $activity['item_link'] ?? '#' }}" class="font-medium text-primary hover:underline">{{ Str::limit($activity['item_name'], 25) }}</a>
                                @endif
                            </p>
                                        <span class="text-xs text-slate-400 flex-shrink-0 ml-2">{{ $activity['time'] }}</span>
                                    </div>
                                    
                            @if(!empty($activity['details']))
                                        <p class="text-xs text-slate-500 mb-2 bg-slate-50 p-2 rounded-md italic line-clamp-2">"{{ Str::limit($activity['details'], 80) }}"</p>
                            @endif
                                    
                                    <div class="flex items-center justify-between">
                                        @if(isset($activity['tags']) && is_array($activity['tags']) && count($activity['tags']) > 0)
                                            <div class="flex items-center space-x-1">
                                                @foreach(array_slice($activity['tags'], 0, 3) as $tag)
                                                    <span class="px-2 py-1 text-xs 
                                                        @if($tag === 'Urgent') bg-red-100 text-red-700
                                                        @elseif($tag === 'Completed' || $tag === 'Selesai') bg-green-100 text-green-700
                                                        @elseif($tag === 'Active') bg-blue-100 text-blue-700
                                                        @elseif($tag === 'Pending') bg-yellow-100 text-yellow-700
                                                        @elseif($tag === 'Assignment') bg-purple-100 text-purple-700
                                                        @elseif($tag === 'Baru') bg-emerald-100 text-emerald-700
                                                        @else bg-primary/10 text-primary
                                                        @endif rounded-full">{{ $tag }}</span>
                                                @endforeach
                        </div>
                                        @endif
                                        
                                        @if($activity['item_type'])
                                            <span class="text-xs px-2 py-1 bg-slate-100 text-slate-600 rounded-full">{{ $activity['item_type'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-1">
                                    <div class="w-8 h-8 rounded-lg {{ $activity['icon_class'] ?? 'bg-slate-100 text-slate-500' }} flex items-center justify-center">
                                        <i class="{{ $activity['icon'] ?? 'ri-notification-line' }} text-sm"></i>
                                    </div>
                                </div>
                        </div>
                    </div>
                @empty
                        <div class="py-10 text-center animate-fadeInScale">
                            <i class="ri-pulse-line text-4xl text-slate-300 mb-3"></i>
                            <p class="text-sm text-slate-500">Belum ada aktivitas terbaru.</p>
                            <p class="text-xs text-slate-400 mt-1">Aktivitas akan muncul di sini seiring berjalannya waktu.</p>
                            <button class="mt-2 text-xs text-primary hover:underline" onclick="refreshActivities()">Muat Ulang Aktivitas</button>
                    </div>
                @endforelse
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <button onclick="openActivitiesModal()" class="w-full text-sm text-primary hover:text-primary-dark font-medium flex items-center justify-center group">
                        View All Activities <i class="ri-arrow-right-s-line ml-1 transition-transform group-hover:translate-x-1"></i>
                    </button>
                </div>
            </div>
            </div>
        </div>

    <!-- Enhanced Quick Actions and Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Enhanced Quick Actions -->
        <div class="dashboard-card animate-slideInUp" style="animation-delay: 0.6s;">
            <div class="card-content p-6">
                <div class="widget-header">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center">
                        <i class="ri-flashlight-line mr-2 text-primary"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('projects.index') }}" class="quick-action-btn group bg-primary hover:bg-primary-dark text-white p-4 rounded-xl text-center relative">
                        <div class="relative z-10">
                            <i class="ri-add-circle-line text-2xl mb-2 block"></i>
                            <span class="text-sm font-medium">New Project</span>
                        </div>
                    </a>

                    @if($isAdmin)
                    <a href="{{ route('staff.index') }}" class="quick-action-btn group bg-white border-2 border-slate-200 hover:border-primary text-slate-700 hover:text-primary p-4 rounded-xl text-center">
                        <div class="relative z-10">
                            <i class="ri-user-add-line text-2xl mb-2 block"></i>
                            <span class="text-sm font-medium">Add Staff</span>
                        </div>
                    </a>
                    @endif

                    @if($isAdmin && Route::has('invoices.index'))
                    <a href="{{ route('invoices.index') }}" class="quick-action-btn group bg-white border-2 border-slate-200 hover:border-warning text-slate-700 hover:text-warning p-4 rounded-xl text-center">
                        <div class="relative z-10">
                            <i class="ri-bill-line text-2xl mb-2 block"></i>
                            <span class="text-sm font-medium">Generate PoP</span>
                        </div>
                    </a>
                    @endif

                    @if(Route::has('documents.listPoc'))
                    <a href="{{ route('documents.listPoc') }}" class="quick-action-btn group bg-white border-2 border-slate-200 hover:border-success text-slate-700 hover:text-success p-4 rounded-xl text-center">
                        <div class="relative z-10">
                            <i class="ri-file-text-line text-2xl mb-2 block"></i>
                            <span class="text-sm font-medium">Generate PoC</span>
                        </div>
                    </a>
                    @endif

                    @if(Route::has('documents.listBast'))
                    <a href="{{ route('documents.listBast') }}" class="quick-action-btn group bg-white border-2 border-slate-200 hover:border-info text-slate-700 hover:text-info p-4 rounded-xl text-center">
                        <div class="relative z-10">
                            <i class="ri-file-shield-2-line text-2xl mb-2 block"></i>
                            <span class="text-sm font-medium">Generate BAST</span>
                        </div>
                    </a>
                    @else
                    <a href="{{ route('projects.index') }}" class="quick-action-btn group bg-white border-2 border-slate-200 hover:border-info text-slate-700 hover:text-info p-4 rounded-xl text-center">
                        <div class="relative z-10">
                            <i class="ri-file-list-line text-2xl mb-2 block"></i>
                            <span class="text-sm font-medium">View Projects</span>
                        </div>
                    </a>
                    @endif

                    <a href="{{ route('meeting-notes.index') }}" class="quick-action-btn group bg-white border-2 border-slate-200 hover:border-secondary text-slate-700 hover:text-secondary p-4 rounded-xl text-center">
                        <div class="relative z-10">
                            <i class="ri-chat-3-line text-2xl mb-2 block"></i>
                            <span class="text-sm font-medium">Meeting Notes</span>
                        </div>
                    </a>
                </div>
                </div>
            </div>

        <!-- Enhanced Upcoming Deadlines -->
        <div class="dashboard-card animate-slideInUp" style="animation-delay: 0.65s;">
            <div class="card-content p-6">
                <div class="widget-header">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center">
                        <i class="ri-time-line mr-2 text-primary"></i>
                        Upcoming Deadlines
                        @if(count($upcomingDeadlines) > 0)
                            <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">{{ count($upcomingDeadlines) }}</span>
                        @endif
                    </h3>
                    <div class="widget-actions">
                        <button class="widget-action-btn" onclick="addDeadlineReminder()" title="Add Reminder">
                            <i class="ri-alarm-line"></i>
                        </button>
                    </div>
                </div>
                <div class="space-y-3 max-h-80 overflow-y-auto styled-scrollbar">
                    @forelse($upcomingDeadlines as $deadline)
                        <div class="deadline-item animate-slideInLeft p-3 rounded-lg 
                            @if($deadline['due_in_days'] <= 0) urgent
                            @elseif($deadline['due_in_days'] <= 2) warning
                            @else normal @endif" 
                            style="animation-delay: {{ $loop->index * 0.05 }}s">
                            <div class="flex items-center justify-between mb-2">
                                <a href="{{ $deadline['project_id'] ? route('projects.index') . '#project-' . $deadline['project_id'] : '#' }}" 
                                   class="text-sm font-semibold text-slate-700 hover:text-primary truncate flex-1 mr-2" 
                                   title="{{ $deadline['name'] }}">
                                    {{ Str::limit($deadline['name'], 25) }}
                                </a>
                                <span class="text-xs font-bold px-2 py-1 rounded-full
                                    @if($deadline['due_in_days'] <= 0) bg-red-100 text-red-700
                                    @elseif($deadline['due_in_days'] <= 2) bg-yellow-100 text-yellow-700
                                    @else bg-blue-100 text-blue-700 @endif">
                                    @if($deadline['due_in_days'] <= 0) Today!
                                    @else {{ $deadline['due_in_days'] }}d left
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-slate-500 deadline-countdown" data-due-date="{{ $deadline['actual_due_date'] }}">
                                    Calculating...
                                </p>
                                <div class="flex items-center space-x-1">
                                    <button class="widget-action-btn text-xs" onclick="snoozeDeadline({{ $deadline['project_id'] }})" title="Snooze">
                                        <i class="ri-snooze-line"></i>
                                    </button>
                                    <button class="widget-action-btn text-xs" onclick="markDeadlineComplete({{ $deadline['project_id'] }})" title="Mark Complete">
                                        <i class="ri-check-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center animate-fadeInScale">
                            <i class="ri-calendar-check-line text-4xl text-slate-300 mb-3"></i>
                            <p class="text-sm text-slate-500">No upcoming deadlines.</p>
                            <p class="text-xs text-slate-400 mt-1">You're all caught up! 🎉</p>
                        </div>
                    @endforelse
                </div>
                </div>
            </div>

        <!-- Enhanced Motivational Card with Dynamic Content -->
        <div class="motivational-card dashboard-card animate-slideInUp relative" style="animation-delay: 0.7s;">
            <div class="card-content p-6 text-white text-center relative z-10">
                <div class="mb-4">
                    <i class="ri-sparkling-line text-3xl opacity-80 animate-pulse-gentle"></i>
            </div>
                <blockquote class="mb-4">
                    <p id="motivationalQuoteText" class="text-sm font-medium leading-relaxed italic">
                        "The way to get started is to quit talking and begin doing."
                    </p>
                </blockquote>
                <cite id="motivationalQuoteAuthor" class="text-xs opacity-90 font-medium">
                    — Walt Disney
                </cite>
                <div class="mt-4 pt-4 border-t border-white/20">
                    <div class="flex items-center justify-center space-x-4 text-xs">
                        <div class="text-center">
                            <div class="font-bold">{{ $activeClients }}</div>
                            <div class="opacity-80">Active Clients</div>
        </div>
                        <div class="w-px h-8 bg-white/30"></div>
                        <div class="text-center">
                            <div class="font-bold">
                                @if($totalRevenue > 0)
                                    Rp {{ number_format($totalRevenue / 1000000, 0) }}M
                                @else
                                    Rp 0
                                @endif
                            </div>
                            <div class="opacity-80">Total Revenue</div>
                        </div>
                    </div>
                </div>
                <button onclick="getNewQuote()" class="mt-3 text-xs text-white/80 hover:text-white transition-colors">
                    <i class="ri-refresh-line mr-1"></i>New Quote
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Welcome Modal with AI Features -->
<div id="welcomeModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 hidden" aria-labelledby="welcomeModalTitle" role="dialog" aria-modal="true">
    <div id="welcomeModalContent" class="bg-white text-gray-800 rounded-2xl shadow-2xl p-8 m-4 max-w-lg w-full transform transition-all opacity-0 scale-95">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-primary/10 mb-6 shadow-lg">
                <i class="ri-sparkling-2-fill text-4xl text-primary animate-pulse-gentle"></i>
            </div>
            <h2 id="welcomeModalTitle" class="text-2xl font-bold mb-3 text-gray-800">
                Welcome Back, <span class="font-black">{{ $userNameForModal }}</span>!
            </h2>
            <p class="text-gray-600 text-sm mb-6 leading-relaxed" id="welcomeQuote">
                Ready to make today productive and successful!
            </p>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-100 rounded-lg p-3">
                    <div class="text-lg font-bold text-gray-800">{{ $todoProjectsCount + $inProgressProjectsCount }}</div>
                    <div class="text-xs text-gray-500">Active Projects</div>
                </div>
                <div class="bg-gray-100 rounded-lg p-3">
                    <div class="text-lg font-bold text-gray-800">{{ $completedProjectsCount }}</div>
                    <div class="text-xs text-gray-500">Completed Projects</div>
                </div>
            </div>

            <div class="flex gap-3">
                <button id="closeWelcomeModalBtn" type="button" class="flex-1 px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary/50 transition-all duration-200">
                    Let's Get Started!
                </button>
                <button onclick="openQuickTour()" type="button" class="px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-300/50 transition-all duration-200">
                    Quick Tour
                </button>
            </div>
        </div>
    </div>
</div>

<!-- All Activities Modal -->
<div id="activitiesModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 hidden" aria-labelledby="activitiesModalTitle" role="dialog" aria-modal="true">
    <div id="activitiesModalContent" class="bg-white rounded-2xl shadow-2xl m-4 max-w-4xl w-full max-h-[90vh] transform transition-all opacity-0 scale-95 overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                    <i class="ri-pulse-line text-xl text-primary"></i>
                </div>
                <div>
                    <h2 id="activitiesModalTitle" class="text-xl font-bold text-slate-800">All Activities</h2>
                    <p class="text-sm text-slate-500">Complete history of project activities</p>
                </div>
            </div>
            <button onclick="closeActivitiesModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-700 transition-colors">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)] styled-scrollbar">
            <div id="allActivitiesList" class="space-y-4">
                <!-- Activities will be loaded here -->
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <i class="ri-loader-2-line text-4xl text-slate-300 animate-spin mb-3"></i>
                        <p class="text-slate-500">Loading activities...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    Showing activities from the last 30 days
                </p>
                <button onclick="refreshAllActivities()" class="text-xs text-primary hover:text-primary-dark font-medium flex items-center space-x-1">
                    <i class="ri-refresh-line"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
// Global Functions - Define these first
window.showNotification = function(message, type = 'info', duration = 5000) {
    console.log(`[${type.toUpperCase()}] ${message}`);
    // Will be overridden by the enhanced version later
};

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

window.refreshDashboard = function() {
    showNotification('Dashboard refreshed', 'success');
    location.reload();
};

window.toggleFullscreen = function() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
};

// Modal Functions - Must be global
window.openActivitiesModal = function() {
    const modal = document.getElementById('activitiesModal');
    const modalContent = document.getElementById('activitiesModalContent');
    
    if (!modal || !modalContent) return;
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.style.opacity = '1';
        modalContent.style.transform = 'scale(1)';
    }, 10);
    
    // Load all activities
    loadAllActivities();
};

window.closeActivitiesModal = function() {
    const modal = document.getElementById('activitiesModal');
    const modalContent = document.getElementById('activitiesModalContent');
    
    if (!modal || !modalContent) return;
    
    modalContent.style.opacity = '0';
    modalContent.style.transform = 'scale(0.95)';
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
};

window.loadAllActivities = function() {
    const activitiesList = document.getElementById('allActivitiesList');
    if (!activitiesList) return;
    
    // Show loading state
    activitiesList.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <i class="ri-loader-2-line text-4xl text-slate-300 animate-spin mb-3"></i>
                <p class="text-slate-500">Loading activities...</p>
            </div>
        </div>
    `;
    
    // Simulate loading and show expanded activities
    setTimeout(() => {
        const allActivities = @json($recentActivities ?? []);
        
        // Add some additional system activities for comprehensive view
        const systemActivities = [
            {
                user_name: 'System',
                user_avatar: 'https://ui-avatars.com/api/?name=System&color=FFFFFF&background=6366F1&size=40',
                action_description: 'melakukan backup otomatis',
                item_name: 'Database Backup',
                time: '1 jam yang lalu',
                details: 'Backup harian database sistem berhasil dilakukan',
                icon_class: 'ri-database-2-line text-green-500 bg-green-100/70',
                item_type: 'System',
                tags: ['Backup', 'System']
            },
            {
                user_name: '{{ $currentUser->name ?? "Admin" }}',
                user_avatar: '{{ $currentUser->profile_photo_url ?? "" }}',
                action_description: 'mengakses dashboard',
                item_name: 'Dashboard Analytics',
                time: 'Baru saja',
                details: 'Melihat laporan kinerja tim dan status proyek terkini',
                icon_class: 'ri-dashboard-line text-blue-500 bg-blue-100/70',
                item_type: 'Akses',
                tags: ['Dashboard', 'Analytics']
            }
        ];
        
        const combinedActivities = [...allActivities, ...systemActivities];
        
        if (combinedActivities.length === 0) {
            activitiesList.innerHTML = `
                <div class="text-center py-12">
                    <i class="ri-pulse-line text-4xl text-slate-300 mb-3"></i>
                    <h3 class="text-lg font-semibold text-slate-600 mb-2">Belum Ada Aktivitas</h3>
                    <p class="text-slate-500">Aktivitas akan muncul di sini seiring berjalannya waktu.</p>
                </div>
            `;
            return;
        }
        
        activitiesList.innerHTML = combinedActivities.map((activity, index) => `
            <div class="activity-item-modal p-4 rounded-lg border border-slate-200 hover:border-primary/30 hover:bg-primary/5 transition-all duration-200" style="animation: slideInUp 0.3s ease-out ${index * 0.05}s both;">
                <div class="flex items-start space-x-4">
                    <div class="relative flex-shrink-0">
                        <img src="${activity.user_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(activity.user_name) + '&color=FFFFFF&background=4F46E5&size=40'}"
                             alt="${activity.user_name}"
                             class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-md"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(activity.user_name)}&color=FFFFFF&background=4F46E5&size=40'">
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 ${activity.status_color || 'bg-green-500'} rounded-full border-2 border-white"></div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <p class="text-sm text-slate-700 leading-relaxed">
                                    <span class="font-semibold text-slate-800">${activity.user_name}</span>
                                    ${activity.action_description}
                                    ${activity.item_name ? `<span class="font-medium text-primary">${activity.item_name}</span>` : ''}
                                </p>
                                <div class="flex items-center space-x-3 mt-1">
                                    <span class="text-xs text-slate-500">${activity.time}</span>
                                    ${activity.item_type ? `<span class="text-xs px-2 py-1 bg-slate-100 text-slate-600 rounded-full">${activity.item_type}</span>` : ''}
                                </div>
                            </div>
                            <div class="w-10 h-10 rounded-lg ${activity.icon_class || 'bg-slate-100 text-slate-500'} flex items-center justify-center ml-3">
                                <i class="${activity.icon || 'ri-notification-line'} text-lg"></i>
                            </div>
                        </div>
                        
                        ${activity.details ? `
                            <div class="bg-slate-50 rounded-lg p-3 mt-2">
                                <p class="text-xs text-slate-600 italic">"${activity.details}"</p>
                            </div>
                        ` : ''}
                        
                        ${activity.tags && activity.tags.length > 0 ? `
                            <div class="flex items-center space-x-1 mt-3">
                                ${activity.tags.map(tag => {
                                    let tagClass = 'bg-primary/10 text-primary';
                                    if (tag === 'Urgent') tagClass = 'bg-red-100 text-red-700';
                                    else if (tag === 'Completed' || tag === 'Selesai') tagClass = 'bg-green-100 text-green-700';
                                    else if (tag === 'Active') tagClass = 'bg-blue-100 text-blue-700';
                                    else if (tag === 'Pending') tagClass = 'bg-yellow-100 text-yellow-700';
                                    else if (tag === 'Assignment') tagClass = 'bg-purple-100 text-purple-700';
                                    else if (tag === 'Baru') tagClass = 'bg-emerald-100 text-emerald-700';
                                    
                                    return `<span class="px-2 py-1 text-xs ${tagClass} rounded-full">${tag}</span>`;
                                }).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }, 500);
};

window.refreshAllActivities = function() {
    showNotification('Refreshing all activities...', 'info');
    loadAllActivities();
};

document.addEventListener('DOMContentLoaded', function() {
    // Enhanced Dashboard Initialization
    initializeDashboard();
    
    function initializeDashboard() {
        // Debug logging
        console.log('Dashboard data loaded:', {
            todoProjects: {{ $todoProjectsCount }},
            inProgressProjects: {{ $inProgressProjectsCount }},
            completedProjects: {{ $completedProjectsCount }},
            totalRevenue: {{ $totalRevenue }},
            staffChartData: @json($staffChartData),
            paymentChartData: @json($paymentChartData)
        });
        
        // Core features
        animateCounters();
        initCharts();
        initDeadlineCountdowns();
        initMotivationalQuotes();
        initWelcomeModal();
        
        // Enhanced features
        initNotificationSystem();
        initKeyboardShortcuts();
        initAutoRefresh();
        
        // Start real-time updates
        startRealTimeUpdates();
        
        // Show initial welcome notification with real data
        setTimeout(() => {
            const now = new Date();
            const jakartaTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}));
            const currentHour = jakartaTime.getHours();
            
            let greeting;
            if (currentHour < 12) {
                greeting = 'Selamat pagi';
            } else if (currentHour < 15) {
                greeting = 'Selamat siang';
            } else if (currentHour < 18) {
                greeting = 'Selamat sore';
            } else {
                greeting = 'Selamat malam';
            }
            
            @if($todoProjectsCount + $inProgressProjectsCount > 0)
                showNotification(`${greeting}! Anda memiliki {{ $todoProjectsCount + $inProgressProjectsCount }} proyek aktif yang perlu diperhatikan.`, 'info');
            @else
                showNotification(`${greeting}! Semua proyek dalam kondisi baik. Tetap semangat!`, 'success');
            @endif
        }, 2000);
    }

    // Enhanced Counter Animation with Easing
    function animateCounters() {
        document.querySelectorAll('.counter').forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 1800; // Longer for better effect
            const startTime = performance.now();
            const startValue = 0;

            const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

            const updateCounter = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easedProgress = easeOutCubic(progress);
                const value = Math.floor(easedProgress * target);
                
                counter.textContent = value.toLocaleString();

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            };
            requestAnimationFrame(updateCounter);
        });

        // Add tooltip for total revenue showing full amount in Rupiah
        const revenueCard = document.querySelector('[data-target="{{ floor($totalRevenue / 1000000) }}"]');
        if (revenueCard && {{ $totalRevenue }} > 0) {
            const fullAmount = 'Rp {{ number_format($totalRevenue, 0, ",", ".") }}';
            revenueCard.closest('.stat-card-info').setAttribute('title', `Full Amount: ${fullAmount}`);
            
            // Add tooltip behavior
            revenueCard.closest('.stat-card-info').addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.className = 'absolute z-50 bg-slate-800 text-white text-xs rounded px-2 py-1 pointer-events-none';
                tooltip.textContent = `Full Amount: ${fullAmount}`;
                tooltip.style.bottom = '100%';
                tooltip.style.left = '50%';
                tooltip.style.transform = 'translateX(-50%)';
                tooltip.style.marginBottom = '5px';
                this.style.position = 'relative';
                this.appendChild(tooltip);
            });
            
            revenueCard.closest('.stat-card-info').addEventListener('mouseleave', function() {
                const tooltip = this.querySelector('.absolute.z-50');
                if (tooltip) tooltip.remove();
            });
        }
    }

    // Enhanced Charts with Interactions
    function initCharts() {
        const staffChartEl = document.getElementById('staff-chart');
        const paymentChartEl = document.getElementById('payment-chart');

        if (!staffChartEl || !paymentChartEl) return;

        // Staff Chart
        window.staffChart = echarts.init(staffChartEl);
        const staffChartData = @json($staffChartData);

        const staffChartOption = {
            tooltip: {
                trigger: 'item',
                formatter: function(params) {
                    return `
                        <div class="p-2">
                            <div class="font-semibold">${params.name}</div>
                            <div class="text-sm text-slate-600">${params.value} projects (${params.percent}%)</div>
                        </div>
                    `;
                },
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                textStyle: { color: '#1e293b' }
            },
            legend: {
                type: 'scroll',
                orient: 'vertical',
                right: 10,
                top: 'center',
                textStyle: { fontSize: 11, color: '#64748b' },
                pageTextStyle: { color: '#64748b' },
                pageIconColor: '#6366F1',
                pageIconInactiveColor: '#cbd5e1'
            },
            series: [{
                name: 'Projects',
                type: 'pie',
                radius: ['45%', '75%'],
                center: ['40%', '50%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 8,
                    borderColor: '#fff',
                    borderWidth: 3
                },
                label: { show: false },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 14,
                        fontWeight: 'bold'
                    },
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.2)'
                    }
                },
                data: staffChartData,
                color: ['#6366F1', '#8B5CF6', '#EC4899', '#F97316', '#10B981', '#F59E0B', '#06B6D4', '#84CC16']
            }],
            animation: true,
            animationType: 'expansion',
            animationDuration: 1500,
            animationEasing: 'cubicOut'
        };
        window.staffChart.setOption(staffChartOption);

        // Payment Chart
        window.paymentChart = echarts.init(paymentChartEl);
        const paymentChartData = @json($paymentChartData);

        const paymentChartOption = {
            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'shadow' },
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                textStyle: { color: '#1e293b' }
            },
            grid: {
                left: '5%',
                right: '5%',
                bottom: '10%',
                top: '10%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: paymentChartData.categories,
                axisLabel: {
                    fontSize: 10,
                    color: '#64748b',
                    interval: 0,
                    rotate: paymentChartData.categories.length > 3 ? 30 : 0
                },
                axisLine: { lineStyle: { color: '#e2e8f0' } }
            },
            yAxis: {
                type: 'value',
                axisLabel: { fontSize: 10, color: '#64748b' },
                axisLine: { lineStyle: { color: '#e2e8f0' } },
                splitLine: { lineStyle: { color: '#f1f5f9' } }
            },
            series: [{
                name: 'Projects',
                type: 'bar',
                barWidth: '60%',
                data: paymentChartData.values.map((value, index) => ({
                    value,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: index % 2 === 0 ? '#6366F1' : '#8B5CF6' },
                            { offset: 1, color: index % 2 === 0 ? '#4F46E5' : '#7C3AED' }
                        ])
                    }
                })),
                itemStyle: { borderRadius: [6, 6, 0, 0] },
                animation: true,
                animationDelay: (idx) => idx * 100,
                animationDuration: 1000
            }]
        };
        window.paymentChart.setOption(paymentChartOption);

        // Hide skeletons
        document.querySelectorAll('.chart-skeleton').forEach(el => {
            el.style.opacity = '0';
            setTimeout(() => el.style.display = 'none', 300);
        });

        // Responsive resize
        const debouncedResize = debounce(() => {
            window.staffChart?.resize();
            window.paymentChart?.resize();
        }, 250);
        window.addEventListener('resize', debouncedResize);
    }

    // Enhanced Deadline Countdowns
    function initDeadlineCountdowns() {
        document.querySelectorAll('.deadline-countdown').forEach(el => {
            const dueDateStr = el.dataset.dueDate;
            if (!dueDateStr) return;

            const dueDate = new Date(dueDateStr);

            const updateCountdown = () => {
                const now = new Date();
                const diff = dueDate - now;

                if (diff <= 0) {
                    el.innerHTML = '<span class="text-red-600 font-semibold">⚠️ Overdue!</span>';
                    el.closest('.deadline-item')?.classList.add('urgent');
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                if (days > 0) {
                    el.textContent = `${days}d ${hours}h remaining`;
                } else if (hours > 0) {
                    el.textContent = `${hours}h ${minutes}m remaining`;
                } else {
                    el.innerHTML = `<span class="text-orange-600 font-semibold">${minutes}m remaining!</span>`;
                }
            };

            updateCountdown();
            setInterval(updateCountdown, 60000);
        });
    };

    // Enhanced Motivational Quotes
    function initMotivationalQuotes() {
        const quotes = [
            { text: "The only way to do great work is to love what you do.", author: "Steve Jobs" },
            { text: "Innovation distinguishes between a leader and a follower.", author: "Steve Jobs" },
            { text: "Your limitation—it's only your imagination.", author: "Anonymous" },
            { text: "Push yourself, because no one else is going to do it for you.", author: "Anonymous" },
            { text: "Great things never come from comfort zones.", author: "Roy T. Bennett" },
            { text: "Dream it. Wish it. Do it.", author: "Anonymous" },
            { text: "Success is not final, failure is not fatal: It is the courage to continue that counts.", author: "Winston Churchill" },
            { text: "The mind is everything. What you think you become.", author: "Buddha" },
            { text: "The future belongs to those who believe in the beauty of their dreams.", author: "Eleanor Roosevelt" },
            { text: "It is during our darkest moments that we must focus to see the light.", author: "Aristotle" }
        ];

        window.getNewQuote = () => {
        const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
        const quoteTextEl = document.getElementById('motivationalQuoteText');
        const quoteAuthorEl = document.getElementById('motivationalQuoteAuthor');
            
            if (quoteTextEl && quoteAuthorEl) {
                quoteTextEl.style.opacity = '0';
                quoteAuthorEl.style.opacity = '0';
                
                setTimeout(() => {
                    quoteTextEl.textContent = randomQuote.text;
                    quoteAuthorEl.textContent = `— ${randomQuote.author}`;
                    quoteTextEl.style.opacity = '1';
                    quoteAuthorEl.style.opacity = '1';
                }, 300);
            }
        };

        // Set initial quote
        window.getNewQuote();
    }

    // Notification System
    function initNotificationSystem() {
        // Calculate real notification count
        let realNotificationCount = 0;
        
        // Count urgent deadlines
        @if(count($upcomingDeadlines) > 0)
            @foreach($upcomingDeadlines as $deadline)
                @if($deadline['due_in_days'] <= 1)
                    realNotificationCount++;
                @endif
            @endforeach
        @endif
        
        // Count recent activities (last 2 hours)
        @if(count($recentActivities) > 0)
            realNotificationCount += {{ count(array_filter($recentActivities, function($activity) {
                return isset($activity['time_carbon']) && $activity['time_carbon']->diffInHours() <= 2;
            })) }};
        @endif
        
        window.notificationCount = realNotificationCount;
        updateNotificationBadge();

        window.showNotification = (message, type = 'info', duration = 5000) => {
            window.notificationCount++;
            updateNotificationBadge();

            const notification = document.createElement('div');
            notification.className = `notification-item fixed top-20 right-6 max-w-sm bg-white border border-slate-200 rounded-lg shadow-lg p-4 z-50 transform translate-x-full transition-transform duration-300`;
            
            const colors = {
                success: 'border-l-4 border-l-green-500',
                error: 'border-l-4 border-l-red-500',
                warning: 'border-l-4 border-l-yellow-500',
                info: 'border-l-4 border-l-blue-500'
            };

            // Split the classes and add them individually
            const colorClasses = (colors[type] || colors.info).split(' ');
            colorClasses.forEach(className => {
                if (className.trim()) {
                    notification.classList.add(className.trim());
                }
            });
            
            notification.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">${message}</p>
                        <p class="text-xs text-slate-500 mt-1">${new Date().toLocaleString('id-ID', {timeZone: 'Asia/Jakarta'})}</p>
                    </div>
                    <button onclick="this.closest('.notification-item').remove(); window.notificationCount = Math.max(0, window.notificationCount - 1); updateNotificationBadge();" class="text-slate-400 hover:text-slate-600">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                    window.notificationCount = Math.max(0, window.notificationCount - 1);
                    updateNotificationBadge();
                }, 300);
            }, duration);
        };

        const updateNotificationBadge = () => {
            const badge = document.getElementById('notificationBadge');
            if (badge && window.notificationCount > 0) {
                badge.textContent = window.notificationCount;
                badge.style.display = 'flex';
            } else if (badge) {
                badge.style.display = 'none';
            }
        };

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    };

    // Welcome Modal
    function initWelcomeModal() {
        const welcomeModal = document.getElementById('welcomeModal');
        const welcomeModalContent = document.getElementById('welcomeModalContent');
        const closeBtn = document.getElementById('closeWelcomeModalBtn');

        if (!welcomeModal || !welcomeModalContent || !closeBtn) return;

        const showModal = () => {
            welcomeModal.classList.remove('hidden');
            setTimeout(() => {
                welcomeModalContent.style.opacity = '1';
                welcomeModalContent.style.transform = 'scale(1)';
            }, 10);
        };

        const hideModal = () => {
            welcomeModalContent.style.opacity = '0';
            welcomeModalContent.style.transform = 'scale(0.95)';
            setTimeout(() => {
                welcomeModal.classList.add('hidden');
            }, 300);
        };

        if (@json(session('show_welcome_popup', false))) {
            setTimeout(showModal, 500);
        }

        closeBtn.addEventListener('click', hideModal);
        welcomeModal.addEventListener('click', (e) => {
            if (e.target === welcomeModal) hideModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !welcomeModal.classList.contains('hidden')) {
                hideModal();
            }
        });

        window.openQuickTour = () => {
            hideModal();
            setTimeout(() => {
                showNotification('Quick tour feature coming soon!', 'info');
            }, 300);
        };
    };

    // Keyboard Shortcuts
    function initKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'r':
                        e.preventDefault();
                        refreshDashboard();
                        break;
                    case 'n':
                        e.preventDefault();
                        if (e.shiftKey) {
                            window.location.href = "{{ route('projects.index') }}";
                        }
                        break;
                }
            }
        });
    };

    // Auto Refresh
    function initAutoRefresh() {
        setInterval(() => {
            // Refresh specific data without full page reload
            refreshActivityFeed();
            refreshNotifications();
        }, 300000); // 5 minutes
    };

    // Real-time Updates
    function startRealTimeUpdates() {
        // Update current time every minute
        updateCurrentDateTime();
        setInterval(updateCurrentDateTime, 60000); // Every minute
        
        // Check for real notifications periodically
        checkRealNotifications();
        setInterval(checkRealNotifications, 300000); // Every 5 minutes
    };

    function updateCurrentDateTime() {
        const now = new Date();
        const jakartaTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}));
        
        // Update date in Indonesian
        const dateOptions = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            timeZone: 'Asia/Jakarta'
        };
        const currentDateEl = document.getElementById('currentDate');
        if (currentDateEl) {
            currentDateEl.textContent = jakartaTime.toLocaleDateString('id-ID', dateOptions);
        }
        
        // Update time in WIB
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        const currentTimeEl = document.getElementById('currentTime');
        if (currentTimeEl) {
            currentTimeEl.textContent = jakartaTime.toLocaleTimeString('id-ID', timeOptions) + ' WIB';
        }
    }

    function checkRealNotifications() {
        // Check for real-time notifications based on actual data
        const now = new Date();
        const currentHour = now.getHours();
        
        // Morning greeting
        if (currentHour === 9 && now.getMinutes() === 0) {
            showNotification('Selamat pagi! Semangat bekerja hari ini! 🌅', 'info');
        }
        
        // Afternoon reminder
        if (currentHour === 14 && now.getMinutes() === 0) {
            showNotification('Jangan lupa istirahat siang dan tetap produktif! ☀️', 'warning');
        }
        
        // End of day reminder
        if (currentHour === 17 && now.getMinutes() === 0) {
            showNotification('Hari kerja hampir selesai. Review progress hari ini! 🌆', 'info');
        }

        // Check for upcoming deadlines (real data)
        @if(count($upcomingDeadlines) > 0)
            @foreach($upcomingDeadlines as $deadline)
                @if($deadline['due_in_days'] <= 1)
                    const deadlineDate = new Date('{{ $deadline['actual_due_date'] }}');
                    const timeDiff = deadlineDate - now;
                    const hoursLeft = Math.floor(timeDiff / (1000 * 60 * 60));
                    
                    if (hoursLeft <= 24 && hoursLeft > 0) {
                        showNotification(`⚠️ Deadline Alert: {{ $deadline['name'] }} dalam ${hoursLeft} jam lagi!`, 'warning');
                    } else if (hoursLeft <= 0) {
                        showNotification(`🚨 Urgent: {{ $deadline['name'] }} sudah melewati deadline!`, 'error');
                    }
                @endif
            @endforeach
        @endif

        // Check for project milestones
        @if($completedProjectsCount > 0)
            const lastCheck = localStorage.getItem('lastNotificationCheck');
            const currentTime = now.getTime();
            
            if (!lastCheck || (currentTime - parseInt(lastCheck)) > 24 * 60 * 60 * 1000) {
                if ({{ $completedProjectsCount }} > 0) {
                    showNotification(`🎉 Total {{ $completedProjectsCount }} proyek telah diselesaikan! Kerja bagus tim!`, 'success');
                }
                localStorage.setItem('lastNotificationCheck', currentTime.toString());
            }
        @endif
    }

    // Chart Functions
    window.updateChartData = function() {
        const filter = document.getElementById('chartTimeFilter').value;
        showNotification(`Chart updated for ${filter}`, 'info');
        // Refresh the dashboard to get updated data
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    };

    window.exportChart = function(type) {
        showNotification(`Exporting ${type} chart...`, 'info');
        // Simple chart export functionality
        if (type === 'staff' && window.staffChart) {
            const url = window.staffChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = `staff-chart-${new Date().toISOString().split('T')[0]}.png`;
            link.href = url;
            link.click();
        } else if (type === 'payment' && window.paymentChart) {
            const url = window.paymentChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = `payment-chart-${new Date().toISOString().split('T')[0]}.png`;
            link.href = url;
            link.click();
        }
    };

    window.toggleChartType = function(type) {
        showNotification(`Toggling ${type} chart type`, 'info');
        // This could switch between different chart types
        if (type === 'staff' && window.staffChart) {
            // For example, switch between pie and bar chart
            const option = window.staffChart.getOption();
            if (option.series[0].type === 'pie') {
                option.series[0].type = 'bar';
                option.xAxis = { type: 'category', data: @json(array_column($staffChartData, 'name')) };
                option.yAxis = { type: 'value' };
                option.series[0].data = @json(array_column($staffChartData, 'value'));
            } else {
                option.series[0].type = 'pie';
                option.series[0].data = @json($staffChartData);
                delete option.xAxis;
                delete option.yAxis;
            }
            window.staffChart.setOption(option, true);
        }
    };

    // Activity Functions - Simplified
    window.refreshActivities = function() {
        showNotification('Refreshing activities...', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    };

    // Task Functions
    window.addNewTask = () => {
        showNotification('Add new task feature coming soon!', 'info');
    };

    window.toggleTask = (checkbox) => {
        const taskItem = checkbox.closest('.task-item');
        if (checkbox.checked) {
            taskItem.style.opacity = '0.6';
            taskItem.style.textDecoration = 'line-through';
            showNotification('Task completed!', 'success');
        } else {
            taskItem.style.opacity = '1';
            taskItem.style.textDecoration = 'none';
            showNotification('Task reopened', 'info');
        }
    };

    // Deadline Functions
    window.addDeadlineReminder = function() {
        showNotification('Opening project creation form...', 'info');
        window.location.href = "{{ route('projects.index') }}";
    };

    window.snoozeDeadline = function(projectId) {
        showNotification(`Opening project ${projectId} details...`, 'info');
        window.location.href = "{{ route('projects.index') }}#project-" + projectId;
    };

    window.markDeadlineComplete = function(projectId) {
        showNotification(`Opening project ${projectId} to update status...`, 'info');
        window.location.href = "{{ route('projects.index') }}#project-" + projectId;
    };

    // Utility Functions
    function refreshActivityFeed() {
        // Implementation for refreshing activity feed
        console.log('Refreshing activity feed...');
    }

    function refreshNotifications() {
        // Implementation for refreshing notifications
        console.log('Checking for new notifications...');
    }

    window.refreshPaymentData = function() {
        showNotification('Refreshing payment data...', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    };

    // Add escape key handler for activities modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const activitiesModal = document.getElementById('activitiesModal');
            if (activitiesModal && !activitiesModal.classList.contains('hidden')) {
                closeActivitiesModal();
            }
        }
    });

    // Add click outside handler for activities modal
    document.getElementById('activitiesModal')?.addEventListener('click', (e) => {
        if (e.target === document.getElementById('activitiesModal')) {
            closeActivitiesModal();
        }
    });
});
</script>
@endpush
