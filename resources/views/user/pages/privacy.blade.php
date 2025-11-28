@extends('user.master_page')
@section('title', 'Privacy Policy | Forward Edge Consulting')
@section('breadcrumb_text', 'Learn how we collect, protect, and use personal information across our platforms and programs.')

@section('main')
    @include('user.partials.breadcrumb')

    <section class="section-gap tj-legal-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="mb-5">
                        <p class="text-muted mb-1">Last updated: October 07, 2025</p>
                        <h2 class="fw-bold mb-3">Forward Edge Consulting – Privacy Policy</h2>
                        <p class="lead text-muted">
                            Your trust matters to us. This statement outlines the personal information we collect, why we
                            collect it, and how we keep it secure across our websites, marketing channels, training platforms,
                            and support workflows.
                        </p>
                    </div>

                    <div class="legal-card border rounded-4 shadow-sm p-4 p-md-5 mb-4 bg-white">
                        <h3 class="h4 mb-3">1. Information we collect</h3>
                        <p class="mb-3">We collect information in three ways:</p>
                        <ul class="ps-3 mb-0">
                            <li><strong>Information you share</strong> such as names, emails, company details, enrolment forms, or payment confirmations.</li>
                            <li><strong>Automated data</strong> like device type, IP address, and analytics events that help us measure platform performance.</li>
                            <li><strong>Third-party sources</strong> (e.g., payment processors, learning tools) that confirm transactions or course progress.</li>
                        </ul>
                    </div>

                    <div class="legal-card border rounded-4 shadow-sm p-4 p-md-5 mb-4 bg-white">
                        <h3 class="h4 mb-3">2. How we use your information</h3>
                        <p class="mb-3">The data helps us:</p>
                        <ul class="ps-3">
                            <li>Deliver services, certificates, and updates related to your program or project.</li>
                            <li>Respond to support requests, learning assessments, or product demos.</li>
                            <li>Send marketing content when you opt-in (you can unsubscribe any time).</li>
                            <li>Improve site reliability, cyber security posture, and quality assurance.</li>
                        </ul>
                        <p class="mb-0">We never sell your data to third parties. Service providers supporting our operations must follow comparable safeguards.</p>
                    </div>

                    <div class="legal-card border rounded-4 shadow-sm p-4 p-md-5 mb-4 bg-white">
                        <h3 class="h4 mb-3">3. Data storage & protection</h3>
                        <p class="mb-3">
                            We host data on reputable cloud infrastructure with encryption in transit and at rest. Access is
                            limited to authorized staff following role-based controls and periodic security reviews.
                        </p>
                        <p class="mb-0">Backups and retention schedules follow Nigerian regulatory obligations and industry best practices.</p>
                    </div>

                    <div class="legal-card border rounded-4 shadow-sm p-4 p-md-5 mb-4 bg-white">
                        <h3 class="h4 mb-3">4. Your rights</h3>
                        <p class="mb-3">
                            You can request access, corrections, deletion, or a copy of your personal data. To exercise these
                            rights, email <a href="mailto:info@forwardedgeconsulting.com">info@forwardedgeconsulting.com</a> with a short description of your request.
                        </p>
                        <p class="mb-0">
                            We will respond within 30 days and may ask for verification to protect your account.
                        </p>
                    </div>

                    <div class="legal-card border rounded-4 shadow-sm p-4 p-md-5 mb-4 bg-white">
                        <h3 class="h4 mb-3">5. Cookies & analytics</h3>
                        <p class="mb-3">
                            Cookies help us remember your preferences, keep sessions secure, and personalize marketing. You can
                            adjust cookie preferences in your browser; disabling some cookies may limit site functionality.
                        </p>
                        <p class="mb-0">
                            Anonymous usage data may be shared with analytics or advertising partners that adhere to GDPR/NDPR
                            standards.
                        </p>
                    </div>

                    <div class="legal-card border rounded-4 shadow-sm p-4 p-md-5 mb-4 bg-white">
                        <h3 class="h4 mb-3">6. Contact</h3>
                        <p class="mb-0">
                            Questions or concerns? Reach us at <a href="mailto:info@forwardedgeconsulting.com">info@forwardedgeconsulting.com</a>
                            or +234&nbsp;703&nbsp;995&nbsp;5591. We’re happy to explain our security approach in more detail for enterprise clients.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
