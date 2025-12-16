# PHP_Laravel12_Send_Mail_Using_Query

This project demonstrates how to send emails using **Laravel 12 queues** and store sent emails in the database. It includes a simple form to send emails and saves each email record in the `email_lists` table.

---

## Table of Contents

Table of Contents

1)Prerequisites

2)Project Setup

3)Database Setup

4)Create Migration & Model

5)Create Mail Class & View

6)Create Controller

7)Setup Routes

8)Create Send Mail Form

9)Queue Setup

10)Configure Mail Settings

11)Running the Project

12)Testing

13)Email Flow Diagram

---

## Prerequisites

Before starting, make sure you have:

* PHP >= 8.1
* Composer
* MySQL
* Laravel 12
* Mailtrap or any SMTP email account for testing

---

## Project Setup

### Step 1 — Create New Laravel Project

Open your terminal and run:

```
composer create-project laravel/laravel PHP_Laravel12_Send_Mail_Using_Query "12.*"
```

Navigate to the project folder:

```
cd PHP_Laravel12_Send_Mail_Using_Query
```

This will create a fresh Laravel 12 project named `PHP_Laravel12_Send_Mail_Using_Query`.

---

## Database Setup

### Step 2 — Configure Database

Open the `.env` file and update the database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=send_mail_using_query
DB_USERNAME=root
DB_PASSWORD=
```

Create the database in MySQL:

```
CREATE DATABASE send_mail_using_query;
```

---

## Create Migration & Model

### Step 3 — Create Migration

Run the command:

```
php artisan make:migration create_email_lists_table --create=email_lists
```

Update the migration file `database/migrations/xxxx_create_email_lists_table.php`:

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_create_email_lists_table.php

public function up()
{
    Schema::create('email_lists', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('subject')->nullable();
        $table->text('message')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_lists');
    }
};

```

Run migration:

```
php artisan migrate
```

This will create the `email_lists` table to store emails.

---

### Step 4 — Create Model

```
php artisan make:model EmailList
```

Update `app/Models/EmailList.php`:

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailList extends Model
{
    use HasFactory;
    protected $fillable = ['name','email','subject','message'];
}
```

> The `$fillable` array allows mass assignment of these fields when storing email records.

---

## Create Mail Class & View

### Step 5 — Create Mailable Class

Run:

```
php artisan make:mail QueryMail
```

Update `app/Mail/QueryMail.php`:

```
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QueryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject($this->details['subject'])
                    ->view('emails.queryMail');
    }
}
```

> `ShouldQueue` ensures emails are sent **asynchronously** using Laravel queues.

---

## Create Blade Templates

### Step 6 — Email Blade Template

Create `resources/views/emails/queryMail.blade.php`:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $details['subject'] }}</title>
    <style>
        /* General reset */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 22px;
        }

        .body {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }

        .body h2 {
            color: #333333;
            margin-bottom: 15px;
        }

        .body p {
            margin-bottom: 15px;
        }

        .footer {
            background-color: #f4f4f7;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888888;
        }

        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white !important;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        @media screen and (max-width: 600px) {
            .container {
                width: 90% !important;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{ $details['subject'] }}
        </div>
        <div class="body">
            <h2>Hello {{ $details['name'] }},</h2>
            <p>{{ $details['message'] }}</p>
            <p>Thank you!</p>
            <a href="#" class="button">Visit Our Website</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </div>
    </div>
</body>
</html>
```

> This template formats the email content neatly with a header, body, and footer.

---

## Create Controller

### Step 7 — Create MailController

```
php artisan make:controller MailController
```

Update `app/Http/Controllers/MailController.php`:

```
<?php

// app/Http/Controllers/MailController.php

namespace App\Http\Controllers;

use App\Models\EmailList;
use App\Mail\QueryMail;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function index()
    {
        return view('send_mail_form');
    }

public function sendMail(Request $request)
{
    // Validate the form data
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    $details = [
        'name' => $request->name,
        'subject' => $request->subject,
        'message' => $request->message,
    ];

    // Store email in DB
    EmailList::create([
        'name' => $request->name,
        'email' => $request->email,
        'subject' => $request->subject,
        'message' => $request->message,
    ]);

    // Send email using queue
    Mail::to($request->email)->queue(new QueryMail($details));

    return back()->with('success', 'Email sent successfully to ' . $request->email);
}

}

```

> Emails are queued and stored in the database simultaneously.

---

## Setup Routes

### Step 8 — Define Routes

Update `routes/web.php`:

```
<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', [MailController::class, 'index']);
Route::post('/send-mail', [MailController::class, 'sendMail'])->name('send.mail');
```

---

## Create Send Mail Form

### Step 9 — Blade Form

Create `resources/views/send_mail_form.blade.php`:

```
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
```

> Simple, responsive form for sending emails.

---

## Configure Mail Settings

### Step 10 — Update `.env`

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

> Mailtrap is recommended for testing to avoid sending real emails.

---

## Queue Setup

### Step 11 — Configure Queue

Create queue table:

```
php artisan queue:table
php artisan migrate
```

Run the queue worker:

```
php artisan queue:work
```

> The queue worker listens for queued jobs and sends emails asynchronously.
> **Note:** If the migration already exists, you may skip `queue:table`.


You will see layout as like bellow if queue is works:

<img width="1432" height="592" alt="image" src="https://github.com/user-attachments/assets/407f5a5d-15cb-40b6-a220-35eb70df7309" />



---


## Running the Project

### Step 12 — Start Laravel Server

```bash
php artisan serve
```

Open browser:
```
http://127.0.0.1:8000/mail
```
Fill the form and click **Send Mail**. Emails will be queued and sent asynchronously.

---


Mail-form Page:


<img width="1919" height="954" alt="Screenshot 2025-12-16 114251" src="https://github.com/user-attachments/assets/52fcca3b-d21d-4b92-80dd-f50f0d434794" />

fill form :

<img width="1917" height="966" alt="Screenshot 2025-12-16 114551" src="https://github.com/user-attachments/assets/5133a25e-46c8-4203-8db5-7698446e991f" />


After submit mail show message:

<img width="1913" height="957" alt="Screenshot 2025-12-16 114633" src="https://github.com/user-attachments/assets/cef03a41-d7d9-4961-bfbd-93254bdf6950" />


You can see in Gmail Output:

<img width="1845" height="970" alt="Screenshot 2025-12-16 115240" src="https://github.com/user-attachments/assets/04b16f76-b361-4c57-9c8d-35a02df71991" />


## Testing

1. Fill the form with test data.
2. Check the `email_lists` table in the database for stored emails.
3. Ensure the queue worker is running (`php artisan queue:work`) to process emails.
4. Check Mailtrap or your SMTP inbox for the sent email.

---

## Email Flow Diagram

```
[User submits form]
        ↓
[Store in email_lists table]
        ↓
[Queue job created]
        ↓
[Queue worker sends email]
        ↓
[Email delivered]
```
