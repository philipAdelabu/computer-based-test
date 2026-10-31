@extends('layouts.app')
@section('title', 'Edit Traits & Skills')
@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection
@section('page-title', 'Edit Traits & Skills')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <form action="{{ route('teacher.report-cards.traits.update', $reportCard->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Editing for: <strong>{{ $reportCard->student->user->name }}</strong></h5>
                <div>
                    <a href="{{ route('teacher.report-cards.show', $reportCard->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                </div>
            </div>

            <div class="row g-4">
                <!-- AFFECTIVE TRAITS -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Affective Traits (1-5)</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Trait</th>
                                        <th class="text-center" style="width: 150px;">Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($affectiveList as $trait)
                                        @php $current = $affectiveTraits[$trait] ?? 4; @endphp
                                        <tr>
                                            <td class="align-middle">{{ $trait }}</td>
                                            <td class="text-center">
                                                <select name="affective_traits[{{ $trait }}]" class="form-select form-select-sm">
                                                    <option value="">N/A</option>
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <option value="{{ $i }}" {{ (int) $current === $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PSYCHOMOTOR SKILLS -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Psychomotor Skills (1-5)</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Skill</th>
                                        <th class="text-center" style="width: 150px;">Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($psychomotorList as $skill)
                                        @php $current = $psychomotorSkills[$skill] ?? 4; @endphp
                                        <tr>
                                            <td class="align-middle">{{ $skill }}</td>
                                            <td class="text-center">
                                                <select name="psychomotor_skills[{{ $skill }}]" class="form-select form-select-sm">
                                                    <option value="">N/A</option>
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <option value="{{ $i }}" {{ (int) $current === $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection