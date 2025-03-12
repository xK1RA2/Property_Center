@props(['Tailwind'=>0,'title'=>"Profile",'Dealers'])

<x-app-layout :$Tailwind :$title>



    <div class="container shadow-lg mt-5 p-4 bg-white rounded">
        <div class="row">
            <!-- Left Section -->
            <div class="col-md-4  ">
                <div class="p-3 text-center border-0 ">
                    <h5 class="fw-bold text-muted mb-4">Account Management</h5>
                    <img id="profileImage" src="images (1).jpg" 
                         class="img-fluid rounded-circle mx-auto d-block mt-2 shadow-sm" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <input type="file" id="imageUpload" class="d-none" accept="image/*">
                    <button class="btn btn-outline-primary mt-3" 
                            onclick="document.getElementById('imageUpload').click()">
                        Upload Photo
                    </button>
                </div>

                <form class="mt-3  p-3 rounded" action="POST">
                    <h6 class="fw-bold text-primary mb-3">Update Password</h6>
                    <div class="mb-3">
                        <label class="form-label text-muted">Old Password</label>
                        <input class="form-control rounded-pill" type="password" placeholder="Enter old password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">New Password</label>
                        <input class="form-control rounded-pill" type="password" placeholder="Enter new password" required>
                    </div>
                    <button class="btn btn-primary w-100 mt-2">Change Password</button>
                </form>

              
            </div>

            <!-- Right Section -->
            <div class="col-md-8 ">
                <div class="card p-4 border-0  position-relative">
                    
                    <h4 class="fw-bold text-dark mb-4 border-bottom">Profile Information</h4>
                    <form id="profileForm" action="POST">
                        <div class="row mb-4">
                            <div class="col-md-6">
                               
                               
                                <div class="p-1  justify-content-between align-items-center">
                                   <label class="   text-dark fs-5 ">Username : </label>
                                    <input class="  border-0 border-bottom form-control  bg-white" id="username" value="abed alrehman" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="p-1  justify-content-between align-items-center">
                                   <label class="   text-dark fs-5 ">Name : </label>
                                    <input class="  border-0 border-bottom form-control  bg-white" id="name" value="abed alrehman" disabled>
                                </div>
                            </div>
                        </div>
            
                        <div class="mt-4">
                            <h4 class="fw-bold text-dark border-bottom">Contact Info</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                <div class="p-1  justify-content-between align-items-center">
                                   <label class="   text-dark fs-5 ">Email : </label>
                                    <input class="  border-0 border-bottom form-control  bg-white" id="email" value="abed alrehman" disabled>
                                </div>
                                </div>
                                <div class="col-md-6">
                                <div class="p-1  justify-content-between align-items-center">
                                   <label class="   text-dark fs-5 ">Phone : </label>
                                    <input class="  border-0 border-bottom form-control  bg-white" id="phone" value="abed alrehman" disabled>
                                </div>
                                </div>
                            </div>
                        </div>
            
                     
                    
                        <button id="updateButton" class="btn btn-success mt-4 w-100 rounded-pill" type="submit" 
                                onclick="updateProfile(event)">
                            Update Profile
                        </button>
                    </form>

                  
                </div>
                <div class="shadow " style="margin-top:38px">
                <form action="" class=" p-3 rounded">

                <h3 class="text-center my-4 text-dark">Become a Trader</h3>

                <button type=" submit" class="btn btn-primary w-100 mt-2"> Submit Request </button>

                </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS and JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImage').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Toggle edit mode for all fields
        document.getElementById('editButton').addEventListener('click', function() {
            const inputs = document.querySelectorAll('#profileForm input');
            const updateButton = document.getElementById('updateButton');
            const isEditing = this.textContent === 'Save';
            
            if (isEditing) {
                // Save mode: disable inputs, remove borders, and change button text back to "Edit"
                inputs.forEach(input => {
                    input.disabled = true;
                    input.classList.remove('border');
                    input.classList.add('border-0');
                });
                this.textContent = 'Edit';
                updateButton.textContent = 'Update Profile'; // Reset Update button text
            } else {
                // Edit mode: enable inputs, add borders, change Update button to "Save Changes", and change Edit button to "Save"
                inputs.forEach(input => {
                    input.disabled = false;
                    input.classList.remove('border-0');
                    input.classList.add('border');
                });
                updateButton.textContent = 'Save Changes'; // Change Update button to "Save Changes"
                this.textContent = 'Save';
            }
        });

        // Update profile functionality
        function updateProfile(event) {
            event.preventDefault();
            const updateButton = document.getElementById('updateButton');
            const inputs = document.querySelectorAll('#profileForm input');
            const username = document.getElementById('username');
            const lastName = document.getElementById('lastName');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');

            if (updateButton.textContent === 'Update Profile') {
                // Switch to edit mode: enable inputs and add borders
                inputs.forEach(input => {
                    input.disabled = false;
                    input.classList.remove('border-0');
                    input.classList.add('border','rounded-pill','px-2' ,'w-1/2');
                });
                updateButton.textContent = 'Save Changes';
                document.getElementById('editButton').style.display = 'none'; // Hide Edit button while editing
                
            } else {
                // Save changes
                if (!username.value.trim() || !lastName.value.trim() || !email.value.trim() || !phone.value.trim()) {
                    alert('Please fill in all required fields (Username, Last Name, Email, and Phone)!');
                    return;
                }

                if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email.value.trim())) {
                    alert('Please enter a valid email address!');
                    return;
                }

                if (!/^\d{10}$/.test(phone.value.trim())) {
                    alert('Please enter a valid 10-digit phone number!');
                    return;
                }

                
                inputs.forEach(input => {
                    input.disabled = true;
                    input.classList.remove('border','rounded-pill','px-2' ,'w-1/2');
                    input.classList.add('border-0');
                });
                
                updateButton.textContent = 'Update Profile';
                document.getElementById('editButton').style.display = 'block'; // Show Edit button again
            }
        }

        // Interactive star rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            let currentRating = 3; // Default rating of 3.0 (3 stars out of 5)

            // Initialize stars based on default rating
            updateStars(currentRating);

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = parseInt(this.getAttribute('data-value'));
                    currentRating = value;
                    updateStars(value);
                    document.getElementById('rankingScore').textContent = `${value}.0`;
                    alert(`Ranking updated to ${value}.0 out of 5!`);
                });

                star.addEventListener('mouseover', function() {
                    const value = parseInt(this.getAttribute('data-value'));
                    updateStars(value, true);
                });

                star.addEventListener('mouseout', function() {
                    updateStars(currentRating);
                });
            });

            function updateStars(rating, hover = false) {
                stars.forEach(star => {
                    const starValue = parseInt(star.getAttribute('data-value'));
                    if (starValue <= rating) {
                        star.classList.add('filled');
                        star.style.color = '#007bff';
                    } else {
                        star.classList.remove('filled');
                        star.style.color = hover ? '#007bff' : '#ccc';
                    }
                });
            }
        });
    </script>
    </x-app-layout>
