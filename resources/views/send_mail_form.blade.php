<!DOCTYPE html>
<html>
<head>
    <title>Send Mail</title>
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
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
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

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #999;
        }

        button {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background: #3a7bc8;
        }

        small {
            color: #888;
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }

        .stats-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .stats-item:last-child {
            border-bottom: none;
        }

        .stats-label {
            color: #666;
        }

        .stats-value {
            font-weight: 600;
            color: #333;
        }

        .template-select {
            margin-bottom: 15px;
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
        <div class="card">
            <h2>Send Email</h2>

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            
            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('send.mail') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if(isset($templates) && count($templates) > 0)
                <div class="form-group template-select">
                    <label>Load Template</label>
                    <select id="template-select">
                        <option value="">-- Select Template --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                
                <div class="form-group">
                    <label>Subject *</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required>
                </div>
                
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" id="message" rows="5" required>{{ old('message') }}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Attachments</label>
                    <input type="file" name="attachments[]" multiple>
                    <small>Max 5MB per file. Multiple files allowed.</small>
                </div>
                
                <div class="form-group">
                    <label>Schedule Date</label>
                    <input type="datetime-local" name="schedule_date">
                    <small>Leave empty to send immediately</small>
                </div>
                
                <button type="submit">Send Email</button>
            </form>
        </div>

        <div class="card">
            <h2>Statistics</h2>
            <div id="stats">
                <div class="stats-item">
                    <span class="stats-label">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load template
        const templateSelect = document.getElementById('template-select');
        if (templateSelect) {
            templateSelect.addEventListener('change', function() {
                if (this.value) {
                    fetch('/get-template/' + this.value)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('subject').value = data.subject;
                            document.getElementById('message').value = data.content;
                        });
                }
            });
        }
        
        // Load stats
        function loadStats() {
            fetch('/email-stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('stats').innerHTML = `
                        <div class="stats-item">
                            <span class="stats-label">Total Emails</span>
                            <span class="stats-value">${data.total}</span>
                        </div>
                        <div class="stats-item">
                            <span class="stats-label">Sent</span>
                            <span class="stats-value">${data.sent}</span>
                        </div>
                        <div class="stats-item">
                            <span class="stats-label">Pending</span>
                            <span class="stats-value">${data.pending}</span>
                        </div>
                        <div class="stats-item">
                            <span class="stats-label">Failed</span>
                            <span class="stats-value">${data.failed}</span>
                        </div>
                        <div class="stats-item">
                            <span class="stats-label">Last 7 Days</span>
                            <span class="stats-value">${data.last_7_days}</span>
                        </div>
                    `;
                });
        }
        
        loadStats();
        setInterval(loadStats, 30000);
    </script>
</body>
</html>