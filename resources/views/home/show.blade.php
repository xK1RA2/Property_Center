
@props(['total'=>0])
 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="C:\Users\bahaa\OneDrive\Desktop\Property_Center\public\css\bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet"> 
 <x-app-layout title="Property Details"  >


    <!-- جافا سكريبت للتقيم -->
    <script>
        document.querySelectorAll('input[name="rating"]').forEach((input) => {
            input.addEventListener('change', function () {
                const selectedStars = this.value;
                const preview = document.getElementById('selectedRating');
                preview.innerHTML = `Your rating: ${'★'.repeat(selectedStars)} (${selectedStars} out of 5)`;
            });
        });
        document.getElementById('ratingForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const selectedRating = document.querySelector('input[name="rating"]:checked');
            if (selectedRating) {
                alert(`Your rating has been submitted: ${selectedRating.value} stars`);
            } else {
                alert('Please select a rating before submitting!');
            }
        });

        // (pop massege)جافا سكريبت لفورم الحجز
        document.getElementById('bookingForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const fullName = document.getElementById('fullName').value;
            const phoneNumber = document.getElementById('phoneNumber').value;
            const appointmentDate = document.getElementById('appointmentDate').value;
            const appointmentTime = document.getElementById('appointmentTime').value;
            const notes = document.getElementById('notes').value;

            if (fullName && phoneNumber && appointmentDate && appointmentTime) {
                alert(`Booking Submitted!\nName: ${fullName}\nPhone: ${phoneNumber}\nDate: ${appointmentDate}\nTime: ${appointmentTime}\nNotes: ${notes || 'None'}`);
                this.reset(); // Reset the form
                bootstrap.Modal.getInstance(document.getElementById('bookingModal')).hide(); // Close the modal
            } else {
                alert('Please fill in all required fields!');
            }
        });
    </script>
   
    
</x-app-layout >

</x-app-layout>