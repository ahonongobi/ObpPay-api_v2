@extends('admin.layout')
<style>
    /* ---- CREDIT CARD ---- */
.credit-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 25px;
    border: 1px solid #e5e7eb;

    
}

@media (max-width: 768px) {
    .credit-card {
        padding: 15px;
        margin-top: 20%;
    }
}


/* DARK MODE */
body.dark-mode .credit-card {
    background: #1f2937;
    border: 1px solid rgba(255,255,255,0.1);
    color: #e5e7eb;
}

/* INPUTS inside the card */
.credit-card input {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #d1d5db;
}

.credit-card input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,.25);
}

/* DARK MODE INPUTS */
body.dark-mode .credit-card input {
    background: #111827;
    color: #e5e7eb;
    border: 1px solid #374151;
}

body.dark-mode .credit-card input::placeholder {
    color: #9ca3af;
}

</style>
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


<div class="credit-card shadow-lg mb-5">
    <h4 class="fw-bold text-secondary mb-3">Créditer un portefeuille</h4>

    <form method="POST" action="{{ route('admin.wallets.processCredit') }}">
        @csrf

        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label">OBP ID du client</label>
                <input type="text" name="obp_id" class="form-control"
                       placeholder="Entrez l'OBP ID du client" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Montant à créditer (XOF)</label>
                <input type="number" name="amount" class="form-control"
                       step="0.01" placeholder="Entrez le montant" required>
            </div>

        </div>

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

