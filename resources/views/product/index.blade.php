@extends('layouts.main')

@section('content')
    <div class="container">
        <h1 class="text-center">Product List</h1>

        <div>
            <a class="btn btn-primary my-4" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="btn btn-primary my-4" id="add-product-btn" href="javascript:void(0)">Add Product</a>
        </div>

        <table class="table my-4" id="product-table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>


    {{-- Product Modal --}}
    <div class="modal fade" id="product-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="product-modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form id="product-form">
                        <input type="hidden" name="product_edit_id" id="product_edit_id" value="">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Name"
                                name="name">
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category_id">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="text" class="form-control" id="price" placeholder="Enter Price"
                                name="price">
                        </div>
                        <div class="modal-footer">
                            <input type="submit" value="" class="btn btn-primary m-3" id="product-submit-btn">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .error {
            color: red;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"
        integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/additional-methods.min.js"
        integrity="sha512-owaCKNpctt4R4oShUTTraMPFKQWG9UdWTtG6GRzBjFV4VypcFi6+M3yc4Jk85s3ioQmkYWJbUl1b2b2r41RTjA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let productTable = $('#product-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: "{{ route('product.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'category_name',
                        name: 'category_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'price',
                        name: 'price',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });


            $('#product-form').validate({
                rules: {
                    name: {
                        required: true,
                        lettersonly: true,
                        maxlength: 50
                    },
                    category_id: {
                        required: true,
                    },
                    price: {
                        required: true,
                        number: true,
                        pattern: /^\d+(\.\d{1,2})?$/,
                        min: 0.01,
                        max: 10000000 //maximum product price (as per requirement)
                    },
                },
                messages: {
                    name: {
                        required: "Name field is required",
                        maxlength: "Please enter less than 50 character"
                    },
                    category_id: {
                        required: 'Please select category',
                    },
                    price: {
                        required: 'Please enter price',
                        number: "Price must be a valid number.",
                        min: "Price must be at least 0.01.",
                        max: "Price must not exceed 10000000.",
                        pattern: "Price must have at most two decimal places"
                    },
                },

                submitHandler: function(form) {
                    let myform = document.getElementById("product-form");
                    let formData = new FormData(myform);

                    $.ajax({
                        type: "POST",
                        url: '{{ route('product.store') }}',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.success) {
                                alert(res.message);
                                $('#product-form').trigger("reset");
                                $('#product-modal').modal('hide');
                                productTable.draw();
                            } else {
                                alert(res.message);
                            }
                        },
                        error: function(response) {
                            if (response.responseJSON.errors) {
                                $.each(response.responseJSON.errors, function(field_name,
                                    error) {
                                    $(document).find('[name=' + field_name + ']')
                                        .after(
                                            '<label class="error" for="' +
                                            field_name +
                                            '">' + error + '</label>')
                                })
                            } else {
                                alert(response.message)
                            }
                        }
                    });
                }
            });

            $(document).on('click', '#add-product-btn', function(e) {
                e.preventDefault();
                $("#product-form").trigger("reset");
                $('label.error').html("");
                $("label.error").hide();
                $("#product_edit_id").val("");
                $('#category option').prop('selected', false);
                $("#product-submit-btn").val('Save');
                $("#product-modal-title").html('Create New Product');
                $("#product-modal").modal('show');
            });

            $(document).on('click', '.product-delete-btn', function(e) {
                e.preventDefault();
                let id = $(this).attr('data-id');
                if (confirm('Are you sure want to delete this data')) {
                    $.ajax({
                        type: "DELETE",
                        url: '{{ route('product.delete') }}',
                        data: {
                            'productId': id,
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                alert(res.message);
                                productTable.draw();
                            } else {
                                alert(res.message);
                            }
                        },
                    });
                }
            })

            $(document).on('click', '.product-edit-btn', function(e) {
                e.preventDefault();
                $("#product-form").trigger("reset");
                $('#category option').prop('selected', false);
                $('label.error').html("");
                $("label.error").hide();
                let id = $(this).attr('data-id');
                $.ajax({
                    type: "POST",
                    url: '{{ route('product.edit') }}',
                    data: {
                        'productId': id,
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $("#product-submit-btn").val('Update');
                            $("#product-modal-title").html('Update User');
                            $("#product_edit_id").val(res.data.product.id);
                            $("#name").val(res.data.product.name);
                            $("#price").val(res.data.product.price);

                            if (res.data.product.category) {
                                $('#category option[value="' + res.data.product.category.id +
                                    '"]').prop(
                                    'selected', true);
                            }

                            $('#product-modal').modal('show');
                        } else {
                            alert(res.message);
                        }
                    },
                });
            })
        });
    </script>
@endPush
