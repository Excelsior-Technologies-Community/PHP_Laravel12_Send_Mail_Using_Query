{{-- resources/views/bulk_email_form.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Bulk Email</title>
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
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 25px;
        }
        
        h1 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .subtitle {
            color: #666;
            font-size: 13px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
        
        .recipients-section {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            background: #fafafa;
        }
        
        .recipients-section h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #555;
        }
        
        .recipient-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .recipient-row input {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
            font-size: 13px;
        }
        
        input, textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            font-family: inherit;
        }
        
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        button[type="submit"] {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        
        button[type="submit"]:hover {
            background: #3a7bc8;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
            font-size: 13px;
        }
        
        .back-link:hover {
            color: #333;
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
        <h1>Bulk Email</h1>
        <p class="subtitle">Send emails to multiple recipients</p>
        
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        
        <form action="{{ route('bulk.send') }}" method="POST">
            @csrf
            
            <div class="recipients-section">
                <h3>Recipients</h3>
                <div id="recipients-container">
                    <div class="recipient-row">
                        <input type="text" name="recipients[0][name]" placeholder="Full Name" required>
                        <input type="email" name="recipients[0][email]" placeholder="Email Address" required>
                        <button type="button" class="btn-remove" onclick="removeRow(this)">Remove</button>
                    </div>
                </div>
                <button type="button" class="btn-add" onclick="addRecipient()">+ Add Recipient</button>
            </div>
            
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" required>
            </div>
            
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" required placeholder="Enter your message here..."></textarea>
            </div>
            
            <button type="submit">Send to All Recipients</button>
        </form>
        
        <a href="/mail" class="back-link">← Back to Single Email</a>
    </div>
    
    <script>
        let recipientCount = 1;
        
        function addRecipient() {
            const container = document.getElementById('recipients-container');
            const newRow = document.createElement('div');
            newRow.className = 'recipient-row';
            newRow.innerHTML = `
                <input type="text" name="recipients[${recipientCount}][name]" placeholder="Full Name" required>
                <input type="email" name="recipients[${recipientCount}][email]" placeholder="Email Address" required>
                <button type="button" class="btn-remove" onclick="removeRow(this)">Remove</button>
            `;
            container.appendChild(newRow);
            recipientCount++;
        }
        
        function removeRow(button) {
            const rows = document.querySelectorAll('.recipient-row');
            if (rows.length > 1) {
                button.closest('.recipient-row').remove();
            } else {
                alert('At least one recipient is required');
            }
        }
    </script>
</body>
</html>