<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Inquiry;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('contact', ['title' => 'Contact Us']);
    }

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('contact');
        }

        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $phone   = trim($_POST['phone']   ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$name || !$email || !$message) {
            $this->flash('error', 'Please fill in all required fields.');
            $this->redirect('contact');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect('contact');
        }

        $inquiry = new Inquiry();
        $inquiry->create([
            ':property_id' => null,
            ':name'        => $name,
            ':email'       => $email,
            ':phone'       => $phone,
            ':message'     => $message,
        ]);

        $this->flash('success', 'Your message has been sent! We\'ll be in touch within 24 hours.');
        $this->redirect('contact');
    }
}
