@php
  use App\Models\Enrollment;
  use App\Models\CourseContent;
  use App\Models\Course;
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Storage;

  $d = $block->data ?? [];
  $tableSource = $d['table_source'] ?? 'enrollments';
  $headers = is_array($d['headers'] ?? null) ? $d['headers'] : [];
  $selectedColumns = collect($headers)->pluck('column')->filter()->values()->all();
  $amountFilter = $d['amount_filter'] ?? 'free';

  $courseId = null;
  if (isset($page) && $page->pageable_type === Course::class) {
      $courseId = $page->pageable_id;
  }

  // Get query parameters for filtering and pagination
  $search = request('search', '');
  $perPage = (int) request('per_page', 50);
  $perPage = in_array($perPage, [10, 25, 50, 100, 200]) ? $perPage : 50;

  $formatMoney = function ($value): string {
      if ($value === null || $value === '') {
          return '0';
      }
      return number_format((float) $value, 2);
  };

  // Country to flag emoji mapping
  $countryFlags = [
      'nigerian' => '🇳🇬', 'nigeria' => '🇳🇬',
      'ghanaian' => '🇬🇭', 'ghana' => '🇬🇭',
      'kenyan' => '🇰🇪', 'kenya' => '🇰🇪',
      'south african' => '🇿🇦', 'south africa' => '🇿🇦',
      'american' => '🇺🇸', 'usa' => '🇺🇸', 'united states' => '🇺🇸',
      'british' => '🇬🇧', 'uk' => '🇬🇧', 'united kingdom' => '🇬🇧',
      'canadian' => '🇨🇦', 'canada' => '🇨🇦',
      'indian' => '🇮🇳', 'india' => '🇮🇳',
      'cameroonian' => '🇨🇲', 'cameroon' => '🇨🇲',
      'ugandan' => '🇺🇬', 'uganda' => '🇺🇬',
      'tanzanian' => '🇹🇿', 'tanzania' => '🇹🇿',
      'rwandan' => '🇷🇼', 'rwanda' => '🇷🇼',
      'egyptian' => '🇪🇬', 'egypt' => '🇪🇬',
      'moroccan' => '🇲🇦', 'morocco' => '🇲🇦',
      'ethiopian' => '🇪🇹', 'ethiopia' => '🇪🇹',
      'zimbabwean' => '🇿🇼', 'zimbabwe' => '🇿🇼',
      'zambian' => '🇿🇲', 'zambia' => '🇿🇲',
      'botswanan' => '🇧🇼', 'botswana' => '🇧🇼',
      'namibian' => '🇳🇦', 'namibia' => '🇳🇦',
      'senegalese' => '🇸🇳', 'senegal' => '🇸🇳',
      'ivorian' => '🇨🇮', 'ivory coast' => '🇨🇮', 'cote d\'ivoire' => '🇨🇮',
      'german' => '🇩🇪', 'germany' => '🇩🇪',
      'french' => '🇫🇷', 'france' => '🇫🇷',
      'chinese' => '🇨🇳', 'china' => '🇨🇳',
      'japanese' => '🇯🇵', 'japan' => '🇯🇵',
      'australian' => '🇦🇺', 'australia' => '🇦🇺',
      'brazilian' => '🇧🇷', 'brazil' => '🇧🇷',
      'mexican' => '🇲🇽', 'mexico' => '🇲🇽',
      'spanish' => '🇪🇸', 'spain' => '🇪🇸',
      'italian' => '🇮🇹', 'italy' => '🇮🇹',
      'dutch' => '🇳🇱', 'netherlands' => '🇳🇱',
      'belgian' => '🇧🇪', 'belgium' => '🇧🇪',
      'swiss' => '🇨🇭', 'switzerland' => '🇨🇭',
      'swedish' => '🇸🇪', 'sweden' => '🇸🇪',
      'norwegian' => '🇳🇴', 'norway' => '🇳🇴',
      'danish' => '🇩🇰', 'denmark' => '🇩🇰',
      'finnish' => '🇫🇮', 'finland' => '🇫🇮',
      'polish' => '🇵🇱', 'poland' => '🇵🇱',
      'russian' => '🇷🇺', 'russia' => '🇷🇺',
      'ukrainian' => '🇺🇦', 'ukraine' => '🇺🇦',
      'pakistani' => '🇵🇰', 'pakistan' => '🇵🇰',
      'bangladeshi' => '🇧🇩', 'bangladesh' => '🇧🇩',
      'indonesian' => '🇮🇩', 'indonesia' => '🇮🇩',
      'malaysian' => '🇲🇾', 'malaysia' => '🇲🇾',
      'singaporean' => '🇸🇬', 'singapore' => '🇸🇬',
      'filipino' => '🇵🇭', 'philippines' => '🇵🇭',
      'vietnamese' => '🇻🇳', 'vietnam' => '🇻🇳',
      'thai' => '🇹🇭', 'thailand' => '🇹🇭',
      'korean' => '🇰🇷', 'south korea' => '🇰🇷',
      'emirati' => '🇦🇪', 'uae' => '🇦🇪', 'united arab emirates' => '🇦🇪',
      'saudi' => '🇸🇦', 'saudi arabia' => '🇸🇦',
      'qatari' => '🇶🇦', 'qatar' => '🇶🇦',
      'kuwaiti' => '🇰🇼', 'kuwait' => '🇰🇼',
      'omani' => '🇴🇲', 'oman' => '🇴🇲',
      'bahraini' => '🇧🇭', 'bahrain' => '🇧🇭',
      'jordanian' => '🇯🇴', 'jordan' => '🇯🇴',
      'lebanese' => '🇱🇧', 'lebanon' => '🇱🇧',
      'israeli' => '🇮🇱', 'israel' => '🇮🇱',
      'turkish' => '🇹🇷', 'turkey' => '🇹🇷',
      'greek' => '🇬🇷', 'greece' => '🇬🇷',
      'irish' => '🇮🇪', 'ireland' => '🇮🇪',
      'portuguese' => '🇵🇹', 'portugal' => '🇵🇹',
      'argentinian' => '🇦🇷', 'argentina' => '🇦🇷',
      'chilean' => '🇨🇱', 'chile' => '🇨🇱',
      'colombian' => '🇨🇴', 'colombia' => '🇨🇴',
      'peruvian' => '🇵🇪', 'peru' => '🇵🇪',
      'jamaican' => '🇯🇲', 'jamaica' => '🇯🇲',
      'trinidadian' => '🇹🇹', 'trinidad' => '🇹🇹', 'trinidad and tobago' => '🇹🇹',
  ];

  $getFlag = function($nationality) use ($countryFlags) {
      if (!$nationality) return '🌍';
      $key = strtolower(trim($nationality));
      return $countryFlags[$key] ?? '🌍';
  };

  $defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=e2e8f0&color=64748b&size=88';

  $enrollmentColumns = [
      'user_photo' => [
          'label' => 'Photo',
          'value' => fn($row) => $row->user,
          'type' => 'photo',
      ],
      'id' => [
          'label' => 'ID',
          'value' => fn($row) => $row->id,
      ],
      'enrollment_id' => [
          'label' => 'Enrollment ID',
          'value' => fn($row) => $row->user?->enrollment_id ?? 'N/A',
      ],
      'user_name' => [
          'label' => 'Student Name',
          'value' => fn($row) => $row->user?->name ?? 'N/A',
      ],
      'user_email' => [
          'label' => 'Email',
          'value' => fn($row) => $row->user?->email ?? 'N/A',
      ],
      'nationality' => [
          'label' => 'Country',
          'value' => fn($row) => $row->user?->country ?? $row->user?->nationality ?? null,
          'type' => 'nationality',
      ],
      'course_title' => [
          'label' => 'Course',
          'value' => fn($row) => $row->course?->title ?? $row->schedule?->course?->title ?? 'N/A',
      ],
      'schedule_dates' => [
          'label' => 'Schedule',
          'value' => function ($row) {
              if ($row->schedule) {
                  $start = $row->schedule->start_date?->format('M d, Y') ?? 'TBA';
                  $end = $row->schedule->end_date?->format('M d, Y') ?? 'TBA';
                  return $start . ' - ' . $end;
              }
              return 'Self paced';
          },
      ],
      'payment_plan' => [
          'label' => 'Plan',
          'value' => fn($row) => Str::title($row->payment_plan ?? 'N/A'),
      ],
      'total_amount' => [
          'label' => 'Amount',
          'value' => fn($row) => $formatMoney($row->total_amount ?? 0),
      ],
      'balance' => [
          'label' => 'Balance',
          'value' => fn($row) => $formatMoney($row->balance ?? 0),
      ],
      'status' => [
          'label' => 'Status',
          'value' => fn($row) => $row->status ?? 'N/A',
          'type' => 'status',
      ],
      'verification_status' => [
          'label' => 'Verified',
          'value' => fn($row) => $row->user?->verification_status ?? 'unverified',
          'type' => 'verification',
      ],
      'created_at' => [
          'label' => 'Enrolled',
          'value' => fn($row) => $row->created_at?->format('M d, Y') ?? 'N/A',
      ],
  ];

  $contentColumns = [
      'id' => [
          'label' => 'Content ID',
          'value' => fn($row) => $row->id,
      ],
      'title' => [
          'label' => 'Title',
          'value' => fn($row) => $row->title ?? 'N/A',
      ],
      'type' => [
          'label' => 'Type',
          'value' => fn($row) => Str::headline($row->type ?? 'N/A'),
      ],
      'price' => [
          'label' => 'Price',
          'value' => fn($row) => $formatMoney($row->price ?? 0),
      ],
      'discount_price' => [
          'label' => 'Discount Price',
          'value' => fn($row) => $formatMoney($row->discount_price ?? 0),
      ],
      'order' => [
          'label' => 'Order',
          'value' => fn($row) => $row->order ?? 0,
      ],
      'created_at' => [
          'label' => 'Created Date',
          'value' => fn($row) => $row->created_at?->format('M d, Y') ?? 'N/A',
      ],
  ];

  $columns = $tableSource === 'course_contents' ? $contentColumns : $enrollmentColumns;
  $selectedColumns = array_values(array_filter($selectedColumns, fn($col) => isset($columns[$col])));

  // If no columns selected, use default columns
  if (empty($selectedColumns) && $tableSource === 'enrollments') {
      $selectedColumns = ['user_photo', 'user_name', 'user_email', 'nationality', 'course_title', 'status', 'created_at'];
  }

  $rows = null;
  $totalCount = 0;

  if ($tableSource === 'course_contents') {
      if ($courseId) {
          $rows = CourseContent::query()
              ->where('course_id', $courseId)
              ->orderBy('order')
              ->paginate($perPage);
          $totalCount = $rows->total();
      }
  } else {
      if ($courseId) {
          $query = Enrollment::with(['user.scholarshipApplications', 'course', 'schedule.course'])
              ->where('status', 'active')
              ->where(function ($q) use ($courseId) {
                  $q->where('course_id', $courseId)
                      ->orWhereHas('schedule', fn($sq) => $sq->where('course_id', $courseId));
              });

          if ($amountFilter === 'paid') {
              $query->where('total_amount', '>', 0);
          } elseif (in_array($amountFilter, ['free', 'zero'], true)) {
              $query->where('total_amount', '<=', 0);
          }

          // Apply search filter
          if ($search) {
              $query->whereHas('user', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('enrollment_id', 'like', "%{$search}%");
              });
          }

          $rows = $query->latest()->paginate($perPage)->withQueryString();
          $totalCount = $rows->total();
      }
  }

  $title = trim((string) ($d['title'] ?? ''));
  if ($title === '') {
      $title = $tableSource === 'course_contents' ? 'Course Contents' : 'Active Enrollments';
  }

  $subtitle = trim((string) ($d['subtitle'] ?? ''));
  if ($subtitle === '') {
      $subtitle = $tableSource === 'course_contents'
          ? 'Content linked to this course'
          : 'Students enrolled in this course';
  }
