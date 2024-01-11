@extends('layouts.master')

@section('content')

<div class="content">
    <div class="grid grid-cols-12 gap-2 mt-4">
        <div class="col-span-12 mt-8">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">
                    Information
                </h2>
                <a href="" class="ml-auto flex items-center text-primary"> <i data-lucide="refresh-ccw"
                        class="w-4 h-4 mr-3"></i> Reload Data </a>
            </div>
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="user" class="report-box__icon text-pending"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6">
                                {{ $adminCount ?? '0'}}
                            </div>
                            <div class="text-base text-slate-500 mt-1">Admin</div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="user" class="report-box__icon text-cyan-300"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6">
                                {{ $htCount }}
                            </div>
                            <div class="text-base text-slate-500 mt-1">HT</div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="user" class="report-box__icon text-pink-500"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6">
                                {{ $hrCount }}
                            </div>
                            <div class="text-base text-slate-500 mt-1">HR</div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="user" class="report-box__icon text-primary"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6">{{ $ordinaryEmployeeCount }}</div>
                            <div class="text-base text-slate-500 mt-1">Employee</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="col-span-12 lg:col-span-4 2xl:col-span-4 mt-2 scrollbar-hidden">
                    <div class="intro-y box p-5 mt-4 scrollbar-hidden">
                        <div class="intro-y">
                            <div class="mt-4">
                                <input id="search-input" type="text" class="search__input form-control bg-slate-100 border-transparent" placeholder="Search...">
                                <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                            </div>
                        </div>
                
                        <div class="mt-4 overflow-hidden scrollbar-hidden" style="max-height: 65rem; overflow-y: auto;">
                            <div class="intro-x">
                                <!-- Iterate over employee data -->
                                @foreach($employees as $employee)
                                    <div class="employee-box box px-5 py-3 mb-3 flex bg-slate-100 items-center zoom-in employee-item" data-employee-id="{{ $employee->id }}" data-employee-permissions="{{ json_encode($employee->permissions) }}">
                                        <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                            @php
                                                $avatarPath = $employee->employee->avatar ? asset('storage/' . $employee->employee->avatar) : ($employee->employee->gender == 'male' ? asset('images/default-boy.jpg') : asset('images/default-women.jpg'));
                                            @endphp
                                            <img class="tooltip rounded-full" src="{{ $avatarPath }}">
                                        </div>
                                        <div class="ml-4 mr-auto overflow-hidden">
                                            <div class="font-medium truncate employee-text">{{ $employee->employee->first_name }} {{ $employee->employee->last_name }}</div>
                                            <!-- Assuming each employee has a division relationship -->
                                            <div class="text-slate-500 text-xs mt-0.5">{{ $employee->employee->division->name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                <!-- End of employee iteration -->
                            </div>
                        </div>
                    </div>
                </div>

                <div id="select-user-message" class="col-span-12 lg:col-span-8 2xl:col-span-8 mt-2">
                    <h3 class="font-medium leading-8 text-center mt-6">Select employee on the box to grant access rights.</h3>
                </div>                
                
                <div id="permission-employee-container" class="col-span-12 lg:col-span-8 2xl:col-span-8 intro-y mt-6 hidden" data-employee-id="{{ $employee->id ?? '' }}" data-employee-name="{{ $employee->name ?? '' }}">
                    <div id="permission-cards-container" class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 sm:col-span-6 xl:col-span-12 intro-y mb-4">
                            <div class="report-box">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Auto select permission by positions.</div>
                                    <select id="role-select" class="form-select">
                                        <option data-employee-permissions="{{ json_encode($employee->permissions ?? '0') }}" value="hr">Human Resource</option>
                                        <option data-employee-permissions="{{ json_encode($employee->permissions ?? '0') }}" value="ht">Head of Tribe</option>
                                        <option data-employee-permissions="{{ json_encode($employee->permissions ?? '0') }}" value="ordinary">Ordinary Employee</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @php
                        $sortedGroups = $permissions->groupBy('group')->sortByDesc(function($groupPermissions) {
                            return count($groupPermissions);
                        });
                        @endphp

                        @foreach ($sortedGroups as $group => $groupPermissions)
                            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mb-4">
                                <form action="">
                                    @csrf
                                    <div class="report-box zoom-in">
                                        <div class="box p-5">
                                            <div class="font-medium leading-8">{{ $group }}</div>
                                            @php
                                            $sortedPermissions = $groupPermissions->sortBy('id');
                                            @endphp
                                            @foreach ($sortedPermissions as $key => $permission)
                                                <div class="form-check mt-2">
                                                    <input id="checkbox-{{ $permission->name }}" class="form-check-input permission-checkbox" type="checkbox" value="{{ $permission->id }}" data-employee-id="{{ $employee->id ?? '' }}" data-employee-name="{{ $employee->name ?? ''}}" {{ in_array($permission->name, $hasPermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="checkbox-{{ $permission->id }}">{{ $permission->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>        
                
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery.noConflict();

jQuery(document).ready(function($) {
    // Function to get permissions for a specific user
    function toggleSelectUserMessage() {
            var isSecondForeachVisible = !$('#permission-employee-container').hasClass('hidden');
            $('#select-user-message').toggleClass('hidden', isSecondForeachVisible);
        }

    function getUpdatedSelectRole() {
        var permissions = [];
                $('[id^="checkbox-"]:checked').each(function() {
                    permissions.push($(this).val());
                });

                // Get the role select element
                var roleSelect = $('#role-select');
                var selectedRole = roleSelect.val();

                // Check if permission IDs 38, 39, 42, and 43 are in the list of checked permissions
                var isPermission38Checked = permissions.includes('38');
                var isPermission39Checked = permissions.includes('39');
                var isPermission42Checked = permissions.includes('42');
                var isPermission43Checked = permissions.includes('43');

                // Update the selected role based on the checkbox state
                if (isPermission39Checked && isPermission43Checked) {
                    selectedRole = 'hr'; // Human Resource
                } else if (isPermission38Checked && isPermission42Checked) {
                    selectedRole = 'ht'; // Head of Tribe
                } else if (!isPermission38Checked || !isPermission39Checked || !isPermission42Checked || !isPermission43Checked) {
                    selectedRole = 'ordinary'; // Ordinary Employee
                } else {
                    // Handle other cases if needed
                }

                // Update the role select
                roleSelect.val(selectedRole);

    }
    function getUserPermissions(userId, employeePermissions, selectedRole) {
        console.log('Fetching permissions for user ID:', userId);
        $.ajax({
            url: `/permission/user/${userId}/permissions`,
            type: 'GET',
            success: function(response) {
                console.log('Permissions for user:', response);

                // Uncheck all checkboxes
                $('.permission-checkbox').prop('checked', false);

                // Check the checkboxes corresponding to the user's permissions
                response.forEach(function(permissionName) {
                    console.log('Checking checkbox for permission:', permissionName);
                    $('#checkbox-' + permissionName).prop('checked', true);
                });

                // initializeCheckboxStates(employeePermissions);
                getUpdatedSelectRole(response);

                // Check if all checkboxes are checked
                var allChecked = true;
                $('.permission-checkbox[data-employee-id="' + userId + '"]').each(function() {
                    if (!$(this).prop('checked')) {
                        allChecked = false;
                        return false; // Break the loop
                    }
                });

                var selectedRole = $('#role-select').val();

                $('.permission-checkbox').each(function() {
                    var permissionId = parseInt($(this).val());
                    var isHR = selectedRole === 'hr';
                    var isHT = selectedRole === 'ht';

                    if ((isHR && (permissionId === 38 || permissionId === 42)) ||
                        (isHT && (permissionId === 39 || permissionId === 43))) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });

                console.log('All checkboxes checked:', allChecked);
                $('#all-permissions-checkbox').prop('checked', allChecked);
            },
            error: function(error) {
                console.error('Error fetching permissions:', error);
            }
        });
    }
    
    // Handle click event on employee items
    $(document).on('click', '.employee-item', function() {
        var userId = $(this).data('employee-id');
        console.log('User clicked. ID:', userId);
        var employeePermissions = $(this).data('employee-permissions');
        console.log('Data in data-employee-permissions:', employeePermissions);
        var permissions = [];
                $('[id^="checkbox-"]:checked').each(function() {
                    permissions.push($(this).val());
                });


        // Add the employee's permissions to the permissions array
        $('[id^="checkbox-"]:checked').each(function() {
            employeePermissions.push($(this).val());
        });

        getUpdatedSelectRole(permissions);
        
        // Get the employee's name
        var employeeName = $(this).find('.employee-text').text();

        // Show a toastr message with the employee's name
        toastr.options = {
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 3000
                    };

        toastr.info('Employee clicked : ' + employeeName);

        var selectedRole = $('#role-select').val();
        console.log('Selected Role:', selectedRole);

        $('#permission-employee-container').data('employee-name', employeeName);
        $('#permission-employee-container').data('employee-id', userId); // Update the data-employee-id
        getUserPermissions(userId, employeePermissions, selectedRole);

        $('#permission-employee-container').removeClass('hidden');
            // Check if all checkboxes for this user are checked
            var allChecked = true;
            $('.permission-checkbox[data-employee-id="' + userId + '"]').each(function() {
                if (!$(this).prop('checked')) {
                    allChecked = false;
                    return false; // Break the loop
                }
            });

            console.log('All checkboxes for user ' + userId + ' checked:', allChecked);
            toggleSelectUserMessage();

            var selectedRole = $('#role-select').val();

            $('.permission-checkbox').each(function() {
                var permissionId = parseInt($(this).val());
                var isHR = selectedRole === 'hr';
                var isHT = selectedRole === 'ht';

                if ((isHR && (permissionId === 38 || permissionId === 42)) ||
                    (isHT && (permissionId === 39 || permissionId === 43))) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });

});

    toggleSelectUserMessage();

    // Handle search input
    $('#search-input').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        $('.employee-item').each(function() {
            var employeeText = $(this).find('.employee-text').text().toLowerCase();
            if (employeeText.includes(query)) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    });

    // Add an event listener for the change event on #role-select dropdown
    $('#role-select').on('change', function() {
                var selectedRole = $(this).val(); // Get the selected role

                // Define a mapping of roles to permission IDs
                var roleToPermissions = {
                    hr: [1, 2, 7, 8, 13, 14, 19, 20, 21, 22, 27, 28, 33, 34, 35, 39, 40, 43], // Map HR role to permission IDs
                    ht: [1, 2, 7, 8, 13, 14, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 38, 40, 42], // Map HT role to permission IDs
                    ordinary: [41]  // Map Ordinary Employee role to permission IDs
                };

                // Get the permission IDs for the selected role
                var selectedPermissions = roleToPermissions[selectedRole];
                
                // Get the user ID from the container
                var userId = $('#permission-employee-container').data('employee-id');
                var names = $('#permission-employee-container').data('employee-name');

                // Log the updated selected role value
                console.log('Selected Role (after change):', selectedRole);

                // Check or uncheck the checkboxes based on the selected role's permissions
                // Check checkboxes based on permissionsToCheck
                $('.permission-checkbox').each(function () {
                    var permissionId = parseInt($(this).val());
                    var shouldCheck = selectedPermissions.includes(permissionId);
                    $(this).prop('checked', shouldCheck);

                    // Disable checkboxes based on permission IDs and roles
                    if ((selectedRole === 'hr' && (permissionId === 38 || permissionId === 42)) ||
                        (selectedRole === 'ht' && (permissionId === 39 || permissionId === 43))) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });

                // Update user permissions based on the selected role's permissions
                updatePermissions(userId, selectedPermissions, names, selectedRole);
            }); 

        // Handle updating user permissions on checkbox change
        $('[id^="checkbox-"]').on('change', function() {
                // Collect all checked permissions and their IDs
                var permissions = [];
                $('[id^="checkbox-"]:checked').each(function() {
                    permissions.push($(this).val());
                });

                // Get the user ID from the container
                var userId = $('#permission-employee-container').data('employee-id');
                var names = $('#permission-employee-container').data('employee-name');

                // Get the role select element
                var roleSelect = $('#role-select');
                var selectedRole = roleSelect.val();

                // Check if permission IDs 38, 39, 42, and 43 are in the list of checked permissions
                var isPermission38Checked = permissions.includes('38');
                var isPermission39Checked = permissions.includes('39');
                var isPermission42Checked = permissions.includes('42');
                var isPermission43Checked = permissions.includes('43');

                // Update the selected role based on the checkbox state
                if (isPermission39Checked && isPermission43Checked) {
                    selectedRole = 'hr'; // Human Resource
                } else if (isPermission38Checked && isPermission42Checked) {
                    selectedRole = 'ht'; // Head of Tribe
                } else if (!isPermission38Checked || !isPermission39Checked || !isPermission42Checked || !isPermission43Checked) {
                    selectedRole = 'ordinary'; // Ordinary Employee
                } else {
                    // Handle other cases if needed
                }

                // Update the role select
                roleSelect.val(selectedRole);

                $('.permission-checkbox').each(function() {
                    var permissionId = parseInt($(this).val());
                    var isHR = selectedRole === 'hr';
                    var isHT = selectedRole === 'ht';

                    // Disable checkboxes based on permission IDs and roles
                    if ((isHR && (permissionId === 38 || permissionId === 42)) ||
                        (isHT && (permissionId === 39 || permissionId === 43))) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });

                // Update user permissions based on multiple checkbox change
                updatePermissions(userId, permissions, names);
            });


        // Function to update user permissions based on multiple checkbox change
        function updatePermissions(userId, permissions, names, selectedRole) {
            $.ajax({
                url: `/permission/user/${userId}/permissions`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    permissions: permissions,
                },

                success: function(response) {
                    console.log('User permissions updated successfully:', response);

                    toastr.options = {
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 3000
                };
                    toastr.success('Permissions updated successfully');

                },
                error: function(error) {
                    console.error('Error updating user permission:', error);
                }
            });
        }

});

</script>

@endsection