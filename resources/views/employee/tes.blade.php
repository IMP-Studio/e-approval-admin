<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <title>Employee Live Search with AJAX</title>
</head>

<body>
    <div class="container">
        <h4 class="text-center mt-4">Live Search in Laravel 8 using AJAX MySql</h3>
            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Search employee"
                                                id="search">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <img src="https://assets.tokopedia.net/assets-tokopedia-  lite/v2/zeus/kratos/af2f34c3.svg"
                                                        alt="">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <table class="table table-striped table-inverse table-responsive d-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Phone Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script>
        $('#search').on('keyup', function(){
            search();
        });
        search();
        function search(){
             var keyword = $('#search').val();
             $.post('{{ route("employee.search") }}',
              {
                 _token: $('meta[name="csrf-token"]').attr('content'),
                 keyword:keyword
               },
               function(data){
                table_post_row(data);
                  console.log(data);
               });
        }
        // table row with ajax
        function table_post_row(res){
        let htmlView = '';
        if(res.employees.length <= 0){
            htmlView+= `
               <tr>
                  <td colspan="4">No data.</td>
              </tr>`;
        }
        for(let i = 0; i < res.employees.length; i++){
            htmlView += `
                <tr>
                   <td>`+ (i+1) +`</td>
                      <td>`+res.employees[i].name+`</td>
                       <td>`+res.employees[i].phone+`</td>
                </tr>`;
        }
             $('tbody').html(htmlView);
        }
        </script>
</body>

</html>
