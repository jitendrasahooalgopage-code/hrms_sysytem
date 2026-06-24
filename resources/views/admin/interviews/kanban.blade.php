@extends('layouts.admin')

@section('title')
    {{ __('Kanban Board') }}
@endsection

@section('header')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3">{{ __('Kanban Pipeline') }}</h1>
        <div class="d-flex gap-2 align-items-center">
            <form action="{{ route('kanban') }}" method="GET" class="d-flex gap-2">
                <select name="position" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Positions</option>
                    @foreach ($positions as $id => $title)
                        <option value="{{ $id }}" {{ request('position') == $id ? 'selected' : '' }}>
                            {{ $title }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('positions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list"></i> List View
            </a>
            <a href="{{ route('positions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Candidate
            </a>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .kanban-board {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            min-height: calc(100vh - 250px);
        }

        .kanban-col {
            min-width: 260px;
            max-width: 260px;
            flex-shrink: 0;
        }

        .kanban-col-header {
            border-radius: 8px 8px 0 0;
            padding: 0.6rem 0.9rem;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kanban-col-body {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 0.6rem;
            min-height: 200px;
        }

        .kanban-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.6rem;
            cursor: grab;
            transition: box-shadow 0.15s, transform 0.15s;
        }

        .kanban-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .kanban-card.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .kanban-col-body.drag-over {
            background: #e8f4fd;
            border-color: #0d6efd;
        }

        .initials-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stage-applied {
            background-color: #6c757d;
            color: #fff;
        }

        .stage-screening {
            background-color: #0dcaf0;
            color: #000;
        }

        .stage-technical {
            background-color: #0d6efd;
            color: #fff;
        }

        .stage-hr_interview {
            background-color: #ffc107;
            color: #000;
        }

        .stage-final_round {
            background-color: #fd7e14;
            color: #fff;
        }

        .stage-offer {
            background-color: #6f42c1;
            color: #fff;
        }

        .stage-hired {
            background-color: #198754;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="kanban-board" id="kanban-board">
        @foreach ($columns as $stageKey => $col)
            <div class="kanban-col" data-stage="{{ $stageKey }}">

                {{-- Column Header --}}
                <div class="kanban-col-header stage-{{ $stageKey }}">
                    <span>
                        <i class="{{ $col['config']['icon'] }} me-1"></i>
                        {{ $col['config']['label'] }}
                    </span>
                    <span class="badge bg-white text-dark fw-bold">
                        {{ $col['applications']->count() }}
                    </span>
                </div>

                {{-- Droppable Body --}}
                <div class="kanban-col-body" id="col-{{ $stageKey }}"
                    ondragover="event.preventDefault(); this.classList.add('drag-over')"
                    ondragleave="this.classList.remove('drag-over')" ondrop="handleDrop(event, '{{ $stageKey }}')">

                    @forelse($col['applications'] as $app)
                        <div class="kanban-card" draggable="true" data-id="{{ $app->id }}"
                            ondragstart="handleDragStart(event, {{ $app->id }})">

                            {{-- Candidate Info --}}
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="initials-badge bg-primary bg-opacity-10 text-primary">
                                    {{ $app->candidate->initials }}
                                </div>
                                <div style="overflow:hidden">
                                    <div class="fw-semibold text-truncate" style="font-size:0.85rem;max-width:170px">
                                        {{ $app->candidate->name }}
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size:0.75rem;max-width:170px">
                                        {{ $app->jobPosition->title }}
                                    </div>
                                </div>
                            </div>

                            {{-- Interview rounds dots --}}
                            @if ($app->interviewRounds->count())
                                <div class="d-flex gap-1 mb-2">
                                    @foreach ($app->interviewRounds as $round)
                                        <span
                                            class="badge bg-{{ \App\Models\InterviewRound::outcomes()[$round->outcome]['color'] }}"
                                            title="{{ $round->round_name }}: {{ $round->outcome_label }}"
                                            style="font-size:10px">R{{ $loop->iteration }}</span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Source & CV --}}
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    @if ($app->source)
                                        <span class="badge bg-light text-dark border" style="font-size:10px">
                                            {{ \App\Models\Application::sources()[$app->source] ?? $app->source }}
                                        </span>
                                    @endif
                                    @if ($app->candidate->hasCv())
                                        <span class="badge bg-light text-info border ms-1" style="font-size:10px">
                                            <i class="fas fa-paperclip"></i> CV
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('positions.show', $app) }}" class="btn btn-xs btn-light border px-2 py-0"
                                    style="font-size:11px">
                                    View
                                </a>
                            </div>

                        </div>
                    @empty
                        <div class="text-center text-muted py-3" style="font-size:0.8rem">
                            <i class="fas fa-inbox d-block mb-1"></i> Empty
                        </div>
                    @endforelse
                </div>

            </div>
        @endforeach
    </div>
    <script>
        let draggedId = null;

        function handleDragStart(event, id) {
            draggedId = id;
            event.currentTarget.classList.add('dragging');
        }

        document.addEventListener('dragend', function() {
            document.querySelectorAll('.kanban-card').forEach(c => c.classList.remove('dragging'));
            document.querySelectorAll('.kanban-col-body').forEach(c => c.classList.remove('drag-over'));
        });

        function handleDrop(event, stage) {

            event.preventDefault();

            event.currentTarget.classList.remove('drag-over');


            if (!draggedId) return;


            fetch(`move-data/${draggedId}/stage/ajax`, {

                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },

                    body: JSON.stringify({
                        stage: stage
                    }),

                })

                .then(res => res.json())

                .then(data => {

                    if (data.success) {

                        // reload page after update
                        location.reload();

                    } else {

                        showToast('Failed to update stage.', 'danger');

                    }

                })

                .catch(() => {

                    showToast('Failed to update stage.', 'danger');

                });


            draggedId = null;
        }

        function showToast(msg, type) {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} position-fixed bottom-0 end-0 m-3 shadow`;
            toast.style.zIndex = 9999;
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
@endsection
