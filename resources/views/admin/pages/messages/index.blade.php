@extends('admin.master_page')
@section('main')
@php use Illuminate\Support\Str; @endphp


<div class="pagetitle">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <h1 class="mb-1">Messages</h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Messages</li>
        </ol>
      </nav>
    </div>
    <form class="d-flex gap-2" action="{{ route('messages.index') }}" method="GET">
      <input type="hidden" name="filter" value="{{ $filter ?? 'all' }}">
      <input type="hidden" name="sort" value="{{ $sort ?? 'new' }}">
      <div class="input-group" style="min-width:360px;">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input name="q" class="form-control" value="{{ $q ?? '' }}" placeholder="Search subject, name, email, message…" />
        <button class="btn btn-outline-primary">Search</button>
      </div>
    </form>
  </div>
</div>

<section class="section">
  <div class="glass-card p-0">
    <div class="p-3 d-flex align-items-center gap-2 flex-wrap">
      <div class="btn-group" role="group">
        <a class="btn btn-ghost btn-sm {{ ($filter??'all')==='all'?'active':'' }}" href="{{ route('messages.index',['filter'=>'all','q'=>$q,'sort'=>$sort]) }}">All <span class="pill ms-1">{{ $stats['all'] ?? '—' }}</span></a>
        <a class="btn btn-ghost btn-sm {{ ($filter??'all')==='unread'?'active':'' }}" href="{{ route('messages.index',['filter'=>'unread','q'=>$q,'sort'=>$sort]) }}">Unread <span class="pill ms-1">{{ $stats['unread'] ?? '—' }}</span></a>
        <a class="btn btn-ghost btn-sm {{ ($filter??'all')==='read'?'active':'' }}" href="{{ route('messages.index',['filter'=>'read','q'=>$q,'sort'=>$sort]) }}">Read <span class="pill ms-1">{{ $stats['read'] ?? '—' }}</span></a>
      </div>
      <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
        <button type="button"
                class="btn btn-outline-danger btn-sm"
                id="clearMessagesBtn"
                {{ ($stats['all'] ?? 0) ? '' : 'disabled' }}>
          <i class="bi bi-trash me-1"></i> Clear all
        </button>
        <div class="dropdown">
          <button class="btn btn-ghost btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-sort-down me-1"></i>{{ ($sort??'new')==='old' ? 'Oldest first' : 'Newest first' }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('messages.index',['filter'=>$filter,'q'=>$q,'sort'=>'new']) }}">Newest first</a></li>
            <li><a class="dropdown-item" href="{{ route('messages.index',['filter'=>$filter,'q'=>$q,'sort'=>'old']) }}">Oldest first</a></li>
          </ul>
        </div>
      </div>
    </div>

    {{-- LIST --}}
    <div>
      @forelse($messages as $m)
        @php
          $isUnread = is_null($m->read_at);
          $preview = Str::limit(strip_tags($m->message ?? ''), 120);
          $serviceTitle = $m->service->title ?? '—';
        @endphp
        <div class="message-row p-3 d-flex align-items-start gap-3 open-message {{ $isUnread ? 'is-unread' : '' }}"
             data-id="{{ $m->id }}">
          <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
              <div class="d-flex align-items-center gap-2">
                <span class="badge {{ $isUnread ? 'bg-primary' : 'bg-secondary' }} align-middle" style="width:8px;height:8px;border-radius:50%;"></span>
                <a class="subject text-decoration-none" href="#" onclick="return false;" title="{{ $m->subject ?? ('Message #'.$m->id) }}">
                  {{ $m->subject ?? ('Message #'.$m->id) }}
                </a>
              </div>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="text-muted small mono">{{ optional($m->created_at)->diffForHumans() }}</div>
                <button type="button"
                        class="btn btn-outline-danger btn-sm delete-message-btn"
                        data-id="{{ $m->id }}"
                        title="Delete message">
                  <i class="bi bi-trash me-1"></i> Delete
                </button>
              </div>
            </div>
            <div class="text-muted small mt-1">
              <strong>{{ $m->name ?? 'Unknown' }}</strong>
              @if($m->email)
                &lt;{{ $m->email }}&gt;
              @endif
              • Service: <span class="fw-semibold">{{ $serviceTitle }}</span>
            </div>
            <div class="text-truncate mt-1">{{ $preview }}</div>
          </div>
        </div>
      @empty
        <div class="p-4 text-center text-muted">No messages found.</div>
      @endforelse
    </div>

    @if(method_exists($messages,'links'))
      <div class="p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="text-muted small">
          Showing
          {{ $messages->firstItem() ?? 0 }}-{{ $messages->lastItem() ?? 0 }}
          of {{ $messages->total() }}
        </div>
        <div>
          {{ $messages->appends(['q'=>$q,'filter'=>$filter,'sort'=>$sort])->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>
</section>

{{-- MODAL --}}
<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSubject">Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12 col-xl-8">
            <div class="glass-card p-3">
              <div class="mb-2 text-muted small">
                From: <span id="modalFrom"></span>
                <span class="ms-2">• Service: <strong id="modalService">—</strong></span>
                <span class="ms-2 text-muted mono" id="modalTime"></span>
              </div>
              <div id="modalBody" style="white-space:pre-wrap"></div>
            </div>

            <div class="glass-card p-3 mt-3" id="repliesWrap" style="display:none;">
              <h6 class="mb-2">Previous Replies</h6>
              <div id="repliesList"></div>
            </div>
          </div>
          <div class="col-12 col-xl-4">
            <div class="glass-card p-3 reply-box">
              <h6 class="mb-2">Reply</h6>
              <form id="replyForm">
                @csrf
                <input type="hidden" id="replyMessageId">
                <div class="mb-2">
                  <label class="form-label">To</label>
                  <input class="form-control" id="replyTo" name="to" readonly>
                </div>
                <div class="mb-2">
                  <label class="form-label">Subject</label>
                  <input class="form-control" id="replySubject" name="subject">
                </div>
                <div class="mb-3">
                  <label class="form-label">Message</label>
                  <textarea class="form-control" id="replyBody" name="body" rows="6" placeholder="Type your reply…"></textarea>
                </div>
                <button class="btn btn-primary w-100" id="replySendBtn"><i class="bi bi-send me-1"></i> Send Reply</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- CSRF meta for AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
(function(){
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const baseUrl = `{{ url('ctrl-panel-v2/messages') }}`;
  let modal, bsModal;

  function el(id){ return document.getElementById(id); }

  function renderReplies(list){
    const wrap = document.getElementById('repliesWrap');
    const target = document.getElementById('repliesList');
    target.innerHTML = '';
    if(!list || !list.length){ wrap.style.display='none'; return; }
    wrap.style.display = '';
    list.forEach(r => {
      const item = document.createElement('div');
      item.className = 'border-bottom pb-2 mb-2';
      item.innerHTML = `
        <div class="d-flex justify-content-between">
          <div>
            <div class="fw-semibold">${r.subject ?? 'Reply'}</div>
            <div class="text-muted small">To: ${r.to_email}${r.admin ? ' • by '+r.admin : ''}</div>
          </div>
          <div class="text-muted small mono">${r.created_at ?? ''}${r.mailed_at ? '<div>Sent: '+r.mailed_at+'</div>' : ''}</div>
        </div>
        <div class="mt-1" style="white-space:pre-wrap;">${r.body ? r.body.replace(/</g,'&lt;').replace(/>/g,'&gt;') : ''}</div>
      `;
      target.appendChild(item);
    });
  }

  async function openMessage(id){
    const res = await fetch(`${baseUrl}/${id}/json`, { credentials: 'same-origin' });
    if(!res.ok){ alert('Failed to load message.'); return; }
    const data = await res.json();
    const m = data.message;

    el('modalSubject').textContent = m.subject || ('Message #'+m.id);
    el('modalFrom').textContent = `${m.name ?? 'Unknown'}${m.email ? ' <'+m.email+'>' : ''}`;
    el('modalService').textContent = m.service || '—';
    el('modalTime').textContent = new Date(m.created_at).toLocaleString();
    el('modalBody').textContent = m.body || '';

    el('replyMessageId').value = m.id;
    el('replyTo').value = m.email || '';
    el('replySubject').value = 'Re: '+(m.subject || ('Message #'+m.id));
    el('replyBody').value = '';

    renderReplies(data.replies || []);

    // update UI row to read (if any badge exists)
    const row = document.querySelector(`.open-message[data-id="${m.id}"]`);
    if(row){
      row.classList.remove('is-unread');
      const badge = row.querySelector('.badge');
      if(badge){ badge.classList.remove('bg-primary'); badge.classList.add('bg-secondary'); }
    }

    if(!bsModal){ modal = document.getElementById('messageModal'); bsModal = new bootstrap.Modal(modal); }
    bsModal.show();
  }

  async function sendReply(e){
    e.preventDefault();
    const id = el('replyMessageId').value;
    const fd = new FormData(el('replyForm'));

    const res = await fetch(`${baseUrl}/${id}/reply`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
      body: fd,
      credentials: 'same-origin'
    });

    if(!res.ok){
      let msg = 'Failed to send reply.';
      try { const j = await res.json(); if(j.message) msg = j.message; } catch(e){}
      alert(msg); return;
    }

    const j = await res.json();
    if(j.ok){
      // append to replies list
      renderReplies([...(document.getElementById('repliesList').children).length ? Array.from(document.getElementById('repliesList').children).map(li=>({})):[], j.reply]);
      // simpler: reload thread
      openMessage(id);
    }
  }

  async function handleDelete(id, row){
    if(!confirm('Delete this message permanently? This cannot be undone.')) return;
    const res = await fetch(`${baseUrl}/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    });
    if(!res.ok){
      alert('Failed to delete this message.');
      return;
    }
    if(row){ row.remove(); }
    if(document.querySelectorAll('.message-row').length === 0){
      window.location.reload();
    }
  }

  async function clearAllMessages(){
    if(!confirm('Delete ALL messages permanently? This cannot be undone.')) return;
    const res = await fetch(baseUrl, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    });
    if(!res.ok){
      alert('Failed to clear messages.');
      return;
    }
    window.location.reload();
  }

  document.querySelectorAll('.open-message').forEach(elm => {
    elm.addEventListener('click', () => openMessage(elm.dataset.id));
  });
  document.getElementById('replyForm').addEventListener('submit', sendReply);
  document.querySelectorAll('.delete-message-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      handleDelete(btn.dataset.id, btn.closest('.message-row'));
    });
  });
  const clearBtn = document.getElementById('clearMessagesBtn');
  if(clearBtn){
    clearBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if(clearBtn.disabled) return;
      clearAllMessages();
    });
  }
})();
</script>
@endsection
