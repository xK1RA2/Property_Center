   <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container pb-5">
                <div class="row g-5">
                    <div class="col-lg-4 col-md-6">
                        <h5 class="text-white mb-4">Get In Touch</h5>
                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Jordan/Amman</p>
                        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+962 787847915</p>

                        <div class="d-flex pt-2">
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h5 class="text-white mb-4">Quick Links</h5>
                        <a class="btn btn-link text-white-50" href="">About Us</a>
                        <a class="btn btn-link text-white-50" href="">Contact Us</a>
                        <a class="btn btn-link text-white-50" href="">Our Services</a>
                        <a class="btn btn-link text-white-50" href="">Privacy Policy</a>
                        <a class="btn btn-link text-white-50" href="">Terms & Condition</a>
                    </div>
                    <div class="col-lg-4 col-md-6" id="newsletter">
                        <h5 class="text-white mb-4">Newsletter</h5>
                        <p>New listings and offers, straight to your inbox.</p>

                        @if (session('newsletter_status'))
                            <div class="alert alert-success py-2 px-3" role="status">{{ session('newsletter_status') }}</div>
                        @endif
                        @if (session('newsletter_error'))
                            <div class="alert alert-danger py-2 px-3" role="alert">{{ session('newsletter_error') }}</div>
                        @endif
                        @error('newsletter_email')
                            <div class="alert alert-danger py-2 px-3" role="alert">{{ $message }}</div>
                        @enderror

                        <form method="POST" action="{{ route('newsletter.subscribe') }}" class="position-relative mx-auto" style="max-width: 400px;">
                            @csrf
                            <input class="form-control bg-transparent w-100 py-3 ps-4 pe-5 text-white" type="email"
                                   name="newsletter_email" value="{{ old('newsletter_email') }}"
                                   placeholder="Your email" required maxlength="255" autocomplete="email">
                            <button type="submit" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">Subscribe</button>
                        </form>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h5 class="text-white mb-4">Photo Gallery</h5>
                        <div class="row g-2 pt-2">
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/property-1.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/property-2.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/property-3.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/property-4.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/property-5.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img/property-6.jpg" alt="">
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
            
        </div>
