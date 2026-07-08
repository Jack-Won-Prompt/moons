<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('sellRequest.customer')->latest()->paginate(15);

        return view('admin.certificates.index', compact('certificates'));
    }
}
