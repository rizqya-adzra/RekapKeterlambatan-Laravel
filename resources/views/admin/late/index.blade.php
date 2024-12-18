@extends('template.app', ['title' => 'Rekap Keterlambatan'])

@section('konten-dinamis')
<section class="container mt-2" style="width: 80%">
    <div class="d-flex justify-content-around align-items-center mb-4">
        <div>
            <h1 class="text-prior">Data Keterlambatan</h1>
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Late</li>
                </ol>
              </nav>
        </div>
        <div class="d-flex" style="gap: 20px">
            <form action="" class="d-flex" style="gap: 7px">
                <input class="form-control border-search " type="date" placeholder="cari berdasarkan tanggal"
                    name="search" aria-label="Search">
                <button class="btn btn-search" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                <button class="btn btn-outline-secondary" type="submit">Clear</button>
            </form>
        </div>
        <a class="btn-prior" href=" {{ route('late.create') }} ">Tambah +</a>
    </div>
    @if (Session::get('success'))
        <div class="alert alert-success"> {{ Session::get('success') }} </div>
    @endif
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" id="keseluruhan-tab" data-bs-toggle="tab" href="#keseluruhan">Keseluruhan Data</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="rekapitulasi-tab" data-bs-toggle="tab" href="#rekapitulasi">Rekapitulasi Data</a>
        </li>
    </ul>
    
    <div class="tab-content">
        <div class="tab-pane fade show active" id="keseluruhan">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nis</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Keterangan Keterlambatan</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($late as $index => $item)
                    <tr>
                        <td> {{ ($late->currentPage() - 1) * $late->perPage() + ($index + 1) }} </td>
                        <td> {{ $item->student?->nis ?? 'Data tidak tersedia' }} </td>
                        <td> {{ $item->student?->name ?? 'Data tidak tersedia' }} </td>
                        <td> {{ $item->date_time_late }} </td>
                        <td> {{ $item->information }} </td>
                        <td>
                            <img style="width: 150px" src="{{ asset('storage/' . $item['bukti']) }}" alt="Bukti">
                        </td>
                        <td>
                            <a class="btn-edit" href="{{ route('rayon.edit', $item->id) }}">Edit</a>
                            <button class="btn-delete" onclick="deleteModal('{{ $item->id }}', '{{ $item->student->name }}')">Hapus</button>
                        </td>
                    </tr>
                    @endforeach        
                </tbody>
            </table>
        </div>
    
        <div class="tab-pane fade" id="rekapitulasi">
            <div class="text-center">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nis</th>
                            <th>Nama</th>
                            <th>Total Keterlambatan</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedByStudent = $late->groupBy(function($item) {
                                return $item->student?->nis; 
                            });
                        @endphp
    
                        @foreach ($groupedByStudent as $nis => $lateness)
                            <tr>
                                <td> {{ ($late->currentPage() - 1) * $late->perPage() + $loop->iteration }} </td>
                                <td>{{ $nis }}</td>
                                <td>{{ $lateness->first()->student?->name ?? 'Data tidak tersedia' }}</td>
                                <td class="{{$lateness->count() >= 3 ? 'text-danger' : ''}}">{{ $lateness->count() }}</td>
                                <td><a href="{{ route('late.show', $lateness->first()->student_id) }}"> Lihat </a></td>
                                <td>
                                    @if ($lateness->count() >= 3)
                                        <a class="btn btn-primary" href="{{ route('late.download',$lateness->first()->id ) }}"> Cetak Surat Pernyataan </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" id="form-delete-late" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Hapus data Akun</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    apakah anda yakin akan menghapus data Keterlambatan <span id="student_id"
                        style="font-weight: bolder"></span> ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Tetap Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
    function deleteModal(id, studentName) {
        let action = '{{ route('late.delete', ':id') }}';
        action = action.replace(':id', id);
        $('#form-delete-late').attr('action', action);
        $('#deleteModal').modal('show');
        $('#student_id').text(studentName); 
    }
    </script>

@endpush