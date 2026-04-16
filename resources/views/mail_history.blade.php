<!DOCTYPE html>
<html>
<head>
    <title>Email History</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .card {
            border-radius: 10px;
        }

        .table th {
            background: #667eea;
            color: white;
        }

        .pagination {
            justify-content: center;
        }
    </style>
</head>

<body>

<div class="container py-5">

    <div class="card shadow">
        <div class="card-body">

            <h2 class="text-center mb-4">📧 Email History</h2>

            @if(session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($emails as $email)
                        <tr>
                            <td>{{ $email->id }}</td>
                            <td>{{ $email->name }}</td>
                            <td>{{ $email->email }}</td>
                            <td>{{ $email->subject }}</td>
                            <td>{{ $email->message }}</td>
                            <td>{{ $email->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                <form action="{{ route('mail.resend', $email->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        🔁 Resend
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $emails->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

</body>
</html>