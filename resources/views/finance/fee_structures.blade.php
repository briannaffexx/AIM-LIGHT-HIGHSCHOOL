@extends('layouts.app')

@section('title', 'Fee Structures - Boarding School System')
@section('page_title', 'Fee Configurations & Category Levies')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Fee Structure Matrix -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Configured Term Levies</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Classification</th>
                            <th>Fee Category</th>
                            <th>Term Assignment</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($structures as $struct)
                            <tr>
                                <td>
                                    <span class="pill {{ $struct->classification == 'boarding' ? 'pill-success' : 'pill-info' }}">
                                        {{ str_replace('_', ' ', $struct->classification) }}
                                    </span>
                                </td>
                                <td><strong>{{ $struct->category->name }}</strong></td>
                                <td>{{ $struct->term->name }} ({{ $struct->term->academicYear->name }})</td>
                                <td style="text-align: right; font-weight: 700; color: #FFFFFF;">
                                    {{ number_format($struct->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No fee structures configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Fee Structure -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Configure Levy Amount</h3>

            <form action="{{ route('finance.fee-structures.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="classification" class="form-label">Student Classification *</label>
                    <select name="classification" id="classification" class="form-control" required>
                        <option value="day_scholar">Day Scholar</option>
                        <option value="boarding">Boarder / Residential</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fee_category_id" class="form-label">Fee Category *</label>
                    <select name="fee_category_id" id="fee_category_id" class="form-control" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="term_id" class="form-label">Apply to Term *</label>
                    <select name="term_id" id="term_id" class="form-control" required>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ $term->is_active ? 'selected' : '' }}>{{ $term->name }} ({{ $term->academicYear->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="amount" class="form-label">Levy Amount *</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="0.00" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Levy Settings</button>
            </form>
        </div>
    </div>
@endsection
