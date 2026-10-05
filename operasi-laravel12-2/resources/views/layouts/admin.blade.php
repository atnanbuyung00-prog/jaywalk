<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','Admin') - RS Mutiara Aini</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<style>
:root{--rs-blue:#07558e;--rs-blue2:#0d6da9;--rs-light:#eef8fc}body{background:#f4f7fa}.sidebar{min-height:100vh;background:linear-gradient(180deg,#064f85,#073c66);color:#fff}.sidebar a{color:#dbeefe;text-decoration:none;border-radius:10px;padding:.65rem .8rem;display:block}.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,.13);color:#fff}.brand{font-weight:800;letter-spacing:.4px}.topbar{background:#fff;border-bottom:1px solid #e7edf2}.card{border:0;box-shadow:0 8px 25px rgba(18,49,74,.07)}.btn-rs{background:var(--rs-blue);color:#fff}.btn-rs:hover{background:#064574;color:#fff}.stat{border-left:5px solid var(--rs-blue)}.table thead th{background:var(--rs-blue);color:#fff;white-space:nowrap}.form-control,.form-select{border-radius:9px}.small-muted{font-size:.8rem;color:#6c757d}
</style>
@stack('styles')
</head>
<body>
<div class="container-fluid"><div class="row">
<aside class="col-lg-2 col-md-3 sidebar p-3">
<div class="d-flex align-items-center gap-2 mb-4"><img src="{{ asset('images/logo-rsma.png') }}" style="width:58px;height:auto"><div class="brand">RS Mutiara Aini<br><small class="fw-normal">Operasi</small></div></div>
<nav class="d-grid gap-1">
<a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
@if(auth()->user()->canAccess('schedule.view'))<a href="{{ route('admin.schedules.index') }}"><i class="bi bi-calendar3 me-2"></i>Jadwal Operasi</a>@endif
@if(auth()->user()->canAccess('master.manage'))
<div class="text-uppercase small opacity-50 mt-3 mb-1">Data Master</div>
<a href="{{ route('admin.masters.index','patients') }}"><i class="bi bi-person-vcard me-2"></i>Pasien</a><a href="{{ route('admin.masters.index','operators') }}"><i class="bi bi-person-badge me-2"></i>Operator</a><a href="{{ route('admin.masters.index','anesthesias') }}"><i class="bi bi-heart-pulse me-2"></i>Anestesi</a><a href="{{ route('admin.masters.index','teams') }}"><i class="bi bi-people me-2"></i>Tim Bedah</a>
@endif
@if(auth()->user()->canAccess('shift.manage'))<a href="{{ route('admin.shifts.index') }}"><i class="bi bi-clock-history me-2"></i>Atur Shift</a>@endif
@if(auth()->user()->canAccess('user.manage'))<a href="{{ route('admin.users.index') }}"><i class="bi bi-shield-lock me-2"></i>User & Akses</a>@endif
<a href="{{ route('display') }}" target="_blank"><i class="bi bi-tv me-2"></i>Display TV</a>
</nav>
<div class="mt-5 pt-5 small opacity-75">Login: <b>{{ auth()->user()->username }}</b><br>Laravel 12</div>
</aside>
<main class="col-lg-10 col-md-9 p-0">
<header class="topbar px-4 py-3 d-flex justify-content-between align-items-center"><div><h5 class="mb-0">@yield('title','Dashboard')</h5><small class="text-muted">Sistem Jadwal Operasi</small></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right"></i> Keluar</button></form></header>
<div class="p-4">@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif @yield('content')</div>
</main></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
@stack('scripts')
</body></html>
