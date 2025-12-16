<!DOCTYPE html>
<html>

<head>
    <title>Send Mail Using Queue</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: #ffffff;
            width: 400px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .success-msg {
            background: #e6fffa;
            color: #0f5132;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #badbcc;
        }

        input {
            width: 90%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            background: #667eea;
            color: #fff;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #5a67d8;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>Send Email</h2>

        @if(session('success'))
            <div class="success-msg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('send.mail') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Enter Name" required>
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="text" name="subject" placeholder="Enter Subject" required>
            <textarea name="message" placeholder="Enter Message" rows="4" style="width:90%;padding:10px;border-radius:6px;border:1px solid #ccc;margin-bottom:15px;"></textarea>
            <button type="submit">Send Mail</button>
        </form>
    </div>

</body>

</html>