@endphp

<section class="pb-table-section section-gap" id="enrollments-table">
  <div class="container">
    <div class="pb-table-card">
      {{-- Header --}}
      <div class="pb-table-header">
        <div class="pb-table-header-left">
          <div class="pb-table-title">{{ $title }}</div>
          <div class="pb-table-subtitle">{{ $subtitle }}</div>
        </div>
        <div class="pb-table-badge">
          <i class="bi bi-people-fill me-1"></i>
          {{ number_format($totalCount) }} {{ Str::plural('student', $totalCount) }}
        </div>
      </div>

      {{-- Filters --}}
      @if ($tableSource === 'enrollments' && $courseId)
      <div class="pb-table-filters">
        <div class="pb-filter-form">
          <div class="pb-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="pb-table-search" placeholder="Search by name, email, or enrollment ID..." class="pb-search-input">
          </div>
          <div class="pb-filter-actions">
            <form method="GET" action="" class="d-flex align-items-center gap-2">
              <select name="per_page" class="pb-select" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per page</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per page</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per page</option>
                <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200 per page</option>
              </select>
            </form>
            <button type="button" id="pb-clear-search" class="pb-btn-clear" style="display: none;">
              <i class="bi bi-x-lg"></i> Clear
            </button>
            <span class="pb-search-count text-muted small" id="pb-search-count" style="display: none;"></span>
          </div>
        </div>
      </div>
      @endif

      {{-- Table --}}
      <div class="pb-table-body">
        @if (!$courseId)
          <div class="pb-table-empty">
            <i class="bi bi-exclamation-circle"></i>
            <p>No course selected for this page.</p>
          </div>
        @elseif (empty($selectedColumns))
          <div class="pb-table-empty">
            <i class="bi bi-table"></i>
            <p>No columns selected for this table.</p>
          </div>
        @elseif (!$rows || $rows->isEmpty())
          <div class="pb-table-empty">
            <i class="bi bi-inbox"></i>
            <p>No records found{{ $search ? ' matching "' . e($search) . '"' : '' }}.</p>
          </div>
        @else
          <div class="table-responsive pb-table-responsive">
            <table class="table pb-table align-middle mb-0">
              <thead>
                <tr>
                  @foreach ($selectedColumns as $columnKey)
                    <th>{{ $columns[$columnKey]['label'] ?? Str::headline($columnKey) }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach ($rows as $row)
                  <tr>
                    @foreach ($selectedColumns as $columnKey)
                      @php
                        $colConfig = $columns[$columnKey] ?? [];
                        $cellValue = $colConfig['value'] ?? null;
                        $value = is_callable($cellValue) ? $cellValue($row) : '';
                        $colType = $colConfig['type'] ?? 'text';
                      @endphp
                      <td>
                        @if ($colType === 'photo')
                          @php
                            $user = $value;
                            $userName = $user?->name ?? 'User';
                            $fallbackAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=e2e8f0&color=64748b&size=88';
                            $photoUrl = $user && $user->photo
                                ? route('user.photo', $user->id)
                                : $fallbackAvatar;
                          @endphp
                          <div class="pb-avatar">
                            <img src="{{ $photoUrl }}" alt="{{ $userName }}" loading="lazy" onerror="this.src='{{ $fallbackAvatar }}'">
                          </div>
                        @elseif ($colType === 'nationality')
                          <span class="pb-country">
                            <span class="pb-flag">{{ $getFlag($value) }}</span>
                            <span class="pb-country-name">{{ $value ?? 'N/A' }}</span>
                          </span>
                        @elseif ($colType === 'status')
                          @php
                            $statusClass = match(strtolower($value ?? '')) {
                                'active' => 'success',
                                'completed' => 'info',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            };
                          @endphp
                          <span class="pb-status pb-status-{{ $statusClass }}">{{ Str::title($value) }}</span>
                        @elseif ($colType === 'verification')
                          @php
                            $verifyClass = match(strtolower($value ?? '')) {
                                'verified' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'secondary'
                            };
                            $verifyIcon = match(strtolower($value ?? '')) {
                                'verified' => 'bi-check-circle-fill',
                                'pending' => 'bi-clock',
                                'rejected' => 'bi-x-circle',
                                default => 'bi-dash-circle'
                            };
                          @endphp
                          <span class="pb-verify pb-verify-{{ $verifyClass }}">
                            <i class="bi {{ $verifyIcon }}"></i>
                          </span>
                        @else
                          {{ $value !== '' && $value !== null ? $value : 'N/A' }}
                        @endif
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Pagination --}}
          @if ($rows->hasPages())
          <div class="pb-table-pagination">
            <div class="pb-pagination-info">
              Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} results
            </div>
            <div class="pb-pagination-links">
              {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
          </div>
          @endif
        @endif
      </div>
    </div>
  </div>
</section>

<style>
  .pb-table-section {
    padding: 60px 0;
  }

  .pb-table-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06), 0 24px 48px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  /* Header */
  .pb-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 24px 28px;
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #0ea5e9 100%);
    color: #ffffff;
  }

  .pb-table-header-left {
    flex: 1;
  }

  .pb-table-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 4px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .pb-table-subtitle {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.85);
  }

  .pb-table-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 10px 18px;
    border-radius: 50px;
    white-space: nowrap;
  }

  /* Filters */
  .pb-table-filters {
    padding: 20px 28px;
    background: linear-gradient(180deg, #f0f9ff 0%, #ffffff 100%);
    border-bottom: 1px solid #e2e8f0;
  }

  .pb-filter-form {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }

  .pb-search-box {
    flex: 1;
    min-width: 280px;
    position: relative;
  }

  .pb-search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 1rem;
  }

  .pb-search-input {
    width: 100%;
    padding: 12px 16px 12px 44px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    background: #ffffff;
    transition: all 0.2s ease;
  }

  .pb-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
  }

  .pb-filter-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .pb-select {
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.9rem;
    background: #ffffff;
    cursor: pointer;
    min-width: 140px;
    transition: all 0.2s ease;
  }

  .pb-select:focus {
    outline: none;
    border-color: #3b82f6;
  }

  .pb-btn-search {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color: #ffffff;
    border: none;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .pb-btn-search:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  }

  .pb-btn-clear {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    background: #fee2e2;
    color: #dc2626;
    border: none;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .pb-btn-clear:hover {
    background: #fecaca;
    color: #b91c1c;
  }

  /* Table Body */
  .pb-table-body {
    background: #ffffff;
  }

  .pb-table-empty {
    padding: 60px 28px;
    text-align: center;
    color: #64748b;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
  }

  .pb-table-empty i {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 16px;
    display: block;
  }

  .pb-table-empty p {
    margin: 0;
    font-size: 1rem;
  }

  /* Table Styles */
  .pb-table {
    width: 100%;
    border-collapse: collapse;
  }

  .pb-table thead th {
    background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #1e40af;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 16px 18px;
    border-bottom: 2px solid #cbd5e1;
    white-space: nowrap;
  }

  .pb-table tbody tr {
    transition: all 0.15s ease;
  }

  .pb-table tbody tr:hover {
    background: linear-gradient(90deg, #eff6ff 0%, #f0f9ff 100%);
  }

  .pb-table tbody tr:not(:last-child) {
    border-bottom: 1px solid #f1f5f9;
  }

  .pb-table td {
    padding: 16px 18px;
    font-size: 0.9rem;
    color: #334155;
    vertical-align: middle;
  }

  /* Avatar */
  .pb-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }

  .pb-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Country with Flag */
  .pb-country {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .pb-flag {
    font-size: 1.4rem;
    line-height: 1;
  }

  .pb-country-name {
    font-size: 0.85rem;
    color: #475569;
  }

  /* Status Badges */
  .pb-status {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .pb-status-success {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #166534;
  }

  .pb-status-warning {
    background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%);
    color: #854d0e;
  }

  .pb-status-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
  }

  .pb-status-info {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    color: #0c4a6e;
  }

  .pb-status-secondary {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #475569;
  }

  /* Verification Badge */
  .pb-verify {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    font-size: 0.9rem;
  }

  .pb-verify-success {
    background: #dcfce7;
    color: #16a34a;
  }

  .pb-verify-warning {
    background: #fef9c3;
    color: #ca8a04;
  }

  .pb-verify-danger {
    background: #fee2e2;
    color: #dc2626;
  }

  .pb-verify-secondary {
    background: #f1f5f9;
    color: #64748b;
  }

  /* Pagination */
  .pb-table-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 28px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
    gap: 16px;
  }

  .pb-pagination-info {
    font-size: 0.875rem;
    color: #64748b;
  }

  .pb-pagination-links .pagination {
    margin: 0;
    gap: 4px;
  }

  .pb-pagination-links .page-link {
    border: none;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 0.875rem;
    color: #1e40af;
    background: #f1f5f9;
    transition: all 0.2s ease;
  }

  .pb-pagination-links .page-link:hover {
    background: #e0f2fe;
    color: #1e40af;
  }

  .pb-pagination-links .page-item.active .page-link {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color: #ffffff;
  }

  .pb-pagination-links .page-item.disabled .page-link {
    background: #f8fafc;
    color: #cbd5e1;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .pb-table-header {
      flex-direction: column;
      align-items: flex-start;
      padding: 20px;
    }

    .pb-table-badge {
      width: 100%;
      justify-content: center;
    }

    .pb-table-filters {
      padding: 16px;
    }

    .pb-filter-form {
      flex-direction: column;
    }

    .pb-search-box {
      width: 100%;
      min-width: auto;
    }

    .pb-filter-actions {
      width: 100%;
      flex-wrap: wrap;
    }

    .pb-select, .pb-btn-search {
      flex: 1;
    }

    .pb-table-pagination {
      flex-direction: column;
      text-align: center;
    }

    .pb-table td, .pb-table th {
      padding: 12px;
    }

    .pb-country-name {
      display: none;
    }
  }

  .pb-search-count {
    padding: 8px 12px;
    background: #f1f5f9;
    border-radius: 8px;
  }

  .pb-table tbody tr.pb-hidden {
    display: none;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('pb-table-search');
  const clearBtn = document.getElementById('pb-clear-search');
  const countSpan = document.getElementById('pb-search-count');
  const tableBody = document.querySelector('.pb-table tbody');

  if (!searchInput || !tableBody) return;

  const rows = tableBody.querySelectorAll('tr');
  const totalRows = rows.length;

  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    let visibleCount = 0;

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      const matches = !searchTerm || text.includes(searchTerm);

      if (matches) {
        row.classList.remove('pb-hidden');
        visibleCount++;
      } else {
        row.classList.add('pb-hidden');
      }
    });

    // Update UI
    if (searchTerm) {
      clearBtn.style.display = 'flex';
      countSpan.style.display = 'inline';
      countSpan.textContent = visibleCount + ' of ' + totalRows + ' shown';
    } else {
      clearBtn.style.display = 'none';
      countSpan.style.display = 'none';
    }
  }

  // Debounce function
  let debounceTimer;
  searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(filterTable, 150);
  });

  clearBtn.addEventListener('click', function() {
    searchInput.value = '';
    filterTable();
    searchInput.focus();
  });
});
</script>
