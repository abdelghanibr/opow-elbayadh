@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h3>🖥️ Devices ZKTeco</h3>
        <a href="{{ route('devices.create') }}" class="btn btn-primary">➕ Ajouter</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>IP</th>
                <th>Port</th>
                <th>Location</th>
                <th>Actions</th>
                <th>Connexion</th>
            </tr>
        </thead>

        <tbody>
        @foreach($devices as $device)
            <tr>
                <td>{{ $device->id }}</td>
                <td>{{ $device->name }}</td>
                <td>{{ $device->ip }}</td>
                <td>{{ $device->port }}</td>
                <td>{{ $device->location }}</td>

                <td class="d-flex gap-2">
                    <a href="{{ route('devices.edit',$device) }}" class="btn btn-sm btn-warning">✏️</a>

                    <form action="{{ route('devices.destroy',$device) }}" method="POST"
                          onsubmit="return confirm('Supprimer ce device ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">🗑️</button>
                    </form>
                </td>

                <td>
                    <button class="btn btn-sm btn-primary mb-1"
                        onclick="connectDevice('{{ route('devices.connect', $device->id) }}', {{ $device->id }})">
                        Connect
                    </button>

                    <button class="btn btn-sm btn-success mb-1"
                        onclick="openZkUsersModal({{ $device->id }})">
                        Import Users
                    </button>

                    <br>
                    <span id="status-{{ $device->id }}" class="badge bg-secondary mt-1">—</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>

<!-- Modal ZK Users -->
<div class="modal fade" id="zkUsersModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">ZK Users</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <table class="table table-bordered" id="zkUsersTable">
          <thead>
            <tr>
              <th><input type="checkbox" id="checkAll"></th>
              <th>UID</th>
              <th>UserID</th>
              <th>Name</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" onclick="importSelected()">Importer sélection</button>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')

<script>
let currentDevice = null;
let zkUsers = [];

/* ===================== CONNECT DEVICE ===================== */
function connectDevice(url, deviceId) {
    const statusEl = document.getElementById('status-' + deviceId);

    statusEl.className = 'badge bg-warning';
    statusEl.innerText = 'Connecting...';

    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('HTTP ' + res.status);
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'connected') {
            statusEl.className = 'badge bg-success';
            statusEl.innerText = 'CONNECTED';
        } else if (data.status === 'inactive') {
            statusEl.className = 'badge bg-secondary';
            statusEl.innerText = 'INACTIVE';
        } else {
            statusEl.className = 'badge bg-danger';
            statusEl.innerText = 'FAILED';
        }
    })
    .catch(err => {
        console.error(err);
        statusEl.className = 'badge bg-danger';
        statusEl.innerText = 'ERROR';
    });
}

/* ===================== OPEN MODAL ===================== */
function openZkUsersModal(deviceId) {
    currentDevice = deviceId;

    fetch(`/devices/${deviceId}/zk-users`)
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(res => {
            console.log('API RESPONSE:', res);

            if (!res.data || typeof res.data !== 'object') {
                throw new Error('Invalid data format');
            }

            // ✅ تحويل OBJECT إلى ARRAY
            zkUsers = Object.values(res.data);

            const tbody = document.querySelector('#zkUsersTable tbody');
            tbody.innerHTML = '';

            if (zkUsers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                `;
            } else {
                zkUsers.forEach((u, i) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>
                              <input type="checkbox" class="user-check" data-index="${i}">
                            </td>
                            <td>${u.uid}</td>
                            <td>${u.userid}</td>
                            <td>${u.name}</td>
                        </tr>
                    `;
                });
            }

            new bootstrap.Modal(
                document.getElementById('zkUsersModal')
            ).show();
        })
        .catch(err => {
            console.error('LOAD USERS ERROR:', err);
            alert('Erreur chargement users : ' + err.message);
        });
}


/* ===================== IMPORT SELECTED ===================== */
function importSelected() {
    let selected = [];

    document.querySelectorAll('.user-check:checked').forEach(cb => {
        selected.push(zkUsers[cb.dataset.index]);
    });

    if (selected.length === 0) {
        alert('Sélectionnez au moins un utilisateur');
        return;
    }

    fetch(`/devices/${currentDevice}/import-selected-users`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ users: selected })
    })
    .then(res => res.json())
    .then(res => {
        alert(`Utilisateurs importés : ${res.count}`);
        location.reload();
    })
    .catch(err => {
        console.error(err);
        alert('Erreur lors de l’import');
    });
}

/* ===================== CHECK ALL ===================== */
document.addEventListener('change', function(e) {
    if (e.target.id === 'checkAll') {
        document.querySelectorAll('.user-check').forEach(cb => {
            cb.checked = e.target.checked;
        });
    }
});
</script>
@endpush
