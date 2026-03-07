@extends('layouts.landing.app')

@section('content')
    <section class="section coming-soon" data-section="section3">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="right-content">
                        <div class="top-content">
                            <h6>Login</h6>
                        </div>
                        <form id="contact" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 ">
                                    <fieldset>
                                        <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                            placeholder="Email" required="" value="{{ old('email') }}">
                                        
                                        <!-- INI BAGIAN PENTING: MENAMPILKAN ERROR EMAIL -->
                                        @error('email')
                                            <div class="text-danger mt-1" style="color: #ff6b6b; font-size: 0.9em; text-align: left;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </fieldset>
                                </div>
                                <div class="col-md-12">
                                    <fieldset>
                                        <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                            placeholder="Password" required="">

                                        <!-- INI BAGIAN PENTING: MENAMPILKAN ERROR PASSWORD -->
                                        @error('password')
                                            <div class="text-danger mt-1" style="color: #ff6b6b; font-size: 0.9em; text-align: left;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </fieldset>
                                </div>
                                <div class="col-md-12">
                                    <fieldset>
                                        <button type="submit" id="form-submit" class="button">Login</button>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection