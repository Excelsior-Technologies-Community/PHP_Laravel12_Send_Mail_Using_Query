{{-- resources/views/mail_history.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Email History</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .nav-bar {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .nav-bar a {
            color: #555;
            text-decoration: none;
            padding: 8px 16px;
            margin: 0 5px;
            border-radius: 4px;
        }
        
        .nav-bar a:hover {
            background: #f0f0f0;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 25px;
        }
        
        h1 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #e9ecef;
        }
        
        .stat-box h3 {
            font-size: 12px;
            font-weight: 500;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .stat-box .number {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filters input, .filters select {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .filters button, .reset-btn {
            padding: 8px 16px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .reset-btn {
            background: #6c757d;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
            color: #555;
        }
        
        td {
            font-size: 13px;
            color: #333;
        }
        
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .status-sent {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn-resend {
            background: #28a745;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
            margin-right: 5px;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
        }
        
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        
        .pagination nav {
            display: inline-block;
        }
        
        .empty-row td {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        
        .error-text {
            color: #dc3545;
            font-size: 11px;
            display: block;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="/mail">Send Email</a>
        <a href="/bulk-email">Bulk Email</a>
        <a href="/mail-history">History</a>
    </div>
    
    <div class="container">
        <h1>Email History</h1>
        
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif
        
        <div class="stats-grid">
            <div class="stat-box">
                <h3>Total</h3>
                <div class="number">{{ $stats['total'] }}</div>
            </div>
            <div class="stat-box">
                <h3>Sent</h3>
                <div class="number">{{ $stats['sent'] }}</div>
            </div>
            <div class="stat-box">
                <h3>Pending</h3>
                <div class="number">{{ $stats['pending'] }}</div>
            </div>
            <div class="stat-box">
                <h3>Failed</h3>
                <div class="number">{{ $stats['failed'] }}</div>
            </div>
        </div>
        
        <form method="GET" class="filters">
            <input type="text" name="search" placeholder="Search by name, email or subject" value="{{ request('search') }}">
            <select name="status">
                <option value="">All Status</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <input type="date" name="date_from" placeholder="From Date" value="{{ request('date_from') }}">
            <input type="date" name="date_to" placeholder="To Date" value="{{ request('date_to') }}">
            <button type="submit">Filter</button>
            <a href="{{ route('mail.history') }}" class="reset-btn">Reset</a>
        </form>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Sent Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($emails as $email)
                <tr>
                    <td>{{ $email->id }}</td>
                    <td>{{ $email->name }}</td>
                    <td>{{ $email->email }}</td>
                    <td>{{ $email->subject }}</td>
                    <td>
                        <span class="status status-{{ $email->status }}">
                            {{ ucfirst($email->status) }}
                        </span>
                        @if($email->error_message)
                            <div class="error-text">{{ substr($email->error_message, 0, 50) }}</div>
                        @endif
                    </td>
                    <td>{{ $email->sent_at ? date('Y-m-d H:i', strtotime($email->sent_at)) : '-' }}</td>
                    <td>
                        <form action="{{ route('mail.resend', $email->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-resend">Resend</button>
                        </form>
                        <form action="{{ route('mail.delete', $email->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this record?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="7">No emails found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if(method_exists($emails, 'links'))
            <div class="pagination">
                {{ $emails->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</body>
</html>