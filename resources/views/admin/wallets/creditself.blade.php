@extends('admin.layout')

<style>
/* ---- CREDIT CARD STYLE ---- */
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
.credit-card input,
.credit-card .form-control {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #d1d5db;
}

.credit-card input:focus,
.credit-card .form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,.25);
}

/* DARK MODE INPUTS */
body.dark-mode .credit-card input,
body.dark-mode .credit-card .form-control {
    background: #111827;
    color: #e5e7eb;
    border: 1px solid #374151;
}

body.dark-mode .credit-card input::placeholder,
body.dark-mode .credit-card .form-control::placeholder {
    color: #9ca3af;
}

/* BUTTONS */
.credit-card .btn {
    border-radius: 8px;
    padding: 8px 20px;
}
</style>

@section('content')

<div class="credit-card shadow-lg mt-5 mx-auto" style="max-width: 500px;">
    <h4 class="fw-bold text-secondary mb-4">Créditer mon compte administrateur</h4>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.wallets.credit.self.post') }}">
        @csrf
        <div class="mb-3">
            <label for="amount" class="form-label">Montant à créditer (XOF)</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="Entrez le montant" required>
        </div>

        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Créditer</button>
        </div>
    </form>
</div>

@endsection
