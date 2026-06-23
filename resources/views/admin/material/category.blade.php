@extends('layouts.admin') 

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Kategori Material Furnitur</h2>
        <a href="{{ route('material.category.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
    </div>
        
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 10%">No</th>
                            <th style="width: 40%">Nama Kategori</th>
                            <th style="width: 30%">Kelompok Material</th> 
                            <th style="width: 20%">Satuan Dasar</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop data dari controller --}}
                        @foreach ($categories as $index => $cat)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td>{{ $cat->nama_Kategori }}</td>
                            
                            <td>
                                <span class="badge {{ $cat->kelompok_material == 'Material Pokok' ? 'bg-primary' : 'bg-success' }}">
                                    {{ $cat->kelompok_material }}
                                </span>
                            </td>
                            
                            <td>{{ $cat->satuan_dasar }}</td>
                        </tr>
                        @endforeach
                        
                        @if($categories->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada data kategori.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection