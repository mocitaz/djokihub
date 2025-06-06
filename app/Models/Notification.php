<?php

// File: app/Models/Notification.php
namespace App\Models;

// Jika menggunakan sistem notifikasi default Laravel, modelnya adalah:
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    // Laravel menangani properti seperti $incrementing dan $keyType untuk DatabaseNotification
}