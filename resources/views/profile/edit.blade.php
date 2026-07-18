@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        {{-- FOTO PROFIL --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Foto Profil</h4>
            </div>
            <div class="card-body">
                @include('profile.partials.update-avatar-form')
            </div>
        </div>

        {{-- INFORMASI PROFIL --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Informasi Profil</h4>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- GANTI PASSWORD --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Ubah Kata Sandi</h4>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- HAPUS AKUN --}}
        <div class="card shadow-sm mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Hapus Akun</h4>
            </div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</div>
@endsection