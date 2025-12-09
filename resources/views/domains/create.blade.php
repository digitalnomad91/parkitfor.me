@extends('layouts.app')

@section('title', 'Add Domain')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <h1 style="margin-bottom: 2rem; color: #2c3e50;">Add New Domain</h1>

    <div class="card">
        <div class="card-header">
            Domain Information
        </div>

        <form method="POST" action="{{ route('domains.store') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Domain Name *</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                       name="name" value="{{ old('name') }}" 
                       placeholder="example.com" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small style="color: #7f8c8d; margin-top: 0.25rem; display: block;">
                    Enter the full domain name (e.g., example.com, subdomain.example.org)
                </small>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                    Additional Options
                </label>
                
                <div style="margin-bottom: 0.5rem;">
                    <label style="font-weight: normal; display: flex; align-items: center;">
                        <input type="checkbox" name="perform_whois" value="1" style="margin-right: 0.5rem;">
                        Perform WHOIS lookup after adding
                    </label>
                </div>
                
                <div>
                    <label style="font-weight: normal; display: flex; align-items: center;">
                        <input type="checkbox" name="perform_dns" value="1" checked style="margin-right: 0.5rem;">
                        Perform DNS lookup after adding
                    </label>
                </div>
            </div>

            <div class="form-group" style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">
                    Add Domain
                </button>
                <a href="{{ route('domains') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            💡 Helpful Information
        </div>
        <div style="padding: 1rem 0;">
            <p><strong>After adding a domain, you can:</strong></p>
            <ul style="margin-left: 1.5rem; line-height: 1.8;">
                <li>View its WHOIS information</li>
                <li>Check DNS records (A, AAAA, MX, NS, CNAME, TXT)</li>
                <li>Monitor domain expiration dates</li>
                <li>Track nameserver changes</li>
            </ul>
            
            <p style="margin-top: 1rem;"><strong>CLI Alternative:</strong></p>
            <code style="background-color: #f8f9fa; padding: 0.5rem 1rem; border-radius: 4px; display: block;">
                php artisan domain:add example.com
            </code>
        </div>
    </div>
</div>
@endsection
