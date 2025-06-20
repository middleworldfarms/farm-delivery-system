@extends('layouts.app')

@section('title', 'Delivery Schedule Management')

@section('content')
<div class="container">
    <h1>Delivery Schedule Management</h1>
    <p>Real-time delivery data from WooCommerce</p>
    
    {{-- API Status --}}
    @if(isset($api_test))
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert {{ $api_test['connection']['success'] ? 'alert-success' : 'alert-danger' }}">
                    <strong>API Connection:</strong> {{ $api_test['connection']['success'] ? 'Connected' : 'Failed' }}
                    @if($api_test['connection']['success'])
                        <br><small>{{ $api_test['connection']['message'] ?? '' }}</small>
                    @endif
                </div>
            </div>
            @if(isset($api_test['auth']))
            <div class="col-md-6">
                <div class="alert {{ $api_test['auth']['success'] ? 'alert-success' : 'alert-danger' }}">
                    <strong>Authentication:</strong> {{ $api_test['auth']['success'] ? 'Authenticated' : 'Failed' }}
                </div>
            </div>
            @endif
        </div>
    @endif
    
    {{-- Error Display --}}
    @if(isset($error) && $error)
        <div class="alert alert-danger">
            <strong>Error:</strong> {{ $error }}
        </div>
    @endif

    {{-- User Search Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-search"></i> Quick User Search & Switch</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" id="userSearch" class="form-control" placeholder="Search for customers by name or email...">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.users.test') }}" class="btn btn-outline-info" target="_blank">
                        <i class="fas fa-flask"></i> Test API Connection
                    </a>
                </div>
            </div>
            <div id="searchResults" class="mt-3" style="display: none;">
                <h6>Search Results:</h6>
                <div id="userList"></div>
            </div>
        </div>
    </div>
    
    {{-- Schedule Data --}}
    @if(isset($scheduleData) && $scheduleData)
        @if(isset($scheduleData['success']) && $scheduleData['success'] && isset($scheduleData['data']))
            @php
                $totalDeliveries = 0;
                $totalCollections = 0;
                $currentWeek = date('W');
                $currentWeekType = ($currentWeek % 2 === 0) ? 'A' : 'B';
                foreach($scheduleData['data'] as $dateData) {
                    $totalDeliveries += count($dateData['deliveries'] ?? []);
                    $totalCollections += count($dateData['collections'] ?? []);
                }
            @endphp
            
            {{-- Week Information Banner --}}
            <div class="alert alert-info mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-1">📅 Current Week Information</h5>
                        <p class="mb-0">
                            <strong>Week {{ $currentWeek }} of {{ date('Y') }}</strong> - 
                            <span class="badge bg-{{ $currentWeekType === 'A' ? 'success' : 'warning' }} ms-1">Week {{ $currentWeekType }}</span>
                        </p>
                        <small class="text-muted">
                            Even weeks = Week A (Fortnightly deliveries) | Odd weeks = Week B (Skip fortnightly)
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-primary">{{ $totalDeliveries }} deliveries</span>
                        <span class="badge bg-success ms-1">{{ $totalCollections }} collections</span>
                    </div>
                </div>
            </div>
            
            <div class="card">`
                <div class="card-header">
                    <h3>Schedule Management 
                        <small class="text-muted">({{ $totalDeliveries }} deliveries, {{ $totalCollections }} collections)</small>
                    </h3>
                    
                    {{-- Navigation Tabs --}}
                    <ul class="nav nav-tabs mt-3" id="scheduleTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                                📋 All ({{ $totalDeliveries + $totalCollections }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="deliveries-tab" data-bs-toggle="tab" data-bs-target="#deliveries" type="button" role="tab" aria-controls="deliveries" aria-selected="false">
                                🚚 Deliveries ({{ $totalDeliveries }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="collections-tab" data-bs-toggle="tab" data-bs-target="#collections" type="button" role="tab" aria-controls="collections" aria-selected="false">
                                📦 Collections ({{ $totalCollections }})
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="scheduleTabContent">
                    <div class="tab-content" id="scheduleTabContent">
                        {{-- All Tab --}}
                        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                            @if($totalDeliveries + $totalCollections > 0)
                                @foreach($scheduleData['data'] as $date => $dateData)
                                    @if(count($dateData['deliveries'] ?? []) > 0 || count($dateData['collections'] ?? []) > 0)
                                        <h4 class="mt-3 mb-3">{{ $dateData['date_formatted'] ?? $date }}</h4>
                                        
                                        {{-- Deliveries for this date --}}
                                        @if(count($dateData['deliveries'] ?? []) > 0)
                                            <h5 class="text-primary">🚚 Deliveries ({{ count($dateData['deliveries']) }})</h5>
                                            @include('admin.deliveries.partials.delivery-table', ['items' => $dateData['deliveries'], 'type' => 'delivery'])
                                        @endif
                                        
                                        {{-- Collections for this date --}}
                                        @if(count($dateData['collections'] ?? []) > 0)
                                            <h5 class="text-success">📦 Collections ({{ count($dateData['collections']) }})</h5>
                                            @include('admin.deliveries.partials.collection-table', ['items' => $dateData['collections'], 'type' => 'collection'])
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No deliveries or collections scheduled for the current period.
                                </div>
                            @endif
                        </div>
                        
                        {{-- Deliveries Only Tab --}}
                        <div class="tab-pane fade" id="deliveries" role="tabpanel" aria-labelledby="deliveries-tab">
                            @if($totalDeliveries > 0)
                                @foreach($scheduleData['data'] as $date => $dateData)
                                    @if(count($dateData['deliveries'] ?? []) > 0)
                                        <h4 class="mt-3 mb-3">{{ $dateData['date_formatted'] ?? $date }}</h4>
                                        @include('admin.deliveries.partials.delivery-table', ['items' => $dateData['deliveries'], 'type' => 'delivery'])
                                    @endif
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-truck"></i> No deliveries scheduled for the current period.
                                </div>
                            @endif
                        </div>
                        
                        {{-- Collections Only Tab --}}
                        <div class="tab-pane fade" id="collections" role="tabpanel" aria-labelledby="collections-tab">
                            @if($totalCollections > 0)
                                @foreach($scheduleData['data'] as $date => $dateData)
                                    @if(count($dateData['collections'] ?? []) > 0)
                                        <h4 class="mt-3 mb-3">{{ $dateData['date_formatted'] ?? $date }}</h4>
                                        @include('admin.deliveries.partials.collection-table', ['items' => $dateData['collections'], 'type' => 'collection'])
                                    @endif
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-box"></i> No collections scheduled for the current period.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <strong>Schedule Data Debug:</strong> 
                <pre>{{ json_encode($scheduleData, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No schedule data available.
        </div>
    @endif
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const searchBtn = document.getElementById('searchBtn');
    const searchResults = document.getElementById('searchResults');
    const userList = document.getElementById('userList');

    function performSearch() {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        // Show loading state
        userList.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
        searchResults.style.display = 'block';

        // Perform AJAX search
        fetch(`{{ route('admin.users.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.users && data.users.length > 0) {
                    let html = '';
                    data.users.forEach(user => {
                        html += `
                            <div class="card mb-2">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <strong>${user.name || 'N/A'}</strong>
                                            <br><small class="text-muted">${user.email || 'No email'}</small>
                                            <br><small class="text-muted">ID: ${user.id}</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <a href="{{ route('admin.users.switch', ['userId' => ':userId']) }}".replace(':userId', user.id) 
                                               class="btn btn-sm btn-primary" 
                                               target="_blank">
                                                <i class="fas fa-user-circle"></i> Switch to User
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    userList.innerHTML = html;
                } else {
                    userList.innerHTML = '<div class="alert alert-info">No users found matching your search.</div>';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                userList.innerHTML = '<div class="alert alert-danger">Error performing search. Please try again.</div>';
            });
    }

    // Search on button click
    searchBtn.addEventListener('click', performSearch);

    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Hide results when input is cleared
    searchInput.addEventListener('input', function() {
        if (this.value.trim().length === 0) {
            searchResults.style.display = 'none';
        }
    });
});
</script>
@endsection
@endsection
