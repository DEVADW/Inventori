# Approval Service Implementation Guide

## 📋 Overview

ApprovalService adalah centralized service untuk mengelola approval logic di semua inspection controller (OFA, Monitor, Proyektor, SS6, Stayolt, UPS).

## ✅ Keuntungan

- **DRY**: Approval logic hanya di satu tempat
- **Konsistensi**: Response format sama di semua controller
- **Reusability**: Bisa digunakan di multiple controller
- **Maintainability**: Mudah update atau tambah feature
- **Testability**: Mudah untuk unit testing

## 📁 File Structure

```
app/
├── Services/
│   ├── ApprovalService.php              ← Service utama
│   ├── APPROVAL_SERVICE_USAGE.md        ← Contoh penggunaan
│   └── MonitorController.example.php    ← Contoh implementasi
├── Http/Controllers/inspeksi/
│   ├── OfaController.php                ← Sudah refactored ✅
│   ├── MonitorController.php            ← Perlu refactor
│   ├── ProyektorController.php          ← Perlu refactor
│   ├── Ss6Controller.php                ← Perlu refactor
│   ├── StavoltController.php            ← Perlu refactor
│   └── UpsController.php                ← Perlu refactor
```

## 🚀 Implementation Steps

### Step 1: Setup ApprovalService (SUDAH SELESAI ✅)

ApprovalService sudah dibuat di `app/Services/ApprovalService.php` dengan methods:
- `approve()` - Approve single inspection
- `approveMultiple()` - Approve multiple inspections
- `generateApprovalData()` - Generate approval data
- `canUpdate()` - Check if record can be updated
- `getApprovalStatus()` - Get approval status info

### Step 2: Update Each Inspection Controller

Pattern yang sama untuk semua controller:

```php
// 1. Import service
use App\Services\ApprovalService;

// 2. Inject via constructor
public function __construct(protected ApprovalService $approvalService)
{
}

// 3. Add approval methods
public function approve(Request $request, ModelClass $model)
{
    $result = $this->approvalService->approve($request, $model);
    if (!$result['success']) {
        return back()->with($result['type'], $result['message']);
    }
    return back()->with($result['type'], $result['message']);
}

// 4. Check approval before update
public function update(Request $request, ModelClass $model)
{
    if (!$this->approvalService->canUpdate($model)) {
        return redirect()->back()
            ->with('error', 'Data yang sudah di-approve tidak dapat diubah.');
    }
    // ... update logic
}
```

### Step 3: Update Routes

Tambahkan approval routes di `routes/web.php`:

```php
Route::middleware('auth')->prefix('inspeksi')->name('inspeksi.')->group(function () {
    // OFA Routes
    Route::prefix('ofa')->name('ofa.')->group(function () {
        Route::post('{ofa}/approve', [OfaController::class, 'approve'])->name('approve');
        Route::post('approve-all', [OfaController::class, 'approveAll'])->name('approve-all');
    });

    // Monitor Routes
    Route::prefix('monitor')->name('monitor.')->group(function () {
        Route::post('{monitor}/approve', [MonitorController::class, 'approve'])->name('approve');
        Route::post('approve-all', [MonitorController::class, 'approveAll'])->name('approve-all');
    });

    // Proyektor Routes
    Route::prefix('proyektor')->name('proyektor.')->group(function () {
        Route::post('{proyektor}/approve', [ProyektorController::class, 'approve'])->name('approve');
        Route::post('approve-all', [ProyektorController::class, 'approveAll'])->name('approve-all');
    });

    // Repeat untuk SS6, Stayolt, UPS
});
```

### Step 4: Update Views

Tambahkan approval buttons di index view:

```blade
<table>
    <tbody>
        @forelse($models as $model)
            <tr>
                <td>{{ $model->code_number_unit }}</td>
                <td>
                    @if($model->approved_at)
                        <span class="badge bg-success">Approved</span>
                        <small>{{ $model->approved_by }}</small>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </td>
                <td>
                    @if($isGroupLeader && !$model->approved_at)
                        <form method="POST" action="{{ route('inspeksi.modelname.approve', $model) }}" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-success">Approve</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="10" class="text-center">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
```

## 🔧 Custom Role (Optional)

Jika ada inspection yang membutuhkan role berbeda dari 'GL':

```php
// Contoh: Jika membutuhkan 'SUPERVISOR' role
public function approve(Request $request, ModelClass $model)
{
    $result = $this->approvalService->approve($request, $model, 'SUPERVISOR');
    // ...
}
```

## 📤 Response Format

### Success Response
```php
[
    'success' => true,
    'message' => 'Inspeksi berhasil di-approve.',
    'type' => 'success',
    'count' => 5  // hanya untuk approveMultiple
]
```

### Error/Info Response
```php
[
    'success' => false,
    'message' => 'Inspeksi ini sudah di-approve.',
    'type' => 'info'
]
```

## 🧪 Testing

```php
// tests/Unit/Services/ApprovalServiceTest.php

public function test_approve_single_inspection()
{
    $service = new ApprovalService();
    $model = OfaModel::factory()->create();
    $user = User::factory()->create(['jabatan' => 'GL']);
    
    $request = Request::create('/', 'POST');
    $request->setUserResolver(fn() => $user);
    
    $result = $service->approve($request, $model);
    
    $this->assertTrue($result['success']);
    $this->assertNotNull($model->fresh()->approved_at);
}

public function test_cannot_approve_already_approved()
{
    $service = new ApprovalService();
    $model = OfaModel::factory()->approved()->create();
    
    $result = $service->approve($request, $model);
    
    $this->assertFalse($result['success']);
}
```

## 🚨 Authorization

Hanya user dengan `jabatan = 'GL'` yang bisa approve. Jika tidak:
- Throw 403 Forbidden
- Message: "Hanya GL yang dapat melakukan approval."

## 📝 TODO Checklist untuk Implement

- [ ] ApprovalService dibuat ✅
- [ ] OfaController refactored ✅
- [ ] MonitorController refactored
- [ ] ProyektorController refactored
- [ ] Ss6Controller refactored
- [ ] StavoltController refactored
- [ ] UpsController refactored
- [ ] Routes updated
- [ ] Views updated
- [ ] Unit tests created
- [ ] Feature tests created

## 💡 Future Enhancements

1. **Email Notification**
   ```php
   // Kirim email setelah approval
   Mail::send(new InspectionApproved($inspection));
   ```

2. **Audit Log**
   ```php
   // Log semua approval action
   AuditLog::create([
       'action' => 'approved',
       'model' => get_class($inspection),
       'model_id' => $inspection->id,
       'user_id' => $request->user()->id,
   ]);
   ```

3. **Approval Workflow**
   ```php
   // Support multiple approval stages
   // e.g., pending -> reviewed -> approved
   ```

4. **Bulk Export**
   ```php
   // Export approved inspections as PDF/Excel
   public function exportApproved()
   ```

## 📞 Support

Lihat file contoh di:
- `app/Services/APPROVAL_SERVICE_USAGE.md`
- `app/Services/MonitorController.example.php`
