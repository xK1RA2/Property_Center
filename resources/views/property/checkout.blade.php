@props(['title'=>"AddProperty" , 'Cities' , 'propertyType','Purchases'])
<x-app-layout :$title >
<button type="button" class="btn btn-primary m-3" data-bs-toggle="modal" data-bs-target="#checkoutModal">
        Open Checkout Form
    </button>
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Complete Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
                    </div>
                    <input type="hidden" id="paymentMethod" name="paymentMethod" value="">
                    <div class="mb-3">
                        <label for="cardNumber" class="form-label">Card Number</label>
                        <div class="card-number-container">
                            <input type="text" class="form-control" id="cardNumber" placeholder="xxxx xxxx xxxx xxxx" maxlength="19" required>
                            <i class="fa-solid fa-credit-card card-icon" id="cardIcon"></i>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="expiry" class="form-label">Expiry Date</label>
                            <input type="text" class="form-control" id="expiry" placeholder="MM/YY" required>
                            <div id="expiry-error" class="error-message">Please enter a valid expiry date (MM/YY) that is not expired.</div>
                        </div>
                        <div class="col">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" placeholder="123" maxlength="3" required>
                            <div id="cvv-error" class="error-message">CVV must be exactly 3 digits.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" rows="3" placeholder="Enter your address" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="completePayment">Complete Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const cardNumberInput = document.getElementById('cardNumber');
        const paymentMethodInput = document.getElementById('paymentMethod');
        const cardIcon = document.getElementById('cardIcon');
        const expiryInput = document.getElementById('expiry');
        const cvvInput = document.getElementById('cvv');
        const expiryError = document.getElementById('expiry-error');
        const cvvError = document.getElementById('cvv-error');
        const completePaymentBtn = document.getElementById('completePayment');

        cardNumberInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 16) {
                value = value.slice(0, 16);
            }
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            e.target.value = formattedValue;

            if (value.length >= 4) {
                const firstTwoDigits = parseInt(value.slice(0, 2));
                const firstDigit = parseInt(value[0]);
                if (firstDigit === 4) {
                    paymentMethodInput.value = 'visa';
                    cardIcon.className = 'fa-brands fa-cc-visa card-icon';
                } else if (firstTwoDigits >= 51 && firstTwoDigits <= 55) {
                    paymentMethodInput.value = 'mastercard';
                    cardIcon.className = 'fa-brands fa-cc-mastercard card-icon';
                } else {
                    paymentMethodInput.value = '';
                    cardIcon.className = 'fa-solid fa-credit-card card-icon';
                }
            } else {
                paymentMethodInput.value = '';
                cardIcon.className = 'fa-solid fa-credit-card card-icon';
            }
        });

        cardNumberInput.addEventListener('keypress', function (e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        cvvInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 3) {
                value = value.slice(0, 3);
            }
            e.target.value = value;
            if (value.length === 3) {
                cvvError.style.display = 'none';
            } else {
                cvvError.style.display = 'block';
            }
        });

        cvvInput.addEventListener('keypress', function (e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        expiryInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) {
                value = value.slice(0, 4);
            }
            if (value.length >= 2) {
                e.target.value = value.slice(0, 2) + (value.length > 2 ? '/' + value.slice(2) : '');
            } else {
                e.target.value = value;
            }
            validateExpiry();
        });

        function validateExpiry() {
            const value = expiryInput.value;
            const regex = /^(0[1-9]|1[0-2])\/[0-9]{2}$/;
            if (!regex.test(value)) {
                expiryError.style.display = 'block';
                return false;
            }
            const [month, year] = value.split('/').map(Number);
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear() % 100;
            const currentMonth = currentDate.getMonth() + 1;
            const fullYear = 2000 + year;
            const expiryDate = new Date(fullYear, month - 1, 1);

            if (year < currentYear || (year === currentYear && month < currentMonth)) {
                expiryError.style.display = 'block';
                return false;
            }
            expiryError.style.display = 'none';
            return true;
        }

        completePaymentBtn.addEventListener('click', function () {
            const isCvvValid = cvvInput.value.length === 3 && /^\d{3}$/.test(cvvInput.value);
            const isExpiryValid = validateExpiry();
            const isPaymentMethodValid = paymentMethodInput.value !== '';

            if (!isCvvValid) {
                cvvError.style.display = 'block';
            }
            if (!isExpiryValid) {
                expiryError.style.display = 'block';
            }
            if (!isPaymentMethodValid) {
                alert('Please enter a valid card number (Visa or Mastercard).');
                return;
            }

            if (isCvvValid && isExpiryValid && isPaymentMethodValid) {
                const formData = {
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    paymentMethod: paymentMethodInput.value,
                    cardNumber: cardNumberInput.value.replace(/\s/g, ''),
                    expiry: expiryInput.value,
                    cvv: cvvInput.value,
                    address: document.getElementById('address').value
                };
                console.log('Form Data:', formData);
                alert('Payment submitted!');
            } else {
                alert('Please correct the errors in the form.');
            }
        });
    </script>
    </x-app-layout  >