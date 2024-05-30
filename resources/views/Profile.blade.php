    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PEMA - PROFILE</title>
        <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="{{ asset('images/logo-pema.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    </head>
    <style>
        .profile-picture-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
    
        .profile-picture {
            width: 150px; /* Sesuaikan ukuran sesuai kebutuhan */
            height: 150px; /* Sesuaikan ukuran sesuai kebutuhan */
            border-radius: 50%; /* Membuat gambar berbentuk bulat */
            object-fit: cover; /* Memastikan gambar tidak terdistorsi */
            border: 2px solid #ccc; /* Opsional: tambahkan border */
        }

        /* Position the edit icon */
    .edit-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border-radius: 50%;
        padding: 5px;
        cursor: pointer;
    }

    /* Style the edit icon */
    .edit-icon i {
        font-size: 20px;
    }

    .separator {
    border-top: 1px solid #ddd; /* Atur warna dan style garis */
    margin: 10px 0; /* Atur jarak di atas dan di bawah garis */
    }

    </style>
    
    <body>
        <div class="container light-style flex-grow-1 container-p-y">
            <h4 class="font-weight-bold py-3 mb-4">
                Account settings
            </h4>
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card overflow-hidden">
                <div class="row no-gutters row-bordered row-border-light">
                    <div class="col-md-3 pt-0">
                        <div class="list-group list-group-flush account-settings-links">
                            <a class="list-group-item list-group-item-action active" data-toggle="list" href="#account-general">General</a>
                            <div class="separator"></div>   
                            <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-change-password">Change password</a>
                            <div class="separator"></div>
                            <div class="vertical-line" style="height: 100%; border-left: 1px solid #ddd; position: absolute; left: 275px; top: 0;"></div> <!-- Garis vertikal -->

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="account-general">
                                <form method="POST" action="{{ route('pema.upload.photo') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <div class="profile-picture-container" id="dropArea">
                                            <img src="{{ asset($user->photo ? 'storage/' . $user->photo : 'images/user_default.svg') }}" alt="Profile Picture" class="profile-picture img-fluid" id="profilePicture">
                                            <div class="edit-icon" id="editIcon">
                                                <i class="fas fa-pencil-alt"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="file" class="form-control-file" id="photo" name="photo" style="display: none;">
                                    <button type="submit" class="btn btn-primary" style="position: absolute; left: 380px;">Upload</button>
                                </form> 
                                
                                
                                <hr class="border-light m-0">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('pema.update.profile') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="nik">NIK</label>
                                            <input type="text" class="form-control" id="nik" name="nik" value="{{ $user->nik }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama">Nama</label>
                                            <input type="text" class="form-control" id="nama" name="nama" value="{{ $user->nama }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="telp">No. Telp</label>
                                            <input type="text" class="form-control" id="telp" name="telp" value="{{ $user->telp }}">
                                        </div>
                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-primary">Save changes</button>&nbsp;
                                            <button type="button" class="btn btn-default" onclick="window.location='{{ route('pema.index') }}'">Back To Home</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="account-change-password">
                                <div class="card-body pb-2">
                                    <form method="POST" action="{{ route('pema.change.password') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label class="form-label">Current password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="current_password" id="current_password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" onclick="togglePassword('current_password', 'current_password_icon')">
                                                        <i class="fa fa-eye" id="current_password_icon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">New password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password" id="new_password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" onclick="togglePassword('new_password', 'new_password_icon')">
                                                        <i class="fa fa-eye" id="new_password_icon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Confirm new password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password_confirmation" id="confirm_password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" onclick="togglePassword('confirm_password', 'confirm_password_icon')">
                                                        <i class="fa fa-eye" id="confirm_password_icon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-primary">Save changes</button>&nbsp;
                                            <button type="button" class="btn btn-default" onclick="window.location='{{ route('pema.index') }}'">Back To Home</button>
                                        </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }


    // Function to handle file input change
    function handleFileInputChange(event) {
            var file = event.target.files[0];
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('profilePicture').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }

        // Select the file input element and attach change event listener
        document.getElementById('photo').addEventListener('change', handleFileInputChange);

        // Select the edit icon and file input
        const editIcon = document.getElementById('editIcon');
        const fileInput = document.getElementById('photo');

        // Add click event listener to the edit icon
        editIcon.addEventListener('click', function() {
            fileInput.click();
        });

        // Function to handle form submission
        document.getElementById('saveChangesBtn').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission

            var formData = new FormData(document.querySelector('form'));

            $.ajax({
                url: "{{ route('pema.update.profile') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Handle success response
                    alert('Changes saved successfully!');
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error(error);
                    alert('Error saving changes!');
                }
            });
        });

    
    </script>

    </html>
