@php
  use App\Models\Enrollment;
  use App\Models\CourseContent;
  use App\Models\Course;
  use Illuminate\Support\Str;

  $d = $block->data ?? [];
  $tableSource = $d['table_source'] ?? 'enrollments';
  $headers = is_array($d['headers'] ?? null) ? $d['headers'] : [];
  $selectedColumns = collect($headers)->pluck('column')->filter()->values()->all();
  $amountFilter = $d['amount_filter'] ?? 'free';

  $courseId = null;
  if (isset($page) && $page->pageable_type === Course::class) {
      $courseId = $page->pageable_id;
  }

  $formatMoney = function ($value): string {
      if ($value === null || $value === '') {
          return '0';
      }
      return number_format((float) $value, 2);
  };

  $enrollmentColumns = [
      'id' => [
          'label' => 'Enrollment ID',
          'value' => fn($row) => $row->id,
      ],
      'user_name' => [
          'label' => 'Student Name',
          'value' => fn($row) => $row->user?->name ?? 'N/A',
      ],
      'user_email' => [
          'label' => 'Student Email',
          'value' => fn($row) => $row->user?->email ?? 'N/A',
      ],
      'course_title' => [
          'label' => 'Course',
          'value' => fn($row) => $row->course?->title ?? $row->schedule?->course?->title ?? 'N/A',
      ],
      'schedule_dates' => [
          'label' => 'Schedule Dates',
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
          'label' => 'Payment Plan',
          'value' => fn($row) => Str::title($row->payment_plan ?? 'N/A'),
      ],
      'total_amount' => [
          'label' => 'Total Amount',
          'value' => fn($row) => $formatMoney($row->total_amount ?? 0),
      ],
      'balance' => [
          'label' => 'Balance',
          'value' => fn($row) => $formatMoney($row->balance ?? 0),
      ],
      'status' => [
          'label' => 'Status',
          'value' => fn($row) => Str::title($row->status ?? 'N/A'),
      ],
      'created_at' => [
          'label' => 'Created Date',
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

  $rows = collect();
  if ($tableSource === 'course_contents') {
      if ($courseId) {
          $rows = CourseContent::query()
              ->where('course_id', $courseId)
              ->orderBy('order')
              ->get();
      }
  } else {
      if ($courseId) {
          $query = Enrollment::with(['user', 'course', 'schedule.course'])
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

          $rows = $query->latest()->get();
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
          : 'Active enrollments for this course';
  }
@endphp

<section class="pb-table-section section-gap">
  <div class="container">
    <div class="pb-table-card">
      <div class="pb-table-header">
        <div>
          <div class="pb-table-title">{{ $title }}</div>
          <div class="pb-table-subtitle">{{ $subtitle }}</div>
        </div>
        <div class="pb-table-meta">{{ $rows->count() }} rows</div>
      </div>
      <div class="pb-table-body">
        @if (!$courseId)
          <div class="pb-table-empty">No course selected for this page.</div>
        @elseif (empty($selectedColumns))
          <div class="pb-table-empty">No columns selected for this table.</div>
        @elseif ($rows->isEmpty())
          <div class="pb-table-empty">No records found.</div>
        @else
          <div class="table-responsive pb-table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  @foreach ($selectedColumns as $columnKey)
                    <th scope="col">{{ $columns[$columnKey]['label'] ?? Str::headline($columnKey) }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach ($rows as $row)
                  <tr>
                    @foreach ($selectedColumns as $columnKey)
                      @php
                        $cellValue = $columns[$columnKey]['value'] ?? null;
                        $value = is_callable($cellValue) ? $cellValue($row) : '';
                      @endphp
                      <td>{{ $value !== '' ? $value : 'N/A' }}</td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
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
    box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }
  .pb-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 22px 28px;
    background: linear-gradient(120deg, #0f172a 0%, #1e293b 100%);
    color: #f8fafc;
  }
  .pb-table-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 4px;
  }
  .pb-table-subtitle {
    font-size: 0.85rem;
    color: #cbd5f5;
  }
  .pb-table-meta {
    font-size: 0.7rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.24);
    padding: 6px 12px;
    border-radius: 999px;
    white-space: nowrap;
  }
  .pb-table-body {
    background: #ffffff;
  }
  .pb-table-empty {
    padding: 28px;
    text-align: center;
    color: #64748b;
    background: #f8fafc;
  }
  .pb-table-section .table thead th {
    background: #f1f5f9;
    color: #0f172a;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }
  .pb-table-section .table tbody tr:hover {
    background: #f8fafc;
  }
  .pb-table-section .table td,
  .pb-table-section .table th {
    padding: 14px 16px;
  }
  @media (max-width: 768px) {
    .pb-table-header {
      flex-direction: column;
      align-items: flex-start;
    }
    .pb-table-meta {
      width: 100%;
      text-align: center;
    }
  }
</style>
