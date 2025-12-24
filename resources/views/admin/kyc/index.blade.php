@extends('admin.layout')
<style>
    /* ---- TABLE STYLING ---- */

    /* Container */
    .table-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px;
        border: 1px solid #e6e6e6;
    }

    /* Dark */
    body.dark-mode .table-card {
        background: #1f2937;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Table wrapper */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    /* Table header */
    .table-modern thead tr th {
        background: transparent;
        font-weight: 600;
        padding: 10px 15px;
        color: #555;
    }

    /* DARK HEADERS */
    body.dark-mode .table-modern thead tr th {
        color: #9ca3af;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Table rows */
    .table-modern tbody tr {
        background: #f8f9fa;
        transition: 0.2s;
        border-radius: 12px;
    }

    .table-modern tbody tr:hover {
        background: #eef1f4;
    }

    /* DARK ROWS */
    body.dark-mode .table-modern tbody tr {
        background: rgba(255, 255, 255, 0.03);
    }

    body.dark-mode .table-modern tbody tr:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    /* Table cells */
    .table-modern tbody td {
        padding: 16px;
        color: #333;
    }

    /* DARK CELL TEXT */
    body.dark-mode .table-modern tbody td {
        color: #e5e7eb;
    }

    /* Badges */
    .badge-modern {
        border-radius: 10px;
        font-size: 12px;
        padding: 6px 10px;
    }

    .badge-pending {
        background: #ffca2c;
        color: #332a00;
    }

    .badge-none {
        background: #6c757d;
    }

    .badge-approved {
        background: #198754;
    }

    /* SEARCH BOX */
    .search-box {
        background: #ffffff;
        border: 1px solid #ddd;
        color: #333;
    }

    body.dark-mode .search-box {
        background: #1f2937;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
    }
</style>
@section('content')

   {{-- success message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    
    <h1 class="fw-bold text-primary mb-4">Validation KYC</h1>

    <div class="table-card shadow-lg">

        <table class="table-modern">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Utilisateur</th>
                    <th>Téléphone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($kycs as $k)
                    <tr>
                        <td>{{ $k->user->obp_id }}</td>
                        <td>{{ $k->user->name }}</td>
                        <td>{{ $k->user->phone }}</td>

                        <td>
                            @if ($k->status === 'approved')
                                <span class="badge-modern badge-approved">Validé</span>
                            @elseif($k->status === 'pending')
                                <span class="badge-modern badge-pending">En attente</span>
                            @else
                                <span class="badge-modern badge-none">Rejeté</span>
                            @endif
                        </td>

                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#kycModal-{{ $k->id }}">
                                Voir
                            </button>

                        </td>
                    </tr>


                    <div class="modal fade" id="kycModal-{{ $k->id }}" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">Détails KYC ({{$k->status}})</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <h4 class="text-primary">{{ $k->user->name }}</h4>
                                    <p><strong>Téléphone :</strong> {{ $k->user->phone }}</p>
                                    <p>
                                        <strong>Status :</strong>
                                        <span class="badge-modern badge-pending">
                                            {{$k->status}}
                                        </span>
                                    </p>

                                    <hr>

                                    <div class="row">
                                        @foreach ($k->docs as $doc)
                                            <div class="col-md-4 text-center mb-3">
                                                <h6 class="fw-bold text-primary">{{ $doc['type'] }}</h6>
                                                <img src="{{ asset('storage/' . $doc['path']) }}"
                                                    class="img-fluid rounded shadow">
                                            </div>
                                        @endforeach
                                    </div>

                                </div>

                                <div class="modal-footer d-flex justify-content-between">

                                    {{-- REJECT --}}
                                    <form method="POST" action="{{ route('admin.kyc.reject', $k->id) }}">
                                        @csrf
                                        {{--  get user_id in hidden input --}}
                                        <input type="hidden" name="user_id" value="{{ $k->user_id }}">
                                        <button class="btn btn-danger" onclick="return confirm('Rejeter ce KYC ?')">
                                            Rejeter
                                        </button>
                                    </form>

                                    {{-- APPROVE --}}
                                    <form method="POST" action="{{ route('admin.kyc.approve', $k->id) }}">

                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $k->user_id }}">
                                        <button class="btn btn-success" onclick="return confirm('Approuver ce KYC ?')">
                                            Approuver
                                        </button>
                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>

    </div>



    <!-- MODAL
    <div class="modal fade" id="kycModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Détails KYC</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="modalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="rejectBtn" class="btn btn-danger">Rejeter</button>
                    <button id="approveBtn" class="btn btn-success">Approuver</button>
                </div>
            </div>
        </div>
    </div>

    -->
@endsection
