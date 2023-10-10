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
                            </div>
                            <div class="text-base text-slate-500 mt-1">1 Admin</div>
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
                            </div>
                            <div class="text-base text-slate-500 mt-1">3 HT</div>
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
                            </div>
                            <div class="text-base text-slate-500 mt-1">2 HR</div>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i data-lucide="user" class="report-box__icon text-primary"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6"></div>
                            <div class="text-base text-slate-500 mt-1">8 Employee</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="col-span-12 lg:col-span-4 2xl:col-span-2 mt-2 scrollbar-hidden">
                    <div class="intro-y box bg-primary p-5 mt-4 scrollbar-hidden">
                        <div class="intro-y">
                            <div class="mt-4">
                                <input id="search-input" type="text" class="search__input form-control border-transparent" placeholder="Search...">
                            </div>
                        </div>
                
                        <div class="mt-4 scrollbar-hidden" style="max-height: 800px; overflow-y: auto;">
                            <div class="intro-x">
                                <!-- Iterate over employee data -->
                                @foreach($employees as $employee)
                                    <div class="employee-box box px-5 py-3 mb-3 flex items-center zoom-in employee-item" data-employee-id="{{ $employee->id }}">
                                        <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                            @if($employee->gender == 'male')
                                                <img class="tooltip rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                            @elseif($employee->gender == 'female')
                                                <img class="tooltip rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                            @endif
                                        </div>
                                        <div class="ml-4 mr-auto overflow-hidden">
                                            <div class="font-medium truncate employee-text">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                            <!-- Assuming each employee has a division relationship -->
                                            <div class="text-slate-500 text-xs mt-0.5">{{ $employee->division->name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                <!-- End of employee iteration -->
                            </div>
                        </div>
                    </div>
                </div>
                
                

                <div id="permission-employee-container" class="col-span-12 lg:col-span-8 2xl:col-span-10 hidden">
                    <div id="permission-cards-container" class="grid grid-cols-12 gap-4 mt-4">
                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Divisions
                                    </div>
                                    <div class="form-check mt-2"> <input id="checkbox-divisions-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-divisions-1">View Divisions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-divisions-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-divisions-2">Add Divisions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-divisions-3" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-divisions-3">Edit Divisions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-divisions-4" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-divisions-4">Delete Divisions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-divisions-5" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-divisions-5">Import Divisions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-divisions-6" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-divisions-6">Export Divisions</label> </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Positions
                                    </div>
                                    <div class="form-check mt-2"> <input id="checkbox-positions-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-positions-1">View Positions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-positions-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-positions-2">Add Positions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-positions-3" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-positions-3">Edit Positions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-positions-4" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-positions-4">Delete Positions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-positions-5" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-positions-5">Import Positions</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-positions-6" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-positions-6">Export Positions</label> </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Employees</div>
                                    <div class="form-check mt-2"> <input id="checkbox-employees-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-employees-1">View Employees</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-employees-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-employees-2">Add Employees</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-employees-3" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-employees-3">Edit Employees</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-employees-4" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-employees-4">Delete Employees</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-employees-5" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-employees-5">Import Employees</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-employees-6" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-employees-6">Export Employees</label> </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Partners</div>
                                    <div class="form-check mt-2"> <input id="checkbox-partners-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-partners-1">View Partners</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-partners-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-partners-2">Add Partners</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-partners-3" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-partners-3">Edit Partners</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-partners-4" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-partners-4">Delete Partners</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-partners-5" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-partners-5">Import Partners</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-partners-6" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-partners-6">Export Partners</label> </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Projects</div>
                                    <div class="form-check mt-2"> <input id="checkbox-projects-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-projects-1">View Projects</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-projects-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-projects-2">Add Projects</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-projects-3" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-projects-3">Edit Projects</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-projects-4" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-projects-4">Delete Projects</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-projects-5" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-projects-5">Import Projects</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-projects-6" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-projects-6">Export Projects</label> </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Roles</div>
                                    <div class="form-check mt-2"> <input id="checkbox-roles-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-roles-1">View Roles</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-roles-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-roles-2">Add Roles</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-roles-3" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-roles-3">Edit Roles</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-roles-4" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-roles-4">Delete Roles</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-roles-5" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-roles-5">Import Roles</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-roles-6" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-roles-6">Export Roles</label> </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Approve</div>
                                    <div class="form-check mt-2"> <input id="checkbox-approve-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-approve-1">Approve Prelimenary</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-approve-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-approve-2">Approve Allowed</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-approve-3" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-approve-3">Reject Presence</label> </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Request Presence</div>
                                    <div class="form-check mt-2"> <input id="checkbox-requestpresence-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-requestpresence-1">View Request Pending</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-requestpresence-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-requestpresence-2">View Request Prelimenary</label> </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Standups</div>
                                    <div class="form-check mt-2"> <input id="checkbox-standups-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-standups-1">View Standups</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-standups-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-standups-2">Export Standups</label> </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Presences</div>
                                    <div class="form-check mt-2"> <input id="checkbox-presences-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-presences-1">View Presences</label> </div>
                                    <div class="form-check mt-2"> <input id="checkbox-presences-2" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-presences-2">Export Presences</label> </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y mt-2">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="font-medium leading-8">Mobile Access</div>
                                    <div class="form-check mt-2"> <input id="checkbox-mobileaccess-1" class="form-check-input permission-checkbox" type="checkbox" value=""> <label class="form-check-label" for="checkbox-mobileaccess-1">Can Access</label> </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
 $(document).ready(function() {
        // Event listener for employee item click
        $('.employee-item').on('click', function() {
            // Auto-check all checkboxes when an employee item is clicked
            $('.permission-checkbox').attr('checked', true);


        });

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
    });
    
    $(document).ready(function() {

    // Handle employee selection
    $('.employee-item').on('click', function() {
        // Get the employee ID
        var employeeId = $(this).data('employee-id');

        // Call a function to auto-store and auto-update permissions for the employee
        autoStoreAndUpdatePermissions(employeeId);

        // Show the employee details and licensing menu container
        $('#permission-employee-container').removeClass('hidden');
    });

    // Function to fetch and display the licensing menu based on employee ID
    function autoStoreAndUpdatePermissions(employeeId) {
        // Fetch the licensing menu based on the employee ID
        // Display the fetched licensing menu in the appropriate container
        // Modify this function according to how you retrieve and display the licensing menu
        console.log('Fetching licensing menu for employee with ID:', employeeId);
    }
});
</script>

@endsection