@extends('admin.layout')

@section('content')

{{-- success message --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- anyerrors from the form --}}

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<h1 class="fw-bold text-primary mb-4">Créditer un portefeuille</h1>
<div class="card shadow-lg bg-success p-4 rounded-4 credit-card">

    <form method="POST" action="{{ route('admin.wallets.processCredit') }}">
        @csrf

        <div class="row g-4">

            <!-- OBP ID -->
            <div class="col-md-6">
                <label class="form-label fw-bold text-white">OBP ID du client</label>
                <input type="text" name="obp_id" class="form-control"
                       placeholder="Entrez l'OBP ID du client" required>
            </div>
            <!-- AMOUNT -->
            <div class="col-md-6">
                <label class="form-label fw-bold text-white">Montant à créditer (XOF)</label>
                <input type="number" name="amount" step="0.01" class="form-control"
                       placeholder="Entrez le montant à créditer" required>
            </div>
        </div>
        <!-- BUTTONS -->
        <div class="d-flex justify-content-end mt-4 gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary px-4">
                Créditer le portefeuille
            </button>
        </div>
    </form>
</div>

@endsection

