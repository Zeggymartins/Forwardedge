<footer class="fe-admin-footer mt-auto">
    <div class="container-fluid">
        <div class="row align-items-center g-3">
            <div class="col-md">
                <p class="fe-footer-label mb-1 text-uppercase">ForwardEdge Consulting Ltd</p>
                <p class="mb-0 text-muted small">
                    &copy; {{ now()->year }} ForwardEdge Consulting Ltd. Built for the next generation of cybersecurity talent.
                </p>
            </div>
            <div class="col-md-auto">
                <div class="fe-footer-links d-flex flex-wrap gap-3 justify-content-md-end">
                    <a href="mailto:{{ config('mail.from.address') }}" class="text-decoration-none">Support</a>
                    <a href="{{ route('pb.pages') }}" class="text-decoration-none">Page Builder</a>
                    <a href="{{ route('dashboard') }}" class="text-decoration-none">Status</a>
                </div>
            </div>
        </div>
    </div>
</footer>
