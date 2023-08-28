@if ($message = Session::get('success'))
    <div id="success-notification-content" class="toastify-content flex w-2/6"> <i class="text-success"
        data-lucide="check-circle"></i>
        <div class="ml-4 mr-4">
            <div class="font-medium text-base capitalize">{{ $message }}</div>
            <div class="text-slate-500 mt-1">Pesan ini akan hilang dalam 5 detik</div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var notificationContent = document.getElementById('success-notification-content');
        
        setTimeout(function () {
            notificationContent.classList.add('hidden');
        }, 5000);
    });
</script>
@elseif($message = Session::get('delete'))
    <div id="success-notification-content" class="toastify-content flex w-2/6"> <i class="text-danger"
        data-lucide="trash-2"></i>
        <div class="ml-4 mr-4">
            <div class="font-medium text-base">{{ $message }}</div>
            <div class="text-slate-500 mt-1">Pesan ini akan hilang dalam 5 detik</div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var notificationContent = document.getElementById('success-notification-content');
        
        setTimeout(function () {
            notificationContent.classList.add('hidden');
        }, 5000);
    });
</script>
@elseif($message = Session::get('error'))
    <div id="success-notification-content" class="toastify-content flex w-2/6"> <i class="text-danger"
        data-lucide="alert-circle"></i>
        <div class="ml-4 mr-4">
            <div class="font-medium text-base">{{ $message }}</div>
            <div class="text-slate-500 mt-1">Pesan ini akan hilang dalam 5 detik</div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var notificationContent = document.getElementById('success-notification-content');
        
        setTimeout(function () {
            notificationContent.classList.add('hidden');
        }, 5000);
    });
</script>
@elseif($message = Session::get('updated'))
<div id="success-notification-content" class="toastify-content flex w-2/6"> <i class="text-pending"
    data-lucide="pencil"></i>
    <div class="ml-4 mr-4">
        <div class="font-medium text-base">{{ $message }}</div>
        <div class="text-slate-500 mt-1">Pesan ini akan hilang dalam 5 detik</div>
    </div>
</div><script>
    document.addEventListener('DOMContentLoaded', function () {
        var notificationContent = document.getElementById('success-notification-content');
        
        setTimeout(function () {
            notificationContent.classList.add('hidden');
        }, 5000);
    });
</script>
@endif



